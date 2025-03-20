<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\TransactionTechnition;
use App\Models\User;
use App\Models\Work;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class WorkProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Laporan ODP Bisnis'
        ];
        return view('pages.transaction.workproduct.index', $data);
    }

    public function getData()
    {
        $data = Work::with(['transaction.userTransaction', 'transaction.branch'])
            ->whereHas('transaction', function ($query) {
                $query->where('purpose', 'other');
            })
            ->orderByDesc('id')
            ->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $transaction = $data->transaction->first();

            $button = '';
            if ($userauth->can('update-work-product')) {
                $button .= ' <a href="' . route('workproduct.edit', ['id' => $transaction->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $transaction->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
            }
            if ($userauth->can('read-work-product')) {
                $button .= ' <a href="' . route('workproduct.details', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
            }
            if ($userauth->can('delete-work-product')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('workproduct.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            // Ambil nama branch dari transaction pertama (jika ada)
            $transaction = $data->transaction->first();
            return $transaction ? $transaction->branch->name : '-';
        })->editColumn('created_at', function ($data) {
            return $data->created_at->format('d M Y H:i');
        })->addColumn('owner', function ($data) {
            // Ambil nama owner dari userTransaction di transaction pertama (jika ada)
            $transaction = $data->transaction->first();
            return $transaction ? $transaction->userTransaction->name : '-';
        })->rawColumns(['action', 'branch', 'created_at', 'owner'])->make(true);
    }

    public function details($id)
    {
        $work = Work::with(['transaction.userTransaction', 'transaction.branch'])->where('id', $id)->firstOrFail();

        $transaction = $work->transaction->first();

        $data = [
            'title' => 'Laporan ODP Bisnis',
            'work' => $work,
            'transaction' => $transaction
        ];

        return view('pages.transaction.workproduct.detail', $data);
    }

    public function create()
    {
        $userRole = auth()->user()->getRoleNames()->first();

        if ($userRole == 'Developer' || $userRole == 'Administrator') {
            $products = Product::all();
        } else {
            $products = Product::whereHas('productRoles', function ($query) use ($userRole) {
                $query->whereHas('role', function ($query) use ($userRole) {
                    $query->where('name', $userRole);
                });
            })->get();
        }

        $data = [
            'title' => 'Laporan ODP Bisnis',
            'branch' => Branch::all(),
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->whereIn('name', ['Technician Odp', 'Technician Backbone']);
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);
                })
                ->orderByDesc('id')
                ->get(),
            // 'technitian' => User::with('roles')
            //     ->whereHas('roles', function ($query) {
            //         $query->where('name', 'Listrik')
            //             ->orWhere('name', 'ODP');
            //     })
            //     ->orderByDesc('id')
            //     ->get(),
        ];

        return view('pages.transaction.workproduct.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|integer|exists:branches,id',
            'technitian' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'branch_id.required' => 'ID cabang wajib diisi.',
            'technitian.required' => 'Teknisi wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'purpose.string' => 'Tujuan harus berupa teks.',
            'branch_id.integer' => 'ID cabang harus berupa angka.',
            'branch_id.exists' => 'ID cabang tidak ditemukan.',
        ]);

        try {
            DB::beginTransaction();
            
            $work = Work::create([
                'name' => $request->name,
            ]);

            $transaction = Transaction::create([
                'branch_id' => $request->branch_id,
                'type' => 'out',
                'user_id' => Auth::user()->id,
                'purpose' => 'other',
                'work_id' => $work->id
            ]);

            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index],
                    'sn_modem' => $request->sn_modem[$index],
                ]);
            }

            foreach ($request->technitian as $index => $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $teknisi
                ]);
            }

            DB::commit();

            return redirect()->route('workproduct')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Data!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e);

            return redirect()->route('workproduct')->with([
                'status' => 'Error!',
                'message' => 'Gagal Menambahkan Data!'
            ]);
        }
    }

    public function show($id)
    {
        $userRole = auth()->user()->getRoleNames()->first();

        // Get products based on user role
        if ($userRole == 'Developer' || $userRole == 'Administrator') {
            $products = Product::all();
        } else {
            $products = Product::whereHas('productRoles', function ($query) use ($userRole) {
                $query->whereHas('role', function ($query) use ($userRole) {
                    $query->where('name', $userRole);
                });
            })->get();
        }

        // Get transaction with relationships
        $transaction = Transaction::with(['WorkTransaction', 'branch', 'Transactionproduct.product', 'Transactiontechnition.user'])
            ->findOrFail($id);

        $data = [
            'title' => 'Laporan ODP Bisnis',
            'transaction' => $transaction,
            'branch' => Branch::all(),
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->whereIn('name', ['Technician Odp', 'Technician Backbone']);
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);
                })
                ->orderByDesc('id')
                ->get(),
        ];

        return view('pages.transaction.workproduct.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'branch_id' => 'required|integer|exists:branches,id',
            'technitian' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'branch_id.required' => 'ID cabang wajib diisi.',
            'technitian.required' => 'Teknisi wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'purpose.string' => 'Tujuan harus berupa teks.',
            'branch_id.integer' => 'ID cabang harus berupa angka.',
            'branch_id.exists' => 'ID cabang tidak ditemukan.',
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::with('WorkTransaction')->findOrFail($id);

            // Update work
            $transaction->WorkTransaction->update([
                'name' => $request->name,
            ]);

            // Update transaction
            $transaction->update([
                'branch_id' => $request->branch_id,
                'purpose' => 'other',
            ]);

            // Delete existing transaction products
            TransactionProduct::where('transaction_id', $transaction->id)->delete();

            // Create new transaction products
            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index],
                    'sn_modem' => $request->sn_modem[$index],
                ]);
            }

            // Delete existing transaction technitions
            TransactionTechnition::where('transaction_id', $transaction->id)->delete();

            // Create new transaction technitions
            foreach ($request->technitian as $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $teknisi
                ]);
            }

            DB::commit();

            return redirect()->route('workproduct')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Data!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('workproduct')->with([
                'status' => 'Error!',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Find the work record
            $work = Work::findOrFail($id);

            // Find associated transaction
            $transaction = Transaction::where('work_id', $work->id)->first();

            if ($transaction) {
                // Delete transaction products
                TransactionProduct::where('transaction_id', $transaction->id)
                    ->chunkById(100, function ($products) {
                        foreach ($products as $product) {
                            $product->delete();
                        }
                    });

                // Delete transaction technicians
                TransactionTechnition::where('transaction_id', $transaction->id)
                    ->chunkById(100, function ($technicians) {
                        foreach ($technicians as $technician) {
                            $technician->delete();
                        }
                    });

                // Delete the transaction
                $transaction->delete();
            }

            // Delete the work record
            $work->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Berhasil Dihapus!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Data Gagal dihapus!',
                'error' => $e->getMessage()
            ]);
        }
    }
}
