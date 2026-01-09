<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupabaseStorage
{
    protected $url;
    protected $serviceKey;
    protected $bucket = 'archivos';

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->serviceKey = config('services.supabase.service_key');
    }

    /**
     * Upload a file to Supabase Storage
     */
    public function upload($file, $path)
    {
        try {
            $fileContent = file_get_contents($file->getRealPath());
            $fileName = $path . '/' . time() . '_' . $file->getClientOriginalName();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->serviceKey,
                'Content-Type' => $file->getMimeType(),
            ])->send('POST', "{$this->url}/storage/v1/object/{$this->bucket}/{$fileName}", [
                'body' => $fileContent
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'path' => $fileName,
                    'url' => $this->getPublicUrl($fileName)
                ];
            }

            Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Supabase upload exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete a file from Supabase Storage
     */
    public function delete($path)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->serviceKey,
            ])->delete("{$this->url}/storage/v1/object/{$this->bucket}/{$path}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Supabase delete exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get public URL for a file
     */
    public function getPublicUrl($path)
    {
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
    }
}
