<?php

/**
 * Provide a login form
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codecanyon.net/user/wonderburo
 * @since      1.0.0
 *
 * @package    Wp_Ott
 * @subpackage Wp_Ott/admin/partials
 */

$period = empty($_POST['wp_ott_period']) ? 'week' : $_POST['wp_ott_period'];
if (!in_array($period, array('week', 'month', 'year')))
	$period = 'week';
$instrument = empty($_POST['wp_ott_instrument']) ? 0 : $_POST['wp_ott_instrument'];
$instruments = $api->instrument_list('all');
$ids = array_column($instruments, 'wp_ott_id');
if (!in_array($instrument, $ids))
	$instrument = 0;
$stats = $api->stats($period, $instrument)['data'];
$utc = new DateTimeZone('UTC');
$data1 = array(
	0 => array(),
	1 => array(),
	2 => array(),
);
foreach($stats['visitors']['items'] as $item) {
	$js_date = date_create_from_format('Y-m-d', substr($item['date'], 0, 10), $utc);
	$data1[0][] = array(
		$js_date->format('D M d Y H:i:s O'),
		$item['users'],
	);
	$data1[1][] = array(
		$js_date->format('D M d Y H:i:s O'),
		$item['clicks'],
	);
	$data1[2][] = array(
		$js_date->format('D M d Y H:i:s O'),
		$item['searches'],
	);
}
$data2 = array();
foreach($stats['income']['items'] as $item) {
	$js_date = date_create_from_format('Y-m-d', substr($item['date'], 0, 10), $utc);
	$data2[] = array(
		$js_date->format('D M d Y H:i:s O'),
		$item['amount'],
	);
}
$timezone_offset = get_option('gmt_offset');
$date_format = get_option('date_format');
$time_format = get_option('time_format');
$data3 = array();
foreach($stats['orders'] as $item) {
	$php_date = date_create_from_format('Y-m-d\TH:i', substr($item['dateCreated'], 0, 16), $utc)->getTimeStamp() + ($timezone_offset * 3600);
	$data3[] = array(
		'reference' => $item['number'],
		'dateCreated' => date_i18n($date_format, $php_date) . ' ' . date_i18n($time_format, $php_date),
		'type' => $item['type'],
		'route' => $item['route'],
		'carrier' => $item['platingCarrier'],
		'status' => $item['status'],
		'income' => $item['income'],
	);
}
?>
<div class="wrap">
<h1><?= __('Stats', 'wp-ott') ?></h1>
<form method="post" action="<?= menu_page_url('wp-ott', false) ?>" novalidate="novalidate" autocomplete="off">
<input name="wp_ott_action" type="hidden" value="stats" />
<table class="form-table">
<tr>
<th scope="row"><label for="wp_ott_period"><?= __('Show data for:', 'wp-ott') ?></label></th>
<td>
	<select name="wp_ott_period" id="wp_ott_period">
		<option <?= selected($period, 'week') ?> value="week"><?= __('Week', 'wp-ott') ?></option>
		<option <?= selected($period, 'month') ?> value="month"><?= __('Month', 'wp-ott') ?></option>
		<option <?= selected($period, 'year') ?> value="year"><?= __('Year', 'wp-ott') ?></option>
	</select>
	<select name="wp_ott_instrument" id="wp_ott_instrument">
		<option <?= selected($instrument, 0) ?> value="0"><?= __('All instruments', 'wp-ott') ?></option>
		<?php foreach($instruments as $item) : ?>
			<option <?= selected($instrument, $item['wp_ott_id']) ?> value="<?= $item['wp_ott_id'] ?>"><?= $item['wp_ott_title'] ?></option>
		<?php endforeach; ?>
	</select>
	<input type="submit" name="wp_ott_submit" id="wp_ott_submit" class="button button-primary" value="<?= __('Update', 'wp-ott') ?>" />
</td>
</tr>
</table>

<table class="form-table">
<tr>
<th scope="row"><?= __('Visitors', 'wp-ott') ?></th>
<td width="10%"><?= $stats['visitors']['users'] ?></td>
<td rowspan="4">
	<div id="chart1" style="height:300px; width:650px;"></div>
	<script type="text/javascript">
		//alert('Chart 1');
		$ = jQuery;
		$(document).ready(function() {
			var data1=<?= json_encode($data1) ?>;
			var plot1 = $.jqplot('chart1', data1, {
				legend: {
					show: true,
					location: 'e',
					placement: 'inside',
					rowSpacing: '0'
				},
				axes:{
					xaxis: {
						renderer:$.jqplot.DateAxisRenderer
					},
					yaxis: {
						min:0
					}
				},
				series:[
					{label:'<?= esc_attr(__('Visitors', 'wp-ott')) ?>', rendererOptions: {barWidth: 50, highlightMouseDown: true}, pointLabels: {show: true}, renderer:$.jqplot.BarRenderer},
					{label:'<?= esc_attr(__('Clicks', 'wp-ott')) ?>', lineWidth:4, markerOptions:{style:'square'}},
					{label:'<?= esc_attr(__('Searches', 'wp-ott')) ?>', lineWidth:4, markerOptions:{style:'square'}}
				]
			});
		});
	</script>
</td>
</tr>
<tr>
<th scope="row"><?= __('Impressions', 'wp-ott') ?></th>
<td><?= $stats['visitors']['shows'] ?></td>
</tr>
<tr>
<th scope="row"><?= __('Searches', 'wp-ott') ?></th>
<td><?= $stats['visitors']['searches'] ?></td>
</tr>
<tr>
<th scope="row"><?= __('Clicks', 'wp-ott') ?></th>
<td><?= $stats['visitors']['clicks'] ?></td>
</tr>
</table>
</form>

<h2><?= __('Income', 'wp-ott') ?></h1>
<table class="form-table">
<tr>
<th scope="row"><?= __('Income for period', 'wp-ott') ?></th>
<td width="10%"><?= $stats['income']['amount'] ?> ₽</td>
<td rowspan="5">
	<!-- <?php foreach($stats['income']['items'] as $item) : ?>
	<pre><?php var_dump($item) ?></pre>
	<?php endforeach; ?> -->
	<div id="chart2" style="height:300px; width:650px;"></div>
	<script type="text/javascript">
		$ = jQuery;
		$(document).ready(function() {
			var data2=<?= json_encode(array($data2)) ?>;
			var plot2 = $.jqplot('chart2', data2, {
				legend: {
					show: true,
					location: 'e',
					placement: 'inside',
					rowSpacing: '0'
				},
				axes:{
					xaxis: {
						renderer:$.jqplot.DateAxisRenderer
					}
				},
				series:[{label:'<?= esc_attr(__('Income', 'wp-ott')) ?>', lineWidth:4, markerOptions:{style:'square'}}]
			});
		});
	</script>

</td>

</tr>
<tr>
<th scope="row"><?= __('Total income', 'wp-ott') ?></th>
<td><?= $stats['income']['profit'] ?> ₽</td>
</tr>
<tr>
<th scope="row"><?= __('Purchases', 'wp-ott') ?></th>
<td><?= $stats['tickets']['purchases'] ?></td>
</tr>
<tr>
<th scope="row"><?= __('Paid', 'wp-ott') ?></th>
<td><?= $stats['tickets']['paid'] ?></td>
</tr>
<tr>
<th scope="row"><?= __('Refunds', 'wp-ott') ?></th>
<td><?= $stats['tickets']['refunds'] ?></td>
</tr>
</table>

<h2><?= __('Orders history', 'wp-ott') ?></h1>
<?php if (empty($data3)) : ?>
<p><?= __('No orders are recorded for selected period', 'wp-ott') ?></p>
<?php else : ?>
	<table class="widefat fixed"><thead><tr>
		<th><?= __('Date/time', 'wp-ott') ?></th>
		<th><?= __('Type', 'wp-ott') ?></th>
		<th><?= __('Status', 'wp-ott') ?></th>
		<th><?= __('Income', 'wp-ott') ?></th>
	</tr></thead>
	<?php foreach($data3 as $item) : ?>
	<tr>
		<td><?= $item['dateCreated'] ?></td>
		<td><?= $item['type'] ?></td>
		<td><?= $item['status'] ?></td>
		<td><?= $item['income'] ?> ₽</td>
	</tr>
	<?php endforeach; ?>
	</tr></table>
<?php endif; ?>

<form method="post" action="<?= menu_page_url('wp-ott', false) ?>" novalidate="novalidate" autocomplete="off" name="lastpass-disable-search">
<input name="wp_ott_action" type="hidden" value="logout" />
<table class="form-table">
<tr>
<th scope="row"><label><?= __('Logged in as:', 'wp-ott') ?></label></th>
<td><?= get_option('wp_ott_username') ?></td>
</tr>
</table>
<p class="submit"><input type="submit" name="wp_ott_submit" id="wp_ott_submit" class="button button-primary" value="<?= __('Disconnect from OTT API', 'wp-ott') ?>" /></p>
</form>
</div>
