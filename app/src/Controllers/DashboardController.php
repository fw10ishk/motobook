<?php

namespace App\Controllers;

use App\Models\ExtraCharge;
use App\Models\NewCar;
use App\Models\OldCar;
use App\Models\Sale;
use Carbon\Carbon;
use Slim\Routing\RouteContext;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController
{
    private $twig;
    private $flash;

    public function __construct($twig, $flash)
    {
        $this->twig = $twig;
        $this->flash = $flash;
    }

    public function index(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        // Total Cars
        $totalNewCars = NewCar::where('status', 'sold')->count();
        $totalOldCars = OldCar::where('status', 'sold')->count();
        $totalCars = $totalNewCars + $totalOldCars;

        // Total Cars This Month
        $totalNewCarsThisMonth = NewCar::where('status', 'sold')->whereMonth('created_at', date('m'))->count();
        $totalOldCarsThisMonth = OldCar::where('status', 'sold')->whereMonth('created_at', date('m'))->count();
        $totalCarsThisMonth = $totalNewCarsThisMonth + $totalOldCarsThisMonth;

        // Total Cars Previous Month
        $totalNewCarsPreviousMonth = NewCar::where('status', 'sold')->whereMonth('created_at', date('m', strtotime('-1 month')))->count();
        $totalOldCarsPreviousMonth = OldCar::where('status', 'sold')->whereMonth('created_at', date('m', strtotime('-1 month')))->count();
        $totalCarsPreviousMonth = $totalNewCarsPreviousMonth + $totalOldCarsPreviousMonth;

        $percentage = 0;
        if ($totalCarsPreviousMonth > 0) {
            $percentage = round((($totalCarsThisMonth - $totalCarsPreviousMonth) / $totalCarsPreviousMonth) * 100, 2);
        } elseif ($totalCarsThisMonth > 0) {
            $percentage = 100; // If previous month was 0 and this month has cars
        }

        // Total Cars this month string
        $prefix = $percentage > 0 ? '+' : ($percentage == 0 ? '' : '-');
        $totalCarsThisMonthPercentageString = "{$prefix}{$percentage}% from last month";

        // Percentage Status Class
        $percentageStatusClass = match (true) {
            $percentage > 0 => 'success',
            $percentage < 0 => 'danger',
            default => 'text-light'
        };

        // Total Revenue
        $totalRevenue = Sale::sum('total_price');

        // Total Revenue This Month
        $totalRevenueThisMonth = Sale::whereMonth('created_at', date('m'))->sum('total_price');

        // Total Revenue Previous Month
        $totalRevenuePreviousMonth = Sale::whereMonth('created_at', date('m', strtotime('-1 month')))->sum('total_price');

        $revenuePercentage = 0;
        if ($totalRevenuePreviousMonth > 0) {
            $revenuePercentage = round((($totalRevenueThisMonth - $totalRevenuePreviousMonth) / $totalRevenuePreviousMonth) * 100, 2);
        } elseif ($totalRevenueThisMonth > 0) {
            $revenuePercentage = 100; // If previous month was 0 and this month has revenue
        }

        // Total Revenue this month string
        $prefix = $revenuePercentage > 0 ? '+' : ($revenuePercentage == 0 ? '' : '-');
        $totalRevenueThisMonthPercentageString = "{$prefix}{$revenuePercentage}% from last month";

        // Percentage Status Class
        $revenuePercentageStatusClass = match (true) {
            $revenuePercentage > 0 => 'success',
            $revenuePercentage < 0 => 'danger',
            default => 'text-light'
        };

        // Total Loaned Sales
        $totalLoanedSales = Sale::where('sale_type', 'loan')->count();

        // Total Loaned Sales This Month
        $totalLoanedSalesThisMonth = Sale::where('sale_type', 'loan')->whereMonth('sale_date', date('m'))->count();

        // Total Loaned Sales Previous Month
        $totalLoanedSalesPreviousMonth = Sale::where('sale_type', 'loan')->whereMonth('sale_date', date('m', strtotime('-1 month')))->count();

        $loanedSalesPercentage = 0;

        if ($totalLoanedSalesPreviousMonth > 0) {
            $loanedSalesPercentage = round((($totalLoanedSalesThisMonth - $totalLoanedSalesPreviousMonth) / $totalLoanedSalesPreviousMonth) * 100, 2);
        } elseif ($totalLoanedSalesThisMonth > 0) {
            $loanedSalesPercentage = 100; // If previous month was 0 and this month has loaned sales
        }

        // Total Loaned Sales this month string
        $prefix = $loanedSalesPercentage > 0 ? '+' : ($loanedSalesPercentage == 0 ? '' : '-');
        $totalLoanedSalesThisMonthPercentageString = "{$prefix}{$loanedSalesPercentage}% from last month";

        // Percentage Status Class
        $loanedSalesPercentageStatusClass = match (true) {
            $loanedSalesPercentage > 0 => 'success',
            $loanedSalesPercentage < 0 => 'danger',
            default => 'text-light'
        };

        $recentSales = Sale::orderBy('created_at', 'asc')
            ->get()
            ->toArray();

        // Fetch all sales
        $sales = Sale::orderBy('created_at', 'asc')->get();

        // Prepare old car sales data
        $oldCarSales = [];
        $newCarSales = [];

        foreach ($sales as $sale) {
            if ($sale->car_type === 'old') {
                $oldCar = OldCar::find($sale->car_id);
                if ($oldCar) {
                    $extraCharges = ExtraCharge::where('old_car_id', $sale->car_id)
                        ->sum('amount');

                    $baseAmount = $oldCar->current_price + $extraCharges;
                    $profit = $sale->total_price - $baseAmount;
                    $prefix = $profit >= 0 ? '+' : '';

                    $oldCarSales[] = [
                        'id' => '#SAL-' . $sale->id,
                        'car_name' => $oldCar->manufacturer_name . ' ' . $oldCar->model_name,
                        'base_amount' => $baseAmount,
                        'sold_amount' => $sale->total_price,
                        'profit_type' => [
                            'type' => $profit >= 0 ? 'success' : 'danger',
                            'value' => $prefix . ' ₹' . $profit
                        ]
                    ];
                }
            } else {
                $newCar = NewCar::find($sale->car_id);
                if ($newCar) {
                    $baseAmount = $newCar->dealer_net_on_road;
                    $profit = $newCar->net_on_road - $baseAmount;
                    $prefix = $profit >= 0 ? '+' : '';

                    $newCarSales[] = [
                        'id' => '#SAL-' . $sale->id,
                        'car_name' => $newCar->manufacturer_name . ' ' . $newCar->model_name,
                        'base_amount' => $baseAmount,
                        'sold_amount' => $newCar->net_on_road,
                        'profit_type' => [
                            'type' => $profit >= 0 ? 'success' : 'danger',
                            'value' => $prefix . ' ₹' . $profit
                        ]
                    ];
                }
            }
        }

        $upcomingInsuranceExpiry = OldCar::where('insurance_expiry_date', '<=', Carbon::now()->addDays(15))
            ->where('insurance_expiry_date', '>=', Carbon::now())
            ->get()
            ->map(function ($car) {
                // Only display int value not float value like: 14, 15 etc.
                $daysUntilExpiry = (int)Carbon::now()->diffInDays($car->insurance_expiry_date, false);

                return [
                    'id' => '#CAR-' . $car->id,
                    'car_name' => $car->manufacturer_name . ' ' . $car->model_name,
                    'insurance_expiry' => $car->insurance_expiry_date,
                    'days_remaining' => $daysUntilExpiry,
                    'status_type' => [
                        'type' => $daysUntilExpiry <= 7 ? 'danger' : 'warning',
                        'value' => $daysUntilExpiry . ' days remaining'
                    ]
                ];
            })
            ->toArray();

        return $this->twig->render($response, 'dashboard.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'recent_sales' => $recentSales,
            'total_cars' => $totalCars,
            'total_sold_cars_this_month' => $totalCarsThisMonth,
            'total_cars_this_month' => $totalCarsThisMonthPercentageString,
            'total_cars_this_month_class' => $percentageStatusClass,
            'total_revenue' => $totalRevenue,
            'total_revenue_this_month' => $totalRevenueThisMonth,
            'total_revenue_this_month_string' => $totalRevenueThisMonthPercentageString,
            'total_revenue_this_month_class' => $revenuePercentageStatusClass,
            'total_loaned_sales' => $totalLoanedSales,
            'total_loaned_sales_this_month' => $totalLoanedSalesThisMonth,
            'total_loaned_sales_this_month_string' => $totalLoanedSalesThisMonthPercentageString,
            'total_loaned_sales_this_month_class' => $loanedSalesPercentageStatusClass,
            'old_car_sales' => $oldCarSales,
            'new_car_sales' => $newCarSales,
            'upcoming_insurance_expiry' => $upcomingInsuranceExpiry
        ]);
    }
}
