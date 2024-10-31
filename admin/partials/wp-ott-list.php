<?php

/**
 * Display a list of forms
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
		$heading = __('Forms', 'wp-ott');
		$edit_title = __('Add new form', 'wp-ott');
		$edit_uri_part = 'form';
		break;
	case 'table':
		$plural = 'tables';
		$heading = __('Tables', 'wp-ott');
		$edit_title = __('Add new table', 'wp-ott');
		$edit_uri_part = 'form_with_result';
		break;
	case 'banner':
		$plural = 'banners';
		$heading = __('Banners', 'wp-ott');
		$edit_title = __('Add new banner', 'wp-ott');
		$edit_uri_part = 'banner';
		break;
	case 'link':
		$plural = 'links';
		$heading = __('Links', 'wp-ott');
		$edit_title = __('Add new link', 'wp-ott');
		$edit_uri_part = 'link';
		break;
}
$myListTable = new Wp_Ott_List_Table($plural);
$myListTable->prepare_items(); 
?>
<script type="text/javascript">new Clipboard('.clip');</script>
<div class="wrap">
	<h1>
		<?= $heading ?>
		<a href="http://partner.onetwotrip.com/#/dashboard/programs/add/<?= $edit_uri_part ?>" class="page-title-action" target="_blank"><?= $edit_title ?></a>
	</h1>
	<form method="post">
		<input type="hidden" name="page" value="wp_ott_<?= $plural ?>">
		<?php $myListTable->display(); ?>
	</form>
</div>