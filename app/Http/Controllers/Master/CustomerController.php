<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Customer;
use App\Models\Odp;
use App\Models\Product;
use App\Models\ProductRole;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\TransactionTechnition;
use App\Models\User;
use App\Models\ZoneOdp;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Laporan Retail',
        ];
        return view('pages.report.customer.index', $data);
    }


    public function getData(Request $request)
    {
        $query = Customer::with('transaction', 'zone', 'branch')->orderByDesc('created_at');

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        $data = $query->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            if ($userauth->can('update-customer')) {
                $button .= ' <a href="' . route('customer.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
            }

            if ($userauth->can('read-customer')) {
                $button .= ' <a href="' . route('customer.detail', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
            }

            if ($userauth->can('delete-customer')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('customer.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            return $data->branch->name;
        })->editColumn('zone', function ($data) {
            return $data->zone->name ?? "";
        })->editColumn('purpose', function ($data) {
            $result = "";

            if ($data->transaction->purpose == "psb") {
                $result = 'Pemasangan Baru';
            } else if ($data->transaction->purpose == "repair") {
                $result = 'Perbaikan';
            }
            return $result;
        })
            // ->editColumn('sn_modem', function ($data) {
            //     $snModemArray = json_decode($data->sn_modem);
            //     $snModemArray = array_filter($snModemArray, function ($value) {
            //         return !empty($value);
            //     });

            //     if (count($snModemArray) > 0) {
            //         return '<span class="text-uppercase">' . implode(', ', $snModemArray) . '</span>';
            //     }

            //     return '<span class="text-uppercase">No Modem</span>';
            // })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->format('d M Y H:i');
            })->addColumn('owner', function ($data) {
                return $data->transaction->userTransaction->name;
            })->rawColumns(['action', 'branch', "zone", 'purpose', 'created_at', 'owner'])->make(true);
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
            'title' => 'Laporan Retail',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);  // Filter by roles: Employee or Technician
                })
                ->orderByDesc('id')
                ->get()
        ];
        return view('pages.report.customer.add', $data);
    }

    public function createPsb()
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
            'title' => 'Laporan Retail (PSB)',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'type' => 'psb',
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);  // Filter by roles: Employee or Technician
                })
                ->orderByDesc('id')
                ->get()
        ];
        return view('pages.report.customer.add', $data);
    }

    public function createRepair()
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
            'title' => 'Laporan Retail (Perbaikan)',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'type' => 'repair',
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);  // Filter by roles: Employee or Technician
                })
                ->orderByDesc('id')
                ->get()
        ];

        return view('pages.report.customer.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'phone' => 'required|integer|regex:/^62[0-9]{9,11}$/',
            'branch_id' => 'required|integer|exists:branches,id',
            // 'zone_id' => 'required',
            'address' => 'required|string|max:255',
            'tecnition' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'purpose.required' => 'Tujuan wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.integer' => 'Nomor telepon harus berupa angka.',
            'phone.regex' => 'Nomor telepon harus dimulai dengan 62 dan memiliki panjang antara 11 hingga 12 digit.',
            'branch_id.required' => 'ID cabang wajib diisi.',
            'zone_id.required' => 'ID zona wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'tecnition.required' => 'Teknisi wajib diisi.',

            'name.string' => 'Nama harus berupa teks.',
            'purpose.string' => 'Tujuan harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 12 karakter.',
            'branch_id.integer' => 'ID cabang harus berupa angka.',
            'branch_id.exists' => 'ID cabang tidak ditemukan.',
            'address.string' => 'Alamat harus berupa teks.',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::create([
                'branch_id' => $request->branch_id,
                'zone_id' => $request->zone_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'odp_id' => $request->odp_id,
                // 'sn_modem' => json_encode($request->sn_modem),
            ]);

            $transaction = Transaction::create([
                'branch_id' => $request->branch_id,
                'customer_id' => $customer->id,
                'type' => 'out',
                'user_id' => Auth::user()->id,
                'purpose' => $request->purpose
            ]);

            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index],
                    'sn_modem' => $request->sn_modem[$index],
                ]);
            }
            foreach ($request->tecnition as $index => $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $teknisi
                ]);
            }

            try {
                if ($request->type == 'psb') {
                    activity('psb')
                        ->causedBy(Auth::user())
                        ->event('created')
                        ->performedOn($customer)
                        ->withProperties([
                            'transaction_id' => $transaction->id,
                            'items' => $request->item_id,
                            'purpose' => $request->purpose,
                            'address' => $customer->address
                        ])
                        ->log("Detail Pembangunan Sambungan Baru (PSB)");
                } else {
                    activity('repair')
                        ->causedBy(Auth::user())
                        ->event('created')
                        ->performedOn($customer)
                        ->withProperties([
                            'transaction_id' => $transaction->id,
                            'items' => $request->item_id,
                            'purpose' => $request->purpose,
                            'address' => $customer->address
                        ])
                        ->log("Perbaikan Pelanggan berhasil dilakukan");
                }
            } catch (Exception $e) {
                Log::error('Activity logging error: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('customer')->with('Success!', 'Data berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('customer')->with([
                'status' => 'Error!',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
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
        $customer = Customer::findOrFail($id);
        $data = [
            'title' => 'Laporan Retail',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'customer' => $customer,
            'product' => $products,
            'technitian' => User::with('employee.position', 'roles')
                ->whereHas('employee.position', function ($query) {
                    $query->where('name', 'Technician');  // Filter by position name
                })
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Employee', 'Technician']);
                })
                ->orderByDesc('id')
                ->get()
        ];
        return view('pages.report.customer.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required',
            'purpose' => 'required',
            'phone' => 'required',
            'branch_id' => 'required',
            'zone_id' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($id);

            $customer->update([
                'branch_id' => $request->branch_id,
                'zone_id' => $request->zone_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'odp_id' => $request->odp_id,
                // 'sn_modem' => json_encode($request->sn_modem),
            ]);


            $transaction = Transaction::where('customer_id', $customer->id)->first();

            if (!$transaction) {
                throw new Exception('Transaction not found for the customer.');
            }

            $transaction->update([
                'branch_id' => $request->branch_id,
                'purpose' => $request->purpose
            ]);

            if (is_array($request->item_id) && is_array($request->quantity)) {
                TransactionProduct::where('transaction_id', $transaction->id)->delete();

                foreach ($request->item_id as $index => $item) {
                    if (isset($request->quantity[$index])) {
                        // Check if sn_modem index exists to avoid "Undefined array key" error
                        $snModem = isset($request->sn_modem[$index]) ? $request->sn_modem[$index] : null;

                        TransactionProduct::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $item,
                            'quantity' => $request->quantity[$index],
                            'sn_modem' => $snModem,
                        ]);
                    }
                }
            }

            if (is_array($request->tecnition)) {
                TransactionTechnition::where('transaction_id', $transaction->id)->delete();
                foreach ($request->tecnition as $tecnisi) {
                    TransactionTechnition::create([
                        'transaction_id' => $transaction->id,
                        'user_id' => $tecnisi
                    ]);
                }
            }

            try {
                if ($request->purpose == 'psb') {
                    activity('psb')
                        ->causedBy(Auth::user())
                        ->event('updated')
                        ->performedOn($customer)
                        ->withProperties([
                            'transaction_id' => $transaction->id,
                            'items' => $request->item_id ?? [],
                            'purpose' => $request->purpose,
                            'address' => $customer->address
                        ])
                        ->log("Detail Pembangunan Sambungan Baru (PSB)");
                } else {
                    activity('repair')
                        ->causedBy(Auth::user())
                        ->event('updated')
                        ->performedOn($customer)
                        ->withProperties([
                            'transaction_id' => $transaction->id,
                            'items' => $request->item_id ?? [],
                            'purpose' => $request->purpose,
                            'address' => $customer->address
                        ])
                        ->log("Perbaikan Pelanggan berhasil dilakukan");
                }
            } catch (Exception $e) {
                Log::error('Activity logging error: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('customer')->with('Success!', 'Customer data updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('customer')->with([
                'status' => 'Error!',
                'message' => $e->getMessage()
            ]);
        }
    }

    // get data odp dinamis
    public function getOdpByZone($zone_id)
    {
        $odp = Odp::where('zone_id', $zone_id)->get();
        return response()->json($odp);
    }

    //detail
    public function details($id)
    {
        $customer = Customer::with(['transaction', 'branch', 'zone'])
            ->where('id', $id)->firstOrFail();

        // dd($customer);
        $data = [
            'title' => 'Detail Laporan Retail',
            'customer' => $customer
        ];

        return view('pages.report.customer.detail', $data);
    }

    //destroy data
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($id);

            $transaction = Transaction::where('customer_id', $customer->id)->first();

            if ($transaction) {
                $stockChanges = [];
                // $transactionProducts = TransactionProduct::where('transaction_id', $transaction->id)->get();
                // foreach ($transactionProducts as $transactionProduct) {
                //     $branchProductStock = BranchProductStock::where('branch_id', $transaction->branch_id)
                //         ->where('product_id', $transactionProduct->product_id)
                //         ->first();

                //     if ($branchProductStock) {
                //         $stockChanges[] = [
                //             'branchProductStock' => $branchProductStock,
                //             'quantity' => $transactionProduct->quantity
                //         ];

                //         $branchProductStock->stock += $transactionProduct->quantity;
                //         $branchProductStock->save();
                //     }
                // }
                TransactionProduct::where('transaction_id', $transaction->id)->delete();
                TransactionTechnition::where('transaction_id', $transaction->id)->delete();
                $transaction->delete();
            }

            $customer->delete();

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($customer->toArray())
                ->log("Data Laporan Retail berhasil dihapus.");

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Data Berhasil Di hapus',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($stockChanges as $change) {
                $change['branchProductStock']->stock -= $change['quantity'];
                $change['branchProductStock']->save();
            }

            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Data Gagal dihapus!',
                'error' => $e->getMessage()
            ]);
        }
    }
}
