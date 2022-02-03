<?php
namespace App\Service\Hautai\Objects;

use App\Service\Hautai\RestClientInterface;


class Batch {

    const API_PATH_BATCH_CREATE = 'companies/%s/datasets/%s/subjects/%s/batches/';

    const API_PATH_BATCH_GET = 'companies/%s/datasets/%s/subjects/%s/batches/';

    const API_PATH_BATCH_DELETE = 'companies/%s/datasets/%s/subjects/%s/batches/%s/';

    /**
     * @var RestClientInterface
     */
    private $restClient;


    /**
     * Batch constructor.
     * @param RestClientInterface $restClient
     */
    public function __construct(RestClientInterface $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * Create a batch
     *
     * We have concept of "batch"
     * We upload images to Dataset by batches.
     * Selfie batch can have one frontal image or it can have three images for left, right and frontal side of the face.
     * Skin batch and Visia batch can have only one image.
     *
     * Currently we use Skin batch
     *
     * @param string $subjectName
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function create(string $companyId, string $datasetId, string $subjectId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->post(
            sprintf(self::API_PATH_BATCH_CREATE, $companyId, $datasetId, $subjectId),
            [],
            ['creation_time' => date('Y.m.d h:i:s')]
        );

        return $this->restClient->getResult($response);
    }

    /**
     * Get a list of all subject 'Batches'
     *
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @param string|null $accessToken
     * @return array|mixed
     */
    public function get(string $companyId, string $datasetId, string $subjectId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->get(sprintf(self::API_PATH_BATCH_GET, $companyId, $datasetId, $subjectId));

        return $this->restClient->getResult($response);
    }

    /**
     * Delete single batch. Deleting the batch, deletes belonging images also.
     *
     * @param string $batchId
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @return array|mixed
     */
    public function delete(string $batchId, string $companyId, string $datasetId, string $subjectId)
    {
        $response = $this->restClient->delete(
            sprintf(self::API_PATH_BATCH_DELETE, $companyId, $datasetId, $subjectId, $batchId)
        );

        return $this->restClient->getResult($response);
    }

}
