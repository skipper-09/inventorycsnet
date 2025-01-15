<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Odp;
use App\Models\Product;
use App\Models\Transaction;
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
        $data = Customer::with('transaction','zone','branch')->orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data->id]) . '" data-proses="' . route('unitproduk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch',function ($data){
            return $data->branch->name;
        })->editColumn('zone',function ($data){
            return $data->zone->name;
        })->editColumn('sn_modem',function ($data){
            $snModemArray = json_decode($data->sn_modem);
            if (is_array($snModemArray)) {
                return '<span class="text-uppercase">' . implode(', ', $snModemArray) . '</span>';
            }
            return '<span class="text-uppercase">No Modem</span>';
        })->rawColumns(['action','branch',"zone","sn_modem"])->make(true);
    }


    public function create()
    {
        $data = [
            'title' => 'Customer',
            "zone" => ZoneOdp::all(),
            'branch' => Branch::all(),
            'product' => Product::all(),
            'technition' => User::with('roles')->where('name', ['Teknisi'])->orderByDesc('id')->get()
        ];
        return view('pages.master.customer.add', $data);
    }


    public function store(Request $request)
    {
        // $request->validate([
        //     'sn_modem' => 'required|array', 
        //     'sn_modem.*' => 'string|required', 
        // ]);

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





    // get data odp dinamis
    public function getOdpByZone($zone_id)
    {
        $odp = Odp::where('zone_id', $zone_id)->get();
        return response()->json($odp);
    }
}
