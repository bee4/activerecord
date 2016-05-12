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
 * NestedEntityCollection implementation
 * Allow to store multiple nested entites as a Traversable object
 * @package BeeBot\Entity
 */
class NestedEntityCollection extends ArrayObject
{
    /**
     * Append a new Entity in the collection
     * @param NestedEntity $value The new NestedEntity to be added
     * @throws \InvalidArgumentException
     */
    public function append($value)
    {
        if (!($value instanceof NestedEntity)) {
            throw new \InvalidArgumentException(
                'It\'s a nested entity collection, you can\'t append anything else than an NestedEntity object'
            );
        }

        parent::append($value);
    }

    /**
     * Set a new Entity at the given offset
     * @param Mixed $offset The position in the array to be modified
     * @param NestedEntity $value The NestedEntity to be added
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!($value instanceof NestedEntity)) {
            throw new \InvalidArgumentException(
                'It\'s a nested entity collection, you can\'t append anything else than an NestedEntity object'
            );
        }

        parent::offsetSet($offset, $value);
    }
}
