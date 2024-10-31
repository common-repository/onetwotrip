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

$username = empty($_POST['wp_ott_username']) ? '' : $_POST['wp_ott_username'];
$password = empty($_POST['wp_ott_password']) ? '' : $_POST['wp_ott_password'];
?>
<div class="wrap">
<h1><?= __('Login', 'wp-ott') ?></h1>
<form method="post" action="<?= menu_page_url('wp-ott', false) ?>" novalidate="novalidate" autocomplete="off" name="lastpass-disable-search">
<input name="wp_ott_action" type="hidden" value="login" />
<table class="form-table">
<tr>
<th scope="row"><label for="username"><?= __('Username', 'wp-ott') ?></label></th>
<td><input name="wp_ott_username" type="text" id="wp_ott_username" value="<?= esc_attr($username) ?>" autocomplete="off" class="regular-text" /></td>
</tr>
<tr>
<th scope="row"><label for="password"><?= __('Password', 'wp-ott') ?></label></th>
<td><input name="wp_ott_password" type="password" id="wp_ott_password" value="<?= esc_attr($password) ?>" autocomplete="off" class="regular-text" /></td>
</tr>
</table>
<p class="submit"><input type="submit" name="wp_ott_submit" id="wp_ott_submit" class="button button-primary" value="<?= __('Connect to OTT API', 'wp-ott') ?>" /></p>
</form>
</div>
