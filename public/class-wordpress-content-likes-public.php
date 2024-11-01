<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @see       www.gulosolutions.com
 * @since      1.0.0
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @author     Gulo Solutions <rad@gulosolutions.com>
 */
class Wordpress_Content_Likes_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     *
     * @var string the current version of this plugin
     */

    private $version;

    private $postid;

    private $id;

    private $page_id;

    private $vote;

    private $user;

    /**
     * The count for each item tracked.
     *
     * @since    1.0.0
     *
     * @var int
     */
    public $like_count;

    /**
     * The ip content ID combinaiton for each item tracked.
     *
     * @since    1.0.0
     *
     * @var int
     */
    public $vote_cookie;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param string $plugin_name the name of the plugin
     * @param string $version     the version of this plugin
     */

    const TABLE_NAME='content_likesdata';

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->register_like_shortcode();
        $this->register_custom_hook();
        $this->export();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__).'/css/wordpress-content-likes-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name.'content_likes', plugin_dir_url(__FILE__).'/js/likesfrontend.js', array('jquery'), $this->version);
        wp_localize_script($this->plugin_name.'content_likes', 'ajax_data', ['ajax_url' => admin_url('admin-ajax.php')]);
        wp_localize_script($this->plugin_name.'content_likes', 'liked_count', ['ajax_url' => admin_url('admin-ajax.php')]);
    }

    public function register_like_shortcode()
    {
        add_shortcode($this->plugin_name.'_like_button', array($this, 'print_like_button'));
    }

    public function register_custom_hook()
    {
        function wp_content_likes_button()
        {
            return _s_like_button();
        }
        do_action('print_like_button');
    }

    public function print_like_button()
    {
        return _s_like_button();
    }

    public function _s_likebtn__handler()
    {
        global $wpdb;
        $table_name = $wpdb->prefix.self::TABLE_NAME;
        $sql = $cur_count = '';

        $this->user = sanitize_text_field($_POST['uniq']);
        $this->postid = sanitize_text_field($_POST['content_like_id']);

        // store total count for post as post meta
        $stored = get_post_meta($this->postid, 'likes', true);

        if (!$stored) {
            $stored = 0;
        }

        if ($stored < 0) {
            $stored = 0;
        }

        $sql = "SELECT id, vote_cookie, post_id, post_hash
            FROM {$table_name}  WHERE post_hash='{$this->user}'
            AND post_id='{$this->postid}'" ;

        $result = $wpdb->get_row($sql);
        $where = ['post_hash' => $this->user, 'post_id' => $this->postid ];

        // if current user previously liked post
        if ($result->id && $result->vote_cookie == 1) {
            $stored--;
            $cur_count = intval($stored);
            update_post_meta($this->postid, 'likes', $stored);

            $data = ['vote_cookie' => 2];

            $wpdb->update(
                $table_name,
                $data,
                $where
            );

            echo json_encode($cur_count);
            wp_die();
        } elseif ($result->id && ($result->vote_cookie == 2)) {
            $stored+=1;
            $cur_count = (int)$stored;
            $res = update_post_meta($this->postid, 'likes', $stored);

            $data = ['vote_cookie' => 1];

            $wpdb->update(
                $table_name,
                $data,
                $where
            );

            echo json_encode($cur_count);
            wp_die();

        // not liked or unliked
        } else {
            $stored+=1;
            $cur_count = intval($stored);

            // if key does not exist
            $update_response = update_post_meta($this->postid, 'likes', $stored);
            // save user
            $wpdb->insert(
                $table_name,
                [
                    'post_hash' => $this->user,
                    'post_id' => $this->postid,
                    'ip_addr' => $this->_s_sl_get_ip(),
                    'vote_cookie' => 1,
                ]
            );

            echo json_encode($cur_count);
            wp_die();
        }
    }

    public function _s_export_liked_count()
    {
        global $wpdb;
        $the_id = $_POST['get_count'];
        $user = $_POST['wp_content_user'];
        $table_name = $wpdb->prefix.self::TABLE_NAME;

        $vote_cookie = '';

        $like_count = get_post_meta($the_id, 'likes', true);
        $like_count = intval($like_count);

        if (!empty($user)) {
            $sql = "SELECT vote_cookie FROM $table_name WHERE post_id='{$the_id}' AND post_hash='{$user}'";
            $result = $wpdb->get_row($sql);
            $vote_cookie = $result->vote_cookie;
        }

        if (null === $result) {
            $vote_cookie = 0;
        }

        $data = ['LIKE' => $like_count, 'VOTE' => $result->vote_cookie];

        echo json_encode($data);

        wp_die();
    }

    public function _s_sl_get_ip()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        }
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        $ip = ($ip === false) ? '0.0.0.0' : $ip;
        return $ip;
    }

    public function export()
    {
        do_action('_s_check_vars');
    }
}
