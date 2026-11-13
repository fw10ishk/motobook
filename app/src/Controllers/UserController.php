<?php

namespace App\Controllers;

use Slim\Routing\RouteContext;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\User;

class UserController
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

        return $this->twig->render($response, 'index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages()
        ]);
    }

    public function registerView(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        return $this->twig->render($response, 'users/create.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages()
        ]);
    }

    public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $email = $data['email'];
        $password = $data['password'];
        $fullName = $data['full_name'];
        $role = $data['role'] ?? 'user';

        $user = User::createUser($email, $password, $fullName, $role);

        $this->flash->addMessage('success', 'User registered successfully!');

        return $response->withHeader('Location', '/users')->withStatus(302);
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $email = $data['email'];
        $password = $data['password'];
        $rememberMe = isset($data['remember_me']);

        // Check if there is a valid remember token in the cookie
        $rememberToken = $_COOKIE['remember_token'] ?? null;
        if ($rememberToken) {
            $user = User::checkRememberToken($rememberToken);
            if ($user) {
                // Automatically log in the user if the remember token is valid
                $_SESSION['user'] = $user->toArray();
                $this->flash->addMessage('success', 'User logged in successfully!');
                return $response->withHeader('Location', '/dashboard')->withStatus(302);
            }
        }

        $result = User::loginUser($email, $password, $rememberMe);

        if (!$result) {
            $this->flash->addMessage('error', 'Invalid email or password');
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Set the user in session
        $_SESSION['user'] = $result['user']->toArray();

        $this->flash->addMessage('success', 'User logged in successfully!');

        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    public function logout(Request $request, Response $response)
    {
        // Clear the remember token cookie if it exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }

        unset($_SESSION['user']);
        $this->flash->addMessage('success', 'User logged out successfully!');

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function settings(Request $request, Response $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $user = User::find($_SESSION['user']['id']);

        return $this->twig->render($response, 'settings/index.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'user' => $user->toArray()
        ]);
    }

    public function updateSettings(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $user = User::find($_SESSION['user']['id']);

        $user->update([
            'email' => $data['email'],
            'full_name' => $data['full_name']
        ]);

        $this->flash->addMessage('success', 'User settings updated successfully!');

        return $response->withHeader('Location', '/settings')->withStatus(302);
    }

    public function changePassword(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $result = User::changePassword($data['current_password'], $data['new_password']);

        if (!$result) {
            $this->flash->addMessage('error', 'Invalid old password');
            return $response->withHeader('Location', '/settings')->withStatus(302);
        }

        $this->flash->addMessage('success', 'Password changed successfully!');

        return $response->withHeader('Location', '/settings')->withStatus(302);
    }

    public function allUsers(Request $request, Response $response)
    {
        // Get query parameters
        $queryParams = $request->getQueryParams();
        $status = $queryParams['status'] ?? 'All';
        $search = $queryParams['search'] ?? '';
        $page = (int)($queryParams['page'] ?? 1);
        $perPage = 10; // Items per page

        // Base Query
        $query = User::query();
        $query->where('id', '!=', $_SESSION['user']['id']);

        // Apply Status Filter
        if ($status !== 'All') {
            $query->where('status', strval($status));
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        // Get total count for pagination
        $totalItems = $query->count();
        $totalPages = ceil($totalItems / $perPage);

        // Apply pagination
        $users = $query->orderByDesc('created_at')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->toArray();

        return $this->twig->render($response, 'users/index.twig', [
            'users' => $users,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'current_page' => $page,
            'current_status' => $status,
            'current_search' => $search
        ]);
    }

    public function edit(Request $request, Response $response, $args)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $user = User::find($args['id']);

        return $this->twig->render($response, 'users/edit.twig', [
            'current_route' => $route->getName(),
            'flash' => $this->flash->getMessages(),
            'user' => $user->toArray()
        ]);
    }

    public function update(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $result = User::updateUser($args['id'], $data);

        if (!$result) {
            $this->flash->addMessage('error', 'Failed to update user!');
            return $response->withHeader('Location', '/users/edit/' . $args['id'])->withStatus(302);
        }

        $this->flash->addMessage('success', 'User updated successfully!');
        return $response->withHeader('Location', '/users')->withStatus(302);
    }

    public function delete(Request $request, Response $response, $args)
    {
        $user = User::find($args['id']);

        $user->delete();

        $this->flash->addMessage('success', 'User deleted successfully!');

        return $response->withHeader('Location', '/users')->withStatus(302);
    }
}
