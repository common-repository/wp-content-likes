<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

class WordPress_Content_Likes_Admin_Settings
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private $options;
    private $name;
    private $labels;
    private $admin_table;
    private $custom_posts;

    /**
     * Start up.
     */
    public function __construct($name)
    {
        $this->custom_posts = $this->getCustomPostTypes();
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        $this->name = $name;
    }

    /**
     * Add options page.
     */
    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            $this->name,
            'manage_options',
            'wordpress-content-likes',
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback.
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option('wp_content_likes_option_name'); ?>

        <div class="wrap">
            <h1><?php echo $this->name; ?></h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields('wp_content_likes_option_group');
        do_settings_sections('wp_content_likes');
        submit_button();
        ?>
            </form>
            <div class="metabox-holder columns-2">
            <div class="meta-box-sortables ui-sortable">
                <form method="GET">
                    <?php
                        $this->admin_table->prepare_items();
        $this->admin_table->display(); ?>
                </form>
			</div>
        </div>
        <?php
    }

    /**
     * Register and add settings.
     */
    public function page_init()
    {
        $wp_content_likes_checkbox = [];

        register_setting(
            'wp_content_likes_option_group', // Option group
            'wp_content_likes_option_name' // Option name
        );

        add_settings_section(
            'setting_section_id', // ID
            'Which posts to enable?', // Title
            array($this, 'print_section_info'), // Callback
            'wp_content_likes' // Page
        );

        add_settings_field(
            'track_posts', // ID
            'Enable posts tracking', // Title
            array($this, 'posts_callback'), // Callback
            'wp_content_likes', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'track_pages', // ID
            'Enable page tracking', // Title
            array($this, 'page_callback'), // Callback
            'wp_content_likes',// Page
            'setting_section_id' // Section
        );

        foreach ($this->custom_posts as $k => $v) {
            add_settings_field(
                "track_custom_post_{$v->name}", // ID
                "Enable {$v->label} tracking", // Title
                array($this, 'custom_posts_callback'), // Callback
                'wp_content_likes',
                'setting_section_id',
                array('label' => "track_custom_post_{$v->name}", 'pos' => $v->menu_position)
            );

            register_setting(
                'wp_content_likes_option_group', // Option group
                'wp_content_likes_checkbox',
                array($this, 'sanitize_email_forms') // Sanitize
            );
        }

        if (!$this->admin_table) {
            $this->after_load_wordpress();
        }
    }

    /**
     * Get the settings option array and print one of its values.
     */
    public function posts_callback()
    {
        printf(
            '<input type="checkbox" id="id_number" name="wp_content_likes_option_name[track_posts]" %s />',
            isset($this->options['track_posts']) ? 'checked' : ''
        );
    }

    public function page_callback()
    {
        printf(
            '<input type="checkbox" id="title" name="wp_content_likes_option_name[track_pages]" %s />',
            isset($this->options['track_pages']) ? 'checked' : ''
        );
    }

    /**
     * Get the settings option array and print one of its values.
     */
    public function custom_posts_callback($args)
    {
        $current_option = $input_text = '';
        $current_option = get_option('wp_content_likes_checkbox');

        if (!empty($current_option[$args['label']])) {
            $current_option = 'checked';
        } else {
            $current_option = '';
        }

        printf(
            sprintf('<input type="checkbox" id="%s" name="wp_content_likes_checkbox[%s]" %s />', $args['label'], $args['label'], $current_option)
        );
    }

    public function print_section_info()
    {
        echo '';
    }

    public function after_load_wordpress()
    {
        $this->admin_table = new AdminTable();
        $this->admin_table->prepare_items();
    }

    public function getCustomPostTypes()
    {
        add_action('admin_init', function () {
            $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

            $this->custom_posts = $post_types;
        });
    }
}
