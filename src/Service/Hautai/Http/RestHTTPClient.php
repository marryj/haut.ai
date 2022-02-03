<?php
namespace App\Service\Hautai\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RestHTTPClient implements RestClientInterface {

    const API_HOST = "https://saas.haut.ai/api/v1/";

    const API_PATH_REFRESH_TOKEN = 'refresh/';

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * HTTP request headers
     *
     * @var []
     */
    private $headers = [
        'Content-type'  => 'application/json',
        'Accept'        => 'application/json'
    ];

    /**
     * RestClient constructor.
     */
    public function __construct(HttpClientInterface $client = null) {

        if ($client) {
            $this->client = $client;
        } else {
            $this->client = HttpClient::create();
        }
    }


    /**
     * Makes a request to the Haut.Ai API using the configured http client
     *
     * @param string $method HTTP Method
     * @param string $uri Fully qualified url
     * @param string[] $params Query string parameters
     * @param string[] $data POST body data
     * @param string[] $headers HTTP Headers
     * @param string $username User for Authentication
     * @param string $password Password for Authentication
     * @param int $timeout Timeout in seconds
     * @return HttpClientInterface Response from the Haut.ai API
     */
    public function request($method, $uri, $query = array(), $data = array(), $headers = array()) {
        try {
            $this->response = $this->client->request(
                $method,
                self::API_HOST . $uri,
                [
                    'headers' => $this->getHeaders(),
                    // these values are automatically encoded before including them in the URL
                    'query' => $query,

                    /**
                     * When uploading data with the POST method, if you don’t define the Content-Type HTTP header explicitly,
                     * Symfony assumes that you’re uploading form data and
                     * adds the required 'Content-Type: application/x-www-form-urlencoded' header for you.
                     */
                    'json' => $data
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception('[HAUT.AI] ' . $e->getMessage());
        }


        return new \App\Service\Hautai\Http\Response($this->response);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function get($uri, $query = array(), $data = array(), $headers = array())
    {
        return $this->request('GET', $uri, $query, $data, $headers);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function post($uri, $query = array(), $data = array(), $headers = array())
    {
        return $this->request('POST', $uri, $query, $data, $headers);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function delete($uri, $query = array(), $data = array(), $headers = array())
    {
        return $this->request('DELETE', $uri, $query, $data, $headers);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function put($uri, $query = array(), $data = array(), $headers = array())
    {
        return $this->request('PUT', $uri, $query, $data, $headers);
    }

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function patch($uri, $query = array(), $data = array(), $headers = array())
    {
        return $this->request('PATCH', $uri, $query, $data, $headers);
    }


    /**
     * @param string $accessToken
     */
    public function setAccessTokenHeader($accessToken)
    {
        $this->setHeaders('Authorization', "Bearer {$accessToken}");
    }

    /**
     * @param string $key
     * @param $value
     */
    public function setHeaders($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }
}
