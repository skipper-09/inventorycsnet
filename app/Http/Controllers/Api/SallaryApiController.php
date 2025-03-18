<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use Illuminate\Http\Request;

class SallaryApiController extends Controller
{
    public function index(Request $request){
        $user = $request->user();

        $data = Salary::with([
            'employee:id,name',
            'employee.deductions.deductionType:id,name',  
            'employee.allowances.allowanceType:id,name',  
        ])->where('employee_id', $user->employee_id)
            ->get();
        
    

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Sallary',
            'data' => $data,
        ]);
    }
}
