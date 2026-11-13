<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\NewCarController;
use App\Controllers\OldCarController;
use App\Controllers\SaleController;
use App\Controllers\InquiryController;

use App\Middlewares\AuthMiddleware;

return function (App $app) {
    $twig = $app->getContainer()->get('view');
    $flash = $app->getContainer()->get('flash');

    $userController = new UserController($twig, $flash);
    $dashboardController = new DashboardController($twig, $flash);
    $newCarController = new NewCarController($twig, $flash);
    $oldCarController = new OldCarController($twig, $flash);
    $saleController = new SaleController($twig, $flash);
    $inquiryController = new InquiryController($twig, $flash);

    $app->group('', function (RouteCollectorProxy $group) use ($userController, $dashboardController, $newCarController, $oldCarController, $saleController, $inquiryController) {
        $group->get('/', [$userController, 'index'])
            ->setName('index');

        $group->get('/register', [$userController, 'registerView'])
            ->setName('register');

        $group->get("/dashboard", [$dashboardController, 'index'])
            ->setName('dashboard');

        // New Cars Routes
        $group->group('/new-cars', function (RouteCollectorProxy $newCarGroup) use ($newCarController) {
            $newCarGroup->get('', [$newCarController, 'index'])
                ->setName('new-cars.index');

            $newCarGroup->get('/create', [$newCarController, 'create'])
                ->setName('new-cars.create');

            $newCarGroup->get('/view/{id:[0-9]+}', [$newCarController, 'view'])
                ->setName('new-cars.view');

            $newCarGroup->get('/edit/{id:[0-9]+}', [$newCarController, 'edit'])
                ->setName('new-cars.edit');
        });

        // Old Cars Routes
        $group->group('/old-cars', function (RouteCollectorProxy $oldCarGroup) use ($oldCarController) {
            $oldCarGroup->get('', [$oldCarController, 'index'])
                ->setName('old-cars.index');

            $oldCarGroup->get('/create', [$oldCarController, 'create'])
                ->setName('old-cars.create');

            $oldCarGroup->get('/view/{id:[0-9]+}', [$oldCarController, 'view'])
                ->setName('old-cars.view');

            $oldCarGroup->get('/edit/{id:[0-9]+}', [$oldCarController, 'edit'])
                ->setName('old-cars.edit');
        });

        // Inquiries Routes
        $group->group('/inquiries', function (RouteCollectorProxy $inquiryGroup) use ($inquiryController) {
            $inquiryGroup->get('', [$inquiryController, 'index'])
                ->setName('inquiries.index');

            $inquiryGroup->get('/create', [$inquiryController, 'create'])
                ->setName('inquiries.create');

            $inquiryGroup->get('/view/{id:[0-9]+}', [$inquiryController, 'view'])
                ->setName('inquiries.view');

            $inquiryGroup->get('/edit/{id:[0-9]+}', [$inquiryController, 'edit'])
                ->setName('inquiries.edit');
        });

        // Sales Routes
        $group->group('/sales', function (RouteCollectorProxy $saleGroup) use ($saleController) {
            $saleGroup->get('', [$saleController, 'index'])
                ->setName('sales.index');

            $saleGroup->get('/create', [$saleController, 'create'])
                ->setName('sales.create');

            $saleGroup->get('/view/{id:[0-9]+}', [$saleController, 'view'])
                ->setName('sales.view');

            $saleGroup->get('/edit/{id:[0-9]+}', [$saleController, 'edit'])
                ->setName('sales.edit');
        });

        // Settings Routes
        $group->get('/settings', [$userController, 'settings'])
            ->setName('settings');

        // Users Route
        $group->group('/users', function (RouteCollectorProxy $usersGroup) use ($userController) {
            $usersGroup->get('', [$userController, 'allUsers'])
                ->setName('users.index');

            $usersGroup->get('/create', [$userController, 'registerView'])
                ->setName('users.create');

            $usersGroup->get('/edit/{id:[0-9]+}', [$userController, 'edit'])
                ->setName('users.edit');
        });
    })->add(new AuthMiddleware($flash));
};
