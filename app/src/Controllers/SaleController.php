<?php

namespace App\Controllers;

use App\Helpers\FileUpload;
use App\Models\DealerFullPayment;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Illuminate\Database\Capsule\Manager as DB;

use App\Models\Sale;
use App\Models\LoanDetail;
use App\Models\Installment;
use App\Models\NewCar;
use App\Models\OldCar;
use App\Models\FullPayment;
use App\Models\OldCarBuyer;

class SaleController
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

        // Get query parameters
        $queryParams = $request->getQueryParams();
        $status = $queryParams['status'] ?? 'All';
        $search = $queryParams['search'] ?? '';
        $page = (int)($queryParams['page'] ?? 1);
        $dateFrom = $queryParams['date_from'] ?? '';
        $dateTo = $queryParams['date_to'] ?? '';
        $perPage = 10; // Items per page

        // Base Query
        $query = Sale::query()->with(['fullPayment.payments', 'dealerFullPayment.payments', 'oldCarBuyer']);

        // Apply Status Filter
        if ($status !== 'All') {
            $query->where('car_type', strtolower($status));
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('buyer_name', 'LIKE', "%{$search}%")
                    ->orWhere('sale_date', 'LIKE', "%{$search}%")
                    ->orWhere('sale_type', 'LIKE', "%{$search}%")
                    ->orWhere('car_type', 'LIKE', "%{$search}%")
                    ->orWhere('total_price', 'LIKE', "%{$search}%");
            });
        }

        // Update the query building section to include date filtering
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('sale_date', [$dateFrom, $dateTo]);
        }

        // Get total count for pagination
        $totalItems = $query->count();
        $totalPages = ceil($totalItems / $perPage);

        // Apply pagination
        $sales = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->toArray();

        foreach ($sales as $key => $sale) {
            $selected_car = $sale['car_type'] === 'new'
                ? NewCar::find($sale['car_id'])
                : OldCar::with('extraCharges')->find($sale['car_id']);

            $sales[$key]['selected_car'] = $selected_car ?? null;
        }

        $sales;

        // var_dump($sales[0]);
        // die();

        // Calculate monthly statistics
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalSales = Sale::count();
        $salesThisMonth = Sale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->count();

        $previousMonth = date('m', strtotime('-1 month'));
        $salesLastMonth = Sale::whereMonth('sale_date', $previousMonth)
            ->whereYear('sale_date', $currentYear)
            ->count();

        $difference = $salesThisMonth - $salesLastMonth;
        $salesDifference = $difference > 0 ? "+$difference sales this month" : ($difference == 0 ? 'No sales this month' : "$difference sales this month");

        $differenceStatus = $difference > 0 ? 'success' : ($difference == 0 ? 'text' : 'danger');

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

        // Total Full Payment Sales This Month
        $totalFullSalesThisMonth = Sale::where('sale_type', 'full')->whereMonth('sale_date', date('m'))->count();

        // Total Full Payment Sales Previous Month
        $totalFullSalesPreviousMonth = Sale::where('sale_type', 'full')->whereMonth('sale_date', date('m', strtotime('-1 month')))->count();

        $totalSalesPercentage = 0;

        if ($totalFullSalesPreviousMonth > 0) {
            $totalSalesPercentage = round((($totalFullSalesThisMonth - $totalFullSalesPreviousMonth) / $totalFullSalesPreviousMonth) * 100, 2);
        } elseif ($totalFullSalesThisMonth > 0) {
            $totalSalesPercentage = 100; // If previous month was 0 and this month has loaned sales
        }

        // Total Full Payment Sales this month string
        $prefix = $totalSalesPercentage > 0 ? '+' : ($totalSalesPercentage == 0 ? '' : '-');
        $totalFullSalesThisMonthPercentageString = "{$prefix}{$totalSalesPercentage}% from last month";

        // Percentage Status Class
        $totalSalesPercentageStatusClass = match (true) {
            $totalSalesPercentage > 0 => 'success',
            $totalSalesPercentage < 0 => 'danger',
            default => 'text-light'
        };

        return $this->twig->render($response, 'sales/index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'total_sales' => $totalSales,
            'sales_this_month' => $salesThisMonth,
            'sales_difference' => $salesDifference,
            'difference_status' => $differenceStatus,
            'sales' => $sales,
            'total_loaned_sales' => $totalLoanedSalesThisMonth,
            'total_loaned_sales_this_month' => $totalLoanedSalesThisMonthPercentageString,
            'total_loaned_sales_this_month_class' => $loanedSalesPercentageStatusClass,
            'total_full_sales' => $totalFullSalesThisMonth,
            'total_full_sales_this_month' => $totalFullSalesThisMonthPercentageString,
            'total_full_sales_this_month_class' => $totalSalesPercentageStatusClass,
            'total_sales_this_month' => $totalSales,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'current_status' => $status,
            'current_search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    public function getCars(Request $request, Response $response, array $args)
    {
        $type = $args['type'];

        $cars = $type === 'new'
            ? NewCar::where('status', '!=', 'sold')->get()
            : OldCar::where('status', '!=', 'sold')->get();

        $response->getBody()->write(json_encode($cars));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getCarDetails(Request $request, Response $response, array $args)
    {
        $type = $args['type'];
        $id = $args['id'];

        $car = $type === 'new'
            ? NewCar::find($id)
            : OldCar::find($id)->with(['extraCharges']);

        $car['car_type'] = $type === 'old' ? 'old' : 'new';

        if (!$car) {
            $response->getBody()->write(json_encode(['error' => 'Car not found']));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $response->getBody()->write(json_encode($car));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $queryParams = $request->getQueryParams();

        // Get all unsold cars
        $newCars = NewCar::where('status', '!=', 'sold')->get();
        $oldCars = OldCar::where('status', '!=', 'sold')->get();

        // Initialize selected car
        $selectedCar = null;

        // Handle URL with query parameters
        if (!empty($queryParams['car_type']) && !empty($queryParams['car_id'])) {
            $carType = $queryParams['car_type'];
            $carId = $queryParams['car_id'];

            // Get car with dealer details based on type
            if ($carType === 'new') {
                $selectedCar = NewCar::find($carId);
            } else if ($carType === 'old') {
                $selectedCar = OldCar::with(['extraCharges'])->find($carId);
            }

            // If car not found or already sold, redirect with error
            if (!$selectedCar || $selectedCar->status === 'sold') {
                $this->flash->addMessage('error', 'Car not found or already sold');
                return $response->withHeader('Location', '/sales')->withStatus(302);
            }

            $selectedCar = $selectedCar->toArray();
        }

        return $this->twig->render($response, 'sales/create.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'new_cars' => $newCars,
            'old_cars' => $oldCars,
            'selected_car' => $selectedCar,
            'selected_car_type' => $selectedCar ? $queryParams['car_type'] : null
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        DB::beginTransaction();

        try {
            // Validate car existence and status
            if ($data['car_type'] === 'new') {
                $car = NewCar::where('id', $data['car_id'])
                    ->where('status', '!=', 'sold')
                    ->first();
                if (!$car) {
                    throw new \Exception('The specified new car is not available for sale.');
                }
            } else {
                $car = OldCar::where('id', $data['car_id'])
                    ->where('status', '!=', 'sold')
                    ->first();

                if (!$car) {
                    throw new \Exception('The specified old car is not available for sale.');
                }
            }

            // Create the sale record
            $sale = Sale::create([
                'car_type' => $data['car_type'],
                'car_id' => $data['car_id'],
                'sale_type' => $data['sale_type'],
                'total_price' => $data['total_price'],
                'sale_date' => $data['sale_date'],
                'buyer_name' => $data['buyer_name']
            ]);

            if ($data['car_type'] === 'old') {
                $requiredDocuments = [
                    'pan_card',
                    'aadhar_card',
                    'driving_license'
                ];

                // Validate required files
                foreach ($requiredDocuments as $document) {
                    if (!isset($files[$document]) || !FileUpload::isValidUpload($files[$document])) {
                        $this->flash->addMessage('error', ucfirst(str_replace('_', ' ', $document)) . ' is required');
                        return $response->withHeader('Location', '/sales/create?car_type=old&car_id=' . $car->id)->withStatus(302);
                    }
                }

                // Process document uploads
                foreach ($requiredDocuments as $document) {
                    if (isset($files[$document])) {
                        $uploadPath = 'uploads/documents';
                        $data[$document] = FileUpload::upload($files[$document], $uploadPath);
                    }
                }

                $newBuyer = [
                    'sale_id' => $sale->id,
                    'name' => $data['buyer_name'],
                    'contact' => $data['contact'],
                    'address_line_1' => $data['address_line_1'],
                    'address_line_2' => $data['address_line_2'],
                    'city_name' => $data['city_name'],
                    'pincode' => $data['pincode'],
                    'email' => $data['email'],
                    'dob' => $data['dob'],
                    'pan_card' => $data['pan_card'],
                    'aadhar_card' => $data['aadhar_card'],
                    'driving_license' => $data['driving_license']
                ];

                $buyer = OldCarBuyer::createBuyer($newBuyer);

                if (!$buyer) {
                    throw new \Exception('Failed to create buyer record.');
                }
            }

            // Update car status
            $car->updateStatus($car->id, 'sold');

            // Handle payment details based on sale type
            if ($data['sale_type'] === 'loan') {
                LoanDetail::createLoanWithInstallments([
                    'sale_id' => $sale->id,
                    'loan_amount' => $data['loan_amount'],
                    'number_of_parts' => $data['number_of_parts'],
                    'installment_amount' => $data['installment_amount'],
                    'interest_rate' => $data['interest_rate']
                ]);
            } else {
                // Create full payment detail record
                FullPayment::createFullPayment([
                    'sale_id' => $sale->id,
                    'total_price' => $data['total_price']
                ]);

                // If initial payment is provided, record it
                if (!empty($data['initial_payment_amount'])) {
                    FullPayment::recordPayment([
                        'full_payment_id' => $sale->fullPaymentDetail->id,
                        'amount' => $data['initial_payment_amount'],
                        'payment_date' => $data['sale_date'],
                        'payment_method' => $data['payment_method'] ?? 'cash',
                        'note' => $data['note'] ?? 'Initial payment at time of sale'
                    ]);
                }
            }

            // Handle Dealer Payment
            $dealerFullPayment = $car->dealer_net_short_payment;

            if ($dealerFullPayment > 0) {
                $dealerPayment = DealerFullPayment::createDealerFullPayment([
                    'sale_id' => $sale->id,
                    'total_price' => $dealerFullPayment
                ]);

                // If initial payment is provided, record it
                if (!empty($data['dealer_down_payment'])) {
                    DealerFullPayment::recordPayment([
                        'full_payment_id' => $dealerPayment->id,
                        'amount' => $data['dealer_down_payment'],
                        'payment_date' => $data['sale_date'],
                        'payment_method' => $data['dealer_payment_method'] ?? 'cash',
                        'note' => $data['note'] ?? 'Initial payment at time of sale'
                    ]);
                }
            }

            DB::commit();

            $this->flash->addMessage('success', 'Sale recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->flash->addMessage('error', 'Failed to record sale: ' . $e->getMessage());
            error_log($e->getMessage());
        }

        return $response->withHeader('Location', '/sales')->withStatus(302);
    }

    public function view(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $sale = Sale::with(['fullPayment.payments', 'dealerFullPayment.payments', 'oldCarBuyer'])->find($args['id'])->toArray();

        if ($sale['car_type'] == "new") {
            $selectedCar = NewCar::find($sale['car_id'])->toArray();
        } else {
            $selectedCar = OldCar::find($sale['car_id'])->toArray();
        }

        if ($sale['sale_type'] == 'loan') {
            $loan = LoanDetail::where('sale_id', $sale['id'])->first();
            $installments = Installment::where('loan_id', $loan['id'])->get();
        } else {
            $loan = null;
            $installments = [];
        }

        if (!$sale) {
            $this->flash->addMessage('error', 'Sale record not found');
            return $response->withHeader('Location', '/sales')->withStatus(302);
        }

        return $this->twig->render($response, 'sales/view.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'sale' => $sale,
            'selected_car' => $selectedCar,
            'loan' => $loan,
            'installments' => $installments
        ]);
    }

    public function recordPayment(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $installment = Installment::find($args['id']);

        $amount_paid = $data['amount_paid'];
        $payment_date = $data['payment_date'];
        $status = 'paid';

        if (!$installment) {
            $this->flash->addMessage('error', 'Installment not found');
            return $response->withHeader('Location', '/sales/view/' . $args['sale_id'])->withStatus(302);
        }

        try {
            $installment->markAsPaid($amount_paid, $payment_date, $status);
            $this->flash->addMessage('success', 'Payment recorded successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to record payment');
            error_log($e->getMessage());
        }

        return $response->withHeader('Location', '/sales')
            ->withStatus(302);
    }

    public function recordFullPayment(Request $request, Response $response, array $args)
    {
        try {
            $data = $request->getParsedBody();

            FullPayment::recordPayment([
                'full_payment_id' => $args['id'],
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'note' => $data['note']
            ]);

            $this->flash->addMessage('success', 'Payment recorded.');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to record payment');
            error_log($e->getMessage());
        }

        return $response
            ->withHeader('Location', '/sales')
            ->withStatus(302);
    }

    public function updateFullPayment(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();

        $fullPayment = FullPayment::find($args['payment_id']);

        if (!$fullPayment) {
            $this->flash->addMessage('error', 'Full Payment record not found');
            return $response->withHeader('Location', '/sales')->withStatus(302);
        }

        try {
            $fullPayment->updatePayment(
                $args['id'],
                [
                    'amount' => $data['amount'],
                    'payment_date' => $data['payment_date'],
                    'payment_method' => $data['payment_method'],
                    'note' => $data['note']
                ]
            );

            $this->flash->addMessage('success', 'Full Payment updated successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update full payment');
            error_log($e->getMessage());
        }

        return $response->withHeader('Location', '/sales')->withStatus(302);
    }

    public function dealerRecordFullPayment(Request $request, Response $response, array $args)
    {
        try {
            $data = $request->getParsedBody();

            DealerFullPayment::recordPayment([
                'dealer_full_payment_id' => $args['id'],
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'note' => $data['note']
            ]);

            $this->flash->addMessage('success', 'Dealer Payment recorded.');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to record dealer payment');
            error_log($e->getMessage());
        }

        return $response
            ->withHeader('Location', '/sales')
            ->withStatus(302);
    }

    public function updateDealerFullPayment(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();

        $fullPayment = DealerFullPayment::find($args['payment_id']);

        if (!$fullPayment) {
            $this->flash->addMessage('error', 'Full Payment record not found');
            return $response->withHeader('Location', '/sales')->withStatus(302);
        }

        try {
            $fullPayment->updatePayment(
                $args['id'],
                [
                    'amount' => $data['amount'],
                    'payment_date' => $data['payment_date'],
                    'payment_method' => $data['payment_method'],
                    'note' => $data['note']
                ]
            );

            $this->flash->addMessage('success', 'Dealer Full Payment updated successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update full payment');
            error_log($e->getMessage());
        }

        return $response->withHeader('Location', '/sales')->withStatus(302);
    }

    public function deleteSale(Request $request, Response $response, array $args)
    {
        $sale = Sale::find($args['id']);

        if (!$sale) {
            $this->flash->addMessage('error', 'Sale record not found');
            return $response->withHeader('Location', '/sales')->withStatus(302);
        }

        DB::beginTransaction();

        try {
            // Determine Car Type
            $carType = $sale->car_type === 'new' ? NewCar::class : OldCar::class;

            // Update car status
            $car = $carType::find($sale->car_id);
            $car->updateStatus($car->id, 'unsold');

            // Delete sale record
            $sale->delete();

            DB::commit();

            $this->flash->addMessage('success', 'Sale record deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->flash->addMessage('error', 'Failed to delete sale record');
            error_log($e->getMessage());
        }

        return $response->withHeader('Location', '/sales')->withStatus(302);
    }
}
