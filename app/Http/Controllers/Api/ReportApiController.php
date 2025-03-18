<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\TransactionTechnition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportApiController extends Controller
{
    public function PsbReport(Request $request){
        $validator = Validator::make($request->all(), 
        [
            'name' => 'required|string|max:255',
            // 'purpose' => 'required|string|max:255',
            'phone' => 'required|integer|regex:/^62[0-9]{9,11}$/',
            'branch_id' => 'required|integer|exists:branches,id',
            // 'zone_id' => 'required',
            'address' => 'required|string|max:255',
            'tecnition' => 'required',
        ], [
            'name.required' => 'Nama wajib diisi.',
            // 'purpose.required' => 'Tujuan wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.integer' => 'Nomor telepon harus berupa angka.',
            'phone.regex' => 'Nomor telepon harus dimulai dengan 62 dan memiliki panjang antara 11 hingga 12 digit.',
            'branch_id.required' => 'ID cabang wajib diisi.',
            'zone_id.required' => 'ID zona wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'tecnition.required' => 'Teknisi wajib diisi.',

            'name.string' => 'Nama harus berupa teks.',
            // 'purpose.string' => 'Tujuan harus berupa teks.',
            'phone.max' => 'Nomor telepon maksimal 12 karakter.',
            'branch_id.integer' => 'ID cabang harus berupa angka.',
            'branch_id.exists' => 'ID cabang tidak ditemukan.',
            'address.string' => 'Alamat harus berupa teks.',
        ]);    
    

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

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
            'user_id' => $user->id,
            'purpose' => "psb"
        ]);

        foreach ($request->item_id as $index => $item) {
            TransactionProduct::create([
                'transaction_id' => $trancsation->id,
                'product_id' => $item,
                'quantity' => $request->quantity[$index]
            ]);
        }
        foreach ($request->tecnition as $index => $teknisi) {
            TransactionTechnition::create([
                'transaction_id' => $trancsation->id,
                'user_id' => $teknisi
            ]);
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Laporan Pemasangan Baru.',
        ]);


    }
}
