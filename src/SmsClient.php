<?php declare(strict_types=1);

namespace Sms\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use OAuth2ClientCredentials\OAuthClient;

class SmsClient
{
    /**
     * @var OAuthClient
     */
    private $oauthClient;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->oauthClient = new OAuthClient(
            config('sms.oauth.url'),
            config('sms.oauth.client_id'),
            config('sms.oauth.client_secret')
        );
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param callable $handler
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function request($handler)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->oauthClient->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauthClient->getAccessToken(true);
        }

        return $response;
    }

    /**
     * @param string $route
     * @return string
     */
    private function getUrl($route)
    {
        return $this->apiUrl . '/api/client/v1' . $route;
    }

    /**
     * @param string $phoneNumber
     * @param string $content
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function send($phoneNumber, $content)
    {
        $params = [
            'phone_number' => $phoneNumber,
            'content' => $content,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/sms/send'), $params);
        })
            ->successful();
    }
}
