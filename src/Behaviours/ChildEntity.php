<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author  Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Behaviours
 */
namespace BeeBot\Entity\Behaviours;

use BeeBot\Entity\Entity;

/**
 * ChildEntity behaviour definition.
 * Simply add parent property management which need to be a valid Entity object
 * @package BeeBot\Entity\Behaviours
 * @author  Stephane HULARD <s.hulard@chstudio.fr>
 */
trait ChildEntity
{
    /**
     * Parent UID
     * @var Entity
     */
    private $parent;

    /**
     * Parent property setter
     * @param Entity $parent Parent entity to be used
     */
    public function setParent(Entity $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Retrieve parent entity
     * @return Entity
     */
    public function getParent()
    {
        return $this->parent;
    }
}
