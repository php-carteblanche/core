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

//use \Library\Session\Session as BaseSession;
use \Library\Session\FlashSession as BaseSession;

/**
 * The session class
 *
 * General session handler
 *
 * @author  Piero Wbmstr <me@e-piwi.fr>
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