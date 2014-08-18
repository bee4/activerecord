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
 * Sample to test ChildEntity behaviour
 * @package BeeBot\Entity\Tests\Samples
 */
class SampleEntity extends \BeeBot\Entity\Entity
{
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
	 * Only readable property (getter)
	 * @var string
	 */
	protected $readable = "readable";

	/**
	 * Only writable property (setter)
	 * @var string
	 */
	protected $writable;

	/**
	 * Totally hidden property (without getter & setter)
	 * @var string
	 */
	protected $hidden;

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
	 * Retrieve readable
	 * @return string
	 */
	public function getReadable() {
		return $this->readable;
	}

	/**
	 * Set writable
	 * @param string $value
	 */
	public function setWritable($value) {
		$this->writable = $value;
	}
}
