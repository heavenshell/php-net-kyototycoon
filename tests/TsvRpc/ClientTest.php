<?php
/**
 * Client test
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
 * @use       HTTP\TsvRpc
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @see       http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Client.pm
 */

namespace HTTP\TsvRpc;
use HTTP\TsvRpc;

/**
 * @see prepare
 */
require_once dirname(__DIR__) . '/prepare.php';

/**
 * Spec of Client library.
 *
 * @use       HTTP\TsvRpc
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Yuya Takeyama <sign.of.the.wolf.pentagram _at_ gmail.com>
 * @license   New BSD License
 * @see       http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Client.pm
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once 'HTTP/TsvRpc/Client.php';
    }

    /**
     * @expectedException \HTTP\TsvRpc\Exception
     */
    public function testShouldThrowExceptionIfArgDontHasKeyNamedBase()
    {
        new Client(array());
    }
}
