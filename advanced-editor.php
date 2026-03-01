<?php
/**
* Plugin Name: Advanced Editor
* Description: The ultimate production-grade Classic Editor extension. Disables Gutenberg and unlocks the full power of
TinyMCE with semantic archetypes.
* Version: 1.0.0
* Author: Dhanveersing
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: advanced-editor
*/

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

define('AE_VERSION', '1.0.0');
define('AE_DIR', plugin_dir_path(__FILE__));
define('AE_URL', plugin_dir_url(__FILE__));

/**
 * 1. Force the Classic Environment (Obliterate Gutenberg)
 */
// Disable Gutenberg Block Editor globally
add_filter('use_block_editor_for_post', '__return_false', 10);
// Disable Gutenberg Widget Editor globally
add_filter('use_widgets_block_editor', '__return_false');

// Remove the "Try Gutenberg" dashboard panel (if it exists)
remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');

/**
 * 2. Initialize the TinyMCE Engine
 */
require_once AE_DIR . 'includes/class-ae-tinymce.php';

// Initialize the class
function run_advanced_editor()
{
    new AE_TinyMCE();
}
add_action('plugins_loaded', 'run_advanced_editor');

/**
 * 3. Enqueue Global Editor Styles
 * Makes the writing area look better in the WP Admin
 */
function ae_add_editor_styles()
{
    // We add this to the tinyMCE iframe specifically
    add_editor_style(AE_URL . 'assets/css/editor-style.css');
}
add_action('after_setup_theme', 'ae_add_editor_styles');