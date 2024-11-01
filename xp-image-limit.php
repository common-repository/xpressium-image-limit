<?php
/*
Plugin Name: xpressium Image Limit
Plugin URI: http://wordpress.org/extend/plugins/xpressium-image-limit
Description: Allows setting a maximum file size for image uploads.
Author: Jorge Castro
Author URI: http://www.xpressium.com
Version: 1.0.0
Text Domain: xpil-plugin
Domain Path: /languages/
*/


define('XPIL_DEBUG', false);

require_once('xp-image-limit-options.php');

class xp_image_limit {
	public function __construct() {
		add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'add_plugin_links'));
		add_filter('wp_handle_upload_prefilter', array($this, 'error_message'));
	}

	public function add_plugin_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="'.get_bloginfo('wpurl').'/wp-admin/options-media.php?settings-updated=true#xpil-limit">Settings</a>'
			),
			$links
		);
	}

	public function get_limit() {
		$option = get_option('xpil_options');

		if(isset($option['img_limit'])){
			$limit = $option['img_limit'];
		} else {
			$limit = $this->xp_limit();
		}

		return $limit;
	}

	public function output_limit() {
		$limit = $this->get_limit();
		$limit_output = $limit;

		return $limit_output;
	}

	public function xp_limit() {
		$output = wp_max_upload_size();
		$megabyte = 1024 * 1024;
		$output = round($output / $megabyte, 2);

		return $output;
	}

	public function error_message($file) {
		$size = $file['size'];
		$size = $size / (1024 * 1024);
		$type = $file['type'];
		$is_image = strpos($type, 'image');
		$limit = $this->get_limit();
		$limit_output = $this->output_limit();
		$unit = 'MB';

		if(($size > $limit) && ($is_image !== false)) {
			$file['error'] = 'Image files must be smaller than '.$limit_output.$unit;
			if(XPIL_DEBUG) {
				$file['error'] .= ' [filesize = '.$size.', limit ='.$limit.']';
			}
		}
		return $file;
	}

	public function load_styles() {
		$limit = $this->get_limit();
		$limit_output = $this->output_limit();
		$wplimit = $this->xp_limit();
		$unit = 'MB';

		?>
		<!-- .Custom Max Upload Size -->
		<style type="text/css">
		.after-file-upload {
			display: none;
		}
		<?php if($limit < $wplimit) : ?>
		.upload-flash-bypass::after {
			content: 'Maximum image limit: '<?php echo $limit_output.$unit; ?>;
			display: block;
			margin: 15px 0;
		}
		<?php endif; ?>

		</style>
		<!-- END Custom Max Upload Size -->
		<?php
	}
}

$xp_image_limit = new xp_image_limit;
//add_action('admin_head', array($xp_image_limit, 'load_styles'));

function load_plugin_xpil() {
	load_plugin_textdomain('xpil-plugin', FALSE, basename(dirname( __FILE__ )) . '/languages/');
}
add_action('plugins_loaded', 'load_plugin_xpil');