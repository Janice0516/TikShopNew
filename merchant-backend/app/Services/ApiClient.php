<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.api.base_url', 'http://localhost:8081/api'), '/');
    }

    /**
     * GET 请求
     */
    public function get(string $path, array $query = [], ?string $token = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $request = Http::withHeaders($this->buildHeaders($token));
        $response = $request->get($url, $query);
        return $this->toArray($response->json());
    }

    /**
     * POST 请求
     */
    public function post(string $path, array $data = [], ?string $token = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $request = Http::withHeaders($this->buildHeaders($token));
        $response = $request->post($url, $data);
        return $this->toArray($response->json());
    }

    /**
     * PATCH 请求
     */
    public function patch(string $path, array $data = [], ?string $token = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $request = Http::withHeaders($this->buildHeaders($token));
        $response = $request->patch($url, $data);
        return $this->toArray($response->json());
    }

    /**
     * DELETE 请求
     */
    public function delete(string $path, array $data = [], ?string $token = null): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $request = Http::withHeaders($this->buildHeaders($token));
        $response = $request->delete($url, $data);
        return $this->toArray($response->json());
    }

    private function buildHeaders(?string $token): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        return $headers;
    }

    private function toArray($json): array
    {
        if (is_array($json)) return $json;
        return [];
    }
}