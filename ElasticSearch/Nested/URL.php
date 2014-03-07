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

/**
 * Define URL object used to represent, page URL or referer URL
 * Separate each url part in a specific var
 * @package BeeBot\Entity\ElasticSearch\Nested
 */
class URL extends NestedEntity implements \JsonSerializable {
	/**
	 * Full and non transformed URL
	 * @var String
	 */
	protected $full;

	/**
	 * URL host part extracted by parse_url function
	 * @var array
	 */
	protected $root = [];

	/**
	 * URL path part extracted by parse_url function
	 * @var String
	 */
	protected $path;

	/**
	 * exploded URL path
	 * @var String
	 */
	protected $levels = [];

	/**
	 * URL query part extracted by parse_url function
	 * @var String
	 */
	protected $query;

	/**
	 * Get full property
	 * @return string
	 */
	public function getFull() {
		return $this->full;
	}

	/**
	 * Set full property
	 * @return string
	 */
	protected function setFull($full) {
		$this->full = $full;
	}

	/**
	 * Get path property
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Get path property
	 * @return string
	 */
	protected function setPath($path) {
		//Build the path correctly (always start with /)
		if($path[0] != '/') {
			$path = '/'.$path;
		}
		$this->path = $path;
	}

	/**
	 * Get levels property
	 * @return string
	 */
	public function getLevels() {
		return $this->levels;
	}

	/**
	 * Set levels property
	 * @return string
	 */
	protected function setLevels($path) {
		$levels = explode('/', $path);
		$length = count($levels);
		foreach ($levels as $key => $level) {
			if ($key != 0){
				$this->levels[($key == $length-1)?'end':$key] = $level;
			}
		}
	}

	/**
	 * Get query property
	 * @return string
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Set query property
	 * @return string
	 */
	protected function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * Get host property
	 * @var string
	 */
	public function getRoot() {
		return $this->root;
	}

	/**
	 * Set host property
	 * @param array $root The list of root items for this url
	 */
	public function setRoot( array $root ) {
		$this->root = $root;
	}

	/**
	 * Add a new root in the collection
	 * @param string $root
	 */
	public function addRoot($root){
		if( !$this->hasRoot($root) ){
			$this->root[] = $root;
		}
	}

	/**
	 * Check if the current root exists
	 * @param string $root
	 * @return boolean
	 */
	public function hasRoot($root){
		if (array_search($root, $this->root)!==false){
			return true;
		}

		return false;
	}

	/**
	 * {@inheritedDoc}
	 */
	protected function hydrate( $mData ) {
		//TODO : replace $this->root by getter and setter
		if( is_string( $mData ) ) {
			if( trim($mData) === "" )
				throw new \Exception('URL Given to build a Nested\URL can\'t be empty!');

			$url = parse_url($mData);
			$root = isset($url['scheme'])?$url['scheme'].'://':null;
			$root .= isset($url['host'])?$url['host']:null;

			$this->setPath(isset($url['path'])?$url['path']:'/');
			$this->setQuery(isset($url['query'])?$url['query']:null);

			//Backup url port if present
			if( isset($url['port']) && $url['port'] != 80 ) {
				$root .= ':'.$url['port'];
			}
			$this->root = $root==""?[]:[$root];

			$this->setLevels($this->getPath());

			//Build a valid FULL which is not really full...
			//For beebot a full is the most common  part of the URL: http://host/toto.html?titi=tata#anchor => /toto.html?titi=tata
			$this->full = $this->path.(isset($this->query)?('?'.$this->query):'');
		} elseif( is_array( $mData ) ) {
			if( !isset($mData['full']) )
				throw new \Exception('You need to give an array with required keys [full, path] others are optionals (scheme, host, query)');

			$this->setFull($mData['full']);

			$this->setPath(isset($mData['path'])?$mData['path']:null);
			$this->root = isset($mData['root'])?$mData['root']:[];
			$this->setQuery(isset($mData['query'])?$mData['query']:null);
			$this->setLevels($this->getPath());

			if( is_string($this->root) ) {
				$this->root = [$this->root];
			}
		} else
			throw new \Exception('To hydrate Nested\URL object you need to give a String URL or a data Array');
	}

	/**
	 * {@inheritedDoc}
	 */
	public function jsonSerialize() {
		return array(
			'full'	=> $this->getFull(),
			'root'	=> count($this->root)==1?$this->root[0]:$this->root,
			'path'	=> $this->getPath(),
			'levels'=> $this->getLevels(),
			'query' => $this->getQuery()
		);
	}
}
