<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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

        
        $hour = date('H');    
        if ($hour >= 5 && $hour < 11) {
            $greeting = "Selamat Pagi";
        } elseif ($hour >= 11 && $hour < 14) {
            $greeting = "Selamat Siang";
        }else if ($hour >= 14 && $hour < 18) {
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
            'branch'=> $branches->count(),
            'product' => $products->count(),
            'user'=> User::where('name','!=',"Developer")->get()->count()
        ];

        return view('pages.dashboard.index', $data);
    }

}
