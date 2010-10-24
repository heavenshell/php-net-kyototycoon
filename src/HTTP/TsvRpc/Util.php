<?php
/**
 * TSV-RPC util.
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
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Parser.pm
 */

namespace HTTP\TsvRpc;
use HTTP\TsvRpc;

/**
 * TSV-RPC util.
 *
 * <pre>
 *   This module is port from Perl TSVRPC::Parser
 *   See also @link.
 * </pre>
 *
 * @use       HTTP\TsvRpc
 * @category  HTTP
 * @package   HTTP\TsvRpc
 * @version   $id$
 * @copyright (c) 2010 Shinya Ohyanagi
 * @author    Shinya Ohyanagi <sohyanagi@gmail.com>
 * @license   New BSD License
 * @link      http://search.cpan.org/~tokuhirom/Cache-KyotoTycoon/lib/TSVRPC/Parser.pm
 */
class Util
{
    /**
     * Parse content-type.
     *
     * @param  mixed $contentType Content-type
     * @access public
     * @return mixed B|U|Q|'' or null
     */
    public static function parseContentType($contentType)
    {
        $pattern = '#text/tab-separated-values(?:; colenc=([BUQ]))?#';
        if (preg_match_all($pattern, $contentType, $match)) {
            return isset($match[1][0]) ? $match[1][0] : '';
        }

        return null;
    }
}
