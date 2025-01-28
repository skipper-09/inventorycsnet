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
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class WorkProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Work Product',
        ];
        return view('pages.transaction.workproduct.index', $data);
    }

    public function getData()
    {
        $data = Work::with(['transaction.userTransaction', 'transaction.branch'])->orderByDesc('id')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            $button .= ' <a href="' . route('workproduct.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
            // $button .= ' <a href="' . route('workproduct.detail', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
            // $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('workproduct.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
            //                                          class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            // Ambil nama branch dari transaction pertama (jika ada)
            $transaction = $data->transaction->first();
            return $transaction ? $transaction->branch->name : '-';
        })->editColumn('purpose', function ($data) {
            // Ambil nama branch dari transaction pertama (jika ada)
            $transaction = $data->transaction->first();
            return $transaction ? $transaction->purpose : '-';
        })->editColumn('created_at', function ($data) {
            return $data->created_at->format('d M Y H:i');
        })->addColumn('owner', function ($data) {
            // Ambil nama owner dari userTransaction di transaction pertama (jika ada)
            $transaction = $data->transaction->first();
            return $transaction ? $transaction->userTransaction->name : '-';
        })->rawColumns(['action', 'branch', 'purpose', 'created_at', 'owner'])->make(true);
    }

    public function details($id)
    {
        $customer = Customer::with(['transaction', 'branch', 'zone'])
            ->where('id', $id)->firstOrFail();

        // dd($customer);
        $data = [
            'title' => 'Detail Customer',
            'customer' => $customer
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
            'title' => 'Work Product',
            'branch' => Branch::all(),
            'product' => $products,
            'technitian' => User::with('roles')
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'Listrik')
                        ->orWhere('name', 'ODP');
                })
                ->orderByDesc('id')
                ->get(),
        ];

        return view('pages.transaction.workproduct.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'branch_id' => 'required|integer|exists:branches,id',
            'technitian' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'purpose.required' => 'Tujuan wajib diisi.',
            'branch_id.required' => 'ID cabang wajib diisi.',
            'technitian.required' => 'Teknisi wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'purpose.string' => 'Tujuan harus berupa teks.',
            'branch_id.integer' => 'ID cabang harus berupa angka.',
            'branch_id.exists' => 'ID cabang tidak ditemukan.',
        ]);

        DB::beginTransaction();

        try {
            $work = Work::create([
                'name' => $request->name,
            ]);

            $transaction = Transaction::create([
                'branch_id' => $request->branch_id,
                'type' => 'out',
                'user_id' => FacadesAuth::user()->id,
                'purpose' => $request->purpose,
                'work_id' => $work->id
            ]);

            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index]
                ]);
            }

            foreach ($request->technitian as $index => $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $teknisi
                ]);
            }

            DB::commit();

            return redirect()->route('workproduct');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
