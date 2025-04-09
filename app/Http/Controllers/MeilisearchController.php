<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MeilisearchController extends Controller
{
    public function getKey(Request $request)
    {

        $host = env('MEILISEARCH_HOST');
        $apiKey = env('MEILISEARCH_KEY');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->get("$host/keys");

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch API keys from Meilisearch',
                    'details' => $response->json(),
                ], $response->status());
            }

            $keys = $response->json()['results'] ?? [];

            // Check if the key exists
            foreach ($keys as $key) {
                if ($key['name'] === 'public_key') {
                    return response()->json([
                        'message' => 'API key already exists',
                        'result' => [
                            'name' => $key['name'],
                            'key' => $key['key'],
                            'actions' => $key['actions'],
                            'indexes' => $key['indexes'],
                            'expiresAt' => $key['expiresAt'],
                        ]
                    ]);
                }
            }


            $createResponse = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post("$host/keys", [
                        'name' => 'public_key',
                        'description' => 'public_key',
                        'actions' => ['search'],
                        'indexes' => ['resources'],
                        'expiresAt' => null,
                    ]);

            if ($createResponse->failed()) {
                return response()->json([
                    'error' => 'Failed to create API key',
                    'details' => $createResponse->json(),
                ], $createResponse->status());
            }

            return response()->json([
                'message' => 'API key created successfully',
                'key' => $createResponse->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to connect to Meilisearch. Please check credentials!'], 500);
        }
    }

}
