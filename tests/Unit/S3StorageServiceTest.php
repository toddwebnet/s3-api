<?php

namespace Tests\Unit\Services;

use App\Services\S3StorageService;
use Aws\S3\S3Client;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Tests\TestCase;

class S3StorageServiceTest extends TestCase
{
    private $awsBucket;
    private $s3Client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->s3Client = $this->setMock(S3Client::class);
    }

    public function testPutObject()
    {
        $key = "RANDOMNAME";

        $stream = $this->setMock(Stream::class);

        $response = ['ObjectURL' => "something://$key"];
        $this->s3Client
            ->shouldReceive('putObject')
            ->withArgs([
                [
                    'Bucket' => "aws_test_bucket",
                    'Key' => $key, //add path here
                    'Body' => $stream,
                    'ACL' => 'public-read'
                ]
            ])
            ->once()
            ->andReturn($response);
        $s3StorageService = new S3StorageService($this->s3Client);
        $expected = $response;
        $expected['key'] = $key;
        $this->assertEquals($expected, $s3StorageService->putObject($stream, $key));

    }

    public function testPutObjectException()
    {
        $key = "RANDOMNAME";

        $stream = $this->setMock(Stream::class);

        $response = ['ObjectURL' => "something://$key"];
        $this->s3Client
            ->shouldReceive('putObject')
//            ->withArgs([
//                [
//                    'Bucket' => "aws_test_bucket",
//                    'Key' => $key, //add path here
//                    'Body' => $stream,
//                    'ACL' => 'public-read'
//                ]
//            ])
                ->withAnyArgs()
            ->once()
            ->andReturn([]);
        $s3StorageService = new S3StorageService($this->s3Client, $key);

        try {
            $s3StorageService->putObject($stream, 'nothing');
            $this->assertEquals("I expected to fail", "");
        } catch (\Exception $e) {
            $this->assertEquals("I expected to fail", "I expected to fail");
        }

    }

    public function testGetObject()
    {
        $url = "http://gumby.com";
        $object = "STUFFYSTUFFHERE";

        $response = $this->setMock(Response::class)
            ->shouldReceive('getBody')
            ->withArgs([])
            ->once()
            ->andReturn('test')
            ->getMock();

        $client = $this->setMock(Client::class)
            ->shouldReceive('request')
            ->withArgs(['GET', $url])
            ->once()
            ->andReturn($response)
            ->getMock();

        $this->s3Client->shouldReceive('getObject')
            ->withArgs([
                [
                    'Bucket' => 'aws_test_bucket',
                    'Key' => $object
                ]
            ])
            ->once()
            ->andReturn(['Body' => "Elvis Lives!"]);

        $s3StorageService = new S3StorageService($this->s3Client);

        $this->assertEquals('test', $s3StorageService->getObject($url));

        $this->assertEquals('Elvis Lives!', $s3StorageService->getObject($object));

    }
    public function testGetUrl()
    {
        $url = "http://dog.com";

        $response = $this->setMock(Response::class)
            ->shouldReceive('getBody')
            ->withArgs([])
            ->once()
            ->andReturn('test')
            ->getMock();

        $client = $this->setMock(Client::class)
            ->shouldReceive('request')
            ->withArgs(['GET', $url])
            ->once()
            ->andReturn($response)
            ->getMock();

        $httpService = new S3StorageService();
        $this->assertEquals('test', $httpService->getUrl($url));

    }
}
