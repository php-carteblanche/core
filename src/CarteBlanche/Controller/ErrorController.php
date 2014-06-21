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
     * @param   null/string/int  $id
     * @see     self::back_home()
     */
    public function indexAction($id = null)
    {
        if (!empty($id)) {
            $_meth = 'error'.$id.'Action';
            if (method_exists($this, $_meth)) {
                return $this->{$_meth}();
            }
        }
        return self::back_home();
    }

    /**
     * @param   null/string/int  $id
     * @see     self::back_home()
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
     * Send an email report to the website contact
     *
     * @param   int $code
     * @return  void
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
        $mail = new \MimeMailer\MimeMessage();
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
        $searchbox = class_exists('\Tool\SearchBox') ? new \Tool\SearchBox(array(
            'hiddens'=>$_args, 'advanced_search'=>true
        )) : '';
        $ctt = (string) $searchbox;

        $_f = CarteBlanche::getContainer()->get('locator')->locateView(self::$views_dir.'403.md');
        $_txt = class_exists('\Tool\Text') ? new \Tool\Text(array(
//          'original_str'=>file_get_contents(CarteBlanche::getPath('root_path')._CarteBlanche_DIR._VIEWSDIR.self::$views_dir.'500.md'),
            'original_str'=>file_get_contents($_f),
            'markdown'=>true,
        )) : file_get_contents($_f);
        $ctt .= (string) $_txt;

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
        $ctt = $this->_getErrorPageContent(404);
        $this->getContainer()->get('router')->setReferer();

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
        $searchbox = class_exists('\Tool\SearchBox') ? new \Tool\SearchBox(array(
            'hiddens'=>$_args, 'advanced_search'=>true
        )) : '';
        $ctt = (string) $searchbox;

        $_f = CarteBlanche::getContainer()->get('locator')->locateView(self::$views_dir.'500.md');
        $_txt = class_exists('\Tool\Text') ? new \Tool\Text(array(
//          'original_str'=>file_get_contents(CarteBlanche::getPath('root_path')._CarteBlanche_DIR._VIEWSDIR.self::$views_dir.'500.md'),
            'original_str'=>file_get_contents($_f),
            'markdown'=>true,
        )) : file_get_contents($_f);
        $ctt .= (string) $_txt;

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

    /**
     * @param   int     $code
     * @param   string  $message
     * @return  string
     */
    protected function _getErrorPageContent($code = 404, $message = '')
    {
        $ctt            = '';
        $source_file    = CarteBlanche::getContainer()->get('locator')
            ->locateView(self::$views_dir.$code.'.md');
        $source_ctt     = file_exists($source_file) ? file_get_contents($source_file) : $message;

        if (class_exists('\Tool\SearchBox')) {
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
            $ctt .= (string) $searchbox;
        }

        if (class_exists('\Tool\Text')) {
            $_txt = new \Tool\Text(array(
                'original_str'=>$source_ctt,
                'markdown'=>true,
            ));
            $ctt .= $_txt;
       } elseif (class_exists('\MarkdownExtended\MarkdownExtended')) {
            $content = \MarkdownExtended\MarkdownExtended::create()
                ->get('Parser')
                ->parse( new \MarkdownExtended\Content($source) )
                ->getContent();
           $ctt .= $content->getBody();
        } else {
            $ctt .= $source_ctt;
        }

        return $ctt;
    }

}

// Endfile