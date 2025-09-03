<?php
/**
 * Plugin Name:       WP-YouFlix
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Display a YouTube channel with a Netflix-style interface.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Jules
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-youflix
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'WP_YOUFLIX_VERSION', '1.0.0' );

// Include the admin menu and settings page
require_once plugin_dir_path( __FILE__ ) . 'admin/wp-youflix-admin.php';

// Include the public-facing functionality
require_once plugin_dir_path( __FILE__ ) . 'public/wp-youflix-public.php';
?>
