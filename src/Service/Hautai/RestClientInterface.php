<?php
namespace App\Service\Hautai;


use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface RestClientInterface {

    /**
     * @param string $username Username to authenticate with
     * @param string $password Password to authenticate with
     */
    public function init($username = null, $password = null);

    /**
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
    public function request($method, $uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function get($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function post($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function delete($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function put($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     * @return HttpClientInterface
     */
    public function patch($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @return array
     */
    public function getResponseArray(ResponseInterface $response);

    /**
     * @param $response
     * @return mixed
     */
    public function isRequestSuccessful(ResponseInterface $response);

    public function login();

    public function refreshToken(string $refreshToken);
}
