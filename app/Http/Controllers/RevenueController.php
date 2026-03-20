<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Change this to your actual Order/Sales model
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index()
    {
        // Get revenue grouped by day for the last 7 days
        $revenueData = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as daily_total') // Change 'total' to your column name
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $totalWeekly = $revenueData->sum('daily_total');

        return view('admin.revenue', compact('revenueData', 'totalWeekly'));
    }
}