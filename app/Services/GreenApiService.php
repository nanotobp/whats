<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class GreenApiService
{
    private Client $client;
    private string $instanceId;
    private string $apiToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->instanceId = config('services.green_api.instance_id');
        $this->apiToken = config('services.green_api.api_token');

        // Extract short ID (first 4 digits) for URL domain
        $shortId = substr($this->instanceId, 0, 4);
        $this->baseUrl = "https://{$shortId}.api.green-api.com/waInstance{$this->instanceId}";

        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);
    }

    public function checkWhatsapp(string $phoneNumber): array
    {
        try {
            $url = "{$this->baseUrl}/CheckWhatsapp/{$this->apiToken}";

            $response = $this->client->post($url, [
                'json' => [
                    'phoneNumber' => $this->formatPhoneNumber($phoneNumber)
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'exists' => $data['existsWhatsapp'] ?? false,
                'phone' => $phoneNumber
            ];
        } catch (GuzzleException $e) {
            Log::error('Green API Check WhatsApp Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'exists' => false,
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendMessage(string $phoneNumber, string $message): array
    {
        try {
            $url = "{$this->baseUrl}/SendMessage/{$this->apiToken}";

            $response = $this->client->post($url, [
                'json' => [
                    'chatId' => $this->formatPhoneNumber($phoneNumber) . '@c.us',
                    'message' => $message
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $data['idMessage'] ?? null,
                'data' => $data
            ];
        } catch (GuzzleException $e) {
            Log::error('Green API Send Message Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendFileByUrl(string $phoneNumber, string $imageUrl, string $caption = ''): array
    {
        try {
            $url = "{$this->baseUrl}/SendFileByUrl/{$this->apiToken}";

            $response = $this->client->post($url, [
                'json' => [
                    'chatId' => $this->formatPhoneNumber($phoneNumber) . '@c.us',
                    'urlFile' => $imageUrl,
                    'fileName' => 'image.jpg',
                    'caption' => $caption
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'message_id' => $data['idMessage'] ?? null,
                'data' => $data
            ];
        } catch (GuzzleException $e) {
            Log::error('Green API Send File Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getStateInstance(): array
    {
        try {
            $url = "{$this->baseUrl}/GetStateInstance/{$this->apiToken}";

            $response = $this->client->get($url);
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'state' => $data['stateInstance'] ?? 'unknown',
                'data' => $data
            ];
        } catch (GuzzleException $e) {
            Log::error('Green API Get State Error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (!str_starts_with($cleaned, '1') && !str_starts_with($cleaned, '5')) {
            $cleaned = '1' . $cleaned;
        }

        return $cleaned;
    }

    public function batchCheckWhatsapp(array $phoneNumbers): array
    {
        $results = [];

        foreach ($phoneNumbers as $phone) {
            $results[] = $this->checkWhatsapp($phone);
            usleep(500000);
        }

        return $results;
    }
}
