<?php
/**
 * TSV-RPC client library.
 *
 * This module is port from Perl Cache::KyotoTycoon.
 *
 * PHP version 5.3
 *
 * Copyright (c) 2010 Shinya Ohyanagi, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Shinya Ohyanagi nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @use       HTTP\TsvRpc\Parser
 * @use       HTTP\TsvRpc\Util
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */

namespace HTTP\TsvRpc;
use HTTP\TsvRpc\Parser,
    HTTP\TsvRpc\Util,
    HTTP\TsvRpc\Exception;

/**
 * Client
 *
  * <pre>
 *   This module is port from Perl Cache::KyotoTycoon.
 *   See also @link.
 * </pre>
 *
 * @use       HTTP\TsvRpc\Parser
 * @use       HTTP\TsvRpc\Util
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */
class Client
{
    /**
     * Version.
     */
    const VERSION = '0.0.3';

    /**
     * Base url.
     *
     * @var    mixed
     * @access private
     */
    private $_base = null;

    /**
     * HTTP Client.
     *
     * @var    mixed
     * @access private
     */
    private $_client = null;

    /**
     * Content-Type parser.
     *
     * @var    mixed
     * @access private
     */
    private $_parser = null;

    /**
     * Last content data.
     *
     * @var    mixed
     * @access private
     */
    private $_lastContent = null;

    /**
     * Constructor
     *
     * @param  array $args
     * @access public
     * @return void
     */
    public function __construct(array $args)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
        $base = '';
        if (isset($args['base'])) {
            $base = $args['base'];
        } else {
            throw new Exception("Missing argument named 'base' for rpc base url.");
        }
        $base    = rtrim($base, '\//') . '/';
        $timeout = isset($args['timeout']) ? $args['timeout'] : 1;
        $agent   = isset($args['agent']) ?: __CLASS__ . '/' . self::VERSION;

        $adapter = isset($args['adapter']) ?: 'HTTP_Request2_Adapter_Socket';
        $config  = array('adapter' => $adapter, 'timeout' => $timeout);
        $client  = new \HTTP_Request2(
            null, \HTTP_Request2::METHOD_POST, $config
        );
        $client->setHeader('user-agent', $agent);

        $this->_client = $client;
        $this->_base   = $base;
        $this->_parser = new Parser();
    }

    /**
     * Send request.
     *
     * @param  mixed $method Rpc method
     * @param  mixed $args Args
     * @param  string $encoding B(base64), U(Urlencode), Q(Quoted printable)
     * @access public
     * @return mixed Status code and HTTP body
     */
    public function call($method, $args = array(), $encoding = 'B')
    {
        $content = $this->_parser->encodeTsvrpc($args, $encoding);
        $client  = $this->_client;
        $uri     = $this->_base . $method;
        $headers = array(
            'Content-Type'   => "text/tab-separated-values; colenc=$encoding",
            'Content-Length' => strlen($content),
            'Connection'     => 'Keep-Alive',
            'Keep-Alive'     => 300,
        );
        $client->setHeader($headers)->setUrl($uri)->setBody($content);
        $response = $client->send();
        if ($response) {
            $code            = $response->getStatus();
            $responseHeaders = $response->getHeader();
            $contentType     = $responseHeaders['content-type'];
            $resEncoding     = Util::parseContentType($contentType);

            // Decode by content-type B|Q|U
            $body = $this->_parser->decodeTsvrpc(
                $response->getBody(), $resEncoding
            );
            $this->_lastContent = $body;
            return array('code' => $code, 'body' => $body);
        }

        throw new Exception('Fail to call ' . $uri);
    }

    /**
     * Get last content.
     *
     * <pre>
     *   This is a debug method.
     * </pre>
     *
     * @access public
     * @return mixed
     */
    public function getLastContent()
    {
        return $this->_lastContent;
    }

    /**
     * Autoload class.
     *
     * @param  mixed $class
     * @access public
     * @return void
     */
    public static function autoload($className)
    {
        // Autoload class.
        // http://groups.google.com/group/php-standards/web/psr-0-final-proposal
        if (!class_exists($className, false)) {
            $className = ltrim($className, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
            require_once $fileName;
        }
    }
}
