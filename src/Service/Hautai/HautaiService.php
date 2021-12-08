<?php
namespace App\Service\Hautai;

use App\Entity\HautAiMedia;
use App\Entity\UserHautAi;
use App\Service\Hautai\AuthenticationService as HautaiAuthenticationService;
use App\Service\Hautai\Objects\Batch;
use App\Service\Hautai\Objects\Image;
use App\Service\Hautai\Objects\Subject;
use App\Service\Hautai\RestHTTPClient as HautaiRestClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserHautAiRepository;
use App\Entity\User;
use App\Utils\Helpers;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HautaiService
{

    /**
     * @var HautaiRestClient
     */
    private $hautAiRestClient;

    /**
     * @var UserHautAiRepository
     */
    private $userHautAiRepository;

    /**
     * @var Subject
     */
    private $hautAiSubject;

    /**
     * @var Batch
     */
    private $hautAiBatch;

    /**
     * @var Image
     */
    private $hautAiImage;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserHautAiRepository $userHautAiRepository, EntityManagerInterface $em)
    {
        $this->userHautAiRepository = $userHautAiRepository;
        $this->em = $em;
    }

    public function init()
    {
        $authService  = new HautaiAuthenticationService(new RestHTTPClient(), new FilesystemAdapter());

        try {
            $authService->authenticate();
        } catch (\Exception $e) {
            throw new BadRequestHttpException(json_encode($e->getMessage()));
        }

        $accessToken = $authService->getAccessToken();

        if ($accessToken) {
            $this->hautAiRestClient = new HautaiRestClient();
            $this->hautAiRestClient->setAccessTokenHeader($accessToken);
        }

        $this->hautAiSubject = new Subject( $this->hautAiRestClient );
        $this->hautAiBatch = new Batch( $this->hautAiRestClient );
        $this->hautAiImage = new Image( $this->hautAiRestClient );
    }


    public function sendImage(HautAiMedia $hautAiMedia, User $user, $imageName)
    {
        $subjectId = $this->getUserSubject($user);

        // If no subject, create one
        if (null == $subjectId) {
            $subjectId = $this->createSubject($user);
        }

        $batchId = $this->createBatch($subjectId);

        $base64 = Helpers::getImageBase64ByPath($hautAiMedia->file->getPathname());
        if (false === $base64) {
            return false;
        }

        $imageId = $this->uploadImage($imageName, $subjectId, $batchId, $base64);

        $this->saveUploadedImageData($hautAiMedia, $batchId, $imageId);
    }


    /**
     * Create subject for a user
     *
     * @param int $userId
     * @return boolean|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function createSubject(User $user)
    {
        if (!$this->hautAiRestClient) {
            throw new Exception('Haut AI Rest client not initialized!');
        }

        $res = $this->hautAiSubject->create("User [{$user->getId()}]", $_SERVER['HAUT_AI_COMPANY_ID'], $_SERVER['HAUT_AI_DATASET_ID']);

        if (true != $res['success']) {
            return false;
        }

        //Save subject data
        $this->userHautAiRepository->create($user, $res['body']['id'], $res['body']['name']);

        return $res['body']['id'];
    }

    /**
     * Get user
     *
     * @param User $user
     * @return UserHautAi|null
     */
    private function getUserSubject(User $user): ?string
    {
        $userHautAi = $this->userHautAiRepository->findOneBy(['user' => $user]);

        if (!$userHautAi) {
            return null;
        }
        return $userHautAi->subjectId;
    }

    /**
     * Create batch for a given subject
     *
     * @param string $subjectId
     * @return bool|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function createBatch(string $subjectId)
    {
        $res = $this->hautAiBatch->create($_SERVER['HAUT_AI_COMPANY_ID'], $_SERVER['HAUT_AI_DATASET_ID'], $subjectId);

        if (true != $res['success']) {
            return false;
        }

        return $res['body']['id'];
    }

    /**
     * @param $imageName
     * @param $subjectId
     * @param $batchId
     * @param $base64
     * @return bool|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function uploadImage($imageName, $subjectId, $batchId, $base64)
    {
        $res = $this->hautAiImage->create(
            $imageName,
            $base64,
            $_SERVER['HAUT_AI_COMPANY_ID'],
            $_SERVER['HAUT_AI_DATASET_ID'],
            $subjectId,
            $batchId
        );

        if (true != $res['success']) {
            return false;
        }

        return $res['body']['id'];
    }

    private function saveUploadedImageData(HautAiMedia $hautAiMedia, $batchId, $imageId)
    {
        $hautAiMedia->batchId = $batchId;
        $hautAiMedia->imageId = $imageId;
        $hautAiMedia->status = HautAiMedia::STATUS_SEND;

        $this->em->persist($hautAiMedia);
        $this->em->flush();
    }

    /**
     * @TODO
     *
     * @param int $user
     * @param int $imageId
     */
    public function getImageResult(int $user, int $imageId)
    {

    }
}
