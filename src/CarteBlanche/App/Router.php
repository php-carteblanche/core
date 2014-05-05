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

namespace CarteBlanche\App;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\Request;
use \Library\Router as BaseRouter;
use \Library\Helper\Url as UrlHelper;
use \Library\Helper\Text as TextHelper;
use \Patterns\Interfaces\RouterInterface;
use \Patterns\Commons\Collection;

/**
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class Router
    extends BaseRouter
    implements RouterInterface
{

    /**
     * Construction
     *
     * @param   string          $route
     * @param   array/object    $routes_table
     * @param   array/object    $arguments_table
     * @param   array/object    $matchers_table
     */
    public function __construct(
        $route = null, array $routes_table = array(), array $arguments_table = array(), array $matchers_table = array()
    ) {
        $routes_table = array_merge($routes_table, CarteBlanche::getConfig('routing.routes', array()));
        $arguments_table = array_merge($arguments_table, CarteBlanche::getConfig('routing.arguments_mapping', array()));
        $matchers_table = array_merge($matchers_table, CarteBlanche::getConfig('routing.matchers', array()));
        parent::__construct($route, $routes_table, $arguments_table, $matchers_table);
    }

    /**
     * Store the referer URL in session
     *
     * @param   string  $uri    The referer URI to store
     * @return  bool
     */
    public static function setReferer($uri = null)
    {
        if (empty($uri)) $uri = UrlHelper::getRequestUrl();
        CarteBlanche::getContainer()->get('session')->set('referer', $uri);
        return true;
    }

    /**
     * Get the referer URL in session
     *
     * @return  string  The referer value in session
     */
    public static function getReferer()
    {
        return (CarteBlanche::getContainer()->get('session')->has('referer') ?
            CarteBlanche::getContainer()->get('session')->get('referer') : null);
    }

    /**
     * Build an application URL with a set of arguments
     *
     * The class will pass arguments values to any `$this->toUrlParam($value)` method for the
     * parameter named `param`.
     *
     * @param   string/array    $param      The parameter name if it's single, an array of parameters and their values otherwise
     * @param   string          $value      The value for the parameter if it's single
     * @param   string          $separator  The argument/value separator (default is escaped ampersand : '&amp;')
     * @return  string          The URL built
     */
    public function buildUrl($param = null, $value = null, $separator = '&amp;')
    {
        if (!is_array($param) && !empty($value)) {
            $param = array( $param=>$value );
        }

        $default_args = array(
            'controller' => CarteBlanche::getConfig('routing.mvc.default_controller'),
            'action' => CarteBlanche::getConfig('routing.mvc.default_action')
        );
        if (!empty($param)) {
            foreach ($param as $_var=>$_val) {
                if (!empty($_val)) {
                    $_meth = 'fromUrl'.TextHelper::toCamelCase($_var);
                    if (method_exists($this, $_meth)) {
                        $_val = call_user_func_array(array($this, $_meth), array($_val));
                    }
                    if (array_key_exists($_var, $default_args) && (
                        $_val===$default_args[$_var] || strtolower($_val)===$default_args[$_var] || parent::urlEncode($_val)===$default_args[$_var]
                    )) {
                        unset($param[$_var]);
                    }
                }
            }
        }

        return parent::generateUrl($param, CarteBlanche::getPath('root_file'), '', $separator);
    }

    /**
     * Make a redirection to a new route (HTTP redirect)
     *
     * @param   mixed   $pathinfo   The path information to redirect to
     * @param   string  $hash       A hash tag to add to the generated URL
     * @param   bool    $force_not_referer  By default, this function will redirect to the referer, otherwise this is TRUE
     * @return  void
     */
    public function redirect($pathinfo = '', $hash = null, $force_not_referer = false)
    {
        if (empty($pathinfo)) {
            $referer_uri = self::getReferer();
            if (!empty($referer_uri) && $force_not_referer!==true) {
                $pathinfo = $referer_uri;
            } else {
                $pathinfo = CarteBlanche::getPath('root_file');
            }
        } elseif (is_array($pathinfo)) {
            $pathinfo = $this->buildUrl($pathinfo, null, '&');
        }
        parent::redirect($pathinfo, $hash);
    }

    /**
     * Forward the application to a new route (no HTTP redirect)
     *
     * @param   mixed   $pathinfo   The path information to forward to
     * @param   string  $hash       A hash tag to add to the generated URL
     * @return  void
     */
    public function forward($pathinfo, $hash = null)
    {
    }

    /**
     *
     */
    public static function toUrlController($value)
    {
        return strtolower(str_replace('Controller', '', $value));
    }

    /**
     *
     */
    public static function fromUrlController($value)
    {
        return TextHelper::toCamelCase($value).'Controller';
    }

    /**
     * Route parser : load and parse the current route
     */
    protected function _parseRoute()
    {
        $route_rule = $this->matchUrl($this->getRoute());
        $route = array();
        if (!empty($route_rule)) {
            if (strpos($route_rule, ':')) {
                $parts = explode(':', $route_rule);
                if (count($parts)>2) {
                    $route = array(
                        'bundle' => $parts[0],
                        'controller' => $parts[1],
                        'action' => $parts[2],
                    );
                } elseif (count($parts)===2) {
                    $route = array(
                        'controller' => $parts[0],
                        'action' => $parts[1],
                    );
                } else {
                    $route = array(
                        'controller' => $parts[0],
                    );
                }
            }
        }
        $this->setRouteParsed($route);
    }

}

// Endfile