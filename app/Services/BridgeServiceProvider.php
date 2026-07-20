<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BridgeServiceProvider
{
    /**
     * Post Data
     */
    public function getAccessToken()
    {

        $bridgeConfig = config('services.bridge');

        $fullUrl  = $bridgeConfig['bridge_base_url'] . '/login';
        $userName = $bridgeConfig['bridge_username'];
        $password = $bridgeConfig['bridge_password'];

        $data = [
            'email'    => $userName,
            'password' => $password,
        ];

        $response = Http::withoutVerifying()
            ->acceptJson()
            ->asForm()
            ->retry(2)
            ->post($fullUrl, $data);

        if ($response->failed()) {
            Log::info('Failed Posting to following URL: ' . $fullUrl);
            Log::info(json_encode($response->body()));
            exit;
        }

        Cache::put('BRIDGE_TOKEN', $response['access_token']);
        $token = Cache::get('BRIDGE_TOKEN');

        return $response['access_token'];
    }

    /**
     * Post Data
     */
    public function postData(string $apiUrl, array $data)
    {

        $token = $this->getAccessToken();

        $bridgeConfig = config('services.bridge');
        $fullUrl      = $bridgeConfig['bridge_base_url'] . $apiUrl;

        $response = Http::withoutVerifying()
            ->connectTimeout(35)
            ->acceptJson()
            ->retry(5)
            ->withToken($token)
            ->post($fullUrl, $data);

        if ($response->unauthorized()) {

            Log::info($apiUrl);
            Log::info('Auth Not Legit');
            $this->getAccessToken();
            $response = $this->postData($apiUrl, $data);
        }

        if ($response->failed()) {
            Log::info('Failed Posting to following URL: ' . $apiUrl);
            Log::info(json_encode($response->body()));
        }

        return $response['ResultData'] ?? null;
    }

    /**
     * Post Data
     */
    public function postDataV2(string $apiUrl, string $token, array $data)
    {

        $fullUrl = env('BRIDGE_BASEURL') . $apiUrl;

        $response = Http::withoutVerifying()
            ->connectTimeout(35)
            ->acceptJson()
            ->withToken($token)
            ->post($fullUrl, $data);
        if ($response->failed()) {
            Log::info('Failed Posting to following URL: ' . $apiUrl);
            Log::info(json_encode($response->body()));
            throw new \ErrorException($response->body());
        }
    }

    /**
     * Get Data
     */
    public function getData(string $apiUrl)
    {

        $token = Cache::get('BRIDGE_TOKEN');
        if (! $token) {
            $this->getAccessToken();
        }

        $token = Cache::get('BRIDGE_TOKEN');

        $fullUrl = env('BRIDGE_BASEURL') . $apiUrl;

        $response = Http::withoutVerifying()
            ->connectTimeout(15)
            ->acceptJson()
            ->withToken($token)
            ->get($fullUrl);

        if ($response->unauthorized()) {
            $this->getAccessToken();
            $response = $this->getData($apiUrl);
        }
        if ($response->failed()) {
            Log::info('Failed Posting to following URL: ' . $apiUrl);
            Log::info(json_encode($response->body()));
        }

        return $response;
    }

    /**
     * Post Data
     */
    public function getDataV2(string $apiUrl, string $token)
    {

        $fullUrl = env('SAP_SERVICE_LAYER_URL') . $apiUrl;

        $response = Http::withoutVerifying()
            ->connectTimeout(15)
            ->acceptJson()
            ->withToken($token)
            ->get($fullUrl);

        if ($response->unauthorized()) {
            $this->getAccessToken();
            $response = $this->getData($apiUrl);
        }
        if ($response->failed()) {
            Log::info('Failed Posting to following URL: ' . $apiUrl);
            Log::info(json_encode($response->body()));
        }

        return $response;
    }
}
