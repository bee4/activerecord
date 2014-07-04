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

/**
 * Initialize the connection from given parameters
 * @package BeeBot\Entity\Connections
 */
class ConnectionFactory
{
	/**
	 * Build an AbstractConnection from the given DSN.
	 * DSN is used to defined a common format for all connections
	 * @param string $dsn Data source name used to initialize connection
	 * @return AbstractConnection
	 */
	public static function build($dsn) {
		$matches = [];
		if(preg_match('/^([^:]+):(.*)$/', $dsn, $matches) === false) {
			throw new \InvalidArgumentException('Invalid DSN given, must be structured like: [type]:[details]');
		}
		
		$connectionName = __NAMESPACE__.'\\'.ucfirst($matches[1]).'Connection';
		if(!class_exists($connectionName)) {
			throw new \RuntimeException("Data source name given can't be used because the connection is not implemented: ".$connectionName);
		}
		
		return new $connectionName($matches[2]);
	}
}
