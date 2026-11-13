<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use Twig\TwigFilter;

use DI\Container;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

// Autoload dependencies
require __DIR__ . '/app/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/app');
$dotenv->load();

// Include bootstrap file
require_once __DIR__ . '/app/bootstrap.php';

// Create Container
$container = new Container();

// Set view in container
$container->set('view', function ($container) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $twig = Twig::create(__DIR__ . '/app/templates', [
        'auto_reload' => $_ENV['APP_ENV'] !== 'production',
        'debug' => $_ENV['APP_ENV'] !== 'production',
        'cache' => $_ENV['APP_ENV'] === 'production' ? __DIR__ . '/app/cache' : false,
    ]);

    // Add global session variables
    $twig->getEnvironment()->addGlobal('session', $_SESSION);

    // Add flash messages to Twig globals
    $twig->getEnvironment()->addGlobal('flash', $container->get('flash')->getMessages());

    // Add Indian number format filter
    $filter = new TwigFilter('indian_format', function ($value) {
        $number = (string)$value;
        $lastThreeDigits = substr($number, -3);
        $remainingDigits = substr($number, 0, -3);

        if ($remainingDigits !== '') {
            $remainingDigits = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remainingDigits);
            return $remainingDigits . ',' . $lastThreeDigits;
        }

        return $lastThreeDigits;
    });

    $twig->getEnvironment()->addFilter($filter);

    return $twig;
});

// Set flash in container
$container->set('flash', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    return new Messages();
});

// Create App Instance with Container
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add Base Middleware
$app->addBodyParsingMiddleware();
$app->add(new MethodOverrideMiddleware());

// Session Middleware
$app->add(function (Request $request, Handler $handler) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $handler->handle($request);
});

// Flash Middleware
$app->add(function (Request $request, Handler $handler) use ($container) {
    $flash = $container->get('flash');
    $view = $container->get('view');

    // Store current flash messages
    $messages = $flash->getMessages();

    // Make flash messages available to templates
    $view->getEnvironment()->addGlobal('flash', $messages);

    // Handle the request
    $response = $handler->handle($request);

    return $response;
});

// Twig Middleware
$app->add(TwigMiddleware::createFromContainer($app));

// CORS Middleware
$app->add(function (Request $request, Handler $handler): Response {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $_ENV['CORS_ORIGIN'] ?? '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

// Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    $_ENV['APP_ENV'] !== 'production',
    true,
    true
);

// Load Routes
(require __DIR__ . '/app/src/Routes/web.php')($app);
(require __DIR__ . '/app/src/Routes/api.php')($app);

// Run the app
$app->run();
