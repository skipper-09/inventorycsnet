<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\Deduction;
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
                return $salary->employee->name; // Assuming you have 'name' column in employees table
            })
            ->addColumn('salary_month', function ($salary) {
                return Carbon::parse($salary->salary_month)->format('d-M-Y'); // Format month as 'Month Year'
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
                // if ($userauth->can('read-salary')) {
                //     $button .= ' <a href="' . route('salary.details', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
                // }
                if ($userauth->can('delete-salary')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('salary.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                    class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function create()
    {
        $data = [
            "title" => "Data Gaji Karyawan",
            "employees" => Employee::with(['allowances', 'deductions'])->get(),
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
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Parse the salary month to get start and end dates
            $salaryDate = Carbon::createFromFormat('Y-m', $request->salary_month);
            $startDate = $salaryDate->copy()->startOfMonth();
            $endDate = $salaryDate->copy()->endOfMonth();

            // Check if salary already exists for this employee and month
            $existingSalary = Salary::where('employee_id', $request->employee_id)
                ->whereYear('salary_month', $salaryDate->year)
                ->whereMonth('salary_month', $salaryDate->month)
                ->first();

            if ($existingSalary) {
                throw new Exception('Gaji untuk karyawan ini pada bulan tersebut sudah ada.');
            }

            // Fetch deductions for the employee in the given month
            $totalDeductions = Deduction::where('employee_id', $request->employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Fetch allowances for the employee in the given month
            $totalAllowances = Allowance::where('employee_id', $request->employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Calculate total salary
            $totalSalary = $request->basic_salary_amount +
                $request->bonus +
                $totalAllowances -
                $totalDeductions;

            // Store the salary data
            $salary = Salary::create([
                'employee_id' => $request->employee_id,
                'salary_month' => $salaryDate->format('Y-m-d'),
                'basic_salary_amount' => $request->basic_salary_amount,
                'bonus' => $request->bonus,
                'deduction' => $totalDeductions,
                'allowance' => $totalAllowances,
                'total_salary' => $totalSalary,
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
            "salary" => Salary::findOrFail($id),
            "employees" => Employee::with(['allowances', 'deductions'])->get(),
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
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Parse the salary month to get start and end dates
            $salaryDate = Carbon::createFromFormat('Y-m', $request->salary_month);
            $startDate = $salaryDate->copy()->startOfMonth();
            $endDate = $salaryDate->copy()->endOfMonth();

            // Fetch deductions for the employee in the given month
            $totalDeductions = Deduction::where('employee_id', $request->employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Fetch allowances for the employee in the given month
            $totalAllowances = Allowance::where('employee_id', $request->employee_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            // Calculate total salary
            $totalSalary = $request->basic_salary_amount +
                $request->bonus +
                $totalAllowances -
                $totalDeductions;

            // Fetch the existing salary record
            $salary = Salary::findOrFail($id);

            // Update only the modified fields (bonus, basic_salary_amount)
            $salary->update([
                'employee_id' => $request->employee_id,
                'salary_month' => $salaryDate->format('Y-m-d'),
                'basic_salary_amount' => $request->basic_salary_amount,
                'bonus' => $request->bonus,  // Only update bonus
                'deduction' => $totalDeductions,  // Recalculate deduction
                'allowance' => $totalAllowances,  // Recalculate allowance
                'total_salary' => $totalSalary,  // Recalculate total salary
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
            // Find the salary
            $salary = Salary::findOrFail($id);

            $salary->delete();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Data Gaji Berhasil Dihapus!'
            ]);

        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error deleting salary: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Menghapus Data Gaji!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
