<?php

namespace App\Http\Controllers\Api;

use App\Services\Forecast;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class WeatherController extends Controller
{
    public function get(Request $request, Forecast $forecast)
    {
        $data = $forecast->get(
            $request->get('coords'),
            $request->get('units')
        );

        return response($data, 200)
            ->header('Content-Type', 'json');
    }

    
}
