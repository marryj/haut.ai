<?php
namespace App\Service\Hautai;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class AuthenticationService {

    /**
     * @var RestClientInterface
     */
    private $restClient;

    /**
     * FilesystemAdapter
     */
    private $cache;

    /**
     * @var string
     */
    private $accessToken;

    public function __construct(RestClientInterface $restClient, FilesystemAdapter $cache)
    {
        $this->restClient = $restClient;
        $this->restClient->init();

        $this->cache = $cache;
    }

    /**
     * @return mixed|string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function authenticate(): string
    {
//        $this->cache->delete('hautai.apiclient.access_token');

        $this->accessToken = $this->cache->get('hautai.apiclient.access_token', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $loginArr = $this->restClient->login();

            if (false === $loginArr['success']) {
                throw new \Exception('[HAUT.AI] Login fail', 400);
            }

            return $loginArr['body']['access_token'];
        });



        return $this->accessToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }


}
