<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Middlewares\AuthMiddleware;

use App\Controllers\UserController;
use App\Controllers\NewCarController;
use App\Controllers\OldCarController;
use App\Controllers\SaleController;
use App\Controllers\InquiryController;
use App\Controllers\SearchController;

return function (App $app) {
    $twig = $app->getContainer()->get('view');
    $flash = $app->getContainer()->get('flash');

    $userController = new UserController($twig, $flash);
    $newCarController = new NewCarController($twig, $flash);
    $oldCarController = new OldCarController($twig, $flash);
    $saleController = new SaleController($twig, $flash);
    $inquiryController = new InquiryController($twig, $flash);
    $searchController = new SearchController();

    // API Routes
    $app->group('/api', function (RouteCollectorProxy $api) use ($userController, $newCarController, $oldCarController, $saleController, $inquiryController, $searchController, $flash) {
        // Public Routes
        $api->post('/login', [$userController, 'login'])->setName('users.store');
        $api->get('/logout', [$userController, 'logout'])->add(new AuthMiddleware($flash))->setName('users.logout');

        $api->get('/cities/search', [$searchController, 'searchCity'])->setName('cities.search');
        $api->get('/manufacturers/search', [$searchController, 'searchManufacturer'])->setName('manufacturers.search');
        $api->get('/car-models/search', [$searchController, 'searchCarModel'])->setName('car-models.search');
        $api->get('/rtos/search', [$searchController, 'searchRTO'])->setName('rtos.search');
        $api->get('/rto-codess/search', [$searchController, 'searchRTOCode'])->setName('rto-codes.search');
        $api->get('/hps/search', [$searchController, 'searchHirePurchase'])->setName('hps.search');
        $api->get('/executive-branchs/search', [$searchController, 'searchExecutiveBranch'])->setName('executive-branchs.search');
        $api->get('/executives/search', [$searchController, 'searchExecutive'])->setName('executives.search');
        $api->get('/insurance-companies/search', [$searchController, 'searchInsuranceCompany'])->setName('insurance-companies.search');
        $api->get('/dealers/search', [$searchController, 'searchDealer'])->setName('dealers.search');

        // New Cars Routes
        $api->group('/new-cars', function (RouteCollectorProxy $newCars) use ($newCarController) {
            $newCars->post('', [$newCarController, 'store'])->setName('new-cars.store');
            $newCars->put('/{id:[0-9]+}', [$newCarController, 'update'])->setName('new-cars.update');
            $newCars->delete('/{id:[0-9]+}', [$newCarController, 'delete'])->setName('new-cars.destroy');
            $newCars->delete('/delete-main-image/{id:[0-9]+}', [$newCarController, 'deleteMainImage'])->setName('new-cars.delete-main-image');
            $newCars->delete('/delete-image/{id:[0-9]+}', [$newCarController, 'deleteImage'])->setName('new-cars.delete-image');
            $newCars->delete('/delete-document/{id:[0-9]+}/{document}', [$newCarController, 'deleteDocument'])->setName('new-cars.delete-document');
            $newCars->get('/{id:[0-9]+}/print', [$newCarController, 'getPrintData'])->setName('new-cars.print');
        })->add(new AuthMiddleware($flash));

        // Old Cars Routes
        $api->group('/old-cars', function (RouteCollectorProxy $oldCars) use ($oldCarController) {
            $oldCars->post('', [$oldCarController, 'store'])->setName('old-cars.store');
            $oldCars->put('/{id:[0-9]+}', [$oldCarController, 'update'])->setName('old-cars.update');
            $oldCars->delete('/{id:[0-9]+}', [$oldCarController, 'delete'])->setName('old-cars.destroy');
            $oldCars->delete('/delete-main-image/{id:[0-9]+}', [$oldCarController, 'deleteMainImage'])->setName('old-cars.delete-main-image');
            $oldCars->delete('/delete-image/{id:[0-9]+}', [$oldCarController, 'deleteImage'])->setName('old-cars.delete-image');
            $oldCars->delete('/delete-document/{id:[0-9]+}/{document}', [$oldCarController, 'deleteDocument'])->setName('old-cars.delete-document');
        })->add(new AuthMiddleware($flash));

        // Sales Routes
        $api->group('/sales', function (RouteCollectorProxy $sales) use ($saleController) {
            $sales->post('', [$saleController, 'store'])->setName('sales.store');
            $sales->delete('/{id:[0-9]+}', [$saleController, 'deleteSale'])->setName('sales.destroy');
            $sales->get('/cars/{type}', [$saleController, 'getCars'])->setName('sales.cars');
            $sales->get('/cars/{type}/{id:[0-9]+}', [$saleController, 'getCarDetails'])->setName('sales.car');
            $sales->put('/record-payment/{id:[0-9]+}/{sale_id:[0-9]+}', [$saleController, 'recordPayment'])->setName('sales.record-payment');
            $sales->put('/record-full-payment/{id:[0-9]+}/{sale_id:[0-9]+}', [$saleController, 'recordFullPayment'])->setName('sales.record-full-payment');
            $sales->put('/record-dealer-full-payment/{id:[0-9]+}/{sale_id:[0-9]+}', [$saleController, 'dealerRecordFullPayment'])->setName('sales.record-dealer-full-payment');
            $sales->put('/update-full-payment/{id:[0-9]+}/{payment_id:[0-9]+}', [$saleController, 'updateFullPayment'])->setName('sales.update-full-payment');
            $sales->put('/update-dealer-full-payment/{id:[0-9]+}/{payment_id:[0-9]+}', [$saleController, 'updateDealerFullPayment'])->setName('sales.update-dealer-full-payment');
        })->add(new AuthMiddleware($flash));

        // Inquiries Routes
        $api->group('/inquiries', function (RouteCollectorProxy $inquiries) use ($inquiryController) {
            $inquiries->post('', [$inquiryController, 'store'])->setName('inquiries.store');
            $inquiries->put('/{id:[0-9]+}', [$inquiryController, 'update'])->setName('inquiries.update');
            $inquiries->delete('/{id:[0-9]+}', [$inquiryController, 'delete'])->setName('inquiries.destroy');
            $inquiries->put('/change-status/{id:[0-9]+}', [$inquiryController, 'changeStatus'])->setName('inquiries.change-status');
        })->add(new AuthMiddleware($flash));

        // Settings Routes
        $api->group('/settings', function (RouteCollectorProxy $settings) use ($userController) {
            $settings->put('/change-password', [$userController, 'changePassword'])->setName('settings.change-password');
            $settings->put('/update-settings', [$userController, 'updateSettings'])->setName('settings.update-settings');
        })->add(new AuthMiddleware($flash));

        // Users Routes
        $api->group('/users', function (RouteCollectorProxy $users) use ($userController) {
            $users->post('', [$userController, 'register'])->setName('users.register');
            $users->put('/{id:[0-9]+}', [$userController, 'update'])->setName('users.update');
            $users->delete('/{id:[0-9]+}', [$userController, 'delete'])->setName('users.destroy');
        })->add(new AuthMiddleware($flash));
    });
};
