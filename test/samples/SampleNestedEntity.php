<?php

/**
 * This file is part of the bee4/activerecord package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Tests\Samples
 */

namespace BeeBot\Entity\Tests\Samples;

/**
 * Sample to test NestedEntity behaviour
 * @package BeeBot\Entity\Tests\Samples
 */
class SampleNestedEntity extends \BeeBot\Entity\NestedEntity implements \JsonSerializable, \Serializable
{
	use
		\BeeBot\Entity\Behaviours\JsonSerializableEntity,
		\BeeBot\Entity\Behaviours\SerializableEntity;

	/**
	 * Public property
	 * @var string
	 */
	public $truite;

	/**
	 * Fully editable property (getter+setter)
	 * @var string
	 */
	protected $editable;

	/**
	 * Retrieve editable
	 * @return string
	 */
	public function getEditable() {
		return $this->editable;
	}

	/**
	 * set editable
	 */
	public function setEditable($value) {
		$this->editable = $value;
	}

	/**
	 * Initialize instance from given data
	 * @param Traversable $data
	 */
	protected function hydrate($data) {
		foreach( $data as $key => $value ) {
			$this->{$key} = $value;
		}
	}
}
