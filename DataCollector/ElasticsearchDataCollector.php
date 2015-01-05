<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 19/12/14
 * Time: 16:19
 */

namespace Elastification\Bundle\ElastificationPhpClientBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ElasticsearchDataCollector extends DataCollector
{

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
     * gets taken time of all commands
     *
     * @return float
     */
    public function getTimeTaken()
    {
        $timeTaken = 0;
//        foreach($this->data['commands'] as $command)
//        {
//            $timeTaken += $command['time_taken'];
//        }
        return $timeTaken;
    }

    /**
     * returns the number of entries in data
     *
     * @return int
     */
    public function getCount()
    {
        return 0;
    }

    public function getName()
    {
        return 'elasticsearch';
    }
}