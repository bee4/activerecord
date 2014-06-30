<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Action
 */

namespace BeeBot\Entity\ElasticSearch;


use BeeBot\Entity\Entity;
use BeeBot\Entity\Behaviours\ChildEntity;
use BeeBot\Entity\Behaviours\DatedEntity;
use BeeBot\Entity\Behaviours\FactoryEntity;

/**
 * Define basic elasticsearch document
 * @package BeeBot\Entity\ElasticSearch
 */
abstract class AbstractDocument extends Entity implements \JsonSerializable {
	use DatedEntity, ChildEntity, FactoryEntity;

	/**
	 * ElasticSearch index name came from XML configuration
	 * @var String
	 * @static
	 */
	protected static $index;

	/**
	 * ElasticSearch webservice URL came from XML configuration
	 * @var String
	 * @static
	 */
	protected static $ws;

	/**
	 * Object data
	 * @var \stdClass
	 */
	protected $data;

	/**
	 * ElasticSearch Document constructor
	 */
	public function __construct() {
		$this->data = new \stdClass();
		$this->setDate(new \DateTime);

		parent::__construct();
	}
	
	/**
	 * Transform current object to JSON
	 * @return stdClass|Array
	 */
	public function jsonSerialize() {
		$oTmp = clone $this->data;

		$oTmp->_id = $this->uid;
		$oTmp->_timestamp = $this->getDate()->format('Y-m-d\TH:i:sP');

		return $oTmp;
	}

	/**
	 * Update object details from decoded JSON
	 * @param Mixed $mData The data to use to wake up
	 */
	protected function hydrate( $mData ) {
		$this->uid = $mData['uid'];
		unset($mData['uid']);

		foreach( $mData as $sKey => $sValue ) {
			$this->data->{$sKey} = $sValue;
		}

		$this->parent = $this->date = null;
		if( isset($mData['fields']) ) {
			$this->parent = isset($mData['fields']['_parent'])?$mData['fields']['_parent']:null;
			$this->date = isset($mData['fields']['_timestamp'])?\DateTime::createFromFormat('Y-m-d\TH:i:sP', $mData['fields']['_timestamp']):null;
		}
	}
}
