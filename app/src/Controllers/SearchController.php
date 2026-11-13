<?php

namespace App\Controllers;

use App\Models\CarDealer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Manufacturer;
use App\Models\CarModel;
use App\Models\HirePurchase;
use App\Models\ExecutiveBranch;
use App\Models\Executive;
use App\Models\InsuranceCompany;
use App\Models\RTO;
use App\Models\City;
use App\Models\RTOCode;

class SearchController
{
    public function searchManufacturer(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $manufacturers = Manufacturer::search($query);

        $response->getBody()->write(json_encode($manufacturers));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchCarModel(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $carModels = CarModel::search($query);

        $response->getBody()->write(json_encode($carModels));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchHirePurchase(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $hirePurchases = HirePurchase::search($query);

        $response->getBody()->write(json_encode($hirePurchases));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchExecutiveBranch(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $executiveBranches = ExecutiveBranch::search($query);

        $response->getBody()->write(json_encode($executiveBranches));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchExecutive(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $executives = Executive::search($query);

        $response->getBody()->write(json_encode($executives));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchInsuranceCompany(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $insuranceCompanies = InsuranceCompany::search($query);

        $response->getBody()->write(json_encode($insuranceCompanies));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchRTO(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $rtos = RTO::search($query);

        $response->getBody()->write(json_encode($rtos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchRTOCode(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $rtos = RTOCode::search($query);

        $response->getBody()->write(json_encode($rtos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchCity(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $cities = City::search($query);

        $response->getBody()->write(json_encode($cities));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function searchDealer(Request $request, Response $response)
    {
        $params = $request->getQueryParams() ?? "";
        $query = $params['query'] ?? "";

        $dealers = CarDealer::search($query);

        $response->getBody()->write(json_encode($dealers));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
