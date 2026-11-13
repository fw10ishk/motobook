<?php

namespace App\Controllers;

use Slim\Routing\RouteContext;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Inquiry;

class InquiryController
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
        $query = Inquiry::query();

        // Apply Status Filter
        if ($status !== 'All') {
            $query->where('status', strtolower($status));
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%")
                    ->orWhere('vehicle_of_interest', 'LIKE', "%{$search}%")
                    ->orWhere('inquiry_source', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        // Update the query building section to include date filtering
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Get total count for pagination
        $totalItems = $query->count();
        $totalPages = ceil($totalItems / $perPage);

        // Apply pagination
        $inquiries = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->toArray();

        // Total Inquiries This Month
        $totalInquiriesThisMonth = Inquiry::whereMonth('created_at', date('m'))->count();

        // Total Inquiries Previous Month
        $totalInquiriesPreviousMonth = Inquiry::whereMonth('created_at', date('m', strtotime('-1 month')))->count();

        $percentage = 0;

        if ($totalInquiriesPreviousMonth > 0) {
            $percentage = round((($totalInquiriesThisMonth - $totalInquiriesPreviousMonth) / $totalInquiriesPreviousMonth) * 100, 2);
        } elseif ($totalInquiriesThisMonth > 0) {
            $percentage = 100; // If previous month was 0 and this month has inquiries
        }

        // Total Inquiries this month string
        $prefix = $percentage > 0 ? '+' : ($percentage === 0 ? '' : '-');
        $totalInquiriesThisMonthPercentageString = "{$prefix}{$percentage}% from last month";

        // Percentage Status Class
        $percentageStatusClass = match (true) {
            $percentage > 0 => 'success',
            $percentage < 0 => 'danger',
            default => 'text-light'
        };

        // Total Completed Inquiries This Month
        $totalCompletedInquiriesThisMonth = Inquiry::where('status', 'completed')->whereMonth('created_at', date('m'))->count();

        // Total Pending Inquiries This Month
        $totalPendingInquiriesThisMonth = Inquiry::where('status', 'pending')->whereMonth('created_at', date('m'))->count();

        // Total Completed Inquiries Previous Month
        $totalCompletedInquiriesPreviousMonth = Inquiry::where('status', 'completed')->whereMonth('created_at', date('m', strtotime('-1 month')))->count();

        // Total Pending Inquiries Previous Month
        $totalPendingInquiriesPreviousMonth = Inquiry::where('status', 'pending')->whereMonth('created_at', date('m', strtotime('-1 month')))->count();

        $completedPercentage = 0;

        if ($totalCompletedInquiriesPreviousMonth > 0) {
            $completedPercentage = round((($totalCompletedInquiriesThisMonth - $totalCompletedInquiriesPreviousMonth) / $totalCompletedInquiriesPreviousMonth) * 100, 2);
        } elseif ($totalCompletedInquiriesThisMonth > 0) {
            $completedPercentage = 100; // If previous month was 0 and this month has completed inquiries
        }

        // Total Completed Inquiries this month string
        $completedPrefix = $completedPercentage > 0 ? '+' : ($completedPercentage === 0 ? '' : '-');
        $totalCompletedInquiriesThisMonthPercentageString = "{$completedPrefix}{$completedPercentage}% from last month";

        // Completed Percentage Status Class
        $completedPercentageStatusClass = match (true) {
            $completedPercentage > 0 => 'success',
            $completedPercentage < 0 => 'danger',
            default => 'text-light'
        };

        $pendingPercentage = 0;

        if ($totalPendingInquiriesPreviousMonth > 0) {
            $pendingPercentage = round((($totalPendingInquiriesThisMonth - $totalPendingInquiriesPreviousMonth) / $totalPendingInquiriesPreviousMonth) * 100, 2);
        } elseif ($totalPendingInquiriesThisMonth > 0) {
            $pendingPercentage = 100; // If previous month was 0 and this month has pending inquiries
        }

        // Total Pending Inquiries this month string
        $pendingPrefix = $pendingPercentage > 0 ? '+' : ($pendingPercentage === 0 ? '' : '-');
        $totalPendingInquiriesThisMonthPercentageString = "{$pendingPrefix}{$pendingPercentage}% from last month";

        // Pending Percentage Status Class
        $pendingPercentageStatusClass = match (true) {
            $pendingPercentage > 0 => 'success',
            $pendingPercentage < 0 => 'danger',
            default => 'text-light'
        };

        return $this->twig->render($response, 'inquiries/index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'inquiries' => $inquiries,
            'total_inquiries_this_month' => $totalInquiriesThisMonth,
            'total_inquiries_this_month_percentage_string' => $totalInquiriesThisMonthPercentageString,
            'percentage_status_class' => $percentageStatusClass,
            'total_completed_inquiries_this_month' => $totalCompletedInquiriesThisMonth,
            'total_completed_inquiries_this_month_percentage_string' => $totalCompletedInquiriesThisMonthPercentageString,
            'completed_percentage_status_class' => $completedPercentageStatusClass,
            'total_pending_inquiries_this_month' => $totalPendingInquiriesThisMonth,
            'total_pending_inquiries_this_month_percentage_string' => $totalPendingInquiriesThisMonthPercentageString,
            'pending_percentage_status_class' => $pendingPercentageStatusClass,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'current_status' => $status,
            'current_search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        return $this->twig->render($response, 'inquiries/create.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages()
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        Inquiry::createInquiry($data);

        $this->flash->addMessage('success', 'Inquiry created successfully');

        return $response->withHeader('Location', '/inquiries')->withStatus(302);
    }

    public function edit(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $inquiry = Inquiry::find($args['id']);

        $sources = [
            'website',
            'walk-in',
            'referral',
            'phone'
        ];

        return $this->twig->render($response, 'inquiries/edit.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'inquiry' => $inquiry,
            'sources' => $sources
        ]);
    }

    public function update(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        Inquiry::updateInquiry($args['id'], $data);

        $this->flash->addMessage('success', 'Inquiry updated successfully');

        return $response->withHeader('Location', '/inquiries')->withStatus(302);
    }

    public function view(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $inquiry = Inquiry::find($args['id']);

        return $this->twig->render($response, 'inquiries/view.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'inquiry' => $inquiry
        ]);
    }

    public function delete(Request $request, Response $response, $args)
    {
        Inquiry::deleteInquiry($args['id']);

        $this->flash->addMessage('success', 'Inquiry deleted successfully');

        return $response->withHeader('Location', '/inquiries')->withStatus(302);
    }

    public function changeStatus(Request $request, Response $response, $args)
    {
        Inquiry::changeStatus($args['id']);

        $this->flash->addMessage('success', 'Inquiry status changed successfully');

        return $response->withHeader('Location', '/inquiries')->withStatus(302);
    }
}
