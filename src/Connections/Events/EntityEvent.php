<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections\Events
 */

namespace BeeBot\Entity\Connections\Events;

use Bee4\Events\EventInterface;
use BeeBot\Entity\Entity;
use BeeBot\Entity\Connections\ConnectionInterface;

/**
 * Event used on entities object. It allow to log all connection calls.
 * @package BeeBot\Entity\Connections\Events
 */
class EntityEvent implements EventInterface
{
    const SAVE = "entity.save";
    const DELETE = "entity.delete";

    /**
     * Entity linked
     * @var Entity
     */
    protected $entity;

    /**
     * Connection linked
     * @var Connection
     */
    protected $connection;

    /**
     * Build an event and define the message
     * @param Entity $entity
     * @param ConnectionInterface $connection
     */
    public function __construct(Entity $entity, ConnectionInterface $connection)
    {
        $this->entity = $entity;
        $this->connection = $connection;
    }

    /**
     * Entity accessor
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Connection accessor
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
