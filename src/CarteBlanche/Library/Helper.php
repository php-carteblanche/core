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

namespace CarteBlanche\Library;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Container;
use \CarteBlanche\Exception\NotFoundException;
use \CarteBlanche\Interfaces\FrontControllerInterface;
use \Library\Command;
use \Library\Helper\Directory as DirectoryHelper;
use \Library\Helper\Text as TextHelper;
use \Library\Helper\Html as HtmlHelper;
use \Library\Helper\Code as CodeHelper;
use \Library\Helper\Url as UrlHelper;
use \DateTime;
use \ReflectionMethod;

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
        return array(
            'date'              => new DateTime(),
            'timezone'          => date_default_timezone_get(),
            'php_uname'         => php_uname(),
            'php_version'       => phpversion(),
            'php_sapi_name'     => php_sapi_name(),
            'sqlite_version'    => function_exists('sqlite_libversion') ?
                sqlite_libversion() : (class_exists('SQLite3') ? \SQLite3::version() : null),
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
