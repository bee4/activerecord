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
 * Default Curl adapter to use with HTTP connections
 * No events, simple HTTP api...
 * @package BeeBot\Entity\Connections\Adapters
 */
class CurlHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Curl open handle
     * @var resource
     */
    protected $handle;

    /**
     * Initialize the adapter
     * @param Client $adaptee
     */
    public function __construct()
    {
        $this->handle = curl_init();
    }

    /**
     * Release curl resource
     */
    public function __destruct() {
        curl_close($this->handle);
    }

    /**
     * Exec the query and retrieve result
     * @param  string     $method  HTTP Method
     * @param  string     $url     URL to call
     * @param  array|null $headers Header collection
     * @param  string     $body    Body to be sent
     * @return string
     */
    private function exec($method, $url, array $headers = null, $body = null) {
        curl_reset($this->handle);

        if( null !== $headers &&
            array_keys($headers) !== range(0, count($headers) - 1)
        ) {
            $headers = array_values(array_walk(function(&$item, $key) {
                $item = $key.': '.$item;
            }, $headers));
        }

        curl_setopt_array($this->handle, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        switch ($method) {
            case 'GET':
                curl_setopt($this->handle, CURLOPT_HTTPGET, true);
                break;
            case 'HEAD':
                curl_setopt($this->handle, CURLOPT_NOBODY, true);
                break;
            case 'POST':
                curl_setopt($this->handle, CURLOPT_POST, true);
            default:
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        return curl_exec($this->handle);
    }

    /**
     * Do nothing because no events here...
     * @param  DispatcherInterface $dispatcher
     */
    protected function mapEvents(DispatcherInterface $dispatcher)
    {}

    public function get($url, array $headers = null)
    {
        return $this->exec(
            'GET',
            $this->getRoot().$url,
            $headers
        );
    }

    public function post($url, $body, array $headers = null)
    {
        return $this->exec(
            'POST',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }

    public function head($url, array $headers = null)
    {
        return $this->exec(
            'HEAD',
            $this->getRoot().$url,
            $headers
        );
    }

    public function put($url, $body, array $headers = null)
    {
        return $this->exec(
            'PUT',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }

    public function delete($url, $body, array $headers = null)
    {
        return $this->exec(
            'DELETE',
            $this->getRoot().$url,
            $headers,
            $body
        );
    }
}
