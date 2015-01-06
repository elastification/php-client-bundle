<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 19/12/14
 * Time: 16:19
 */

namespace Elastification\Bundle\ElastificationPhpClientBundle\DataCollector;

use Elastification\Client\Request\RequestInterface;
use Elastification\Client\Response\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ElasticsearchDataCollector extends DataCollector
{
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';

    /**
     * @var array
     */
    private $config = array();

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {

    }

    /**
     * adds a new request/response to the collector
     *
     * @param string $status
     * @param float $timeTaken
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @author Daniel Wendlandt
     */
    public function add($status, $timeTaken, RequestInterface $request, ResponseInterface $response = null)
    {

         $data = array(
            'status' => $status,
            'timeTaken' => $timeTaken,
            'requestClass' => get_class($request),
            'method' => $request->getMethod(),
            'index' => $request->getIndex(),
            'type' => $request->getType(),
            'action' => $request->getAction(),
            'responseClass' => $request->getSupportedClass(),
            'bodyRaw' => $request->getBody(),
            'config' => $this->config
        );

        if(null !== $data['bodyRaw'] && is_string($data['bodyRaw'])) {
            $data['body'] = json_decode($request->getBody(), true);
        }

        if(null !== $response) {
            $data['response'] = $response->getData()->getGatewayValue();
            $data['responseRaw'] = $response->getRawData();
        }

        $this->data[] = $data;
    }

    /**
     * gets taken time of all commands
     *
     * @return float
     */
    public function getTimeTaken()
    {
        $timeTaken = 0;

        foreach($this->data as $query)
        {
            $timeTaken += $query['timeTaken'];
        }

        return $timeTaken;
    }

    /**
     * returns the number of entries in data
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->data);
    }

    /**
     * getter for collected data
     *
     * @return array
     * @author Daniel Wendlandt
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'elasticsearch';
    }

    /**
     * setter for config
     *
     * @param array $config
     * @author Daniel Wendlandt
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}