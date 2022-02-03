<?php
namespace App\Service\Hautai\Objects;

use App\Service\Hautai\RestClientInterface;


class Image {

    const API_PATH_IMAGE_CREATE = 'companies/%s/datasets/%s/subjects/%s/batches/%s/images/';

    const API_PATH_IMAGE_GET = 'companies/%s/datasets/%s/subjects/%s/batches/%s/images/';

    const API_PATH_IMAGE_RESULT_GET = 'companies/%s/datasets/%s/subjects/%s/batches/%s/images/%s/results/';

    /**
     * @var RestClientInterface
     */
    private $restClient;


    /**
     * Image constructor.
     * @param RestClientInterface $restClient
     */
    public function __construct(RestClientInterface $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * Upload image
     *
     * We have concept of "batch"
     * We upload images to Dataset by batches.
     * Selfie batch can have one frontal image or it can have three images for left, right and frontal side of the face.
     * Skin batch and Visia batch can have only one image.
     *
     * Currently we use Skin batch
     *
     * @param string $name
     * @param string $base64
     * @param string $subjectName
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @param string $batchId
     * @param string $accessToken
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function create(
        string $name,
        string $base64,
        string $companyId,
        string $datasetId,
        string $subjectId,
        string $batchId,
        string $accessToken = null
    )
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        /**
         *  See side_id parameter below. To review such parameters please refer to Dict API (/dicts/image_types/).
         */
        $response = $this->restClient->post(
            sprintf(self::API_PATH_IMAGE_CREATE, $companyId, $datasetId, $subjectId, $batchId),
            [],
            [
                'b64data' => $base64,
                'light_id' => 1,
                'name' => $name,
                'side_id' => 1
            ]
        );

        return $this->restClient->getResult($response);
    }

    /**
     * Get batch images
     *
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @param string|null $accessToken
     * @return array|mixed
     */
    public function get(string $companyId, string $datasetId, string $subjectId, string $batchId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->get(sprintf(self::API_PATH_IMAGE_GET, $companyId, $datasetId, $subjectId, $batchId));

        return $this->restClient->getResult($response);
    }

    //API_PATH_IMAGE_RESULT_GET
    /**
     * Get single image result
     *
     * @param string $companyId
     * @param string $datasetId
     * @param string $subjectId
     * @param string $batchId
     * @param string $imageId
     * @param string|null $accessToken
     * @return array|mixed
     */
    public function getResult(string $companyId, string $datasetId, string $subjectId, string $batchId, string $imageId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->get(sprintf(
            self::API_PATH_IMAGE_RESULT_GET,
            $companyId,
            $datasetId,
            $subjectId,
            $batchId,
            $imageId
        ));

        return $this->restClient->getResult($response);
    }

}
