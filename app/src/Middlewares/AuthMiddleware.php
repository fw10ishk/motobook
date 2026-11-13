<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Psr7Response;
use App\Models\RememberToken;
use App\Models\User;
use Carbon\Carbon;

class AuthMiddleware
{
    private $flash;
    private $publicRoutes = ['/', '/register'];

    public function __construct($flash)
    {
        $this->flash = $flash;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $uri = $request->getUri()->getPath();
        $isLoggedIn = isset($_SESSION['user']);
        $rememberToken = $_COOKIE['remember_token'] ?? null;

        // Token-based login if not already logged in
        if (!$isLoggedIn && $rememberToken) {
            $tokenRecord = RememberToken::where('token', $rememberToken)
                ->where('is_valid', true)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if ($tokenRecord) {
                $user = User::find($tokenRecord->user_id);
                if ($user) {
                    // Log in the user
                    $_SESSION['user'] = $user->toArray();

                    // Optionally refresh token expiry
                    $tokenRecord->update(['expires_at' => Carbon::now()->addDays(30)]);
                    $isLoggedIn = true;
                }
            }
        }

        // If user is logged in and trying to access public routes
        if ($isLoggedIn && in_array($uri, $this->publicRoutes)) {
            $response = new Psr7Response();
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        // If user is not logged in and trying to access protected routes
        if (!$isLoggedIn && !in_array($uri, $this->publicRoutes)) {
            $response = new Psr7Response();
            $this->flash->addMessage('error', 'You must be logged in to access this page');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $handler->handle($request);
    }
}
