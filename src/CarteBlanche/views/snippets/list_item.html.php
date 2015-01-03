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

// the item index to work on
if (!isset($item_name)) $item_name=null;
// the item value to work on
if (!isset($item_val)) $item_val=null;
// may item value be closable if it is a list
if (empty($closable_items)) $closable_items=false;
// template used for sub-items list
if (empty($list_template)) $list_template='default_list';
?>

<?php if (!empty($item_name) || !empty($item_val)) : ?>
    <?php if (!is_string($item_name) || (is_string($item_name) && !empty($item_name))) : ?>
        <strong><?php _echo($item_name); ?></strong>
        &nbsp;:&nbsp;
    <?php endif; ?>
    <?php if (!empty($item_val)) : ?>
        <?php if (is_array($item_val)) : ?>
            <?php view($list_template, array(
                'items'=>$item_val,
            )); ?>
        <?php elseif (is_url($item_val)) : ?>
            <a href="<?php _echo($item_val); ?>" title="Follow <?php _echo($item_val); ?>"><?php _echo($item_val); ?></a>
        <?php elseif (is_email($item_val)) : ?>
            <a href="mailto:<?php _echo($item_val); ?>" title="Contact <?php _echo($item_val); ?>"><?php _echo($item_val); ?></a>
        <?php elseif (is_string($item_val) || is_numeric($item_val) || is_float($item_val)) : ?>
            <?php _echo($item_val); ?>
        <?php elseif (is_bool($item_val)) : ?>
            <?php _echo((string) $item_val); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
