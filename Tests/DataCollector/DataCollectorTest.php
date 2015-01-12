<?php

namespace Elastification\Bundle\ElastificationPhpClientBundle\Tests\DataCollector;

use Elastification\Bundle\ElastificationPhpClientBundle\DataCollector\ElasticsearchDataCollector;

class DataCollectorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $response;


    /**
     * @var ElasticsearchDataCollector
     */
    private $dataCollector;

    /**
     * @var array
     */
    private $config = array('host' => 'localhost');

    protected function setUp()
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Elastification\Client\Request\RequestInterface')
            ->getMock();
        $this->response = $this->getMockBuilder('Elastification\Client\Response\ResponseInterface')
            ->getMock();

        $this->dataCollector = new ElasticsearchDataCollector();
        $this->dataCollector->setConfig($this->config);

    }
    protected function tearDown()
    {
        $this->request = null;
        $this->response = null;
        $this->dataCollector = null;
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(
            'Elastification\Bundle\ElastificationPhpClientBundle\DataCollector\ElasticsearchDataCollector',
            $this->dataCollector);
        $this->assertInstanceOf(
            'Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface',
            $this->dataCollector);
        $this->assertInstanceOf(
            'Symfony\Component\HttpKernel\DataCollector\DataCollector',
            $this->dataCollector);
    }

    public function testGetName()
    {
        $this->assertEquals('elasticsearch', $this->dataCollector->getName());
    }

    public function testAddWithNullResponse()
    {
        $timeTaken = 20;

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $this->request->expects($this->once())
            ->method('getIndex')
            ->willReturn('index');

        $this->request->expects($this->once())
            ->method('getType')
            ->willReturn('type');

        $this->request->expects($this->once())
            ->method('getAction')
            ->willReturn('action');

        $this->request->expects($this->once())
            ->method('getSupportedClass')
            ->willReturn('supportedClass');

        $this->request->expects($this->exactly(2))
            ->method('getBody')
            ->willReturn('{"id": 1}');


        $this->dataCollector->add(ElasticsearchDataCollector::TYPE_SUCCESS, $timeTaken, $this->request);

        $this->assertEquals(1, $this->dataCollector->getCount());
        $data = $this->dataCollector->getData();
        $this->assertEquals(1, count($data));

        $this->assertEquals(ElasticsearchDataCollector::TYPE_SUCCESS, $data[0]['status']);
        $this->assertEquals('GET', $data[0]['method']);
        $this->assertEquals($timeTaken, $data[0]['timeTaken']);
        $this->assertEquals('index', $data[0]['index']);
        $this->assertEquals('type', $data[0]['type']);
        $this->assertEquals('action', $data[0]['action']);
        $this->assertEquals('{"id": 1}', $data[0]['bodyRaw']);
        $this->assertEquals(array('id' => 1), $data[0]['body']);
        $this->assertEquals(get_class($this->request), $data[0]['requestClass']);
        $this->assertEquals($this->config, $data[0]['config']);
    }

    public function testAddWithResponse()
    {
        $timeTaken = 20;

        $this->request->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        $this->request->expects($this->once())
            ->method('getIndex')
            ->willReturn('index');

        $this->request->expects($this->once())
            ->method('getType')
            ->willReturn('type');

        $this->request->expects($this->once())
            ->method('getAction')
            ->willReturn('action');

        $this->request->expects($this->once())
            ->method('getSupportedClass')
            ->willReturn('supportedClass');

        $this->request->expects($this->exactly(2))
            ->method('getBody')
            ->willReturn('{"id": 1}');

        $gateway = $this->getMockBuilder('Elastification\Client\Serializer\Gateway\GatewayInterface')->getMock();
        $gateway->expects($this->once())->method('getGatewayValue')->willReturn(array('data' => 1));

        $this->response->expects($this->once())->method('getData')->willReturn($gateway);
        $this->response->expects($this->once())->method('getRawData')->willReturn('rawData');

        $this->dataCollector->add(
            ElasticsearchDataCollector::TYPE_SUCCESS,
            $timeTaken,
            $this->request,
            $this->response);

        $this->assertEquals(1, $this->dataCollector->getCount());
        $data = $this->dataCollector->getData();
        $this->assertEquals(1, count($data));

        $this->assertEquals(ElasticsearchDataCollector::TYPE_SUCCESS, $data[0]['status']);
        $this->assertEquals('GET', $data[0]['method']);
        $this->assertEquals($timeTaken, $data[0]['timeTaken']);
        $this->assertEquals('index', $data[0]['index']);
        $this->assertEquals('type', $data[0]['type']);
        $this->assertEquals('action', $data[0]['action']);
        $this->assertEquals('{"id": 1}', $data[0]['bodyRaw']);
        $this->assertEquals(array('id' => 1), $data[0]['body']);
        $this->assertEquals(get_class($this->request), $data[0]['requestClass']);
        $this->assertEquals($this->config, $data[0]['config']);
        $this->assertEquals(array('data' => 1), $data[0]['response']);
        $this->assertEquals('rawData', $data[0]['responseRaw']);
    }

    public function testGetTimeTaken()
    {
        $timeTaken = 20;

        $this->request->expects($this->exactly(2))
            ->method('getMethod')
            ->willReturn('GET');

        $this->request->expects($this->exactly(2))
            ->method('getIndex')
            ->willReturn('index');

        $this->request->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('type');

        $this->request->expects($this->exactly(2))
            ->method('getAction')
            ->willReturn('action');

        $this->request->expects($this->exactly(2))
            ->method('getSupportedClass')
            ->willReturn('supportedClass');

        $this->request->expects($this->exactly(4))
            ->method('getBody')
            ->willReturn('{"id": 1}');

        $this->dataCollector->add(ElasticsearchDataCollector::TYPE_SUCCESS, $timeTaken, $this->request);
        $this->dataCollector->add(ElasticsearchDataCollector::TYPE_SUCCESS, $timeTaken, $this->request);

        $this->assertEquals(40, $this->dataCollector->getTimeTaken());
    }

    public function testGetCount()
    {
        $timeTaken = 20;

        $this->request->expects($this->exactly(2))
            ->method('getMethod')
            ->willReturn('GET');

        $this->request->expects($this->exactly(2))
            ->method('getIndex')
            ->willReturn('index');

        $this->request->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('type');

        $this->request->expects($this->exactly(2))
            ->method('getAction')
            ->willReturn('action');

        $this->request->expects($this->exactly(2))
            ->method('getSupportedClass')
            ->willReturn('supportedClass');

        $this->request->expects($this->exactly(4))
            ->method('getBody')
            ->willReturn('{"id": 1}');

        $this->dataCollector->add(ElasticsearchDataCollector::TYPE_SUCCESS, $timeTaken, $this->request);
        $this->dataCollector->add(ElasticsearchDataCollector::TYPE_SUCCESS, $timeTaken, $this->request);

        $this->assertEquals(2, $this->dataCollector->getCount());
    }

    public function testCollect()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();

        $this->dataCollector->collect($request, $response);
    }
}