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
        $employee = Employee::with(['department', 'position', 'tasks', 'leaves', 'salaries'])
            ->findOrFail($currentUser->employee_id);

        // Menghitung jumlah tugas aktif
        $activeTasks = $employee->tasks()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        // Menghitung sisa cuti
        $usedLeaves = $employee->leaves()
            ->whereYear('created_at', date('Y'))
            ->where('status', 'approved')
            ->get()
            ->sum(function ($leave) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            });

        $remainingLeaves = 12 - $usedLeaves; // Asumsi 12 hari cuti tahunan

        // Mengambil gaji bulan ini
        $currentSalary = $employee->salaries()
            ->whereYear('salary_month', date('Y'))
            ->whereMonth('salary_month', date('m'))
            ->first();

        $netSalary = $currentSalary ? ($currentSalary->basic_salary_amount + $currentSalary->bonus + $currentSalary->allowance - $currentSalary->deduction) : 0;

        // Menghitung jumlah tugas dengan tenggat waktu yang akan datang
        $upcomingDeadlines = $employee->tasks()
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();

        // Mengambil tugas terbaru
        $recentTasks = $employee->tasks()
            ->with('assignes') // Load task assignments
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($task) {
                $task->status_color = [
                    'pending' => 'warning',
                    'in_progress' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                ][$task->status] ?? 'secondary';
                $task->deadline_formatted = Carbon::parse($task->end_date)->format('d M Y');
                return $task;
            });

        // Mengambil cuti terbaru
        $recentLeaves = $employee->leaves()
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($leave) {
                $leave->status_color = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger'
                ][$leave->status] ?? 'secondary';
                $leave->created_at_formatted = Carbon::parse($leave->created_at)->format('d M Y');
                $leave->start_date_formatted = Carbon::parse($leave->start_date)->format('d M Y');
                $leave->end_date_formatted = Carbon::parse($leave->end_date)->format('d M Y');
                return $leave;
            });

        // Mengambil riwayat gaji
        $salaryHistory = $employee->salaries()
            ->select('salary_month', 'basic_salary_amount', 'bonus', 'deduction', 'allowance') // Select required columns
            ->orderBy('salary_month', 'desc')
            ->take(6)
            ->get()
            ->map(function ($salary) {
                // Calculate net_salary dynamically
                $salary->amount = $salary->basic_salary_amount + $salary->bonus + $salary->allowance - $salary->deduction;
                $salary->month = Carbon::parse($salary->salary_month)->format('M Y'); // Format the date
                return $salary;
            })
            ->reverse();

        // Menentukan ucapan berdasarkan waktu
        $hour = date('H');
        if ($hour >= 5 && $hour < 11) {
            $greeting = "Selamat Pagi";
        } elseif ($hour >= 11 && $hour < 14) {
            $greeting = "Selamat Siang";
        } elseif ($hour >= 14 && $hour < 18) {
            $greeting = "Selamat Sore";
        } else {
            $greeting = "Selamat Malam";
        }

        $data = [
            'title' => 'Dashboard',
            'greeting' => $greeting,
            'employee' => $employee,
            'activeTasks' => $activeTasks,
            'remainingLeaves' => $remainingLeaves,
            'currentSalary' => $currentSalary,
            'upcomingDeadlines' => $upcomingDeadlines,
            'recentTasks' => $recentTasks,
            'recentLeaves' => $recentLeaves,
            'salaryHistory' => $salaryHistory,
        ];

        return view('pages.dashboard.employeedashboard', $data);
    }
}

