<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @author  Julien DRAGOUNOFF <j.dragounoff@gmail.com>
 * @package BeeBot\Entity\ElasticSearch
 */

namespace BeeBot\Entity\ElasticSearch;

class Relation extends AbstractDocument {


	const INTERNAL = "internal";
	const EXTERNAL = "external";

	const PICTURE = "picture";
	const TEXT = "text";
	const BUTTON = "button";

	private static $types = [
		self::INTERNAL,
		self::EXTERNAL
	];

	public function __construct(){
		parent::__construct();

		$this->data->to = null;
		$this->data->type = self::INTERNAL;
		$this->data->recursive = [
			'is'=>false,
			'repeat'=>0
		];
		$this->data->repeat = 1;
		$this->data->labels = [];
		$this->data->modified = null;

		//Label cache to quickly access keys
		$this->labels = [];
	}

	/**
	 * To property setter
	 * @param mixed Type depend of relation Type: INTERNAL => PAGE, EXTERNAL => URL(string)
	 */
	public function setTo ($oTo){
		if( $this->getType() === self::INTERNAL && !($oTo instanceof Page) ) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('To must be a Page instance for INTERNAL Relations!!', $oTo);
		} elseif( $this->getType() === self::EXTERNAL && !is_string($oTo) ) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('To must be an URL for EXTERNAL Relation!!', $oTo);
		}

		$this->data->to = method_exists ($oTo, 'getUID')?$oTo->getUID():$oTo;
	}

	/**
	 * To property accessor
	 * @return string
	 */
	public function getTo (){
		return $this->data->to;
	}

	/**
	 * Type property setter
	 * @param string
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function setType($type){
		if (!in_array($type, self::$types)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected valide type', $type);
		}
		$this->data->type = $type;
	}

	/**
	 * Type property accessor
	 * @return string
	 */
	public function getType(){
		return $this->data->type;
	}

	/**
	 * IsRecursive property setter
	 * @param bool
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function setIsRecursive($isRecursive){
		if (!is_bool($isRecursive)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected boolean', $isRecursive);
		}
		$this->data->recursive['is'] = $isRecursive;
	}

	/**
	 * IsRecursive property accessor
	 * @return bool
	 */
	public function getIsRecursive(){
		return $this->data->recursive['is'];
	}

	/**
	 * NbRecursiveRepeat property setter
	 * @param type int
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function setNbRecursiveRepeat($nbRecursiveRepeat){
		if (!is_int($nbRecursiveRepeat)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected integer', $nbRecursiveRepeat);
		}
		$this->data->recursive['repeat'] = $nbRecursiveRepeat;
	}

	/**
	 * NbRecursiveRepeat property accessor
	 * @return int
	 */
	public function getNbRecursiveRepeat(){
		return $this->data->recursive['repeat'];
	}

	/**
	 * NbRepeat property setter
	 * @param int
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function setNbRepeat($nbRepeat){
		if (!is_int($nbRepeat)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected integer', $nbRepeat);
		}
		$this->data->repeat = $nbRepeat;
	}

	/**
	 * NbRepeat property accessor
	 * @return int
	 */
	public function getNbRepeat(){
		return $this->data->repeat;
	}

	/**
	 * Label property setter
	 * @param string $label
	 * @param int $position
	 * @param string $rel
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function addLabel($label, $position, $type = self::TEXT, $rel =  null){
		if (!is_string($label)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected string', $label);
		} if (!is_int($position)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected integer', $position);
		}	if (isset($rel) && !is_string($rel)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('Expected string', $rel);
		}

		if(($key = array_search($label, $this->labels)) === false ) {
			$key = count($this->labels);
			$this->labels[] = $label;
			$this->data->labels[] = ['label' => $label, 'repeat' => 1, 'position' => [], 'type' => [ $type => [] ]];
		} else {
			$this->data->labels[$key]['repeat']++;
			if( !isset($this->data->labels[$key]['type'][$type]) ) {
				$this->data->labels[$key]['type'][$type] = [];
			}
		}

		$this->data->labels[$key]['position'][] = $position;
		$this->data->labels[$key]['type'][$type][] = $position;

		if( !is_null($rel) ) {
			if(!isset($this->data->labels[$key][$rel])) {
				$this->data->labels[$key][$rel] = [];
			}
			$this->data->labels[$key][$rel][] = $position;
		}
	}

	/**
	 * Labels property accessor
	 * @return array
	 */
	public function getLabels(){
		return $this->data->labels;
	}

	/**
	 * Modified property setter
	 * @param \DateTime $oDate
	 */
	public function setModified( \DateTime $oDate ) {
		$this->data->modified = $oDate->format('Y-m-d\TH:i:sP');
	}

	/**
	 * Modified property accessor
	 * @return string
	 */
	public function getModified(){
		return $this->data->modified;
	}

	/**
	 * Transform current object to JSON
	 * @return String
	 * @throws \BeeBot\Exception\Native\InvalidArgumentException
	 */
	public function jsonSerialize() {
		if( !isset($this->parent)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('from need to be set before to save current Page!!!');
		} if( !isset($this->data->to)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('to need to be set before to save current Page!!!');
		} if( !isset($this->data->type)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('type need to be set before to save current Page!!!');
		} if( !isset($this->data->modified)) {
			throw new \BeeBot\Exception\Native\InvalidArgumentException('modified need to be set before to save current Page!!!');
		}

		return parent::jsonSerialize();
	}
}