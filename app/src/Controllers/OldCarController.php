<?php

namespace App\Controllers;

use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\OldCar;
use App\Models\ExtraCharge;
use App\Models\OldCarImages;

use App\Helpers\FileUpload;

class OldCarController
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
        $perPage = 10; // Items per page

        // Base query
        $query = OldCar::with('extraCharges');

        // Apply status filter
        if ($status !== 'All') {
            $query->where('status', strtolower($status));
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('registration_no', 'LIKE', "%{$search}%")
                    ->orWhere('chassis_no', 'LIKE', "%{$search}%")
                    ->orWhere('manufacturer_name', 'LIKE', "%{$search}%")
                    ->orWhere('model_name', 'LIKE', "%{$search}%");
            });
        }

        // Get total count for pagination
        $totalItems = $query->count();
        $totalPages = ceil($totalItems / $perPage);

        // Apply pagination
        $cars = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->toArray();

        // Total Cars
        $totalCars = OldCar::count();

        // Calculate the per month data for total cars
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalCarsThisMonth = OldCar::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousMonth = date('m', strtotime('-1 month'));
        $totalCarsPreviousMonth = OldCar::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $difference = $totalCarsThisMonth - $totalCarsPreviousMonth;
        $oldCarsThisMonth = $difference > 0 ? "+$difference Old Cars this month" : ($difference == 0 ? 'No Old Cars This Month' : "$difference Old Cars this month");

        // Sold Cars this month based on delivery date & status
        $soldCarsThisMonth = OldCar::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('status', 'sold')->count();

        // Calculate the percentage of sold cars from total cars
        $soldCarsPercentage = $totalCars > 0 ? round(($soldCarsThisMonth / $totalCars) * 100, 2) : 0;

        // Unsold Cars this month based on delivery date & status
        $unsoldCarsThisMonth = OldCar::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('status', 'unsold')->count();

        // Calculate the percentage of unsold cars from total cars
        $unsoldCarsPercentage = $totalCars > 0 ? round(($unsoldCarsThisMonth / $totalCars) * 100, 2) : 0;

        if ($difference > 0) {
            $differenceStatus = 'success';
        } elseif ($difference == 0) {
            $differenceStatus = 'text';
        } else {
            $differenceStatus = 'danger';
        }

        return $this->twig->render($response, 'old-cars/index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'total_cars' => $totalCars,
            'old_cars_this_month' => $oldCarsThisMonth,
            'difference_status' => $differenceStatus,
            'sold_cars_this_month' => $soldCarsThisMonth,
            'sold_cars_percentage' => $soldCarsPercentage,
            'unsold_cars_this_month' => $unsoldCarsThisMonth,
            'unsold_cars_percentage' => $unsoldCarsPercentage,
            'total_cars_this_month' => $totalCarsThisMonth,
            'cars' => $cars,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'current_status' => $status,
            'current_search' => $search
        ]);
    }

    public function create(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $data = [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
        ];

        return $this->twig->render($response, 'old-cars/create.twig', $data);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        try {
            // Required document fields
            $requiredDocuments = [
                'rc_book',
                'owner_id_proof',
            ];

            $optionalDocuments = [
                'insurance_doc',
                'pan_card',
                'aadhar_card',
                'noc',
                'form_35',
                'purchase_agreement',
            ];

            $data['main_image'] = FileUpload::upload($files['main_image'], "uploads/old-cars");

            // Validate required files
            foreach ($requiredDocuments as $document) {
                if (!isset($files[$document]) || !FileUpload::isValidUpload($files[$document])) {
                    $this->flash->addMessage('error', ucfirst(str_replace('_', ' ', $document)) . ' is required');
                    return $response->withHeader('Location', '/old-cars/create')->withStatus(302);
                }
            }

            // Process document uploads
            foreach ($requiredDocuments as $document) {
                if (isset($files[$document])) {
                    $uploadPath = 'uploads/documents';
                    $data[$document] = FileUpload::upload($files[$document], $uploadPath);
                }
            }

            // Process optional document uploads
            foreach ($optionalDocuments as $document) {
                if (isset($files[$document])) {
                    $uploadPath = 'uploads/documents';
                    $data[$document] = FileUpload::upload($files[$document], $uploadPath);
                }
            }

            // Create the car record
            $car = OldCar::createOldCar($data);

            // Process extra charges
            $extraChargeNames = $data['extra_charge_name'] ?? [];
            $extraChargeAmounts = $data['extra_charge_amount'] ?? [];

            foreach ($extraChargeNames as $index => $name) {
                if (!empty($name) && isset($extraChargeAmounts[$index])) {
                    ExtraCharge::addExtraCharge($car->id, $name, $extraChargeAmounts[$index]);
                }
            }

            // Handle additional images
            if (isset($files['additional_images'])) {
                $imageCount = 0;
                foreach ($files['additional_images'] as $image) {
                    if ($imageCount >= 4) break;

                    if (FileUpload::isValidUpload($image)) {
                        $imagePath = FileUpload::upload($image, 'uploads/old-cars');
                        OldCarImages::create([
                            'old_car_id' => $car->id,
                            'image_url' => $imagePath
                        ]);
                        $imageCount++;
                    }
                }
            }

            $this->flash->addMessage('success', 'Car added successfully');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        } catch (\Exception $e) {
            // Log the error
            var_dump($e->getMessage());
            die();
            $this->flash->addMessage('error', 'Failed to add car: ' . $e->getMessage());
            return $response->withHeader('Location', '/old-cars/create')->withStatus(302);
        }
    }

    public function edit(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $car = OldCar::with([
            'additionalImages',
            'extraCharges',
        ])->find($args['id'])->toArray();

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        $data = [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'car' => $car,
        ];

        return $this->twig->render($response, 'old-cars/edit.twig', $data);
    }

    public function update(Request $request, Response $response, $args)
    {
        $car = OldCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        // Handle file uploads if new files are provided
        if ($files['rc_book']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->rc_book);
            $data['rc_book'] = FileUpload::upload($files['rc_book'], 'uploads/documents');
        } else if (empty($car->rc_book)) {
            $this->flash->addMessage('error', 'RC Book is Required');
            return $response->withHeader('Location', '/old-cars/edit/' . $car->id)->withStatus(302);
        }

        if ($files['insurance_doc']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->insurance_doc);
            $data['insurance_doc'] = FileUpload::upload($files['insurance_doc'], 'uploads/documents');
        }

        if ($files['owner_id_proof']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->owner_id_proof);
            $data['owner_id_proof'] = FileUpload::upload($files['owner_id_proof'], 'uploads/documents');
        } else if (empty($car->owner_id_proof)) {
            $this->flash->addMessage('error', 'ID Proof is Required');
            return $response->withHeader('Location', '/old-cars/edit/' . $car->id)->withStatus(302);
        }

        if ($files['pan_card']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->pan_card);
            $data['pan_card'] = FileUpload::upload($files['pan_card'], 'uploads/documents');
        }

        if ($files['aadhar_card']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->aadhar_card);
            $data['aadhar_card'] = FileUpload::upload($files['aadhar_card'], 'uploads/documents');
        }

        if ($files['noc']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->noc);
            $data['noc'] = FileUpload::upload($files['noc'], 'uploads/documents');
        }

        if ($files['form_35']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->form_35);
            $data['form_35'] = FileUpload::upload($files['form_35'], 'uploads/documents');
        }

        if ($files['purchase_agreement']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->purchase_agreement);
            $data['purchase_agreement'] = FileUpload::upload($files['purchase_agreement'], 'uploads/documents');
        }

        if ($files['main_image']->getSize() > 0) {
            FileUpload::delete('uploads/old-cars', $car->main_image);
            $data['main_image'] = FileUpload::upload($files['main_image'], 'uploads/old-cars');
        }

        // Extract extra charges
        $extraChargeNames = $data['extra_charge_name'] ?? [];
        $extraChargeAmounts = $data['extra_charge_amount'] ?? [];

        // Remove extra charges from main data array
        unset($data['extra_charge_name']);
        unset($data['extra_charge_amount']);

        try {
            // Update the car record
            $car->updateOldCar($car->id, $data);

            // Delete existing extra charges
            ExtraCharge::where('old_car_id', $car->id)->delete();

            // Add new extra charges
            foreach ($extraChargeNames as $index => $name) {
                if (!empty($name) && isset($extraChargeAmounts[$index])) {
                    ExtraCharge::addExtraCharge(
                        $car->id,
                        $name,
                        $extraChargeAmounts[$index]
                    );
                }
            }

            $existingImagesCount = count($car->additionalImages);
            $validNewImagesCount = 0;

            // Count only valid images with actual content
            foreach ($files['additional_images'] as $image) {
                if ($image->getSize() > 0) {
                    $validNewImagesCount++;
                }
            }

            if ($existingImagesCount + $validNewImagesCount > 4) {
                $this->flash->addMessage('error', 'You can upload a maximum of 4 additional images');
                return $response->withHeader('Location', '/old-cars/edit/' . $car->id)->withStatus(302);
            }

            // Process valid images
            foreach ($files['additional_images'] as $image) {
                if ($image->getSize() > 0) {
                    $imagePath = FileUpload::upload($image, 'uploads/old-cars');
                    OldCarImages::create([
                        'old_car_id' => $car->id,
                        'image_url' => $imagePath
                    ]);
                }
            }

            $this->flash->addMessage('success', 'Car updated successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update car. Please try again.');
            // You might want to log the error here
        }

        return $response->withHeader('Location', '/old-cars')->withStatus(302);
    }

    public function view(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $car = OldCar::with([
            'additionalImages',
            'extraCharges'
        ])->find($args['id'])->toArray();

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        return $this->twig->render($response, 'old-cars/view.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'car' => $car
        ]);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $car = OldCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        // If Car is Sold then it can't be deleted
        if ($car->status == 'sold') {
            $this->flash->addMessage('error', 'Sold Car can\'t be deleted');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        // Delete associated files
        FileUpload::delete('uploads/documents', $car->rc_book);
        FileUpload::delete('uploads/documents', $car->insurance_doc);
        FileUpload::delete('uploads/documents', $car->owner_id_proof);
        FileUpload::delete('uploads/documents', $car->pan_card);
        FileUpload::delete('uploads/documents', $car->aadhar_card);
        FileUpload::delete('uploads/documents', $car->noc);
        FileUpload::delete('uploads/documents', $car->form_35);
        FileUpload::delete('uploads/documents', $car->purchase_agreement);
        FileUpload::delete('uploads/documents', $car->main_image);

        // Delete additional images
        foreach ($car->additionalImages as $image) {
            FileUpload::delete('uploads/old-cars', $image->image_path);
            $image->delete();
        }

        // Delete the car record
        $car->delete();

        $this->flash->addMessage('success', 'Car deleted successfully');
        return $response->withHeader('Location', '/old-cars')->withStatus(302);
    }

    public function deleteDocument(Request $request, Response $response, $args)
    {
        $car = OldCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        $document = $args['document'];

        if ($document == 'rc_book') {
            FileUpload::delete('uploads/documents', $car->rc_book);
            $car->rc_book = null;
        } else if ($document == 'insurance_doc') {
            FileUpload::delete('uploads/documents', $car->insurance_doc);
            $car->insurance_doc = null;
        } else if ($document == 'owner_id_proof') {
            FileUpload::delete('uploads/documents', $car->owner_id_proof);
            $car->owner_id_proof = null;
        } else if ($document == 'pan_card') {
            FileUpload::delete('uploads/documents', $car->pan_card);
            $car->pan_card = null;
        } else if ($document == 'aadhar_card') {
            FileUpload::delete('uploads/documents', $car->aadhar_card);
            $car->aadhar_card = null;
        } else if ($document == 'noc') {
            FileUpload::delete('uploads/documents', $car->noc);
            $car->noc = null;
        } else if ($document == 'form_35') {
            FileUpload::delete('uploads/documents', $car->form_35);
            $car->form_35 = null;
        } else if ($document == 'purchase_agreement') {
            FileUpload::delete('uploads/documents', $car->purchase_agreement);
            $car->purchase_agreement = null;
        }

        $car->save();

        $this->flash->addMessage('success', 'Document deleted successfully');
        return $response->withHeader('Location', '/old-cars/edit/' . $car->id)->withStatus(302);
    }

    public function deleteMainImage(Request $request, Response $response, $args)
    {
        $car = OldCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        FileUpload::delete('uploads/old-cars', $car->main_image);
        $car->main_image = null;
        $car->save();

        $this->flash->addMessage('success', 'Main image deleted successfully');
        return $response->withHeader('Location', '/old-cars/edit/' . $car->id)->withStatus(302);
    }

    public function deleteImage(Request $request, Response $response, $args)
    {
        $image = OldCarImages::find($args['id']);

        if (!$image) {
            $this->flash->addMessage('error', 'Image not found');
            return $response->withHeader('Location', '/old-cars')->withStatus(302);
        }

        FileUpload::delete('uploads/old-cars', $image->image_url);
        $image->delete();

        $this->flash->addMessage('success', 'Image deleted successfully');
        return $response->withHeader('Location', '/old-cars/edit/' . $image->old_car_id)->withStatus(302);
    }
}
