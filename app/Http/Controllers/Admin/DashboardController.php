<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Detection;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stat cards
        $totalDetections = Detection::count();
        $cercosporaCount = Detection::where('predicted_class', 'Cercospora')->count();
        $commonRustCount = Detection::where('predicted_class', 'Common_Rust')->count();
        $northernLeafBlightCount = Detection::where('predicted_class', 'Northern_Leaf_Blight')->count();
        $healthyCount = Detection::where('predicted_class', 'Healthy')->count();

        // Recent detections
        $recentDetectionsQuery = Detection::orderBy('created_at', 'desc');
        
        $monthFilter = request('month_filter');
        
        if ($monthFilter) {
            $year = substr($monthFilter, 0, 4);
            $month = substr($monthFilter, 5, 2);
            $recentDetectionsQuery->whereYear('created_at', $year)
                                  ->whereMonth('created_at', $month);
        }
        
        $recentDetections = $recentDetectionsQuery->paginate(5, ['*'], 'detections_page')->withQueryString();

        // Average Confidence
        $averageConfidence = Detection::avg('confidence') ?? 0;

        // Products
        $totalProducts = Produk::count();
        $recentProducts = Produk::with('jenisProduk')->orderBy('created_at', 'desc')->paginate(5, ['*'], 'products_page')->withQueryString();

        // Chart data: detections per day/week/month/year
        $filter = request('filter', 'weekly');
        $query = Detection::select('created_at', 'predicted_class');
        
        $dayMap = ['Mon'=>'Sen', 'Tue'=>'Sel', 'Wed'=>'Rab', 'Thu'=>'Kam', 'Fri'=>'Jum', 'Sat'=>'Sab', 'Sun'=>'Min'];
        $monthMap = ['01'=>'Jan', '02'=>'Feb', '03'=>'Mar', '04'=>'Apr', '05'=>'Mei', '06'=>'Jun', '07'=>'Jul', '08'=>'Agt', '09'=>'Sep', '10'=>'Okt', '11'=>'Nov', '12'=>'Des'];

        if ($filter == 'daily') {
            $detections = $query->where('created_at', '>=', now()->subDays(7))->get();
            $grouped = $detections->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });
        } elseif ($filter == 'monthly') {
            $detections = $query->where('created_at', '>=', now()->subMonths(12))->get();
            $grouped = $detections->groupBy(function($item) {
                return $item->created_at->format('Y-m');
            });
        } elseif ($filter == 'yearly') {
            $detections = $query->get();
            $grouped = $detections->groupBy(function($item) {
                return $item->created_at->format('Y');
            });
        } else { // weekly default
            $detections = $query->where('created_at', '>=', now()->subWeeks(4))->get();
            $grouped = $detections->groupBy(function($item) {
                return $item->created_at->startOfWeek()->format('Y-m-d');
            });
        }

        $chartData = collect();
        foreach ($grouped as $date => $items) {
            $classCounts = $items->countBy('predicted_class');
            foreach ($classCounts as $class => $count) {
                $chartData->push((object)[
                    'raw_date' => $date,
                    'predicted_class' => $class,
                    'count' => $count
                ]);
            }
        }
        $chartData = $chartData->sortBy('raw_date')->values();

        // Organize chart data for ECharts
        $rawDates = $chartData->pluck('raw_date')->unique()->values()->toArray();
        $dateLabels = [];
        foreach ($rawDates as $index => $rd) {
            if ($filter == 'daily') {
                $day = date('D', strtotime($rd));
                $dateLabels[$rd] = $dayMap[$day] ?? $day;
            } elseif ($filter == 'monthly') {
                $m = date('m', strtotime($rd . '-01'));
                $dateLabels[$rd] = $monthMap[$m] ?? $m;
            } elseif ($filter == 'yearly') {
                $dateLabels[$rd] = $rd;
            } else { // weekly
                $dateLabels[$rd] = 'Minggu ' . ($index + 1);
            }
        }

        $dates = [];
        foreach ($rawDates as $rd) {
            $dates[] = $dateLabels[$rd];
        }
        $classColors = [
            'Cercospora' => '#FF9B9B',
            'Common_Rust' => '#FFD6A5',
            'Northern_Leaf_Blight' => '#FFFEC4',
            'Healthy' => '#CBFFA9',
        ];

        $optMap = [
            'Cercospora' => 'Bercak Abu',
            'Common_Rust' => 'Karat Daun',
            'Northern_Leaf_Blight' => 'Hawar Daun',
            'Healthy' => 'Sehat'
        ];

        $series = [];
        foreach ($classColors as $class => $color) {
            $data = [];
            foreach ($rawDates as $rd) {
                $found = $chartData->where('raw_date', $rd)->where('predicted_class', $class)->first();
                $data[] = $found ? $found->count : 0;
            }
            $series[] = [
                'name' => $optMap[$class] ?? str_replace('_', ' ', $class),
                'type' => 'line',
                'smooth' => true,
                'data' => $data,
                'itemStyle' => ['color' => $color],
                'lineStyle' => ['width' => 3],
            ];
        }

        return view('admin.dashboard', compact(
            'totalDetections',
            'cercosporaCount',
            'commonRustCount',
            'northernLeafBlightCount',
            'healthyCount',
            'recentDetections',
            'averageConfidence',
            'totalProducts',
            'recentProducts',
            'dates',
            'series',
            'classColors',
            'filter',
            'monthFilter'
        ));
    }
}
