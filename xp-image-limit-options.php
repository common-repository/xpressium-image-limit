<?php
/**
 * xpressium Image Limit - Options
 *
 * @since Version 1.0
 */


/**
 * Register the form setting for our xpil_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * @since Version 1.0
 */
function xpil_options_init() {
	if(false === xpil_get_options()) {
		add_option('xpil_options', xpil_get_default_options());
	}

	register_setting(
		'media',
		'xpil_options',
		'xpil_options_validate'
	);

	add_settings_field(
		'img_limit',
		'Image max limit',
		'xpil_settings_field_img_limit',
		'media',
		'uploads'
	);
}
add_action('admin_init', 'xpil_options_init');

/**
 * Returns the default options.
 *
 * @since Version 1.0
 */

function xpil_get_default_options() {
	$xpil = new xp_image_limit;
	$limit = $xpil->xp_limit();
	$default_options = array(
		'img_limit' => $limit,
	);

	return apply_filters('xpil_default_options', $default_options);
}

/**
 * Returns the options array.
 *
 * @since Version 1.0
 */
function xpil_get_options() {
	return get_option('xpil_options', xpil_get_default_options());
}

/**
 * Renders the maximum upload size setting field.
 *
 * @since Version 1.0
 *
 */

function xpil_settings_field_img_limit() {
	$options = xpil_get_options();
	$xpil = new xp_image_limit;
	$limit = $xpil->xp_limit();

	$id = 'img_limit';

	if(isset($options[$id]) && ($options[$id] < $limit)) {
		$value = $options[$id];
	} else {
		$value = $limit;
	}

	$field = '<p>
		<input name="xpil_options['.$id.']'.'" id="xpil-limit" type="text" value="'.$value.'" size="3" maxlength="3" /> MB
		<br>
		<span class="description">Server max limit: '.$limit.' MB</span>
	</p>';

	echo $field;
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see xpil_options_init()
 * @since Version 1.0
 */
function xpil_options_validate($input) {
	$output = $defaults = xpil_get_default_options();
	$xpil = new xp_image_limit;
	$limit = $xpil->xp_limit();

	$output['img_limit'] = str_replace(',', '', $input['img_limit']);
	$output['img_limit'] = absint(intval($output['img_limit']));

	if($output['img_limit'] > $limit) {
		$output['img_limit'] = $limit;
	}

	return apply_filters('xpil_options_validate', $output, $input, $defaults);
}

function unique_identifyer_admin_notices() {
	settings_errors('img_limit');
}
add_action('admin_notices', 'unique_identifyer_admin_notices');