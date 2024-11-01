<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.gulosolutions.com
 * @since      1.0.0
 *
 * @package    Wordpress_Content_Likes
 * @subpackage Wordpress_Content_Likes/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wordpress_Content_Likes
 * @subpackage Wordpress_Content_Likes/includes
 * @author     Gulo Solutions <rad@gulosolutions.com>
 */
class Wordpress_Content_Likes
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wordpress_Content_Likes_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('WORDPRESS_CONTENT_LIKES_VERSION')) {
            $this->version = WORDPRESS_CONTENT_LIKES_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'wordpress-content-likes';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Wordpress_Content_Likes_Loader. Orchestrates the hooks of the plugin.
     * - Wordpress_Content_Likes_i18n. Defines internationalization functionality.
     * - Wordpress_Content_Likes_Admin. Defines all hooks for the admin area.
     * - Wordpress_Content_Likes_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wordpress-content-likes-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wordpress-content-likes-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wordpress-content-likes-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wordpress-content-likes-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/wordpress-content-likes-public-display.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wordpress-content-likes-admin-table.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wordpress-content-likes-query-content.php';

        $this->loader = new Wordpress_Content_Likes_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Wordpress_Content_Likes_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Wordpress_Content_Likes_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Wordpress_Content_Likes_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        if ($this->getPostsOptions() || $this->getCustomPostsOptions() || $this->getPagesOptions()) {
            $this->loader->add_action('wp_dashboard_setup', $plugin_admin, 'wordpress_content_likes_widget', 10);
        }
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'wpdocs_register_meta_boxes', 10);
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'wpdocs_register_meta_boxes_pages', 10);
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'wpdocs_register_meta_boxes_custom_post', 10);
        if ($this->getPostsOptions()) {
            $this->loader->add_filter('manage_posts_columns', $plugin_admin, 'likes_filter_posts_columns', 10);
            $this->loader->add_filter('manage_edit-post_sortable_columns', $plugin_admin, 'post_sortable_likes_column', 10);
        }
        if ($this->getCustomPostsOptions()) {
            $this->loader->add_action('init', $plugin_admin, 'likes_pages_custom_column', 10);
        }
        if ($this->getPagesOptions()) {
            $this->loader->add_filter('manage_pages_columns', $plugin_admin, 'likes_filter_pages_columns', 10);
            $this->loader->add_filter('manage_edit-page_sortable_columns', $plugin_admin, 'page_sortable_likes_column', 10);
        }
        $this->loader->add_action('add_meta_boxes', $plugin_admin, 'wpdocs_register_meta_boxes_custom_post', 10);
        $this->loader->add_action('init', $plugin_admin, 'wordpress_content_likes_custom_column', 10);
        $this->loader->add_action('wp_ajax_nopriv_delete_handler', $plugin_admin, '_s_delete_button_handler');
        $this->loader->add_action('wp_ajax_delete_handler', $plugin_admin, '_s_delete_button_handler');
        $this->loader->add_action('pre_get_posts', $plugin_admin, 'wp_content_likes_orderby', 10);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Wordpress_Content_Likes_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 30);
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 30);

        $this->loader->add_action('wp_ajax_nopriv__s_likebtn__handler', $plugin_public, '_s_likebtn__handler');
        $this->loader->add_action('wp_ajax__s_likebtn__handler', $plugin_public, '_s_likebtn__handler');

        $this->loader->add_action('wp_ajax_nopriv__s_export_liked_count', $plugin_public, '_s_export_liked_count');
        $this->loader->add_action('wp_ajax__s_export_liked_count', $plugin_public, '_s_export_liked_count');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wordpress_Content_Likes_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    public function getPostsOptions()
    {
        if (isset(get_option('wp_content_likes_option_name')['track_posts'])) {
            if (get_option('wp_content_likes_option_name')['track_posts'] == 'on') {
                return true;
            }
        }

        return false;
    }

    public function getCustomPostsOptions()
    {
        if (isset(get_option('wp_content_likes_option_name')['track_custom_posts'])) {
            if (get_option('wp_content_likes_option_name')['track_custom_posts'] == 'on') {
                return true;
            }
        }

        return false;
    }

    public function getPagesOptions()
    {
        if (isset(get_option('wp_content_likes_option_name')['track_pages'])) {
            if (get_option('wp_content_likes_option_name')['track_pages'] == 'on') {
                return true;
            }
        }

        return false;
    }
}
