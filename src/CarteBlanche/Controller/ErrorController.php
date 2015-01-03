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

namespace CarteBlanche\Controller;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\Abstracts\AbstractController;

/**
 * Errors controller extending the abstract \CarteBlanche\Abstracts\AbstractController class
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
 */
class ErrorController
    extends AbstractController
{

    /**
     * The directory where to search the views files
     */
    static $views_dir = 'error/';

    /**
     * @see self::back_home()
     */
    public function indexAction($id = null)
    {
        return self::back_home();
    }

    /**
     * @see self::back_home()
     */
    public function emptyAction($altdb = null)
    {
        return self::back_home();
    }

    /**
     * Back to the homepage of the application
     */
    public function back_home()
    {
        $this->getContainer()->get('router')->redirect();
    }

    /**
     * Send a mail report to the website contact
     */
    public function reportAction($code = null)
    {
        $referer = $this->getContainer()->get('router')->getReferer();
        $txt_message = $this->view(
            'error_mail_content',
            array(
                'url'=>$referer,
                'code'=>$code
            )
        );

        $webmaster_email = CarteBlanche::getConfig('app.webmaster_email', null);
        $webmaster_name = CarteBlanche::getConfig('app.webmaster_name', null);
        $mail = new \MimeEmail\Lib\MimeEmail();
        $mail
            ->setTo($webmaster_email, $webmaster_name)
            ->setSubject($this->trans('Error report'))
            ->setText($txt_message);
        $ok = $mail->send(true);
        if ($ok) {
            $this->getContainer()->get('session')
                ->setFlash("info:".$this->trans('OK - A report has been sent concerning your error. Thank you.'));
        } else {
            $this->getContainer()->get('session')
                ->setFlash("error:".$this->trans('ERROR - An error occured while trying to send email ...'));
        }
        $this->getContainer()->get('router')->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * The "not found" error page
     *
     * @return string The 403 forbidden error page view content
     */
    public function error403Action()
    {
        $this->getContainer()->get('router')->setReferer();
        $_args=array(
            'offset'=>null,'limit'=>null,'table'=>null,'altdb'=>null,
            'orderby'=>null, 'orderway'=>null
        );
        $url_args = CarteBlanche::getConfig('routing.arguments_mapping');
        foreach ($_args as $_arg_var=>$_arg_val) {
            if (!empty($_arg_val)) {
                if (in_array($_arg_var, $url_args))
                    $args[ array_search($_arg_var, $url_args) ] = $_arg_val;
                else
                    $args[ $_arg_var ] = $_arg_val;
            }
        }
        $searchbox = new \Tool\SearchBox(array(
            'hiddens'=>$_args, 'advanced_search'=>true
        ));
        $ctt = (string) $searchbox;

        $_f = CarteBlanche::getContainer()->get('locator')->locateView(self::$views_dir.'403.md');
        $_txt = new \Tool\Text(array(
//            'original_str'=>file_get_contents(CarteBlanche::getPath('root_path')._CarteBlanche_DIR._VIEWSDIR.self::$views_dir.'404.md'),
            'original_str'=>file_get_contents($_f),
            'markdown'=>true,
        ));
        $ctt .= $_txt;

        $params = array(
            'title'=>$this->trans('OOPS ! You can\'t access this page !!'),
            'content'=>$ctt,
            'sitemap_url'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'cms','action'=>'sitemap'
            )),
            'report_error'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'error','action'=>'report','code'=>'403'
            ))
        );
        $params['output'] = CarteBlanche::getContainer()->get('front_controller')
            ->view(self::$views_dir.'error', $params);
        return CarteBlanche::getContainer()->get('front_controller')->render($params);
    }

    /**
     * The "not found" error page
     *
     * @return string The 404 error page view content
     */
    public function error404Action()
    {
        $this->getContainer()->get('router')->setReferer();
        $_args=array(
            'offset'=>null,'limit'=>null,'table'=>null,'altdb'=>null,
            'orderby'=>null, 'orderway'=>null
        );
        $url_args = CarteBlanche::getConfig('routing.arguments_mapping');
        foreach ($_args as $_arg_var=>$_arg_val) {
            if (!empty($_arg_val)) {
                if (in_array($_arg_var, $url_args))
                    $args[ array_search($_arg_var, $url_args) ] = $_arg_val;
                else
                    $args[ $_arg_var ] = $_arg_val;
            }
        }
        $searchbox = new \Tool\SearchBox(array(
            'hiddens'=>$_args, 'advanced_search'=>true
        ));
        $ctt = (string) $searchbox;

        $_f = CarteBlanche::getContainer()->get('locator')->locateView(self::$views_dir.'404.md');
        $_txt = new \Tool\Text(array(
//            'original_str'=>file_get_contents(CarteBlanche::getPath('root_path')._CarteBlanche_DIR._VIEWSDIR.self::$views_dir.'404.md'),
            'original_str'=>file_get_contents($_f),
            'markdown'=>true,
        ));
        $ctt .= $_txt;

        $params = array(
            'title'=>$this->trans('OOPS ! The page was not found !!'),
            'content'=>$ctt,
            'sitemap_url'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'cms','action'=>'sitemap'
            )),
            'report_error'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'error','action'=>'report','code'=>'404'
            ))
        );
        $params['output'] = CarteBlanche::getContainer()->get('front_controller')
            ->view(self::$views_dir.'error', $params);
        return CarteBlanche::getContainer()->get('front_controller')->render($params);
    }

    /**
     * The "internal server error" page
     *
     * @return string The 500 error page view content
     */
    public function error500Action()
    {
        $this->getContainer()->get('router')->setReferer();
        $_args=array(
            'offset'=>null,'limit'=>null,'table'=>null,'altdb'=>null,
            'orderby'=>null, 'orderway'=>null
        );
        $url_args = CarteBlanche::getConfig('routing.arguments_mapping');
        foreach ($_args as $_arg_var=>$_arg_val) {
            if (!empty($_arg_val)) {
                if (in_array($_arg_var, $url_args))
                    $args[ array_search($_arg_var, $url_args) ] = $_arg_val;
                else
                    $args[ $_arg_var ] = $_arg_val;
            }
        }
        $searchbox = new \Tool\SearchBox(array(
            'hiddens'=>$_args, 'advanced_search'=>true
        ));
        $ctt = (string) $searchbox;

        $_f = CarteBlanche::getContainer()->get('locator')->locateView(self::$views_dir.'500.md');
        $_txt = new \Tool\Text(array(
//            'original_str'=>file_get_contents(CarteBlanche::getPath('root_path')._CarteBlanche_DIR._VIEWSDIR.self::$views_dir.'500.md'),
            'original_str'=>file_get_contents($_f),
            'markdown'=>true,
        ));
        $ctt .= $_txt;

        $params = array(
            'title'=>$this->trans('OOPS ! An error occurred !!'),
            'content'=>$ctt,
            'sitemap_url'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'cms','action'=>'sitemap'
            )),
            'report_error'=>CarteBlanche::getContainer()->get('router')->buildUrl(array(
                'controller'=>'error','action'=>'report','code'=>'500'
            ))
        );
        $params['output'] = CarteBlanche::getContainer()->get('front_controller')
            ->view(self::$views_dir.'error', $params);
        return CarteBlanche::getContainer()->get('front_controller')->render($params);
    }

}

// Endfile