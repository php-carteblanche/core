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
 *
 * Common functions aliases
 *
 */

use \CarteBlanche\CarteBlanche;
use \Library\Helper;
use \Library\Tool;

if (!function_exists('view')) 
{
    function view($view = null, $params = null, $display = false, $exit = false)
    {
        $ctt = \CarteBlanche\App\FrontController::getInstance()->view($view, $params, $display, $exit);
        if (!empty($ctt) && true!==$display) {
            _echo($ctt);
            return;
        }
        return $ctt;
    }
}

if (!function_exists('get_path')) 
{
    function get_path($name)
    {
        return CarteBlanche::getPath($name);
    }
}

if (!function_exists('get_now')) 
{
    function get_now()
    {
        return \CarteBlanche\Model\AbstractModel::getNow();
    }
}

if (!function_exists('_urlencode')) 
{
    function _urlencode( $str=null )
    {
        return \CarteBlanche\App\Router::urlEncode($str);
    }
}

if (!function_exists('fetch_arguments')) 
{
    function fetch_arguments( $_class=null, $_method=null, $args=null )
    {
        return \Library\Helper\Code::fetchArguments($_method, $args, $_class);
    }
}

if (!function_exists('build_url')) 
{
    function build_url( $param=null, $value=null, $separator='&amp;' )
    {
        return CarteBlanche::getContainer()->get('router')->buildUrl($param, $value, $separator);
    }
}

if (!function_exists('current_url')) 
{
    function current_url( $entities=false, $base=false, $no_file=false )
    {
        return Helper\Url::getRequestUrl($entities, $base, $no_file);
    }
}

if (!function_exists('url_parser')) 
{
    function url_parser($url)
    {
        return Helper\Url::parse($url);
    }
}

if (!function_exists('is_url')) 
{
    function is_url($url=null, $protocols=array('http','https','ftp'), $localhost=false)
    {
        return Helper\Url::isUrl($url, $protocols, $localhost);
    }
}

if (!function_exists('is_email')) 
{
    function is_email($email=null)
    {
        return Helper\Url::isEmail($email);
    }
}

if (!function_exists('_trans')) 
{
    function _trans()
    {
        return _echo(
            call_user_func_array(array('\CarteBlanche\CarteBlanche', 'trans'), func_get_args())
        );
    }
}

if (!function_exists('_pluralize')) 
{
    function _pluralize()
    {
        return _echo(
            call_user_func_array(array('\CarteBlanche\CarteBlanche', 'trans'), func_get_args())
        );
    }
}

if (!function_exists('_javascriptize')) 
{
    function _javascriptize($str = '', $protect_quotes = false)
    {
        return _echo(
            \Library\Helper\HTML::javascriptProtect($str, $protect_quotes)
        );
    }
}

if (!function_exists('_trans_js')) 
{
    function _trans_js()
    {
        return _javascriptize(
            call_user_func_array(array('\CarteBlanche\CarteBlanche', 'trans'), func_get_args())
        );
    }
}

// Endfile