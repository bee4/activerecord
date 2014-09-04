<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity
 */

namespace BeeBot\Entity;

/**
 * Parse a docblock and extract all important details
 * @package BeeBot\Entity
 */
class DocBlockParser
{
	/**
	 * Tag extraction pattern
	 */
	const TAG_REGEX = '@([^ ]+)(?:\s+(.*?))?(?=(\n[ \t]*@|\s*$))';

	/**
	 * Keyword to identify which type of data is going to be processed (desc, tag)
	 * @var sring
	 */
	protected $position;

	/**
	 * Cleanup comment string
	 * @var string
	 */
	protected $comment;

	/**
	 * Contain the number of comment lines
	 * @var integer
	 */
	protected $lineno;

	/**
	 * Pointer position in the doc block
	 * @var integer
	 */
	protected $cursor;

	/**
	 * Parse a doc block and extract meta data
	 * @param string $comment The comment docblock to process
	 * @return array
	 */
	public function parse($comment) {
		// remove comment characters and normalize
		$comment = preg_replace(array('#^/\*\*\s*#', '#\s*\*/$#', '#^\s*\*#m'), '', trim($comment));
		$this->comment = "\n".preg_replace('/(\r\n|\r)/', "\n", $comment);
		$this->position = 'desc';
		$this->lineno = 1;
		$this->cursor = 0;

		$doc = ['short'=>null,'long'=>null,'tags'=>[]];
		while ($this->cursor < strlen($this->comment)) {
			if( $this->position === 'desc' ) {
				list($short, $long) = $this->parseDesc();
				$doc['short'] = $short;
				$doc['long'] = $long;
			} elseif( $this->position === 'tag' ) {
				list($type, $values) = $this->parseTag();
				$doc['tags'][$type] = $values;
			}

			if (preg_match('/\s*$/As', $this->comment, $match, null, $this->cursor)) {
				$this->cursor = strlen($this->comment);
			}
		}

		return $doc;
	}

	/**
	 * Parse descriptions
	 * @return array
	 */
	protected function parseDesc() {
		if (preg_match('/(.*?)(\n[ \t]*'.self::TAG_REGEX.'|$)/As', $this->comment, $match, null, $this->cursor)) {
			$this->move($match[1]);

			// short desc ends at the first dot or when \n\n occurs
			$tmp = trim($match[1]);
			if (preg_match('/(.*?)(\.\s|\n\n|$)/s', $tmp, $match)) {
				$long = trim(substr($tmp, strlen($match[0])));
				$short = trim($match[0]);
			} else {
				$short = $tmp;
				$long = '';
			}

			// remove single lead space
			$short = preg_replace('/^ /', '', $short);
			$long = preg_replace('/^ /m', '', $long);
		}

		$this->position = 'tag';

		return array(str_replace("\n", '', $short), $long);
	}

	/**
	 * Parse all documentation tag properties
	 * @return array
	 * @throws \LogicException
	 */
	protected function parseTag() {
		if (!preg_match('/\n\s*'.self::TAG_REGEX.'/As', $this->comment, $match, null, $this->cursor)) {
			// skip
			$this->cursor = strlen($this->comment);
			throw new \LogicException(sprintf('Unable to parse block comment near "... %s ...".', substr($this->comment, max(0, $this->cursor - 15), 15)));
		}

		$this->move($match[0]);
		switch ($type = $match[1]) {
			case 'param':
				if ( !preg_match('/^([^\s]*)\s*(?:(?:\$|\&\$)([^\s]+))?\s*(.*)$/s', $match[2], $m) ) {
					throw new \LogicException(sprintf('Unable to parse "@%s" tag "%s"', $type, $match[2]));
				}
				return array($type, array($this->parseHint(trim($m[1])), trim($m[2]), $this->normalizeString($m[3])));

			case 'return':
			case 'var':
				if ( !preg_match('/^([^\s]+)\s*(.*)$/s', $match[2], $m) ) {
					throw new \LogicException(sprintf('Unable to parse "@%s" tag "%s"', $type, $match[2]));
				}
				return array($type, array($this->parseHint(trim($m[1])), $this->normalizeString($m[2])));

			case 'throws':
				if ( !preg_match('/^([^\s]+)\s*(.*)$/s', $match[2], $m) ) {
					throw new \LogicException(sprintf('Unable to parse "@%s" tag "%s"', $type, $match[2]));
				}
				return array($type, array(trim($m[1]), $this->normalizeString($m[2])));

			default:
				return array($type, $this->normalizeString($match[2]));
		}
	}

	/**
	 * Parse additional information on the property type
	 * @param string $hint
	 * @return array
	 */
	protected function parseHint($hint) {
		$hints = array();
		foreach (explode('|', $hint) as $hint) {
			if ('[]' == substr($hint, -2)) {
				$hints[] = substr($hint, 0, -2);
			} else {
				$hints[] = $hint;
			}
		}

		return $hints;
	}

	/**
	 * Normalize the string by removing EOL characters
	 * @param string $str
	 * @return string
	 */
	protected function normalizeString($str) {
		return preg_replace('/\s*\n\s*/', ' ', trim($str));
	}

	/**
	 * Move the parser cursor in the text
	 * @param string $text
	 */
	protected function move($text) {
		$this->lineno += substr_count($text, "\n");
		$this->cursor += strlen($text);
	}
}