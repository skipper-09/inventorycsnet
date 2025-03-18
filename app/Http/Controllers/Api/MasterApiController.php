<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\ZoneOdp;
use Illuminate\Http\Request;

class MasterApiController extends Controller
{
    public function getZone(){
        $data = ZoneOdp::with(['odps'])->orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Zone dengan odp.',
            'data' => $data,
        ]);
    }


    public function GetTechnition(){
        $data =  User::with('employee.position')
        ->whereHas('employee.position', function ($query) {
            $query->where('name', 'Technitian');
        })
        ->orderByDesc('created_at')
        ->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Technition.',
            'data' => $data,
        ]);
    }


    public function GetProduct(){
        $data = Product::with(['unit'])->orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Barang.',
            'data' => $data,
        ]);
    }

    public function GetBranch(){
        $data = Branch::orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Cabang.',
            'data' => $data,
        ]);
    }
}
