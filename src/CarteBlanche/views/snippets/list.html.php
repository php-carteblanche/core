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

// items to list on
if (empty($items)) $items=array();
// may primary items be closable
if (empty($closable_items)) $closable_items=false;
// template called for each item
if (empty($list_item_template)) $list_item_template='default_list_item';
?>

<?php if (0!=count($items)) : ?>
<ul>
	<?php foreach ($items as $item_name=>$item_val) : ?>
	<li>
        <?php view($list_item_template, array(
            'item_name'=>$item_name,
            'item_val'=>$item_val,
            'closable_items' => $closable_items,
        )); ?>
	</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
