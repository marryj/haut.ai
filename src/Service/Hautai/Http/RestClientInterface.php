<?php
namespace App\Service\Hautai\Http;

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
     * @param string[] $query Query string parameters
     * @param string[] $data POST body data
     * @param string[] $headers HTTP Headers
     * @param int $timeout Timeout in seconds
     */
    public function request($method, $uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     */
    public function get($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     */
    public function post($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     */
    public function delete($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     */
    public function put($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @param $uri
     * @param array $query
     * @param array $data
     * @param array $headers
     */
    public function patch($uri, $query = array(), $data = array(), $headers = array());

    /**
     * @return array
     */
    public function login() :array;

    /**
     * @param string $refreshToken
     * @return array
     */
    public function refreshToken(string $refreshToken): array;

}
