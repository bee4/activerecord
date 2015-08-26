<?php

/**
 * This file is part of the beebot package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Bee4 2015
 * @author Stephane HULARD <s.hulard@chstudio.fr>
 * @package BeeBot\Entity\Connections\Adapters
 */

namespace BeeBot\Entity\Connections\Adapters;

/**
 * Define a global canvas for connection adapters.
 * @package BeeBot\Entity\Connections\Adapters
 */
interface HttpAdapterInterface extends AdapterInterface
{
    /**
     * Set URL root used for requests
     * @param string $url
     */
    public function setRoot($url);

    /**
     * HTTP GET implementation
     * @param  string     $url     URL to call
     * @param  array|null $headers Specific headers array
     * @return string              Response content
     */
    public function get($url, array $headers = null);

    /**
     * HTTP POST implementation
     * @param  string     $url     URL to call
     * @param  string     $body    Request content
     * @param  array|null $headers Specific headers array
     * @return string              Response content
     */
    public function post($url, $body, array $headers = null);

    /**
     * HTTP HEAD implementation
     * @param  string     $url     URL to call
     * @param  array|null $headers Specific headers array
     * @return string              Response content
     */
    public function head($url, array $headers = null);

    /**
     * HTTP PUT implementation
     * @param  string     $url     URL to call
     * @param  string     $body    Request content
     * @param  array|null $headers Specific headers array
     * @return string              Response content
     */
    public function put($url, $body, array $headers = null);

    /**
     * HTTP DELETE implementation
     * @param  string     $url     URL to call
     * @param  string     $body    Request content
     * @param  array|null $headers Specific headers array
     * @return string              Response content
     */
    public function delete($url, $body, array $headers = null);
}
