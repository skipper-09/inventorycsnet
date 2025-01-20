<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Customer;
use App\Models\Odp;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\TransactionTechnition;
use App\Models\User;
use App\Models\ZoneOdp;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Customer',
        ];
        return view('pages.master.customer.index', $data);
    }


    public function getData()
    {
        $data = Customer::with('transaction', 'zone', 'branch')->orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('customer.edit', ['id' => $data->id]) . '" data-proses="' . route('customer.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('customer.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            return $data->branch->name;
        })->editColumn('zone', function ($data) {
            return $data->zone->name;
        })->editColumn('purpose', function ($data) {
            $result = "";

            if ($data->transaction->purpose == "psb") {
                $result = 'Pemasangan Baru';
            } else if ($data->transaction->purpose == "repair") {
                $result = 'Perbaikan';
            }
            return $result;
        })->editColumn('sn_modem', function ($data) {
            $snModemArray = json_decode($data->sn_modem);
            if (is_array($snModemArray)) {
                return '<span class="text-uppercase">' . implode(', ', $snModemArray) . '</span>';
            }
            return '<span class="text-uppercase">No Modem</span>';
        })->rawColumns(['action', 'branch', "zone", "sn_modem", 'purpose'])->make(true);
    }


    public function create()
    {
        $data = [
            'title' => 'Customer',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'product' => Product::all(),
            'technition' => User::with('roles')->whereHas('roles', function ($query) {
                $query->where('name', 'Teknisi');
            })->orderByDesc('id')->get()
        ];
        return view('pages.master.customer.add', $data);
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'purpose' => 'required',
            'phone' => 'required',
            'branch_id' => 'required',
            'zone_id' => 'required',
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
                'sn_modem' => json_encode($request->sn_modem),
            ]);

            $trancsation = Transaction::create([
                'branch_id' => $request->branch_id,
                'customer_id' => $customer->id,
                'type' => 'out',
                'purpose' => $request->purpose
            ]);

            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $trancsation->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index]
                ]);


                // $branchProductStock = BranchProductStock::where('branch_id', $request->branch_id)
                //     ->where('product_id', $item)
                //     ->first();

                // if (!$branchProductStock || $branchProductStock->stock < $request->quantity[$index]) {
                //     return response()->json([
                //         'success' => false,
                //         'status' => "Gagal",
                //         'message' => 'Stok ' . $branchProductStock->product->name . ' tidak mencukupi untuk pengeluaran. Stok saat ini: ' . ($branchProductStock->stock ?? 0),
                //     ], 400);
                // }

                // $branchProductStock->stock -= $request->quantity[$index];
                // $branchProductStock->save();
            }
            foreach ($request->tecnition as $index => $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $trancsation->id,
                    'user_id' => $teknisi
                ]);
            }
            DB::commit();

            return redirect()->route('customer');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }



    public function update(Request $request, $id)
    {
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
                'sn_modem' => json_encode($request->sn_modem),
            ]);

            $transaction = Transaction::where('customer_id', $customer->id)->first();

            if (!$transaction) {
                throw new Exception('Transaction not found for the customer.');
            }

            $transaction->update([
                'branch_id' => $request->branch_id,
                'purpose' => $request->purpose
            ]);

            $originalStockChanges = [];

            TransactionProduct::where('transaction_id', $transaction->id)->delete();

            foreach ($request->item_id as $index => $item) {
                TransactionProduct::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item,
                    'quantity' => $request->quantity[$index]
                ]);

                $branchProductStock = BranchProductStock::where('branch_id', $request->branch_id)
                    ->where('product_id', $item)
                    ->first();

                if (!$branchProductStock || $branchProductStock->stock < $request->quantity[$index]) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'status' => "Gagal",
                        'message' => 'Stok ' . $branchProductStock->product->name . ' tidak mencukupi untuk pengeluaran. Stok saat ini: ' . ($branchProductStock->stock ?? 0),
                    ], 400);
                }

                $originalStockChanges[] = [
                    'branch_product_stock' => $branchProductStock,
                    'previous_stock' => $branchProductStock->stock
                ];

                $branchProductStock->stock -= $request->quantity[$index];
                $branchProductStock->save();
            }

            TransactionTechnition::where('transaction_id', $transaction->id)->delete();

            foreach ($request->tecnition as $teknisi) {
                TransactionTechnition::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $teknisi
                ]);
            }

            DB::commit();

            return redirect()->route('customer')->with('success', 'Customer data updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($originalStockChanges as $change) {
                $change['branch_product_stock']->stock = $change['previous_stock'];
                $change['branch_product_stock']->save();
            }

            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }




    // get data odp dinamis
    public function getOdpByZone($zone_id)
    {
        $odp = Odp::where('zone_id', $zone_id)->get();
        return response()->json($odp);
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
                $transactionProducts = TransactionProduct::where('transaction_id', $transaction->id)->get();
                foreach ($transactionProducts as $transactionProduct) {
                    $branchProductStock = BranchProductStock::where('branch_id', $transaction->branch_id)
                        ->where('product_id', $transactionProduct->product_id)
                        ->first();

                    if ($branchProductStock) {
                        $stockChanges[] = [
                            'branchProductStock' => $branchProductStock,
                            'quantity' => $transactionProduct->quantity
                        ];

                        $branchProductStock->stock += $transactionProduct->quantity;
                        $branchProductStock->save();
                    }
                }
                TransactionProduct::where('transaction_id', $transaction->id)->delete();
                TransactionTechnition::where('transaction_id', $transaction->id)->delete();
                $transaction->delete();
            }

            $customer->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Customer data and related transactions have been deleted successfully, and stock has been restored.',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            foreach ($stockChanges as $change) {
                $change['branchProductStock']->stock -= $change['quantity'];
                $change['branchProductStock']->save();
            }

            return response()->json([
                'success' => false,
                'status' => "Failed",
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


}
