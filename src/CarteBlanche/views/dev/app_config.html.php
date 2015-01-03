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

if (empty($constants)) $constants=array();
if (empty($config)) $config=array();
if (!isset($debug)) $debug=false;

?>

<h3><?php _trans('Constants list'); ?></h3>
<ul>
<?php if (0!=count($constants)) : ?>

	<?php foreach ($constants as $_name=>$_val) : ?>
	<li>
	    <strong><?php _echo($_name); ?></strong>
	    &nbsp;:&nbsp;
	    <?php if (
	        substr($_name, -(strlen('INTERFACE')))==='INTERFACE' ||
	        substr($_name, -(strlen('ABSTRACT')))==='ABSTRACT' ||
	        substr($_name, -(strlen('DEFAULT_NAMESPACE')))==='DEFAULT_NAMESPACE' ||
	        substr($_name, -(strlen('CLASSNAME')))==='CLASSNAME' ||
	        substr($_name, -(strlen('CLASS')))==='CLASS'
	    ) : ?>
    	    <a href="<?php _echo(
    	        \CarteBlanche\Library\Dev\DevHelper::getDocUrl($_val)
    	    ); ?>" title="<?php _trans_js('See documentation'); ?>"><?php _echo($_val); ?></a>
	    <?php else : ?>
    	    <?php _echo($_val); ?>
	    <?php endif; ?>
	</li>
	<?php endforeach; ?>

<?php endif; ?>
</ul>

<h3><?php _trans('Internal configuration'); ?></h3>

<?php view('default_list', array('items'=>$config)); ?>
