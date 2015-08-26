<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests
 */

namespace BeeBot\Entity\Tests;

/**
 * Description of EntityCollectionTest
 * @package BeeBot\Entity\Tests
 */
class EntityCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \BeeBot\Entity\EntityCollection
     */
    protected $object;

    public function setUp()
    {
        $this->object = new \BeeBot\Entity\EntityCollection;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAppend()
    {
        $this->object->append("not an entity");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSet()
    {
        $this->object[] = "not an entity";
    }
}
