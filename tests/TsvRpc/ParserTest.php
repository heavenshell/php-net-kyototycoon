<?php
/**
 * Parser test
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
 * @see       http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Parser.pm
 */

namespace HTTP\TsvRpc;
use HTTP\TsvRpc;

/**
 * @see prepare
 */
require_once dirname(__DIR__) . '/prepare.php';

/**
 * Spec of Parser library.
 *
 * @use       HTTP\TsvRpc
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @see       http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Parser.pm
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    private $_parser = null;
    public function setUp()
    {
        require_once 'HTTP/TsvRpc/Parser.php';
        $this->_parser = new Parser();
    }

    public function testShouldReturnPlaneStringWhenEnocdingNotSet()
    {
        $this->assertSame(
            $this->_parser->encodeTsvrpc(array('foo' => 'bar')),
            "foo\tbar"
        );
    }

    public function testShouldReturnBase64StringWhenBSet()
    {
        $this->assertSame(
            $this->_parser->encodeTsvrpc(array('foo' => 'bar'), 'B'),
            "Zm9v\tYmFy"
        );
    }

    public function testShouldReturnUriEncodedStringWhenUSet()
    {
        $this->assertSame(
            $this->_parser->encodeTsvrpc(array('foo' => "\0"), 'U'),
            "foo\t%00"
        );
    }

    public function testShouldReturnQuotedPrintableStringWhenQSet()
    {
        $this->assertSame(
            $this->_parser->encodeTsvrpc(array('foo' => "\0"), 'Q'),
            "foo\t=00"
        );
    }

    public function testShouldReturnArrayWhenTsvStringSet()
    {
        $this->assertSame(
            $this->_parser->decodeTsvrpc("foo\tbar"),
            array('foo' => 'bar')
        );
    }

    public function testShouldReturnArrayWhenBase64EncodedStringSet()
    {
        $this->assertSame(
            $this->_parser->decodeTsvrpc("Zm9v\tYmFy", 'B'),
            array('foo' => 'bar')
        );
    }

    public function testShouldReturnArrayWhenUrlEncodedStringSet()
    {
        $this->assertSame(
            $this->_parser->decodeTsvrpc("foo\t%00", 'U'),
            array('foo' => "\0")
        );
    }

    public function testShouldReturnArrayWhenQuotedPrintableEncodedStringSet()
    {
        $this->assertSame(
            $this->_parser->decodeTsvrpc("foo\t=00", 'Q'),
            array('foo' => "\0")
        );
    }
}
