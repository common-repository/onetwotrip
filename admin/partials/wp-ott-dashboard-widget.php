<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin/partials
 */

$period = self::get_dashboard_widget_options(self::wid)['interval'];
if (!in_array($period, array('week', 'month', 'year')))
	$period = 'week';
$api = new Wp_Ott_API();
$stats = $api->stats($period)['data'];
?>
<?php if (!is_null($stats)) : ?>
<table class="form-table"><tr>
	<th scope="row"><?= __('Period', 'wp-ott') ?>:</th>
	<td><?= __($period, 'wp-ott'); ?></td>
</tr><tr>
	<th scope="row"><?= __('Income for period', 'wp-ott') ?>:</th>
	<td><?= $stats['income']['amount'] ?> ₽</td>
</tr><tr>
	<th scope="row"><?= __('Total income', 'wp-ott') ?>:</th>
	<td><?= $stats['income']['profit'] ?> ₽</td>
</tr><tr>
	<td></td>
	<td align="right"><a href="?page=wp-ott"><?= __('Detailed stats', 'wp-ott') ?></a></td>
</td></table>
<?php else : ?>
<p style="text-align:center;"><?= __('You are not logged into OTT API!', 'wp-ott') ?><br/><a href="?page=wp-ott"><?= __('Please login', 'wp-ott') ?></a></p>
<?php endif; ?>