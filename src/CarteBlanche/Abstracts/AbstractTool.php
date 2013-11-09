<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 *
 * The default tool abstract class
 */

namespace CarteBlanche\Abstracts;

use \CarteBlanche\CarteBlanche;
use \CarteBlanche\App\Kernel;
use \CarteBlanche\App\FrontController;

/**
 * Any tool class must extend this abstract one
 */
abstract class AbstractTool
{
	/**
	 * The views directory 
	 *
	 * This must be a sub-directory of the tool directory
	 */
	var $views_dir;

	/**
	 * The view file
	 */
	var $view;

	/**
	 * The direct output (if no view is set or parse)
	 */
	var $output;

	/**
	 * The views arguments
	 */
	var $_args=array();

	/**
	 * The constructor : overrides the tool options
	 * @param array $opts An array of the tool options
	 */
	public function __construct( $opts=array() )
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
	 */
	public function render()
	{
		$args = $this->buildViewParams();
		if (isset($args['output']))
			$this->output = $args['output'];

		if (!empty($this->view)) {
			return FrontController::getInstance()
			    ->view( $this->views_dir.$this->view, $args );
		} elseif (isset($this->output)) {
			return $this->output;
		} else {
			throw new RuntimeException("Tool '".get_class($this)."' do not render anything !");
		}
	}
	
	/**
	 * The tool logic : construction of the objects parameters passed to final view
	 */
	abstract function buildViewParams();

}

// Endfile