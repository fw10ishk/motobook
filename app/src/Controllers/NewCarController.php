<?php

namespace App\Controllers;

use Slim\Routing\RouteContext;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\NewCar;

use App\Models\NewCarImages;

use App\Helpers\FileUpload;

class NewCarController
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
        $query = NewCar::query();

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

        // Calculate statistics with filters applied
        $totalCars = NewCar::count();
        $currentMonth = date('m');
        $currentYear = date('Y');

        $totalCarsThisMonth = NewCar::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $previousMonth = date('m', strtotime('-1 month'));
        $totalCarsPreviousMonth = NewCar::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $difference = $totalCarsThisMonth - $totalCarsPreviousMonth;
        $newCarsThisMonth = $difference > 0 ? "+$difference New Cars this month" : ($difference == 0 ? 'No New Cars this month' : "$difference New Cars this month");

        $soldCarsThisMonth = NewCar::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('status', 'sold')
            ->count();

        $soldCarsPercentage = $totalCars > 0 ? round(($soldCarsThisMonth / $totalCars) * 100, 2) : 0;

        $unsoldCarsThisMonth = NewCar::whereMonth('delivery_date', $currentMonth)
            ->whereYear('delivery_date', $currentYear)
            ->where('status', 'unsold')
            ->count();

        $unsoldCarsPercentage = $totalCars > 0 ? round(($unsoldCarsThisMonth / $totalCars) * 100, 2) : 0;

        $differenceStatus = $difference > 0 ? 'success' : ($difference == 0 ? 'text' : 'danger');

        return $this->twig->render($response, 'new-cars/index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'total_cars' => $totalCars,
            'new_cars_this_month' => $newCarsThisMonth,
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

        return $this->twig->render($response, 'new-cars/create.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages()
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        // Extract form data
        $docket_number = $data['docket_number'];
        $entry_type = $data['entry_type'];
        $booking_date = $data['booking_date'];
        $delivery_date = $data['delivery_date'];
        $customer_name = $data['customer_name'];
        $customer_phone = $data['customer_phone'];
        $customer_address1 = $data['customer_address1'];
        $customer_address2 = $data['customer_address2'];
        $city_name = $data['city_name'];
        $pincode = $data['pincode'];
        $email = $data['email'];
        $customer_dob = $data['customer_dob'];

        // Nominee details
        $nominee_relationship = $data['nominee_relationship'];
        $nominee_name = $data['nominee_name'];
        $nominee_dob = $data['nominee_dob'];

        // Vehicle information
        $manufacturer_name = $data['manufacturer_name'];
        $model_name = $data['model_name'];
        $color = $data['color'];
        $registration_no = $data['registration_no'];
        $chassis_no = $data['chassis_no'];
        $engine_no = $data['engine_no'];
        $hp_name = $data['hp_name'];

        // Additional details
        $dealer_name = $data['dealer_name'];
        $location = $data['location'];
        $executive_branch_name = $data['executive_branch_name'];
        $executive_name = $data['executive_name'];
        $insurance_company_name = $data['insurance_company_name'];
        $rto_name = $data['rto_name'];
        $rto_code = $data['rto_code'];
        $parsing_status = $data['parsing_status'];
        $number_plate = $data['number_plate'];
        $choice_number = $data['choice_number'];
        $scheme = $data['scheme'];
        $broker_name = $data['broker_name'];
        $other_remarks = $data['other_remarks'];

        // Pricing Details
        $ex_showroom_price = $data['ex_showroom_price'] ?? 0;
        $rto_tax = $data['rto_tax'] ?? 0;
        $insurance = $data['insurance'] ?? 0;
        $amc_gmc_tax = $data['amc_gmc_tax'] ?? 0;
        $ext_warranty = $data['ext_warranty'] ?? 0;
        $accessories = $data['accessories'] ?? 0;
        $krunal_accessories = $data['krunal_accessories'] ?? 0;
        $fast_tag = $data['fast_tag'] ?? 0;
        $tcs = $data['tcs'] ?? 0;
        $loyalty_card = $data['loyalty_card'] ?? 0;
        $driver_price = $data['driver_price'] ?? 0;

        // Dealer Pricing Details
        $dealer_ex_showroom_price = $data['dealer_ex_showroom_price'] ?? 0;
        $dealer_rto_tax = $data['dealer_rto_tax'] ?? 0;
        $dealer_insurance = $data['dealer_insurance'] ?? 0;
        $dealer_amc_gmc_tax = $data['dealer_amc_gmc_tax'] ?? 0;
        $dealer_ext_warranty = $data['dealer_ext_warranty'] ?? 0;
        $dealer_accessories = $data['dealer_accessories'] ?? 0;
        $dealer_krunal_accessories = $data['dealer_krunal_accessories'] ?? 0;
        $dealer_fast_tag = $data['dealer_fast_tag'] ?? 0;
        $dealer_tcs = $data['dealer_tcs'] ?? 0;
        $dealer_loyalty_card = $data['dealer_loyalty_card'] ?? 0;
        $dealer_driver_price = $data['dealer_driver_price'] ?? 0;

        // Handle file uploads
        $uploadedFiles = $request->getUploadedFiles();
        $mainImage = $uploadedFiles['main_image'];
        $additionalImages = $uploadedFiles['additional_images'];

        // Required document fields
        $requiredDocuments = [
            'kyc',
            'deal_agreement'
        ];

        // Validate required files
        foreach ($requiredDocuments as $document) {
            if (!isset($uploadedFiles[$document]) || !FileUpload::isValidUpload($uploadedFiles[$document])) {
                $this->flash->addMessage('error', ucfirst(str_replace('_', ' ', $document)) . ' is required');
                return $response->withHeader('Location', '/new-cars/create')->withStatus(302);
            }
        }

        // Process document uploads
        foreach ($requiredDocuments as $document) {
            if (isset($uploadedFiles[$document])) {
                $uploadPath = 'uploads/documents';
                $data[$document] = FileUpload::upload($uploadedFiles[$document], $uploadPath);
            }
        }

        // Handle main image
        if ($mainImage && $mainImage->getError() === UPLOAD_ERR_OK) {
            try {
                $main_image = FileUpload::upload($mainImage, 'uploads/new-cars');
            } catch (\Exception $e) {
                // Log error, but don't prevent product creation
                error_log('Main image upload failed: ' . $e->getMessage());
            }
        }

        // Calculate total on-road price
        $total_on_road = (float)$ex_showroom_price + (float)$rto_tax + (float)$insurance + (float)$amc_gmc_tax + (float)$ext_warranty + (float)$accessories + (float)$krunal_accessories + (float)$fast_tag + (float)$tcs + (float)$loyalty_card;

        $consumer_offer = $data['consumer_offer'] ?? 0;
        $corporate_discount = $data['corporate_discount'] ?? 0;
        $other_discount = $data['other_discount'] ?? 0;


        // Calculate net on-road price
        $net_on_road = (float)$total_on_road - (float)$consumer_offer - (float)$corporate_discount - (float)$other_discount;

        $disbursement = $data['disbursement'] ?? 0;
        $down_payment = $data['down_payment'] ?? 0;

        // Calculate net short payment
        $net_short_payment = (float)$net_on_road - (float)$disbursement - (float)$down_payment;

        // Calculate dealer total on-road price
        $dealer_total_on_road = (float)$dealer_ex_showroom_price + (float)$dealer_rto_tax + (float)$dealer_insurance + (float)$dealer_amc_gmc_tax + (float)$dealer_ext_warranty + (float)$dealer_accessories + (float)$dealer_krunal_accessories + (float)$dealer_fast_tag + (float)$dealer_tcs + (float)$dealer_loyalty_card;

        $dealer_consumer_offer = $data['dealer_consumer_offer'] ?? 0;
        $dealer_corporate_discount = $data['dealer_corporate_discount'] ?? 0;
        $dealer_other_discount = $data['dealer_other_discount'] ?? 0;

        // Calculate dealer net on-road price
        $dealer_net_on_road = (float)$dealer_total_on_road - (float)$dealer_consumer_offer - (float)$dealer_corporate_discount - (float)$dealer_other_discount;

        $dealer_disbursement = $data['dealer_disbursement'] ?? 0;
        $dealer_down_payment = $data['dealer_down_payment'] ?? 0;

        // Calculate dealer net short payment
        $dealer_net_short_payment = (float)$dealer_net_on_road - (float)$dealer_disbursement - (float)$dealer_down_payment;

        // Prepare new car data
        $newCar = [
            'docket_number' => $docket_number,
            'entry_type' => $entry_type,
            'booking_date' => $booking_date,
            'delivery_date' => $delivery_date,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'customer_address1' => $customer_address1,
            'customer_address2' => $customer_address2,
            'city_name' => $city_name,
            'pincode' => $pincode,
            'email' => $email,
            'customer_dob' => $customer_dob,
            'nominee_relationship' => $nominee_relationship,
            'nominee_name' => $nominee_name,
            'nominee_dob' => $nominee_dob,
            'manufacturer_name' => $manufacturer_name,
            'model_name' => $model_name,
            'color' => $color,
            'registration_no' => $registration_no,
            'chassis_no' => $chassis_no,
            'engine_no' => $engine_no,
            'dealer_name' => $dealer_name,
            'location' => $location,
            'hp_name' => $hp_name,
            'executive_branch_name' => $executive_branch_name,
            'executive_name' => $executive_name,
            'insurance_company_name' => $insurance_company_name,
            'rto_name' => $rto_name,
            'rto_code' => $rto_code,
            'parsing_status' => $parsing_status,
            'number_plate' => $number_plate,
            'choice_number' => $choice_number,
            'scheme' => $scheme,
            'broker_name' => $broker_name,
            'other_remarks' => $other_remarks,
            'kyc' => $data['kyc'],
            'deal_agreement' => $data['deal_agreement'],
            'main_image' => $main_image,
            'ex_showroom_price' => $ex_showroom_price,
            'rto_tax' => $rto_tax,
            'insurance' => $insurance,
            'amc_gmc_tax' => $amc_gmc_tax,
            'ext_warranty' => $ext_warranty,
            'accessories' => $accessories,
            'krunal_accessories' => $krunal_accessories,
            'fast_tag' => $fast_tag,
            'tcs' => $tcs,
            'loyalty_card' => $loyalty_card,
            'driver_price' => $driver_price,
            'total_on_road' => $total_on_road,
            'consumer_offer' => $consumer_offer,
            'corporate_discount' => $corporate_discount,
            'other_discount' => $other_discount,
            'net_on_road' => $net_on_road,
            'disbursement' => $disbursement,
            'down_payment' => $down_payment,
            'net_short_payment' => $net_short_payment,
            'dealer_ex_showroom_price' => $dealer_ex_showroom_price,
            'dealer_rto_tax' => $dealer_rto_tax,
            'dealer_insurance' => $dealer_insurance,
            'dealer_amc_gmc_tax' => $dealer_amc_gmc_tax,
            'dealer_ext_warranty' => $dealer_ext_warranty,
            'dealer_accessories' => $dealer_accessories,
            'dealer_krunal_accessories' => $dealer_krunal_accessories,
            'dealer_fast_tag' => $dealer_fast_tag,
            'dealer_tcs' => $dealer_tcs,
            'dealer_loyalty_card' => $dealer_loyalty_card,
            'dealer_driver_price' => $dealer_driver_price,
            'dealer_total_on_road' => $dealer_total_on_road,
            'dealer_consumer_offer' => $dealer_consumer_offer,
            'dealer_corporate_discount' => $dealer_corporate_discount,
            'dealer_other_discount' => $dealer_other_discount,
            'dealer_net_on_road' => $dealer_net_on_road,
            'dealer_disbursement' => $dealer_disbursement,
            'dealer_down_payment' => $dealer_down_payment,
            'dealer_net_short_payment' => $dealer_net_short_payment
        ];

        $car = NewCar::createNewCar($newCar, $additionalImages);

        if ($car) {
            $this->flash->addMessage('success', 'Car created successfully');
        } else {
            $this->flash->addMessage('error', 'Car creation failed');
        }

        return $response->withHeader('Location', '/sales/create?car_type=new&car_id=' . $car->id)->withStatus(302);
    }

    public function edit(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $id = $args['id'];
        $car = NewCar::with([
            'additionalImages',
        ])->find($id)->toArray();

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        // In Lowercase
        $nomimeeRelations = [
            [
                'id' => 'father',
                'name' => 'Father'
            ],
            [
                'id' => 'mother',
                'name' => 'Mother'
            ],
            [
                'id' => 'son',
                'name' => 'Son'
            ],
            [
                'id' => 'daughter',
                'name' => 'Daughter'
            ],
            [
                'id' => 'wife',
                'name' => 'Wife'
            ],
            [
                'id' => 'husband',
                'name' => 'Husband'
            ],
            [
                'id' => 'brother',
                'name' => 'Brother'
            ],
            [
                'id' => 'sister',
                'name' => 'Sister'
            ],
        ];


        return $this->twig->render($response, 'new-cars/edit.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'car' => $car,
            'nomimeeRelations' => $nomimeeRelations,
        ]);
    }

    public function update(Request $request, Response $response, $args)
    {
        $car = NewCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        if ($files['kyc']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->kyc);
            $data['kyc'] = FileUpload::upload($files['kyc'], 'uploads/documents');
        } else if (empty($car->kyc)) {
            $this->flash->addMessage('error', 'KYC is Required');
            return $response->withHeader('Location', '/new-cars/edit/' . $car->id)->withStatus(302);
        }

        if ($files['deal_agreement']->getSize() > 0) {
            FileUpload::delete('uploads/documents', $car->deal_agreement);
            $data['deal_agreement'] = FileUpload::upload($files['deal_agreement'], 'uploads/documents');
        } else if (empty($car->deal_agreement)) {
            $this->flash->addMessage('error', 'Deal Agreement is Required');
            return $response->withHeader('Location', '/new-cars/edit/' . $car->id)->withStatus(302);
        }

        if ($files['main_image']->getSize() > 0) {
            FileUpload::delete('uploads/new-cars', $car->main_image);
            $data['main_image'] = FileUpload::upload($files['main_image'], 'uploads/new-cars');
        }

        try {
            // Update the car record
            $car->update($data);

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
                return $response->withHeader('Location', '/new-cars/edit/' . $car->id)->withStatus(302);
            }

            // Process valid images
            foreach ($files['additional_images'] as $image) {
                if ($image->getSize() > 0) {
                    $imagePath = FileUpload::upload($image, 'uploads/new-cars');
                    NewCarImages::create([
                        'new_car_id' => $car->id,
                        'image_url' => $imagePath
                    ]);
                }
            }

            $this->flash->addMessage('success', 'Car updated successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update car. Please try again.');
            // You might want to log the error here
        }

        return $response->withHeader('Location', '/new-cars')->withStatus(302);
    }

    public function view(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $car = NewCar::with([
            'additionalImages'
        ])->find($args['id'])->toArray();

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        return $this->twig->render($response, 'new-cars/view.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'car' => $car
        ]);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $car = NewCar::find($id);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        // If Car is Sold then it can't be deleted
        if ($car->status == 'sold') {
            $this->flash->addMessage('error', 'Sold Car can\'t be deleted');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        $car->deleteNewCar($id);

        $this->flash->addMessage('success', 'Car deleted successfully');
        return $response->withHeader('Location', '/new-cars')->withStatus(302);
    }

    public function deleteDocument(Request $request, Response $response, $args)
    {
        $car = NewCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        $document = $args['document'];

        if ($document == 'kyc') {
            FileUpload::delete('uploads/documents', $car->kyc);
            $car->kyc = null;
        } else if ($document == 'deal_agreement') {
            FileUpload::delete('uploads/documents', $car->deal_agreement);
            $car->deal_agreement = null;
        }

        $car->save();

        $this->flash->addMessage('success', 'Document deleted successfully');
        return $response->withHeader('Location', '/new-cars/edit/' . $car->id)->withStatus(302);
    }

    public function deleteMainImage(Request $request, Response $response, $args)
    {
        $car = NewCar::find($args['id']);

        if (!$car) {
            $this->flash->addMessage('error', 'Car not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        FileUpload::delete('uploads/new-cars', $car->main_image);
        $car->main_image = null;
        $car->save();

        $this->flash->addMessage('success', 'Main image deleted successfully');
        return $response->withHeader('Location', '/new-cars/edit/' . $car->id)->withStatus(302);
    }

    public function deleteImage(Request $request, Response $response, $args)
    {
        $image = NewCarImages::find($args['id']);

        if (!$image) {
            $this->flash->addMessage('error', 'Image not found');
            return $response->withHeader('Location', '/new-cars')->withStatus(302);
        }

        FileUpload::delete('uploads/new-cars', $image->image_url);
        $image->delete();

        $this->flash->addMessage('success', 'Image deleted successfully');
        return $response->withHeader('Location', '/new-cars/edit/' . $image->new_car_id)->withStatus(302);
    }

    public function getPrintData(Request $request, Response $response, $args)
    {
        $car = NewCar::find($args['id']);

        if (!$car) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->getBody()->write(json_encode(['error' => 'Car not found']));
        }

        $printData = [
            'docket_number' => $car->docket_number,
            'booking_date' => date('d/m/Y', strtotime($car->booking_date)),
            'dealer_name' => $car->dealer_name,
            'manufacturer_name' => $car->manufacturer_name,
            'model_name' => $car->model_name,
            'engine_no' => $car->engine_no,
            'color' => $car->color,
            'chassis_no' => $car->chassis_no,
            'customer_name' => $car->customer_name,
            'customer_address1' => $car->customer_address1,
            'customer_address2' => $car->customer_address2,
            'email' => $car->email,
            'customer_phone' => $car->customer_phone,
            'customer_dob' => date('d/m/Y', strtotime($car->customer_dob)),
            'nominee_name' => $car->nominee_name,
            'nominee_relationship' => $car->nominee_relationship,
            'nominee_dob' => date('d/m/Y', strtotime($car->nominee_dob)),
            'hp_name' => $car->hp_name,
            'scheme' => $car->scheme ?? 'NILL',
            'delivery_date' => date('d/m/Y', strtotime($car->delivery_date)),
            'accessories' => $car->accessories ?? 'NILL',
            'ext_warranty' => $car->ext_warranty ?? 'NILL',
            'choice_number' => $car->choice_number ?? 'NILL',
        ];

        $response->getBody()->write(json_encode($printData));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
