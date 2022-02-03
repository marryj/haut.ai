<?php
namespace App\Service\Hautai;

use App\Service\Hautai\Exceptions\ConfigurationException as HautAiConfigurationException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use App\Service\Hautai\Http\RestClientInterface;

class AuthenticationService {

    const ENV_ACCOUNT_USER = "HAUT_AI_ACCOUNT_USER";

    const ENV_ACCOUNT_PASS = "HAUT_AI_ACCOUNT_PASS";

    const API_PATH_LOGIN = 'login/';

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

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;


    public function __construct(RestClientInterface $restClient, FilesystemAdapter $cache)
    {
        $this->restClient = $restClient;
        $this->cache = $cache;
        $this->init();
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
     * @return mixed|string
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function authenticate(): string
    {
//        $this->cache->delete('hautai.apiclient.access_token');

        $this->accessToken = $this->cache->get('hautai.apiclient.access_token', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $loginArr = $this->login();

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

    /**
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function login(): array
    {
        /** @var \App\Service\Hautai\Http\Response $response */
        $response = $this->restClient->post(
            self::API_PATH_LOGIN,
            [],
            ['username' => $this->username, 'password' => $this->password]
        );

        return $response->getResult();
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
    public function refreshToken(string $refreshToken): array
    {
        /** @var \App\Service\Hautai\Http\Response $response */
        $response = $this->post(
            self::API_PATH_REFRESH_TOKEN,
            [],
            ['refresh_token' => $refreshToken]
        );

        return $response->getResult();
    }


}
