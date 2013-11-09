<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\App;

use \Library\Converter\Html2Text;

use DevDebug\Debugger as BaseDebugger;

/**
 * The global application debugger singleton instance
 */
class Debugger extends BaseDebugger
{

	protected static $shutdown=false;

	public static function shutdown( $exit=false, $callback=null )
	{
    	$_this =& self::getInstance();
		if (self::$shutdown===true) return true;
		$_this->checkUri();

    	if (!empty($_this->messages)) 
    	{
			Debugger::$shutdown=true;

			if (!empty($callback) && is_callable($callback) &&
				(empty($_this->format) && empty($_this->mailto))
			) {
				$params = func_get_args();
				array_shift($params);
				array_shift($params);
				array_push($params, $_this);
				call_user_func_array($callback, $params);
			} else {
				$dbg_str = '<h1>'.$_this->profiler->renderProfilingTitle().'</h1>'.'<p>'.$_this->profiler->renderProfilingInfo().'</p>';
				if (!empty($_this->format)) {
					$dbg_str .= $_this->render($_this->format);
				} else {
					$dbg_str .= $_this->render();
				}
				if (!empty($_this->format)) {
					$_this->renderFormatedString( $dbg_str, $_this->format );
				} elseif (!empty($_this->mailto)) {
					$_this->sendByEmail( $dbg_str, $_this->mailto );
					$_this->setMailto(null);
					return self::shutdown( $exit, $callback );
				} else {
					if (class_exists('\App\Response'))
					{
						$_res = new \App\Response;
						if (php_sapi_name() == 'cli') $_res->setContentType('text');
						$_res->addContent(null, $dbg_str);
						return $_res->send();
					} else {
						echo $dbg_str;
					}
				}
			}
	
			if ($exit===true) exit;
		}
		return true;
	}

}

// Endfile