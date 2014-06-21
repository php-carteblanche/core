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
     * @return  \CarteBlanche\App\Container singleton instance
     */
    public static function getContainer()
    {
        return \CarteBlanche\App\Container::getInstance();
    }

    /**
     * @return  \CarteBlanche\App\Kernel instance
     */
    public static function getKernel()
    {
        return self::getContainer()->get('kernel');
    }

    /**
     * @alias   \CarteBlanche\App\Config::getConfig()
     */
    public static function getConfig($var, $default = null, $app_only = false)
    {
        return self::getContainer()->get('config')
            ->get($var, \CarteBlanche\App\Config::NOT_FOUND_GRACEFULLY, $default);
    }

    /**
     * @alias \CarteBlanche\App\Kernel::addPath(xxx, val, bool, bool)
     */
    public static function addPath($name, $value, $must_exists = false, $must_be_writable = false)
    {
        return self::getContainer()->get('kernel')
            ->addPath($name, $value, $must_exists, $must_be_writable);
    }

    /**
     * @alias \CarteBlanche\App\Kernel::getPath(xxx)
     */
    public function getPath($name)
    {
        return self::getContainer()->get('kernel')
            ->getPath($name);
    }

    /**
     * @alias   \CarteBlanche\App\Config::getPath(xxx, true)
     */
    public function getFullPath($name)
    {
        return self::getContainer()->get('kernel')
            ->getPath($name, true);
    }

    /**
     * @param   string  $config
     * @alias   \CarteBlanche\App\Kernel::getMode($config)
     */
    public static function getKernelMode($config = 'dev')
    {
        return self::getContainer()->get('kernel')
            ->getMode($config);
    }

    /**
     * @alias \CarteBlanche\App\Logger::log()
     */
    public static function log($message, $level = \Library\Logger::DEBUG, array $context = array(), $logname = null)
    {
        $logger = self::getContainer()->get('logger');
        if (!empty($logger)) {
            return $logger->log($level, $message, $context, $logname);
        }
        return null;
    }

    /**
     * @return  string
     * @alias   \Locale::getDefault()
     */
    public static function getlocale()
    {
        return \Locale::getDefault();
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
     * @param   string/DateTime     $arg1
     * @param   string/int          $arg2
     * @param   array/string        $arg3
     * @param   string              $arg4
     * @param   string              $arg5
     * @return  mixed
     * @alias   \I18n\I18n::translate($index, $args, $lang)
     * @alias   \I18n\I18n::pluralize($indexes, $number, $args, $lang)
     * @alias   \I18n\I18n::getLocalizedDateString($date, $mask, $charset, $lang)
     * @alias   \I18n\I18n::getLocalizedNumberString($number, $lang)
     * @alias   \I18n\I18n::getLocalizedPriceString($number, $lang)
     */
    public static function trans($arg1, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        $i18n = self::getContainer()->get('i18n');
        if (is_object($arg1) && ($arg1 instanceof \DateTime)) {
            if (empty($i18n)) {
                return $arg1->format( !empty($arg2) ? $arg2 : 'Y-m-d H:i:s');
            }
            return \I18n\I18n::getLocalizedDateString($arg1, $arg2, $arg3, $arg4);
        } elseif (is_array($arg1)) {
            if (empty($i18n)) {
                return isset($arg1[$arg2]) ? $arg1[$arg2] : array_shift($arg1);
            }
            return \I18n\I18n::pluralize($arg1, $arg2, $arg3, $arg4);
        } elseif (is_string($arg1)) {
            if ($arg1==='number') {
                if (empty($i18n)) {
                    return $arg2;
                }
                return \I18n\I18n::getLocalizedNumberString($arg2, $arg3);
            } elseif($arg1==='price') {
                if (empty($i18n)) {
                    return $arg2;
                }
                return \I18n\I18n::getLocalizedPriceString($arg2, $arg3);
            } else {
                if (empty($i18n)) {
                    return $arg1;
                }
                return \I18n\I18n::translate($arg1, $arg2, $arg3);
            }
        } else {
            return null;
        }
    }

    /**
     * @alias \CarteBlanche\App\FrontController::view($view, $params, $display, $exit)
     */
    public static function view($view = null, $params = null, $display = false, $exit = false)
    {
        return self::getContainer()->get('front_controller')
            ->view($view, $params, $display, $exit);
    }

    /**
     * @alias \CarteBlanche\App\Router::buildUrl($param, $value, $separator)
     */
    public static function buildUrl($param = null, $value = null, $separator = '&amp;')
    {
        return self::getContainer()->get('router')
            ->buildUrl($param, $value, $separator);
    }

    /**
     * @alias \Library\Helper\Url::getRequestUrl($entities, $base, $no_file)
     */
    public static function currentUrl($entities = false, $base = false, $no_file = false)
    {
        return \Library\Helper\Url::getRequestUrl($entities, $base, $no_file);
    }

    /**
     * Full `\CarteBlanche\App\Locator` handler
     *
     * @param   string          $filename
     * @param   null|string     $type   'data', 'config', 'language' or 'i18n', 'view', 'controller'
     * @alias   \CarteBlanche\App\Locator::locate($filename)
     */
    public static function locate($filename, $type = null)
    {
        $locator = self::getContainer()->get('locator');
        switch ($type) {
            case 'data':
                return $locator->locateData($filename);
                break;
            case 'config':
                return $locator->locateConfig($filename, true);
                break;
            case 'language': case 'i18n':
                return $locator->locateLanguage($filename, true);
                break;
            case 'view':
                return $locator->locateView($filename, true);
                break;
            case 'controller':
                return $locator->locateController($filename);
                break;
            default:
                return $locator->locate($filename);
                break;
        }
    }

}

/*
\CarteBlanche::log(\App\Logger::DEBUG, 'my message');
*/

// Endfile