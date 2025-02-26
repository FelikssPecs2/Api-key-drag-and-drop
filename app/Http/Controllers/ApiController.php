<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function fetchData(Request $request)
    {
        $url = $request->input('url');

        if (!$url) {
            return response()->json(['error' => 'API URL is required.'], 400);
        }

        try {
            // Fetch data from the API using Laravel's HTTP Client (Http facade)
            $response = Http::get($url);

            // Check if the request was successful
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Failed to fetch data from API.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }
}