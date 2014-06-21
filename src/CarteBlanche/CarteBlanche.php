<?php
/**
 * CarteBlanche - PHP framework package
 * (c) Pierre Cassat and contributors
 * 
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * License Apache-2.0
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CarteBlanche;

/**
 * @author      Piero Wbmstr <me@e-piwi.fr>
 */
class CarteBlanche
{

    /**
     * @return \CarteBlanche\App\Container singleton instance
     */
    public static function getContainer()
    {
        return \CarteBlanche\App\Container::getInstance();
    }

    /**
     * @return \CarteBlanche\App\Kernel instance
     */
    public static function getKernel()
    {
        return \CarteBlanche\App\Container::getInstance()->get('kernel');
    }

    /**
     * @alias \CarteBlanche\App\Config::getConfig()
     */
    public static function getConfig($var, $default = null, $app_only = false)
    {
        return \CarteBlanche\App\Container::getInstance()->get('config')
           ->get($var, \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, $default);
    }

    /**
     * @alias \CarteBlanche\App\Config::addPath()
     */
    public static function addPath($name, $value, $must_exists = false, $must_be_writable = false)
    {
        return \CarteBlanche\App\Container::getInstance()->get('kernel')
           ->addPath($name, $value, $must_exists, $must_be_writable);
    }

    /**
     * @alias \CarteBlanche\App\Config::getPath()
     */
    public function getPath($name)
    {
        return \CarteBlanche\App\Container::getInstance()->get('kernel')->getPath($name);
    }

    /**
     * @alias \CarteBlanche\App\Config::getPath(xxx, true)
     */
    public function getFullPath($name)
    {
        return \CarteBlanche\App\Container::getInstance()->get('kernel')->getPath($name, true);
    }

    /**
     * @alias \CarteBlanche\App\Logger::log()
     */
    public static function log($message, $level = \Library\Logger::DEBUG, array $context = array(), $logname = null)
    {
        $logger = \CarteBlanche\App\Container::getInstance()->get('logger');
        if (!empty($logger)) {
            return $logger->log($level, $message, $context, $logname);
        }
        return null;
    }

    /**
     * @alias \Locale::getDefault()
     */
    public static function getlocale()
    {
        return \Locale::getDefault();
    }

    /**
     * @alias \CarteBlanche\App\Kernel::getMode($config)
     */
    public static function getKernelMode($config = 'dev')
    {
        return \CarteBlanche\App\Container::getInstance()->get('kernel')->getMode($config);
    }

    /**
     * Full aliasing of internationalization
     *
     * - will `datify` if `$arg1` is a `DateTime`:
     *      `\I18n\I18n::getLocalizedDateString( date: $arg1 = DateTime , [mask: $arg2 = null] , [charset: $arg3 = 'UTF-8'] , [lang: $arg4 = null] )`
     * - will `pluralize` if `$arg1` is an array:
     *      `\I18n\I18n::pluralize( indexes: $arg1 = array , [count: $arg2 = 0] , [args: $arg3 = array] , [lang: $arg4 = null] )`
     * - will `numberify` if `$arg1`=='number':
     *      `\I18n\I18n::getLocalizedNumberString( number: $arg2 = float , [lang: $arg3 = null] )`
     * - will `currencify` if `$arg1`=='price':
     *      `\I18n\I18n::getLocalizedPriceString( price: $arg2 = float , [lang: $arg3 = null] )`
     * - will `translate` if `$arg1` is a string and not 'number' or 'price':
     *      `\I18n\I18n::translate( index: $arg1 = string, [params: $arg2 = array] , [lang: $arg3 = null] )`
     *
     * @alias \I18n\I18n::translate($index, $args, $lang)
     * @alias \I18n\I18n::pluralize($indexes, $number, $args, $lang)
     * @alias \I18n\I18n::getLocalizedDateString($date, $mask, $charset, $lang)
     * @alias \I18n\I18n::getLocalizedNumberString($number, $lang)
     * @alias \I18n\I18n::getLocalizedPriceString($number, $lang)
     */
    public static function trans($arg1, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        if (is_object($arg1) && ($arg1 instanceof \DateTime)) {
            return \I18n\I18n::getLocalizedDateString($arg1, $arg2, $arg3, $arg4);
        } elseif (is_array($arg1)) {
            return \I18n\I18n::pluralize($arg1, $arg2, $arg3, $arg4);
        } elseif (is_string($arg1)) {
            if ($arg1==='number') {
                return \I18n\I18n::getLocalizedNumberString($arg2, $arg3);
            } elseif($arg1==='price') {
                return \I18n\I18n::getLocalizedPriceString($arg2, $arg3);
            } else {
                return \I18n\I18n::translate($arg1, $arg2, $arg3);
            }
        } else {
            return null;
        }
    }

}

/*
\CarteBlanche::log(\App\Logger::DEBUG, 'my message');
*/

// Endfile