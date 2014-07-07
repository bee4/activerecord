<?php
/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2013
 * @author	Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\ElasticSearch
 */

namespace BeeBot\Entity\ElasticSearch;

/**
 * Define Page entity linked with statistics
 * @package BeeBot\Entity\ElasticSearch
 */
class Page extends AbstractDocument {
	const CRAWLED = 'crawled';
	const INVALID = 'invalid';

	/**
	 * Page entity constructor
	 */
	public function __construct() {
		//Initialize elasticsearch details
		parent::__construct();

		$this->data->url = null;
		$this->data->modified = null;
		$this->data->kind = "undefined";
		$this->data->types = [];
		$this->data->meta = null;
		$this->data->headers = [];
		$this->data->content = [
			"hierarchy" => [],
			"hash" => null,
			"breadcrumb" => null
		];
		$this->data->state = null;
		$this->data->depth = null;
	}

	/**
	 * Page meta setter
	 * @param $aMeta
	 * @throws \Exception
	 */
	public function setMeta($aMeta){
		if(!is_array($aMeta))
			throw new \Exception('Array given must be a valid array');
		$this->data->meta = $aMeta;
	}

	/**
	 * Meta getter
	 * @return array
	 */
	public function getMeta(){
		return $this->data->meta;
	}

	/**
	 * Page depth setter
	 * @param $depth
	 */
	public function setDepth($depth){
		if(!isset($this->data->depth) || ($depth < $this->data->depth)){
			$this->data->depth = $depth;
		}
	}

	/**
	 * Depth getter
	 * @return string
	 */
	public function getDepth(){
		return $this->data->depth;
	}

	/**
	 * URL property setter
	 * @param String $url URL of the current page
	 */
	public function setURL( Nested\URL $url ) {
		$this->data->url = $url;
	}

	/**
	 * URL Property accessor
	 * @return Nested\URL
	 */
	public function getURL() {
		return $this->data->url;
	}
	/**
	 * Set headers array
	 * @param string $headers
	 */
	public function setHeaders($headers){
		$this->data->headers = $headers;
		if( isset($this->data->headers['location']) && is_string($this->data->headers['location']) && trim($this->data->headers['location']) != '' ) {
			$this->data->headers['location'] = Nested\URL::factory($this->data->headers['location']);
		}
	}
	/**
	 * Headers accessor
	 * @return array
	 */
	public function getHeaders(){
		return $this->data->headers;
	}
	/**
	 * Page specific Header accessor
	 * @param string $header
	 * @return string
	 */
	public function getHeader($header) {
		if(isset($this->data->headers) && array_search($header, array_keys($this->data->headers)) !== false)
			return $this->data->headers[$header];
		else
			return null;
	}

	/**
	 * Set modified date
	 * @param \DateTime $oDate
	 */
	public function setModified( \DateTime $oDate ) {
		$this->data->modified= $oDate->format('Y-m-d\TH:i:sP');
	}

	/**
	 * Modified Property accessor
	 * @return string
	 */
	public function getModified() {
		return $this->data->modified;
	}

	/**
	 * Kind property setter
	 * @param $sKind
	 * @throws \Exception
	 */
	public function setKind( $sKind ) {
		if( trim($sKind) == "" )
			throw new \Exception('Kind can\'t be an empty string!!');

		$this->data->kind = $sKind;
	}

	/**
	 * Kind property getter
	 * @return String
	 */
	public function getKind() {
		return $this->data->kind;
	}

	/**
	 * Types property setter
	 * @param Array $aTypes List of page types
	 */
	public function setTypes( Array $aTypes ) {
		$this->data->types = $aTypes;
	}

	/**
	 * Types property getter
	 * @return Array
	 */
	public function getTypes() {
		return $this->data->types;
	}

	/**
	 * State property getter
	 * @return string
	 */
	public function getState() {
		return $this->data->state;
	}

	/**
	 * State property setter
	 * @param string
	 */
	public function setState($state) {
		$this->data->state = $state;
	}

	/**
	 * Content hierarchy setter
	 * @param array
	 */
	public function setContentHierarchy( array $hierarchy ) {
		$this->data->content['hierarchy'] = $hierarchy;
	}
	/**
	 * Content hierarchy accessor
	 * @return array
	 */
	public function getContentHierarchy() {
		return $this->data->content['hierarchy'];
	}

	/**
	 * Content hash setter
	 * @param string
	 */
	public function setContentHash( $hash ) {
		$this->data->content['hash'] = $hash;
	}
	/**
	 * Content hash accessor
	 * @return string
	 */
	public function getContentHash() {
		return $this->data->content['hash'];
	}

	/**
	 * Content breadcrumb setter
	 * @param array
	 */
	public function setContentBreadcrumb( array $breadcrumb ) {
		$this->data->content['breadcrumb'] = $breadcrumb;
	}
	/**
	 * Content breadcrumb accessor
	 * @return string
	 */
	public function getContentBreadcrumb() {
		return $this->data->content['breadcrumb'];
	}

	/**
	 * Go a little deeper than the default hydrate method because of
	 * Nested\URL object initialisation
	 * @param Mixed $mData
	 */
	protected function hydrate( $mData ) {
		if( is_array($mData) ) {
			parent::hydrate($mData);
			$this->data->url = Nested\URL::factory($this->data->url);
		} elseif( is_string($mData) ) {
			$this->data->url = Nested\URL::factory($mData);
		} else {
			throw new \InvalidArgumentException('Hydate data must be array or string!');
		}
	}

	/**
	 * Transform current object to JSON
	 * @return String
	 * @throws \Exception
	 */
	public function jsonSerialize() {
		if( !isset($this->data->modified) ) {
			$this->data->modified = (new \DateTime)->format('Y-m-d\TH:i:sP');
		}
		
		return parent::jsonSerialize();
	}

	/**
	 * Check if the current page exists with the given root (http://domain:port)
	 * @param string $url
	 * @param string $root
	 * @return boolean
	 */
	public static function existWithRoot($url, $root){
		$bExists = false;

		$foundPages = Page::fetchBy('url.full', $url);
		foreach ( $foundPages as $pages) {
			if ($pages->getURL()->hasRoot($root)) {
				$bExists = true;
				break;
			}
		}
		if (count($foundPages) == 1 && !$bExists){
			Page::implementRoot($root, $pages);
			$bExists = true;
		}

		return $bExists;
	}

	/**
	 * Implement the new root in the given page url if needed
	 * @param string $root
	 * @param \BeeBot\Entity\ElasticSearch\Page $page
	 */
	public static function implementRoot($root, Page $page){
		$page->getUrl()->addRoot($root);
		$page->save();
	}
}