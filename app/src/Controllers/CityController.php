<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\City;

class CityController
{
    public function search(Request $request, Response $response)
    {
        $query = $request->getQueryParams()['query'] ?? '';
        $cities = City::where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($city) {
                return ['value' => $city->name, 'text' => $city->name];
            });

        $response->getBody()->write(json_encode($cities));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
