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
?>Webpage "<?php echo $url; ?>" sent an HTTP error code "<?php echo $code; ?>" at <?php echo date('Y-m-d H:i:s'); ?>.

<?php
// HTTP headers of the error
@file_get_contents($url);
if (isset($http_response_header) && is_array($http_response_header) && count($http_response_header)) {
	echo "\n\n-- http_headers:";
	foreach($http_response_header as $var=>$val)
		echo "\n".' #'.$val;
	echo "\n---- ";
}
?>

<?php
// session infos
if (isset($_SESSION)) {
	$session_str = '';
	foreach($_SESSION as $sess_var=>$sess_val){
		if ($sess_val && !is_array($sess_val) && strlen($sess_val)>0)
			$session_str .= "\n".'#'.$sess_var.' => '.$sess_val;
		if ($sess_val && is_array($sess_val) && count($sess_val)>0){
			$session_str .= "\n".'#'.$sess_var.' => ';
			foreach($sess_val as $key => $val)
				$session_str .= "\n".'##'.$key.' => '.$val;
		}
				
	}
	echo "\n\n-- session (only not empty values): \n".$session_str."\n---- ";
}
?>

<?php
// Backtrace PHP
@ob_start();
@debug_print_backtrace();
$backtrace = @ob_get_contents();
@ob_end_clean();
if ($backtrace)
	echo "\n\n-- backtrace: \n".$backtrace."\n---- ";
?>
