<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;

        // Untuk role selain 'Employee'
        if ($currentUserRole !== 'Employee') {
            // Data untuk admin atau role lainnya
            $branches = Branch::all()->keyBy('id');
            $branchProductStocks = BranchProductStock::with('branch', 'product')
                ->get()
                ->groupBy('branch_id');

            $branchNames = [];
            $productStocks = [];
            $products = Product::all()->keyBy('id');
            $productNames = $products->pluck('name')->toArray();

            foreach ($branchProductStocks as $branchId => $stocks) {
                $branch = $branches->get($branchId);
                $branchNames[] = $branch->name;
                $productStocksForBranch = array_fill(0, count($products), 0);
                foreach ($stocks as $stock) {
                    $productStocksForBranch[$stock->product_id - 1] = $stock->stock;
                }
                $productStocks[] = $productStocksForBranch;
            }

            // Menentukan ucapan berdasarkan waktu
            $hour = date('H');
            if ($hour >= 5 && $hour < 11) {
                $greeting = "Selamat Pagi";
            } elseif ($hour >= 11 && $hour < 14) {
                $greeting = "Selamat Siang";
            } else if ($hour >= 14 && $hour < 18) {
                $greeting = "Selamat Sore";
            } else {
                $greeting = "Selamat Malam";
            }

            $data = [
                'title' => 'Dashboard',
                'branchNames' => $branchNames,
                'productStocks' => $productStocks,
                'productNames' => $productNames,
                'greeting' => $greeting,
                'branch' => $branches->count(),
                'product' => $products->count(),
                'user' => User::where('name', '!=', "Developer")->get()->count()
            ];

            return view('pages.dashboard.index', $data); // untuk admin dan lainnya
        }

        // Mengambil data employee berdasarkan ID yang terkait dengan user
        $employee = Employee::with(['department', 'position', 'leaves', 'salaries'])
            ->findOrFail($currentUser->employee_id);

        // Menghitung sisa cuti
        $currentYear = date('Y');
        $usedLeaves = $employee->leaves()
            ->whereYear('created_at', $currentYear)
            ->where('status', 'approved')
            ->get()
            ->sum(function ($leave) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            });

        $annualLeaveAllowance = 12; // Asumsi 12 hari cuti tahunan
        $remainingLeaves = $annualLeaveAllowance - $usedLeaves;

        // Mengambil gaji bulan ini
        $currentMonth = date('m');
        $currentSalary = $employee->salaries()
            ->whereYear('salary_month', $currentYear)
            ->whereMonth('salary_month', $currentMonth)
            ->first();

        $netSalary = 0;
        if ($currentSalary) {
            $netSalary = $currentSalary->basic_salary_amount +
                $currentSalary->bonus +
                $currentSalary->allowance -
                $currentSalary->deduction;
        }

        // Mengambil cuti terbaru
        $statusColors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ];

        $recentLeaves = $employee->leaves()
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($leave) use ($statusColors) {
                $leave->status_color = $statusColors[$leave->status] ?? 'secondary';
                $leave->created_at_formatted = Carbon::parse($leave->created_at)->format('d M Y');
                $leave->start_date_formatted = Carbon::parse($leave->start_date)->format('d M Y');
                $leave->end_date_formatted = Carbon::parse($leave->end_date)->format('d M Y');
                return $leave;
            });

        // Mengambil riwayat gaji
        $salaryHistory = $employee->salaries()
            ->select('salary_month', 'basic_salary_amount', 'bonus', 'deduction', 'allowance')
            ->orderBy('salary_month', 'desc')
            ->take(6)
            ->get()
            ->map(function ($salary) {
                // Calculate net_salary dynamically
                $salary->amount = $salary->basic_salary_amount +
                    $salary->bonus +
                    $salary->allowance -
                    $salary->deduction;
                $salary->month = Carbon::parse($salary->salary_month)->format('M Y');
                return $salary;
            })
            ->reverse();

        // Menentukan ucapan berdasarkan waktu
        $hour = (int) date('H');
        $greeting = match (true) {
            $hour >= 5 && $hour < 11 => "Selamat Pagi",
            $hour >= 11 && $hour < 14 => "Selamat Siang",
            $hour >= 14 && $hour < 18 => "Selamat Sore",
            default => "Selamat Malam"
        };

        $data = [
            'title' => 'Dashboard',
            'greeting' => $greeting,
            'employee' => $employee,
            'remainingLeaves' => $remainingLeaves,
            'netSalary' => $netSalary,
            'currentSalary' => $currentSalary,
            'recentLeaves' => $recentLeaves,
            'salaryHistory' => $salaryHistory,
        ];

        return view('pages.dashboard.employeedashboard', $data);
    }
}

