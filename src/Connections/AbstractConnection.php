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

use Bee4\Events\DispatcherAwareTrait;
use BeeBot\Entity\Entity;
use BeeBot\Entity\Connections\Events\EntityEvent;

/**
 * Define a global canvas for connection adapters.
 * @package BeeBot\Entity\Connections
 */
abstract class AbstractConnection implements ConnectionInterface
{
    //Add Event dispatcher behaviour to allow events
    use DispatcherAwareTrait;

    /**
     * @param Entity $entity
     * @return boolean
     */
    public function save(Entity $entity)
    {
        $this->dispatch(EntityEvent::SAVE, new EntityEvent($entity, $this));
        return true;
    }

    /**
     * @param Entity $entity
     * @return boolean
     */
    public function delete(Entity $entity)
    {
        $this->dispatch(EntityEvent::DELETE, new EntityEvent($entity, $this));
        return true;
    }
}
