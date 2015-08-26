<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections
 */

namespace BeeBot\Entity\Connections;

use Bee4\Events\DispatcherInterface;

/**
 * A more specific canvas for connections which relied on HTTP requests
 * @package BeeBot\Entity\Connections
 */
abstract class AdaptableHttpConnection
    extends AbstractConnection
    implements HttpConnectionInterface
{
    /**
     * HTTP client adapter
     * @var HttpAdapterInterface
     */
    private $adapter;

    /**
     * Set root URL on the adapter
     * @param string $url
     */
    public function setRoot($url)
    {
        $this->adapter->setRoot($url);

        return $this;
    }

    public function setDispatcher(DispatcherInterface $dispatcher)
    {
        parent::setDispatcher($dispatcher);
        $this->getAdapter()->setDispatcher($dispatcher);
    }

    /**
     * Retrieve adapter instance
     * @return HttpAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the Http adapter
     * @param HttpAdapterInterface $adapter
     * @return AbstractHttpConnection
     */
    public function setAdapter(HttpAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        if( $this->hasDispatcher() ) {
            $this->adapter->setDispatcher($adapter);
        }

        return $this;
    }
}
