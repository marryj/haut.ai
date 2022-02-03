<?php
namespace App\Service\Hautai\Http;

interface RestClientInterface {

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
}
