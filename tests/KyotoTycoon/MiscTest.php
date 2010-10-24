<?php
/**
 * Spec of \Net\KyotoTycoon.
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
 * @use       Net
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */

namespace Net;
use Net;

/**
 * @see prepare
 */
require_once dirname(__DIR__) . '/prepare.php';

/**
 * Misc test.
 *
 * @use       Net
 * @category  Net
 * @package   Net\KyotoTycoon
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/
 */
class MiscTest extends \PHPUnit_Framework_TestCase
{
    private $_kt = null;
    public function setUp()
    {
        require_once 'Net/KyotoTycoon.php';
        $this->_kt = new KyotoTycoon();
    }

    public function testShouldEchoInputData()
    {
        $input = array('foo' => 'bar', 'hoge' => 'fuga');
        $got   = $this->_kt->echoBack($input);
        $this->assertSame($input, $got);
    }

    public function testShouldReturnServerReport()
    {
        $got = $this->_kt->report();
        $this->assertTrue(count($got) > 0);
    }

    public function testShouldReturnServerStatus()
    {
        $got = $this->_kt->status();
        $this->assertTrue(isset($got['count']));
        $this->assertTrue(isset($got['size']));
    }

    public function testShouldRunSynchronize()
    {
        $got = $this->_kt->synchronize();
        $this->assertTrue($got);
    }

    public function testShouldRunVacuum()
    {
        $this->_kt->vacuum();
        $got = $this->_kt->vacuum(1);
        $this->assertTrue($got instanceof KyotoTycoon);
    }

    public function testShouldClearDatabase()
    {
        $this->_kt->set('clr_1', 'foo');
        $this->assertSame($this->_kt->get('clr_1'), 'foo');
        $this->_kt->clear();
        $this->assertSame($this->_kt->get('clr_1'), null);
    }
}
