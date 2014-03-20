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
use \Library\Logger as BaseLogger;
use \Library\Helper\Directory as DirectoryHelper;

/**
 * Write some log infos in log files
 *
 * For compliance, this class implements the [PSR Logger Interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).
 *
 * @author 		Piero Wbmstr <piwi@ateliers-pierrot.fr>
 */
class Logger extends BaseLogger
{

	/**
	 * Load the configuration infos
	 *
	 * @param array $user_options
	 * @param string $logname
	 *
	 * @return void
	 */
	protected function init(array $user_options = array(), $logname = null)
	{
		$app_config = CarteBlanche::getConfig('log', array(), true);
		$app_config['directory'] = CarteBlanche::getFullPath('log_dir');
		$user_config = CarteBlanche::getConfig('log', array());
		if (!empty($user_config)) {
    		$config = array_merge($app_config, $user_config, $user_options);
    	} else {
    		$config = array_merge($app_config, $user_options);
    	}
		parent::init($config, $logname);
	}


	/**
	 * Get the log file path
	 *
	 * @param int $level The level of the current log info (default is 100)
	 *
	 * @return string The absolute path of the logfile to write in
	 */
	protected function getFilePath($level = 100)
	{
	    $mode = CarteBlanche::getKernel()->getMode();
		return DirectoryHelper::slashDirname($this->directory)
		    .$this->getFileName($level)
			.($mode!='prod' ? '_'.$mode : '' )
			.'.'.trim($this->logfile_extension, '.');
	}

}

/*
class TestClass
{
    var $msg;
    function __construct( $str )
    {
        $this->msg = $str;
    }
    function __toString()
    {
        return $this->msg;
    }
}

// test of global logger
$logger = getContainer()->get('logger');
var_export($logger);

// write a simple log
$ok = getContainer()->get('logger')->log($logger::DEBUG, 'my message');
var_export($ok);

// write a log message with placeholders
$ok = getContainer()->get('logger')->log($logger::DEBUG, 'my message with placeholders : {one} and {two}', array(
    'one' => 'my value for first placeholder',
    'two' => new TestClass( 'my test class with a toString method' )
));
var_export($ok);

// write logs in a specific "test" file
$ok = getContainer()->get('logger')->log($logger::DEBUG, 'my message', array(), 'test');
var_export($ok);

// write many logs
for ($i=0; $i<1000; $i++)
{
    $ok = getContainer()->get('logger')->log( \App\Logger::DEBUG, '[from ?] a simple message qsmldkf jfqksmldkfjqmlskdf jmlqksjmdlfkj jKMlkjqmlsdkjf ' );
    $ok = getContainer()->get('logger')->log( \App\Logger::ERROR, 'a long message qsmldkf jfqksmldkfjqmlskdf jmlqksjmdlfkj jKMlkjqmlsdkjf ' );
    $ok = getContainer()->get('logger')->log( \App\Logger::INFO, 'a long message qsmldkf jfqksmldkfjqmlskdf jmlqksjmdlfkj jKMlkjqmlsdkjf ', $_GET, 'test' );
}

// write error logs
		try{
//			fopen(); // error
			if (2 != 4) // false
				throw new \CarteBlanche\Exception\Exception("Capture l'exception par défaut", 12);
		} catch(\CarteBlanche\Exception\Exception $e) {
			echo $e;
		}
*/

// Endfile