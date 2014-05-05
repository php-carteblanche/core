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
use \CarteBlanche\App\Container;
use \CarteBlanche\Abstracts\AbstractCommandLineController;
use \Library\CommandLine\Helper;
use \Library\CommandLine\Formater;
use \Library\CommandLine\Stream;

/**
 * Default controller for command line operations
 *
 * @author  Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class CommandLineController
    extends AbstractCommandLineController
{

    /**
     * The home page of the controller
     *
     * @return string The home page view content
     */
    public function indexAction()
    {
        $this->render(array(
            'output'=> 'YO'
        ));
    }

    /**
     * Page of system errors
     *
     * @return string The view content
     */
    public function bootErrorAction(array $errors = null)
    {
        $session = $this->getContainer()->get('session');
        $original_errors = $session->has('boot_errors') ? $session->get('boot_errors') : $errors;
        $running_user = $this->getKernel()->whoAmI();

        $output = $this->view(self::$views_dir.'errors', array(
            'title'=>$this->trans('System errors'),
            'original_errors'=>$original_errors,
            'running_user' => $running_user,
            'errors'=>$errors
        ));
        $this->render(array(
            'output'=> $output
        ));
    }

    /**
     * Default presentation options
     */
    protected $options = array(
        'title'=>'Command line tool of CarteBlanche',
        'title_options'=>array(
            'foreground'=>'cyan',
            'background'=>'blue',
            'text_options'=>'bold',
            'autospaced'=>false
        ),
        'argv_options'=>array(
            'h'=>'help',
        ),
        'argv_long_options'=>array(
            'help'=>'help',
        ),
        'commands'=>array(
            'env::'=>'environment',
        ),
        'aliases'=>array(
        ),
    );

// ------------------------------------
// MAGIC METHODS
// ------------------------------------

    public function __construct(Container $_container, array $options = array())
    {
        parent::__construct($options);
        if (empty($this->params)) {
            return self::writeNothingToDo();
        }
        if ($this->written===false AND $this->verbose===true) {
            self::writeIntro();
            $this->written=true;
        }
        self::_treatOptions();
    }

    private function __init()
    {
        if ($this->written===false AND $this->verbose===true) {
            self::writeIntro();
            $this->written=true;
        }
    }

    public function writeIntro()
    {
        $this->stream->write(
            $this->formater->parse(
                $this->formater->spacedStr($this->options['title'], 'title', true)
            ).PHP_EOL
        );
    }

    public function writeNothingToDo(  )
    {
        self::__init();
        self::writeThinError( '> Nothing to do ! (run "--help" option to see help)' );
        $this->stream->__exit();
    }

// ------------------------------------
// CONTROLLER METHODS
// ------------------------------------

    public function runArgumentHelp($arg = null)
    {
        $help_descr = $this->getOptionHelp( $arg );
        if ($help_descr!=$arg) {
            $this->debugWrite( ">> [help] displaying help for option \"$arg\"" );
            $help_ctt = Helper::formatHelpString( ucfirst($arg), $help_descr, $this->formater );
            self::write( $this->formater->parse($help_ctt) );
            self::writeStop();
        }
        $this->debugWrite( ">> [help] no help found for option \"$arg\"" );
        return false;
    }

    /**
     * List of all options and features of the command line tool ; for some commands, a specific help can be available, running <var>--command --help</var>
     * Some command examples are purposed running <var>--console --help</var>
     */
    public function runHelpCommand($opt = null)
    {
        if (!empty($opt)) {
            if (!is_array($opt)) $opt = array( $opt=>'' );
            $opt_keys = array_keys($opt);
            $ok=false;
            while ($ok===false) {
                if (count($opt_keys)==0) break;
                $current_option = array_shift( $opt_keys );
                $ok = self::runArgumentHelp( $current_option );
            }
        }
        $this->debugWrite( '>> [help] displaying global help' );
        $help_str = Helper::getHelpInfo($this->options, $this->formater, $this);
        self::write( $this->formater->parse($help_str) );
        self::writeStop();
    }

    /**
     * Get an information about current environement
     *
     * Get an information about current environement ; optional arguments are :
     *     - '<option>php</option>' : get PHP version (default option),
     *     - '<option>apache</option>' : get Apache version,
     *     - '<option>apache-modules</option>' : get Apache modules list,
     *     - '<option>gd</option>' : get GD library version,
     *     - '<option>all</option>' : get all above inforamtions.
     */
    public function runEnvironmentCommand($which = null)
    {
        switch($which) {
            case 'php': default:
                self::write( 'PHP version: '.phpversion() );
                break;
            case 'apache':
                if (function_exists('apache_get_version'))
                    self::write( 'Apache version: '.apache_get_version() );
                else
                    self::writeInfo( 'Apache version not available !' );
                break;
            case 'apache-modules':
                if (function_exists('apache_get_modules'))
                    self::write( 'Apache modules: '.var_export(apache_get_modules(),1) );
                else
                    self::writeInfo( 'Apache modules not available !' );
                break;
            case 'gd':
                if (function_exists('gd_info'))
                    self::write( 'GD library informations: '.var_export(gd_info(),1) );
                else
                    self::writeInfo( 'GD library not available !' );
                break;
        }
    }

}

// Endfile