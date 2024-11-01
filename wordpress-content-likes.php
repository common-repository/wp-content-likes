<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              www.gulosolutions.com
 * @since             1.0.0
 * @package           Wordpress_Content_Likes
 *
 * @wordpress-plugin
 * Plugin Name:       WP Content Likes
 * Plugin URI:        https://wordpress.org/plugins/wp-content-likes
 * Description:       Track likes for different types of WP content.
 * Version:           1.1.3
 * Author:            Gulo Solutions, LLC
 * Author URI:        https://www.gulosolutions.com/?utm_source=wp-admin&utm_medium=wp-plugin&utm_campaign=wp-content-likes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-content-likes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WORDPRESS_CONTENT_LIKES_VERSION', '1.1.3');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-content-likes-activator.php
 */
function activate_wordpress_content_likes()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-activator.php';
    Wordpress_Content_Likes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-content-likes-deactivator.php
 */
function deactivate_wordpress_content_likes()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-deactivator.php';
    Wordpress_Content_Likes_Deactivator::deactivate();
}

function activate_wordpress_content_likes_table()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-create-table.php';
    Wordpress_Content_Likes_Table_Activator::activate();
    Wordpress_Content_Likes_Table_Activator::install_table_data();
}


require plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes.php';
require plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-admin-table.php';
require plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-admin-settings.php';
require plugin_dir_path(__FILE__) . 'includes/class-wordpress-content-likes-admin-widget.php';

register_activation_hook(__FILE__, 'activate_wordpress_content_likes');
register_deactivation_hook(__FILE__, 'deactivate_wordpress_content_likes');
register_activation_hook(__FILE__, 'activate_wordpress_content_likes_table');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wordpress_content_likes()
{
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . '/wp-admin/includes/plugin.php');
    }

    // load settings
        $plugin_name = get_plugin_data(__FILE__, $markup = true, $translate = true)['Name'];
        $my_settings_page = new Wordpress_Content_Likes_Admin_Settings($plugin_name);

        $plugin = new Wordpress_Content_Likes();
        $plugin->run();
}
run_wordpress_content_likes();
