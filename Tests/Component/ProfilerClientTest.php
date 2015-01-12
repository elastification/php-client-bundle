<?php

namespace Elastification\Bundle\ElastificationPhpClientBundle\Tests\Component;

use Elastification\Bundle\ElastificationPhpClientBundle\Component\ProfilerClient;
use Elastification\Bundle\ElastificationPhpClientBundle\DataCollector\ElasticsearchDataCollector;
use Elastification\Client\Exception\ClientException;

class ProfilerClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataCollector;

    /**
     * @var ProfilerClient
     */
    private $profilerClient;

    protected function setUp()
    {
        parent::setUp();
        $this->client = $this->getMockBuilder('Elastification\Client\ClientInterface')
            ->getMock();
        $this->dataCollector = $this->getMockBuilder('Elastification\Bundle\ElastificationPhpClientBundle\DataCollector\ElasticsearchDataCollector')
            ->getMock();

        $this->profilerClient = new ProfilerClient($this->client, $this->dataCollector);

    }
    protected function tearDown()
    {
        $this->client = null;
        $this->dataCollector = null;
        $this->profilerClient = null;
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Elastification\Client\ClientInterface', $this->profilerClient);
        $this->assertInstanceOf('Elastification\Bundle\ElastificationPhpClientBundle\Component\ProfilerClient', $this->profilerClient);
    }

    public function testGetRequest()
    {
        $this->client
            ->expects($this->once())
            ->method('getRequest')
            ->with($this->equalTo('test'))
            ->willReturn('request');

        $this->assertSame('request', $this->profilerClient->getRequest('test'));
    }

    public function testSend()
    {
        $request = $this->getMockBuilder('Elastification\Client\Request\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Elastification\Client\Response\ResponseInterface')->getMock();

        $this->dataCollector
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(ElasticsearchDataCollector::TYPE_SUCCESS),
                $this->greaterThan(0),
                $this->equalTo($request),
                $this->equalTo($response)
            );

        $this->client
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo($request))
            ->willReturn($response);

        $result = $this->profilerClient->send($request);
        $this->assertEquals($response, $result);
    }

    public function testSendException()
    {
        $request = $this->getMockBuilder('Elastification\Client\Request\RequestInterface')->getMock();
        $response = $this->getMockBuilder('Elastification\Client\Response\ResponseInterface')->getMock();

        $this->dataCollector
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(ElasticsearchDataCollector::TYPE_ERROR),
                $this->greaterThan(0),
                $this->equalTo($request)
            );

        $exception = new ClientException();

        $this->client
            ->expects($this->once())
            ->method('send')
            ->with($this->equalTo($request))
            ->willThrowException($exception);

        try {
            $this->profilerClient->send($request);

        } catch(ClientException $clientException) {

            $this->assertSame($exception, $clientException);
            return;

        }

        $this->fail('No ClientException thrown');
    }
}