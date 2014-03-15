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

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche,
    \CarteBlanche\App\Container,
    \CarteBlanche\Exception\NotFoundException,
    \CarteBlanche\Interfaces\FrontControllerInterface;

use \Library\Command,
    \Library\Helper\Directory as DirectoryHelper,
    \Library\Helper\Text as TextHelper,
    \Library\Helper\Html as HtmlHelper,
    \Library\Helper\Code as CodeHelper,
    \Library\Helper\Url as UrlHelper;

use \DateTime,
    \ReflectionMethod;

/**
 */
class Helper
{

    public static function getSecuredRealpath($path)
    {
        return str_replace(CarteBlanche::getPath('root_path'), '/[***]', $path);
    }

    public static function getRelativePath($path)
    {
        return str_replace(CarteBlanche::getPath('web_path'), '', $path);
    }

    public static function getAbsolutePath($path)
    {
        return CarteBlanche::getPath('web_path').str_replace(CarteBlanche::getPath('web_path'), '', $path);
    }

    public static function getDateTimeFromTimestamp($timestamp)
    {
        $time = new DateTime;
        $time->setTimestamp( $timestamp );
        return $time;
    }

    public static function getDirectorySize($path)
    {
        $docbook = CarteBlanche::getContainer()->get('front_controller');
        CarteBlanche::getContainer()->get('front_controller');
        $tmp = DirectoryHelper::slashDirname(CarteBlanche::getPath('var_path'));

        $du_cmd = Command::getCommandPath('du');
        $grep_cmd = Command::getCommandPath('grep');
        $command = $du_cmd.' -cLak '.$path.' | '.$grep_cmd.' total';
        list($stdout, $status, $stderr) = CarteBlanche::getContainer()->get('terminal')->run($command);

        if ($stdout && !$status) {
            $result = explode(' ', $stdout);
            return (1024*array_shift($result));
        }
        return 0;
    }

    public static function getProfiler()
    {
        $docbook = CarteBlanche::getContainer()->get('front_controller');
        return array(
            'date'              => new DateTime(),
            'timezone'          => date_default_timezone_get(),
			'php_uname'         => php_uname(),
			'php_version'       => phpversion(),
			'php_sapi_name'     => php_sapi_name(),
            'sqlite_version'    => sqlite_libversion(),
			'server_version'    => 
			    function_exists('apache_get_version') ? apache_get_version() : null,
			'user_agent'        => $_SERVER['HTTP_USER_AGENT'],
			'git_clone'         => DirectoryHelper::isGitClone(CarteBlanche::getPath('root_path')),
			'request'           => UrlHelper::getRequestUrl(),
        );
    }

    public static function isHome($route)
    {
        if (
            empty($route) ||
            $route==='/' ||
            $route===CarteBlanche::getPath('web_path')
        ) {
            return true;
        }
        return false;
    }

    public static function buildPageTitle($str)
    {
        $name = CarteBlanche::getConfig('app.name');
        return strip_tags($str.(!empty($name) ? ' - '.$name : ''));
    }

}

// Endfile
