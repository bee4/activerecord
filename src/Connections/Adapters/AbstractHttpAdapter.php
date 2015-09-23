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

use Bee4\Events\DispatcherAwareInterface;
use Bee4\Events\DispatcherAwareTrait;
use Bee4\Events\DispatcherInterface;

/**
 * Default implementation for Http adapters
 * @package BeeBot\Entity\Connections\Adapters
 */
abstract class AbstractHttpAdapter implements
    HttpAdapterInterface,
    DispatcherAwareInterface
{
    use DispatcherAwareTrait {
        setDispatcher as private baseSetDispatcher;
    }

    /**
     * Url root
     * @var string
     */
    protected $root;

    /**
     * Set URL root used for requests
     * @param string $url
     */
    public function setRoot($url)
    {
        $this->root = $url;
    }

    /**
     * Retrieve url root
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Extension to setDispatcher
     * Allow to map Adapted events to connection events
     * @param DispatcherInterface $dispatcher
     */
    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        $result = $this->baseSetDispatcher($dispatcher);
        $this->mapEvents($dispatcher);

        return $result;
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
    }
}
