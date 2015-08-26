<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections\Adapters
 */

namespace BeeBot\Entity\Connections\Adapters;

use Bee4\Transport\MagicHandler;
use Bee4\Transport\Client;
use Bee4\Transport\Events\ErrorEvent;
use Bee4\Transport\Events\MessageEvent;
use Bee4\Transport\Message\Request\AbstractRequest;

/**
 * Default implementation for Http adapters
 * @package BeeBot\Entity\Connections\Adapters
 */
class Bee4TransportAdapter extends AbstractHttpAdapter
{
    /**
     * Adapted instance
     * @var Client
     */
    protected $adaptee;

    /**
     * Initialize the adapter
     * @param Client $adaptee
     */
    public function __construct(Client $adaptee)
    {
        if( !($adaptee instanceof MagicHandler) ) {
            $adaptee = new MagicHandler($adaptee);
        }

        $this->adaptee = $adaptee;
    }

    public function get($url, array $headers = null)
    {
        return $this->handleRequest(
            $this->adaptee->get($this->getRoot().$url),
            null,
            $headers
        );
    }

    public function post($url, $body, array $headers = null)
    {
        return $this->handleRequest(
            $this->adaptee->post($this->getRoot().$url),
            $body,
            $headers
        );
    }

    public function head($url, array $headers = null)
    {
        return $this->handleRequest(
            $this->adaptee->head($this->getRoot().$url),
            null,
            $headers
        );
    }

    public function put($url, $body, array $headers = null)
    {
        return $this->handleRequest(
            $this->adaptee->put($this->getRoot().$url),
            $body,
            $headers
        );
    }

    public function delete($url, $body, array $headers = null)
    {
        return $this->handleRequest(
            $this->adaptee->delete($this->getRoot().$url),
            $body,
            $headers
        );
    }

    /**
     * Handle a Request object, populate it then execute it on the client
     * @param  AbstractRequest $request Loaded request
     * @param  string          $body    Request body
     * @param  array|null      $headers Complement headers
     * @return string                   Response body
     */
    private function handleRequest(
        AbstractRequest $request,
        $body = null,
        array $headers = null)
    {
        if( isset($body) ) {
            $request->setBody($body);
        }
        if( isset($headers) ) {
            $request->addHeaders($headers);
        }

        return $request->send()->getBody();
    }

    /**
     * Map adapter events to Connection events
     *  - Connection::REQUEST
     *  - Connection::RESPONSE
     *  - Connection::ERROR
     * @param  DispatcherInterface $dispatcher
     */
    protected function mapEvents(DispatcherInterface $dispatcher)
    {
        $dispatcher->add(
            MessageEvent::REQUEST,
            function (MessageEvent $event) {
                $this->dispatch(
                    ConnectionEvent::REQUEST,
                    new ConnectionEvent($event->getMessage())
                );
            }
        );
        $dispatcher->add(
            MessageEvent::RESPONSE,
            function (MessageEvent $event) {
                $this->dispatch(
                    ConnectionEvent::RESULT,
                    new ConnectionEvent($event->getMessage())
                );
            }
        );
        $dispatcher->add(
            ErrorEvent::ERROR,
            function (ErrorEvent $event) {
                $this->dispatch(
                    ConnectionEvent::ERROR,
                    new ConnectionEvent($event->getError())
                );
            }
        );
    }
}
