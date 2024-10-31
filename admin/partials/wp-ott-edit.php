<?php

/**
 * Provide a editing form
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin/partials
 */

switch ($type) {
	case 'form':
		$plural = 'forms';
		$heading = __('Edit form "%s"', 'wp-ott');
		$edit_title = __('Edit form layout', 'wp-ott');
		break;
	case 'table':
		$plural = 'tables';
		$heading = __('Edit table "%s"', 'wp-ott');
		$edit_title = __('Edit table layout', 'wp-ott');
		break;
	case 'banner':
		$plural = 'banners';
		$heading = __('Edit banner "%s"', 'wp-ott');
		$edit_title = __('Edit banner layout', 'wp-ott');
		break;
	case 'link':
		$plural = 'links';
		$heading = __('Edit link "%s"', 'wp-ott');
		$edit_title = __('Edit link layout', 'wp-ott');
		break;
}

$id = $instrument['id'];
$autoinsert = $api->get_autoinsert($id);
$mode = $autoinsert['mode'];
$categories = $autoinsert['categories'];
$tags = $autoinsert['tags'];
$site_categories = get_categories();
$site_tags = get_tags();
?>
<script type="text/javascript">new Clipboard('.clip');</script>
<div class="wrap">
<h1>
	<?= sprintf($heading, $instrument['name']) ?>
	<a href="http://partner.onetwotrip.com/#/dashboard/programs/<?= $id ?>/edit" class="page-title-action" target="_blank"><?= $edit_title ?></a>
</h1>
<form method="post" action="<?= sprintf('?page=wp-ott-%s&action=edit&id=%s', $plural, $_GET['id']) ?>" novalidate="novalidate" autocomplete="off">
<input name="wp_ott_action" type="hidden" value="update" />
<table class="form-table">
<tr>
<th scope="row"><label for="wp_ott_shorcode"><?= __('Shortcode to insert', 'wp-ott') ?></label></th>
<td><input name id="wp_ott_module" type="text" readonly="readonly" value="<?= esc_attr($api->shortcode($id)) ?>" autocomplete="off" class="regular-text" /> <button class="clip" onclick="return false;" data-clipboard-target="#wp_ott_module"><?= __('Copy to clipboard', 'wp-ott') ?></button></td>
</tr>
</table>
<h2><?= __('Auto-insert', 'wp-ott') ?></h2>
<p><?= __('Here, you can automatically add your forms and tables to any posts, using categories or tags. Table or form will be added to all posts of the selected category.', 'wp-ott') ?></p>
<table class="form-table">
<tr>
<th scope="row"><label for="wp_ott_autoinsert_mode"><?= __('Location', 'wp-ott') ?></label></th>
<td><select name="wp_ott_autoinsert_mode" id="wp_ott_autoinsert_mode">
	<option <?= selected($mode, 'disabled') ?> value="disabled"><?= __('Disabled (do not use auto-insert)', 'wp-ott') ?></option>
	<option <?= selected($mode, 'top') ?> value="top"><?= __('Top', 'wp-ott') ?></option>
	<option <?= selected($mode, 'bottom') ?> value="bottom"><?= __('Bottom', 'wp-ott') ?></option>
	<option <?= selected($mode, 'both') ?> value="both"><?= __('Both', 'wp-ott') ?></option>
</select></td>
</tr>
<tr>
<th scope="row"><label for="wp_ott_autoinsert_categories"><?= __('Categories', 'wp-ott') ?></label></th>
<td><fieldset id="wp_ott_autoinsert_categories" class="wp-ott-scroll-fieldset">
	<?php foreach ($site_categories as $category) : ?>
	<input <?= checked(in_array($category->term_id, $categories)) ?> type="checkbox" name="wp_ott_categories[]" id="wp_ott_category_<?= $category->term_id ?>" value="<?= $category->term_id ?>"> <?= $category->name ?><br>
	<?php endforeach; ?>
</fieldset></td>
</tr>
<tr>
<th scope="row"><label for="wp_ott_autoinsert_tags"><?= __('Tags', 'wp-ott') ?></label></th>
<td><fieldset id="wp_ott_autoinsert_tags" class="wp-ott-scroll-fieldset">
	<?php foreach ($site_tags as $tag) : ?>
	<input <?= checked(in_array($tag->term_id, $tags)) ?> type="checkbox" name="wp_ott_tags[]" id="wp_ott_tag_<?= $tag->term_id ?>" value="<?= $tag->term_id ?>"> <?= $tag->name ?><br>
	<?php endforeach; ?>
</fieldset></td>
</tr>

</table>
<p class="submit"><input type="submit" name="wp_ott_submit" id="wp_ott_submit" class="button button-primary" value="<?= __('Update', 'wp-ott') ?>" /></p>
</form>
</div>
