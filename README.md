#HAUT.AI

## Documentation
The Haut.Ai API Swagger can be found [here][apiswagger].

The Haut.Ai documentation can be found [here][apidocumentation].

## Installation
You can install **haut.ai** via composer or by downloading the source.

### Via Composer:
```
composer require marryj/haut.ai
```
## Quickstart
### Send image
We upload images to Dataset by batches.
Selfie batch can have one frontal image or it can have three images for left, right and frontal side of the face.
 
Please note, we have concept of "subjects" - these are your end customers, 
and every image should be associated with a Subject. 
If you don't need to associate every customer with unique subject, just create one default 
subject ("My Subject Name" in code below to edit).

#### Init service
```php
$authService  = new App\Service\Hautai\AuthenticationService(new RestHTTPClient(), new FilesystemAdapter());
$authService->authenticate();
$accessToken = $authService->getAccessToken();

$hautAiRestClient = new App\Service\Hautai\RestHTTPClient();
$hautAiRestClient->setAccessTokenHeader($accessToken);

$hautAiSubject = new App\Service\Hautai\Objects\Subject( $hautAiRestClient );
$hautAiBatch = new App\Service\Hautai\Objects\Batch( $hautAiRestClient );
$hautAiImage = new App\Service\Hautai\Objects\Image( $hautAiRestClient );
```

#### 1. Create subject
```php
$res = $hautAiSubject->create("User subject", $_SERVER['HAUT_AI_COMPANY_ID'], $_SERVER['HAUT_AI_DATASET_ID']);

if (true === $res['success']) {
    $subjectId = $res['body']['id'];
}
```

#### 2. Create batch
```php
$resBatch = $hautAiBatch->create($_SERVER['HAUT_AI_COMPANY_ID'], $_SERVER['HAUT_AI_DATASET_ID'], $subjectId);
$batchId = $resBatch['body']['id'];
```

#### 3. Send image
```php
$res = $this->hautAiImage->create(
    'image-name.jpg',
    'base64 string',
    $_SERVER['HAUT_AI_COMPANY_ID'],
    $_SERVER['HAUT_AI_DATASET_ID'],
    $subjectId,
    $batchId
);
```

[apiswagger]: https://saas.haut.ai/api/swagger/
[apidocumentation]: https://docs.saas.haut.ai/