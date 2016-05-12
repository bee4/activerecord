<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author  Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

use ArrayObject;

/**
 * EntityCollection implementation
 * Allow to store multiple entites as a Traversable object
 * @package BeeBot\Entity
 */
class EntityCollection extends ArrayObject
{
    /**
     * Append a new Entity in the collection
     * @param Entity $value The new Entity to be added
     * @throws \InvalidArgumentException
     */
    public function append($value)
    {
        if (!($value instanceof Entity)) {
            throw new \InvalidArgumentException(
                'It\'s an entity collection, you can\'t append anything else than an Entity object'
            );
        }

        parent::append($value);
    }

    /**
     * Set a new Entity at the given offset
     * @param Mixed $offset The position in the array to be modified
     * @param Entity $value The Entity to be added
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Entity)) {
            throw new \InvalidArgumentException(
                'It\'s an entity collection, you can\'t append anything else than an Entity object'
            );
        }

        parent::offsetSet($offset, $value);
    }
}
