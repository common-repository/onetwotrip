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
?>
<?php /* ?>
<input type="hidden" name="update-widget" value="<?= self::wid ?>">
<p>
	<label for="category"><?= __('Category', 'wp-ott') ?></label>
	<select class="widefat" id="category" name="category">
		<?php foreach (self::$categories as $key => $value) : ?>
		<option value="<?= $key ?>"<?= selected($category, $key, false) ?>><?= $value ?></option>
		<?php endforeach; ?>
	</select>
</p>
<p>
	<label for="instrument"><?= __('Instrument', 'wp-ott') ?></label>
	<select class="widefat" id="instrument" name="instrument">
		<?php foreach (self::$instruments as $key => $value) : ?>
		<option value="<?= $key ?>"<?= selected($instrument, $key, false) ?>><?= $value ?></option>
		<?php endforeach; ?>
	</select>
</p>
<?php */ ?>
<p>
	<label for="interval"><?= __('Period', 'wp-ott') ?></label>
	<select class="widefat" id="interval" name="interval">
		<?php foreach (self::$intervals as $key => $value) : ?>
		<option value="<?= $key ?>"<?= selected($interval, $key, false) ?>><?= $value ?></option>
		<?php endforeach; ?>
	</select>
</p>
