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

namespace CarteBlanche\Abstracts;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\FrontController;
use \Library\Helper\Directory as DirectoryHelper;
use \CarteBlanche\Exception\RuntimeException;

/**
 * Any tool class must extend this abstract one
 */
abstract class AbstractTool
{

    /**
     * @var string The views directory
     *
     * This must be a sub-directory of the tool directory
     */
    public $views_dir;

    /**
     * @var string The view file
     */
    public $view;

    /**
     * @var string The direct output (if no view is set or parse)
     */
    public $output;

    /**
     * @var array The views arguments
     */
    public $_args = array();

    /**
     * The constructor : overrides the tool options
     *
     * @param array $opts An array of the tool options
     */
    public function __construct($opts = array())
    {
        if (!empty($opts))
        foreach ($opts as $_opt_var=>$_opt_val) {
            if (property_exists($this, $_opt_var))
                $this->{$_opt_var} = $_opt_val;
            else
                $this->_args[$_opt_var] = $_opt_val;
        }
        if (empty($this->views_dir)) {
            $this->views_dir = \CarteBlanche\App\Locator::getToolPath( get_called_class() ).'/'
                .CarteBlanche::getPath('views_dir');
        }
    }

    /**
     * Direct rendering of the tool object
     *
     * It basically allows to directly write `echo $tool`
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch( \Exception $e) {
            trigger_error( $e->getMessage(), E_USER_WARNING );
        }
    }

    /**
     * The final rendering of the tool
     *
     * @return string
     * @throws \CarteBlanche\Exception\RuntimeException if the rendering is empty
     */
    public function render()
    {
        $args = $this->buildViewParams();
        if (isset($args['output']))
            $this->output = $args['output'];

        if (!empty($this->view)) {
            return FrontController::getInstance()
                ->view( DirectoryHelper::slashDirname($this->views_dir).$this->view, $args );
        } elseif (isset($this->output)) {
            return $this->output;
        } else {
            throw new RuntimeException("Tool '".get_class($this)."' do not render anything !");
        }
    }

    /**
     * The tool logic : construction of the objects parameters passed to final view
     *
     * @return array
     */
    abstract function buildViewParams();

}

// Endfile