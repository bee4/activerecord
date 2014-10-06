<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2014
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections
 */

namespace BeeBot\Entity\Connections;

use Bee4\Events\EventInterface;
use BeeBot\Event\AbstractEvent;

/**
 * Event used on connection object. It allow to log all connection calls.
 * @package BeeBot\Entity\Connections
 */
class ConnectionEvent implements EventInterface {
	const REQUEST = "data.request";
	const ERROR = "data.error";
	const RESULT = "data.result";

	const SAVE = "connection.save";
	const DELETE = "connection.delete";

	/**
	 * Event message
	 * @var mixed
	 */
	protected $message;

	/**
	 * Build an event and define the message
	 * Message can be an object, a string or something else...
	 * Then the listener must know what to do with it because it depends of Connection used
	 * @param mixed $msg
	 */
	public function __construct($msg) {
		$this->message = $msg;
	}

	/**
	 * Message accessor
	 * @return mixed
	 */
	public function getMessage() {
		return $this->message;
	}
}
