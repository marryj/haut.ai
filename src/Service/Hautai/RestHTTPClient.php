<?php
namespace App\Service\Hautai;

use App\Service\Hautai\Exceptions\ConfigurationException as HautAiConfigurationException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class RestHTTPClient implements RestClientInterface {

    const ENV_ACCOUNT_USER = "HAUT_AI_ACCOUNT_USER";

    const ENV_ACCOUNT_PASS = "HAUT_AI_ACCOUNT_PASS";

    const API_HOST = "https://saas.haut.ai/api/v1/";

    const API_PATH_LOGIN = 'login/';

    const API_PATH_REFRESH_TOKEN = 'refresh/';

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var HttpClientInterface
     */
    private $client;

    /**
     * @var CurlResponse
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
     * @param string $username Username to authenticate with
     * @param string $password Password to authenticate with
     * @throws HautAiConfigurationException
     */
    public function init($username = null, $password = null) {
        if($username) {
            $this->username = $username;
        } else {
            if (array_key_exists(self::ENV_ACCOUNT_USER, $_ENV)) {
                $this->username = $_ENV[self::ENV_ACCOUNT_USER];
            }
        }

        if($password) {
            $this->password = $password;
        } else {
            if (array_key_exists(self::ENV_ACCOUNT_PASS, $_ENV)) {
                $this->password = $_ENV[self::ENV_ACCOUNT_PASS];
            }
        }

        if(!$this->username || !$this->password) {
            throw new HautAiConfigurationException("Credentials are required to create a Client");
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


        return $this->response;
    }

    /**
     * @param CurlResponse $response
     * @return int
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getStatusCode(CurlResponse $response)
    {
        return $response->getStatusCode();
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
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getResponseArray(ResponseInterface $response)
    {
        if ($response->isRequestSuccessful()) {
            return $response->toArray();
        }

        return [];
    }

    /**
     * @param CurlResponse $response
     * @return bool
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function isRequestSuccessful(ResponseInterface $response)
    {
        if (Response::HTTP_OK == $response->getStatusCode()) {
            return true;
        }

        return false;
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

    /**
     * Get a request result.
     * Returns an array with a response body or and error code => reason.
     * @param Response $response
     * @throws ClientException
     * @return array|mixed
     */
    public function getResult(ResponseInterface $response)
    {
        // Workaround for export methods getRouteAsGPX, getRouteAsTCX:
        if (is_string($response)) {
            return $response;
        }

        $status = $response->getStatusCode();

        $expandedResponse = [];

        try {
            $expandedResponse['headers'] = $response->getHeaders();
            $expandedResponse['body'] = $response->toArray();
        } catch (\Exception $e) {
            $expandedResponse['exception'] = $e->getMessage();
        }

        $expandedResponse['success'] = $status === 200 || $status === 201;
        $expandedResponse['status'] = $status;

        return $expandedResponse;
    }

    /**
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function login()
    {
        $response = $this->post(
            self::API_PATH_LOGIN,
            [],
            ['username' => $this->username, 'password' => $this->password]
        );

        return $this->getResult($response);
    }

    /**
     * Exchange refresh token for access token. You receive the same access token with extended lifetime of 3600 sec.
     * A refresh token allows an application to obtain a new access token without prompting the user.
     * In our case no need to refresh the token as we don`t get credentials from a client.
     *
     * @param $refreshToken
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function refreshToken(string $refreshToken)
    {
        $response = $this->post(
            self::API_PATH_REFRESH_TOKEN,
            [],
            ['refresh_token' => $refreshToken]
        );

        return $this->getResult($response);
    }
}
