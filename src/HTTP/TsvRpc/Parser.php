<?php
/**
 * TSV-RPC parser.
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
 * TSV-RPC parser.
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
class Parser
{
    /**
     * Encode plain array to TSV with $encoding.
     *
     * @param  array $data Array data to encode
     * @param  mixed $encoding Encode type
     * @access public
     * @return String Encoded string
     */
    public function encodeTsvrpc(array $data, $encoding = null)
    {
        $encoders = array(
            'U' => function($value) { return urlencode($value); },
            'Q' => function($value) { return quoted_printable_encode($value); },
            'B' => function($value) { return base64_encode($value); }
        );

        if (isset($encoders[$encoding])) {
            $encoder = $encoders[$encoding];
        } else {
            // When $encoding is not in $encoders, nothing to do.
            $encoder = function($value) { return $value; };
        }
        $res = array();
        foreach ($data as $k => $v) {
            $res[] = $encoder($k) . "\t" . $encoder($v);
        }

        return implode("\n", $res);
    }

    /**
     * Decode TSV to plain array.
     *
     * @param  mixed $data String data to decode
     * @param  mixed $encoding Encode type
     * @access public
     * @return array Decoded data
     */
    public function decodeTsvrpc($data, $encoding = null)
    {
        $decoders = array(
            'U' => function($value) { return urldecode($value); },
            'Q' => function($value) { return quoted_printable_decode($value); },
            'B' => function($value) { return base64_decode($value); }
        );

        if (isset($decoders[$encoding])) {
            $decoder = $decoders[$encoding];
        } else {
            // When $encoding is not in $decoders, nothing to do.
            $decoder = function($value) { return $value; };
        }
        $res = array();
        if ($data === '') {
            return $res;
        }

        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            list($k, $v) = array_map($decoder, explode("\t", $line));
            $res[$k] = $v;
        }

        return $res;
    }
}
