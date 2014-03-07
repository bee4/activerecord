<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\ElasticSearch\Nested
 */

namespace BeeBot\Entity\ElasticSearch\Nested;

use BeeBot\Entity\NestedEntity;
use BeeBot\Tools\Robot\RobotDetector;

/**
 * Define UserAgent object used to represent, user agent details
 * user RobotDetector to define who
 * @package BeeBot\Entity\ElasticSearch\Nested
 */
class UserAgent extends NestedEntity implements \JsonSerializable {
	/**
	 * UserAgent complete string
	 * @var String
	 */
	protected $full;

	/**
	 * Bot name detected by the RobotDetector object
	 * @var String
	 */
	protected $who;

	/**
	 * UserAgent constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Access full user Agent
	 * @return String
	 */
	public function getFull() {
		return $this->full;
	}

	/**
	 * Access who property
	 * @return \BeeBot\Tools\Robot\Bots\AbstractBot
	 */
	public function getWho() {
		return $this->who;
	}

	public function setFull($sValue){
		$this->full = $sValue;
	}

	/**
	 * {@inheritedDoc}
	 */
	protected function hydrate( $mData ) {
		if( is_string( $mData ) ) {
			if( trim($mData) === "" )
				throw new \Exception('UserAgent Given to build a Nested\UserAgent can\'t be empty!');

			$this->full = $mData;
			$this->who = RobotDetector::whoIs($this->full);
		} elseif( is_array( $mData ) ) {
			if( !isset($mData['full']) || !isset($mData['who']) )
				throw new \Exception('You need to give an array with required keys [full, who]');

			$this->full = $mData['full'];
			$this->who = $mData['who'];
		} else
			throw new \Exception('To hydrate Nested\UserAgent object you need to give a String UserAgent or a data Array');
	}

	/**
	 * {@inheritedDoc}
	 */
	public function jsonSerialize() {
		return array(
			'full' => $this->full,
			'who' => $this->who
		);
	}
}