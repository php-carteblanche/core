<?php
/**
 * This file is part of the CarteBlanche PHP framework.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * License Apache-2.0 <http://github.com/php-carteblanche/carteblanche/blob/master/LICENSE>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche\Interfaces;

/**
 * This is the classic CarteBlanche's configuration interface
 *
 * @author      Piero Wbmstr <me@e-piwi.fr>
 */
interface ConfigInterface
{

    /**
     * Load and parse a configuration file
     *
     * @param   string          $filename
     * @param   bool            $merge_globals
     * @param   null/string     $stack_name
     * @param   null/string     $handler    A classname to parse concerned config content
     */
    public function load($filename, $merge_globals = true, $stack_name = null, $handler = null);

    /**
     * Parse and set a configuration array
     *
     * @param   array           $config
     * @param   bool            $merge_globals
     * @param   null|string     $stack_name
     */
    public function set(array $config, $merge_globals = true, $stack_name = null);

    /**
     * Get a configuration stack or entry
     *
     * @param   string      $index
     * @param   int         $flag
     * @param   null/mixed  $default
     * @param   string      $stack_name
     */
    public function get($index, $flag = self::NOT_FOUND_GRACEFULLY, $default = null, $stack_name = 'global');

    /**
     * Get the full configuration array
     *
     * @return  array
     */
    public function dump();

}

// Endfile