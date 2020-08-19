<?php

namespace App\Services;

use Aws\S3\S3Client;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class S3StorageService
{
    private $s3Client;
    private $bucket;

    public function __construct($s3Client = null)
    {
        if ($s3Client === null) {
            $this->s3Client = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'endpoint' => env('AWS_S3_ENDPOINT'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ]
            ]);
        } else {
            $this->s3Client = $s3Client;
        }
        $this->bucket = env('AWS_BUCKET');
    }

    public function putObject($fileStream, $path)
    {
        $response = $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => $fileStream,
            'ACL' => 'public-read'
        ]);
        if (
            $response['ObjectURL'] &&
            strpos($response['ObjectURL'], $path) !== false
        ) {
            $response['key'] = $path;
        } else {
            throw new \Exception("S3 not saving right");
        }
        return $response;

    }

    public function getObject($objectUrl)
    {
        if (strpos(strtolower($objectUrl), 'http') === 0) {
            return $this->getUrl($objectUrl);
        }
        $retrive = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $objectUrl
        ]);
        return $retrive['Body'];
    }

    public function getUrl($url)
    {
        /** @var Response $res */
        $client = app()->make(Client::class);
        $res = $client->request('GET', $url);
        return $res->getBody();
    }

    public function getObjectList(){
        $results = $this->s3Client->getPaginator('ListObjects', [
            'Bucket' => $this->bucket
        ]);
        $objects = [];
        foreach ($results as $result) {
            foreach ($result['Contents'] as $object) {
                dd($object);
                $objects[] = $object['Key'];
            }
        }
        return $objects;
    }
}
