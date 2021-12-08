<?php
namespace App\Service\Hautai\Objects;

use App\Service\Hautai\RestHTTPClient;


class Subject {

    const API_PATH_SUBJECT_CREATE = 'companies/%s/datasets/%s/subjects/';

    const API_PATH_SUBJECT_GET = 'companies/%s/datasets/%s/subjects/';

    const API_PATH_SUBJECT_DELETE = 'companies/%s/datasets/%s/subjects/delete/';

    /**
     * @var RestHTTPClient
     */
    private $restClient;


    /**
     * RestClient constructor.
     */
    public function __construct(RestHTTPClient $restClient)
    {
        $this->restClient = $restClient;
    }

    /**
     * Create a subject
     * cWe have concept of "subjects" - these are your end customers, and every image
     * should be associated with a Subject.
     * If you don't need to associate every customer with unique subject,
     * just create a default one.
     *
     * @param string $subjectName
     * @param string $companyId
     * @param string $datasetId
     * @return array|bool
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function create(string $subjectName, string $companyId, string $datasetId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->post(
            sprintf(self::API_PATH_SUBJECT_CREATE, $companyId, $datasetId),
            [],
            ['name' => $subjectName]
        );

        return $this->restClient->getResult($response);
    }

    /**
     * Get a list of all dataset 'Subjects'
     *
     * @param string $companyId
     * @param string $datasetId
     * @param string|null $accessToken
     * @return array|mixed
     */
    public function get(string $companyId, string $datasetId, string $accessToken = null)
    {
        if (null != $accessToken) {
            $this->restClient->setAccessTokenHeader($accessToken);
        }

        $response = $this->restClient->get(sprintf(self::API_PATH_SUBJECT_GET, $companyId, $datasetId));

        return $this->restClient->getResult($response);
    }

    /**
     * Delete single subject
     *
     * @param $subjectId
     * @param string $companyId
     * @param string $datasetId
     * @return array|mixed
     */
    public function delete(string $subjectId, string $companyId, string $datasetId)
    {
        $response = $this->restClient->delete(
            sprintf(self::API_PATH_SUBJECT_DELETE, $companyId, $datasetId),
            [],
            ['subjects_ids' => [$subjectId]]
        );
        return $this->restClient->getResult($response);
    }

}
