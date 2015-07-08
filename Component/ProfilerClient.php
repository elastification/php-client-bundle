<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */
namespace Elastification\Bundle\ElastificationPhpClientBundle\Component;

use Elastification\Bundle\ElastificationPhpClientBundle\DataCollector\ElasticsearchDataCollector;
use Elastification\Client\ClientInterface;
use Elastification\Client\Exception\ClientException;
use Elastification\Client\Exception\RequestException;
use Elastification\Client\Request\RequestInterface;
use Elastification\Client\Response\ResponseInterface;


class ProfilerClient implements ClientInterface
{
    /**
     * @var ElasticsearchDataCollector
     */
    private $dataCollector;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ElasticsearchDataCollector $dataCollector
     * @param ClientInterface $client
     */
    public function __construct(
        ClientInterface $client,
        ElasticsearchDataCollector $dataCollector
    ) {
        $this->client = $client;
        $this->dataCollector = $dataCollector;
    }

    /**
     * performs sending the request
     *
     * @param RequestInterface $request
     *
     * @throws ClientException
     * @throws RequestException
     * @return ResponseInterface
     * @author Daniel Wendlandt
     */
    public function send(RequestInterface $request)
    {
        $timeTaken = microtime(true);

        try {

            $response = $this->client->send($request);

            if(null !== $this->dataCollector) {
                $this->dataCollector->add(
                    ElasticsearchDataCollector::TYPE_SUCCESS,
                    (microtime(true) - $timeTaken) * 1000,
                    $request,
                    $response);
            }

            return $response;
        } catch(ClientException $exception) {
            if(null !== $this->dataCollector) {
                $this->dataCollector->add(
                    ElasticsearchDataCollector::TYPE_ERROR,
                    (microtime(true) - $timeTaken) * 1000,
                    $request);
            }

            throw $exception;
        }
    }

    /**
     * Setter for dataCollector. It also can be null.
     *
     * @param ElasticsearchDataCollector $dataCollector
     */
    public function setDataCollector(ElasticsearchDataCollector $dataCollector = null)
    {
        $this->dataCollector = $dataCollector;
    }

    /**
     * @inheritdoc
     */
    public function getRequest($name)
    {
        return $this->client->getRequest($name);
    }

}
