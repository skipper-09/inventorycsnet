<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\AllowanceType;
use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Gaji Karyawan',
        ];

        return view('pages.master.salary.index', $data);
    }

    public function getData()
    {
        $data = Salary::with([
            'employee',
        ])->orderByDesc('id')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($salary) {
                return $salary->employee->name;
            })
            ->addColumn('salary_month', function ($salary) {
                return Carbon::parse($salary->salary_month)->format('d-M-Y');
            })
            ->addColumn('total_salary', function ($salary) {
                return 'Rp ' . number_format($salary->total_salary, 0, ',', '.');
            })
            ->addColumn('deduction', function ($salary) {
                return 'Rp ' . number_format($salary->deduction, 0, ',', '.');
            })
            ->addColumn('allowance', function ($salary) {
                return 'Rp ' . number_format($salary->allowance, 0, ',', '.');
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-salary')) {
                    $button .= ' <a href="' . route('salary.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                if ($userauth->can('delete-salary')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('salary.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i class="fas fa-trash"></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function create()
    {
        $data = [
            "title" => "Data Gaji Karyawan",
            "employees" => Employee::all(),
            "allowance_types" => AllowanceType::all(),
            "deduction_types" => DeductionType::all(),
            "months" => collect(range(1, 12))->map(function ($month) {
                return [
                    'value' => Carbon::create(null, $month, 1)->format('Y-m'),
                    'label' => Carbon::create(null, $month, 1)->format('F Y')
                ];
            }),
        ];

        return view("pages.master.salary.add", $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|date_format:Y-m',
            'basic_salary_amount' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'allowances' => 'array',
            'allowances.*.allowance_type_id' => 'required|exists:allowance_types,id',
            'allowances.*.amount' => 'required|numeric|min:0',
            'deductions' => 'array',
            'deductions.*.deduction_type_id' => 'required|exists:deduction_types,id',
            'deductions.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $salaryDate = Carbon::createFromFormat('Y-m', $request->salary_month);
            
            // Check if salary already exists for this employee and month
            $existingSalary = Salary::where('employee_id', $request->employee_id)
                ->whereYear('salary_month', $salaryDate->year)
                ->whereMonth('salary_month', $salaryDate->month)
                ->first();

            if ($existingSalary) {
                throw new Exception('Gaji untuk karyawan ini pada bulan tersebut sudah ada.');
            }

            // Create Salary record
            $salary = Salary::create([
                'employee_id' => $request->employee_id,
                'salary_month' => $salaryDate->format('Y-m-d'),
                'basic_salary_amount' => $request->basic_salary_amount,
                'bonus' => $request->bonus,
                'deduction' => 0, // Will be updated after creating deductions
                'allowance' => 0, // Will be updated after creating allowances
                'total_salary' => $request->basic_salary_amount + $request->bonus,
            ]);

            // Create Allowances
            $totalAllowances = 0;
            if (!empty($request->allowances)) {
                foreach ($request->allowances as $allowanceData) {
                    Allowance::create([
                        'employee_id' => $request->employee_id,
                        'allowance_type_id' => $allowanceData['allowance_type_id'],
                        'amount' => $allowanceData['amount'],
                    ]);
                    $totalAllowances += $allowanceData['amount'];
                }
            }

            // Create Deductions
            $totalDeductions = 0;
            if (!empty($request->deductions)) {
                foreach ($request->deductions as $deductionData) {
                    Deduction::create([
                        'employee_id' => $request->employee_id,
                        'deduction_type_id' => $deductionData['deduction_type_id'],
                        'amount' => $deductionData['amount'],
                    ]);
                    $totalDeductions += $deductionData['amount'];
                }
            }

            // Update salary totals
            $salary->update([
                'allowance' => $totalAllowances,
                'deduction' => $totalDeductions,
                'total_salary' => $request->basic_salary_amount + $request->bonus + $totalAllowances - $totalDeductions,
            ]);

            DB::commit();

            return redirect()->route('salary')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Data Gaji!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Salary Creation Error: ' . $e->getMessage());

            return redirect()->route('salary')->with([
                'status' => 'Error!',
                'message' => 'Gagal Menambahkan Data: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $data = [
            "title" => "Data Gaji Karyawan",
            "salary" => Salary::with(['employee.allowances', 'employee.deductions'])->findOrFail($id),
            "employees" => Employee::all(),
            "allowance_types" => AllowanceType::all(),
            "deduction_types" => DeductionType::all(),
            "months" => collect(range(1, 12))->map(function ($month) {
                return [
                    'value' => Carbon::create(null, $month, 1)->format('Y-m'),
                    'label' => Carbon::create(null, $month, 1)->format('F Y')
                ];
            }),
        ];

        return view("pages.master.salary.edit", $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_month' => 'required|date_format:Y-m',
            'basic_salary_amount' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'allowances' => 'array',
            'allowances.*.allowance_type_id' => 'required|exists:allowance_types,id',
            'allowances.*.amount' => 'required|numeric|min:0',
            'deductions' => 'array',
            'deductions.*.deduction_type_id' => 'required|exists:deduction_types,id',
            'deductions.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $salary = Salary::findOrFail($id);
            $salaryDate = Carbon::createFromFormat('Y-m', $request->salary_month);

            // Delete existing allowances and deductions
            Allowance::where('employee_id', $salary->employee_id)->delete();
            Deduction::where('employee_id', $salary->employee_id)->delete();

            // Create new Allowances
            $totalAllowances = 0;
            if (!empty($request->allowances)) {
                foreach ($request->allowances as $allowanceData) {
                    Allowance::create([
                        'employee_id' => $request->employee_id,
                        'allowance_type_id' => $allowanceData['allowance_type_id'],
                        'amount' => $allowanceData['amount'],
                    ]);
                    $totalAllowances += $allowanceData['amount'];
                }
            }

            // Create new Deductions
            $totalDeductions = 0;
            if (!empty($request->deductions)) {
                foreach ($request->deductions as $deductionData) {
                    Deduction::create([
                        'employee_id' => $request->employee_id,
                        'deduction_type_id' => $deductionData['deduction_type_id'],
                        'amount' => $deductionData['amount'],
                    ]);
                    $totalDeductions += $deductionData['amount'];
                }
            }

            // Update salary record
            $salary->update([
                'employee_id' => $request->employee_id,
                'salary_month' => $salaryDate->format('Y-m-d'),
                'basic_salary_amount' => $request->basic_salary_amount,
                'bonus' => $request->bonus,
                'allowance' => $totalAllowances,
                'deduction' => $totalDeductions,
                'total_salary' => $request->basic_salary_amount + $request->bonus + $totalAllowances - $totalDeductions,
            ]);

            DB::commit();

            return redirect()->route('salary')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Data Gaji!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Salary Update Error: ' . $e->getMessage());

            return redirect()->route('salary')->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengupdate Data: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $salary = Salary::findOrFail($id);
            
            // Delete related allowances and deductions
            Allowance::where('employee_id', $salary->employee_id)->delete();
            Deduction::where('employee_id', $salary->employee_id)->delete();
            
            // Delete salary
            $salary->delete();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Gaji Berhasil Dihapus!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting salary: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Menghapus Data Gaji!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}