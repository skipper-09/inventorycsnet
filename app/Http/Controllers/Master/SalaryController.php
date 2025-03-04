<?php

namespace App\Http\Controllers\Master;

use App\Exports\SalaryExport;
use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\AllowanceType;
use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
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

    public function getData(Request $request)
    {
        $query = Salary::with(['employee']);

        // Apply month filter if provided
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('salary_month', $request->month);
        }

        // Apply year filter if provided
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('salary_month', $request->year);
        }

        $data = $query->orderByDesc('id')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($salary) {
                return $salary->employee->name;
            })
            ->addColumn('salary_month', function ($salary) {
                return Carbon::parse($salary->salary_month)->format('d-M-Y');
            })
            ->addColumn('basic_salary_amount', function ($salary) {
                return 'Rp ' . number_format($salary->basic_salary_amount, 0, ',', '.');
            })
            ->addColumn('bonus', function ($salary) {
                return 'Rp ' . number_format($salary->bonus, 0, ',', '.');
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
                $button = '';

                if (auth()->user()->can('read-salary')) {
                    $button .= '<a href="' . route('salary.details', ['id' => $data->id]) . '"
                  class="btn btn-sm btn-info"
                   data-id="' . $data->id . '"
                   data-type="details"
                   data-toggle="tooltip"
                   data-placement="bottom"
                   title="Details">
                   <i class="fas fa-eye"></i>
               </a>';
                }
                if (auth()->user()->can('update-salary')) {
                    $button .= '<a href="' . route('salary.edit', ['id' => $data->id]) . '"
                  class="btn btn-sm btn-success ms-1"
                   data-id="' . $data->id . '"
                   data-type="edit"
                   data-toggle="tooltip"
                   data-placement="bottom"
                   title="Edit Data">
                   <i class="fas fa-pen"></i>
               </a>';
                }
                if (auth()->user()->can('delete-salary')) {
                    $button .= ' <button class="btn btn-sm btn-danger action ms-1"
                        data-id="' . $data->id . '"
                        data-type="delete"
                        data-route="' . route('salary.delete', ['id' => $data->id]) . '"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Delete Data">
                    <i class="fas fa-trash-alt"></i>
                </button>';
                }
                return '<div class="d-flex">' . $button . '</div>';
            })->rawColumns(['action', 'employee_name', 'salary_month', 'basic_salary_amount', 'bonus', 'total_salary', 'deduction', 'allowance'])->make(true);
    }

    public function details($id)
    {
        try {
            // Find salary with relationships
            $salary = Salary::with([
                'employee',
                'employee.allowances.allowanceType',
                'employee.deductions.deductionType',
                'employee.position',
                'employee.department'
            ])->findOrFail($id);

            // Calculate percentages for visualization
            $totalIncome = $salary->basic_salary_amount + $salary->bonus + $salary->allowance;
            $netSalary = $salary->total_salary;

            $statistics = [
                'basic_salary_percentage' => ($salary->basic_salary_amount / $totalIncome) * 100,
                'bonus_percentage' => ($salary->bonus / $totalIncome) * 100,
                'allowance_percentage' => ($salary->allowance / $totalIncome) * 100,
                'deduction_percentage' => ($salary->deduction / $totalIncome) * 100,
                'net_percentage' => ($netSalary / $totalIncome) * 100
            ];

            // Group allowances by type for better organization
            $groupedAllowances = $salary->employee->allowances->groupBy('allowance_type_id')
                ->map(function ($items) {
                    return [
                        'type' => $items->first()->allowanceType->name,
                        'total' => $items->sum('amount'),
                        'items' => $items
                    ];
                });

            // Group deductions by type
            $groupedDeductions = $salary->employee->deductions->groupBy('deduction_type_id')
                ->map(function ($items) {
                    return [
                        'type' => $items->first()->deductionType->name,
                        'total' => $items->sum('amount'),
                        'items' => $items
                    ];
                });

            $data = [
                'title' => 'Detail Gaji Karyawan',
                'salary' => $salary,
                'statistics' => $statistics,
                'grouped_allowances' => $groupedAllowances,
                'grouped_deductions' => $groupedDeductions,
                'formatted' => [
                    'basic_salary' => 'Rp ' . number_format($salary->basic_salary_amount, 0, ',', '.'),
                    'bonus' => 'Rp ' . number_format($salary->bonus, 0, ',', '.'),
                    'total_allowance' => 'Rp ' . number_format($salary->allowance, 0, ',', '.'),
                    'total_deduction' => 'Rp ' . number_format($salary->deduction, 0, ',', '.'),
                    'net_salary' => 'Rp ' . number_format($salary->total_salary, 0, ',', '.'),
                    'salary_month' => Carbon::parse($salary->salary_month)->format('F Y')
                ]
            ];

            return view('pages.master.salary.details', $data);

        } catch (Exception $e) {
            Log::error('Salary Detail Error: ' . $e->getMessage());

            return redirect()->route('salary')->with([
                'status' => 'Error!',
                'message' => 'Gagal Menampilkan Detail Gaji: ' . $e->getMessage()
            ]);
        }
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

    public function generateSalarySlip($id)
    {
        // Find the salary record
        $salary = Salary::with('employee')->findOrFail($id);

        // Get the salary_month from the salary record
        $salaryMonth = Carbon::parse($salary->salary_month);
        $year = $salaryMonth->year;
        $month = $salaryMonth->month;

        // Get employee
        $employee = $salary->employee;

        // Get allowances for the specific month using raw DB queries for the date parts
        $allowances = Allowance::with('allowanceType')
            ->where('employee_id', $employee->id)
            ->get();

        // Group allowances by type for the slip
        $grouped_allowances = [];
        foreach ($allowances as $allowance) {
            $type = $allowance->allowanceType->name;
            if (!isset($grouped_allowances[$type])) {
                $grouped_allowances[$type] = 0;
            }
            $grouped_allowances[$type] += $allowance->amount;
        }

        // Format for view
        $formatted_allowances = [];
        foreach ($grouped_allowances as $type => $amount) {
            $formatted_allowances[] = [
                'type' => $type,
                'amount' => $amount
            ];
        }

        // Get deductions for the specific month using raw DB queries
        $deductions = Deduction::with('deductionType')
            ->where('employee_id', $employee->id)
            ->get();

        // Group deductions by type for the slip
        $grouped_deductions = [];
        foreach ($deductions as $deduction) {
            $type = $deduction->deductionType->name;
            if (!isset($grouped_deductions[$type])) {
                $grouped_deductions[$type] = 0;
            }
            $grouped_deductions[$type] += $deduction->amount;
        }

        // Format for view
        $formatted_deductions = [];
        foreach ($grouped_deductions as $type => $amount) {
            $formatted_deductions[] = [
                'type' => $type,
                'amount' => $amount
            ];
        }

        // Calculate totals from filtered data
        $total_allowances = $allowances->sum('amount');
        $total_deductions = $deductions->sum('amount');

        // Calculate net salary
        $net_salary = $salary->basic_salary_amount + $total_allowances - $total_deductions + $salary->bonus;

        // Prepare data for the PDF
        $data = [
            'employee' => $employee,
            'salary' => $salary,
            'allowances' => $formatted_allowances,
            'deductions' => $formatted_deductions,
            'total_allowances' => $total_allowances,
            'total_deductions' => $total_deductions,
            'net_salary' => $net_salary,
            'salaryMonth' => $salaryMonth->format('F Y'),
            'company' => [
                'name' => config('app.company_name', 'Your Company'),
                'address' => config('app.company_address', 'Your Company Address'),
                'phone' => config('app.company_phone', 'Your Company Phone'),
                'email' => config('app.company_email', 'company@example.com'),
            ]
        ];

        // Generate and stream the PDF
        $pdf = Pdf::loadView('pages.master.salary.salary_slip', $data);
        return $pdf->stream("salary_slip_{$employee->name}_{$salaryMonth->format('Y-m')}.pdf");
    }

    public function exportSalary(Request $request)
    {
        // Apply filters
        $query = Salary::with(['employee']);

        // Apply month filter if provided
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('salary_month', $request->month);
        }

        // Apply year filter if provided
        if ($request->has('year') && $request->year != '') {
            $query->whereYear('salary_month', $request->year);
        }

        $data = $query->orderByDesc('id')->get();

        // Generate filename based on filters
        $filename = 'salary_data';
        if ($request->month) {
            $monthName = Carbon::create(null, $request->month, 1)->format('F');
            $filename .= '_' . $monthName;
        }
        if ($request->year) {
            $filename .= '_' . $request->year;
        }
        $filename .= '.xlsx';

        return Excel::download(new SalaryExport($data), $filename);
    }
}