<?php
/**
 * CarteBlanche - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License Apache-2.0 <http://www.apache.org/licenses/LICENSE-2.0.html>
 * Sources <http://github.com/php-carteblanche/carteblanche>
 */

namespace CarteBlanche\Library;

//use \Library\Session\Session as BaseSession;
use \Library\Session\FlashSession as BaseSession;

/**
 * The session class
 *
 * General session handler
 *
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 */
class Session extends BaseSession
{

	const SESSION_FLASHESNAME = 'my-flashes';
}

/*
echo '<pre>';

$session = CarteBlanche::getContainer()->get('session');

//$session->set('test', array('one', 'two'));
if ($session->has('test')) {
    echo '<br />test :: '.var_export($session->get('test'),1);
} else {
    echo '<br />test is empty';
}

//$session->setFlash('test qsdf qsdfqsdf', 'ok');
//$session->setFlash('test qsdf qsdfqsdf', 'error');
if ($session->hasFlash()) {
    echo '<br />flashes :: '.var_export($session->allFlashes(),1);
} else {
    echo '<br />flashes are empty';
}

echo '<br /><br />'.var_export($session,1);
echo '<br /><br />'.var_export($_SESSION,1);
echo '<br />';
exit('yo');
*/
// Endfile