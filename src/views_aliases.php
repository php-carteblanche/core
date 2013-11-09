<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * Common functions aliases
 *
 */

use \CarteBlanche\CarteBlanche;
use Library\Helper, Library\Tool;

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
		return \Model\AbstractModel::getNow();
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

// Endfile