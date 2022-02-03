<?php
namespace App\Service\Hautai\Http;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Response {

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response) {
        $this->response = $response;
    }

    /**
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getResponseArray(): array
    {
        if ($this->response->isRequestSuccessful()) {
            return $this->response->toArray();
        }

        return [];
    }

    /**
     * @return int
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @param CurlResponse $response
     * @return bool
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function isRequestSuccessful(): bool
    {
        if (\Symfony\Component\HttpFoundation\Response::HTTP_OK == $this->response->getStatusCode()) {
            return true;
        }

        return false;
    }

    /**
     * Get a request result.
     * Returns an array with a response body or and error code => reason.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return array|mixed
     *@throws ClientException
     */
    public function getResult(): array
    {
        $status = $this->response->getStatusCode();

        $expandedResponse = [];

        try {
            $expandedResponse['headers'] = $this->response->getHeaders();
            $expandedResponse['body'] = $this->response->toArray();
        } catch (\Exception $e) {
            $expandedResponse['exception'] = $e->getMessage();
        }

        $expandedResponse['success'] = $status === 200 || $status === 201;
        $expandedResponse['status'] = $status;

        return $expandedResponse;
    }
}