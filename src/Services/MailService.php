<?php

namespace IdQueue\IdQueuePackagist\Services;

use Illuminate\Support\Facades\Http;

class MailService
{
    /**
     * Send a mail using the external mail service.
     *
     * @param string $email
     * @param string $subject
     * @param string|null $cc
     * @param string $msg
     * @return array
     */
    public function sendMail(string $email, string $subject, ?string $cc, string $msg): array
    {
        $details = compact('email', 'subject', 'cc', 'msg');
        return $this->makePostRequest('api/send-mail', $details);
    }

    /**
     * Send an email request via API.
     *
     * @param array $requestData
     * @return array
     */
    public function sendEmailRequest(array $requestData): array
    {
        return $this->makePostRequest('api/submit-request-mail', $requestData);
    }

    /**
     * Make a POST request to the mail service.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makePostRequest(string $endpoint, array $data): array
    {
        $url = rtrim(config('idqueuepackagist.mail-service'), '/') . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::withHeaders($this->getDefaultHeaders())->post($url, $data);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => 'Request completed successfully.',
                    'data' => $response->json(),
                ];
            }

            return [
                'status' => 'error',
                'message' => $response->body(),
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred during the request.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get the default headers for the HTTP request.
     *
     * @return array
     */
    private function getDefaultHeaders(): array
    {
        return [
            'accept' => '*/*',
            'accept-language' => 'en-US,en;q=0.8',
            'content-type' => 'application/json',
        ];
    }
}
