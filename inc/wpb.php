<?php

abstract class wpbp {

    const SLUG = 'wpbp';

    protected function __construct() {

        /* custom post */
        // add_action('init', array(&$this, 'add_cpt'), 0);     

        /* azioni */        	
        add_action('wp_enqueue_scripts', array(&$this, 'scripts_and_styles'));
        add_action('admin_menu', array(&$this, 'add_settings_page'));
        add_action('wp_ajax_nopriv_hello_world_ajax', array(&$this, 'hello_world_ajax'));
        add_action('wp_ajax_hello_world_ajax', array(&$this, 'hello_world_ajax'));

        /* attivazione e disattivazione */
        register_activation_hook(__FILE__, array(&$this, 'activation'));
        register_deactivation_hook( __FILE__, array(&$this, 'deactivation'));
    }

    /**
     * Attivazione plugin
     */
    public function activation(){
        self::add_settings();
    }

    /**
     * Disattivazione plugin
     */
    public function deactivation(){
		//self::remove_cpt();
        self::remove_settings();
	  }

    /**
     * Aggiunta CSS e javascript
     */
    public function scripts_and_styles() {
        wp_enqueue_style( 'wpbp', plugin_dir_url( __FILE__ ) . '../assets/css/style.css' , array(), mt_rand());
        wp_enqueue_script('wpbp', plugin_dir_url( __FILE__ ) . '../assets/js/wordpress-plugin-boilerplate.js', array('jquery'), mt_rand(), true);
        wp_localize_script('init', 'init_ajax', array('url' => admin_url('admin-ajax.php')));
    }

    /**
     * Aggiunta custom post
     */
	public function add_cpt() {
        $labels = array(
            'name'                  => _x( 'Post Types', 'Post Type General Name', 'wpbp' ),
            'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'wpbp' ),
            'menu_name'             => __( 'Post Types', 'wpbp' ),
            'name_admin_bar'        => __( 'Post Types', 'wpbp' ),
            'archives'              => __( 'Item Archives', 'wpbp' ),
            'parent_item_colon'     => __( 'Parent Item:', 'wpbp' ),
            'all_items'             => __( 'All Items', 'wpbp' ),
            'add_new_item'          => __( 'Add New Item', 'wpbp' ),
            'add_new'               => __( 'Add New', 'wpbp' ),
            'new_item'              => __( 'New Item', 'wpbp' ),
            'edit_item'             => __( 'Edit Item', 'wpbp' ),
            'update_item'           => __( 'Update Item', 'wpbp' ),
            'view_item'             => __( 'View Item', 'wpbp' ),
            'search_items'          => __( 'Search Item', 'wpbp' ),
            'not_found'             => __( 'Not found', 'wpbp' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'wpbp' ),
            'featured_image'        => __( 'Featured Image', 'wpbp' ),
            'set_featured_image'    => __( 'Set featured image', 'wpbp' ),
            'remove_featured_image' => __( 'Remove featured image', 'wpbp' ),
            'use_featured_image'    => __( 'Use as featured image', 'wpbp' ),
            'insert_into_item'      => __( 'Insert into item', 'wpbp' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpbp' ),
            'items_list'            => __( 'Items list', 'wpbp' ),
            'items_list_navigation' => __( 'Items list navigation', 'wpbp' ),
            'filter_items_list'     => __( 'Filter items list', 'wpbp' ),
        );
        $rewrite = array(
            'slug'                  => self::SLUG,
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __( 'Post Type', 'wpbp' ),
            'description'           => __( 'Post Type Description', 'wpbp' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
            'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5.2,
            'menu_icon'             => 'dashicons-admin-post',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'custom-post-type',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $rewrite,
            'capability_type'       => 'page',
        );
        register_post_type(self::SLUG, $args);
    }

    /**
     * Rimozione custom post
     */
    private function remove_cpt() {
        global $wpdb;
        global $wp_post_types;

        $prefix = $wpdb->prefix;
        if (post_type_exists(self::SLUG)) {

            // deregistro il cpt
            unset($wp_post_types[self::SLUG]);

            // rimuovo la pagina di menu
            remove_menu_page(self::SLUG);

            // recupero le revisioni del custom post
            $rows = $wpdb->get_results ("SELECT ID FROM {$prefix}posts WHERE post_type = '{self::SLUG}'");
            $ids = '';
            for ($i = 0; $i < count($rows); $i++) {
                $ids .= $rows[$i]->ID . ',';
            }
            $ids = substr($ids, 0, -1);

            //rimuovo le revisioni
            $query = "DELETE FROM {$prefix}posts WHERE post_type = 'revision' and post_parent IN ($ids)";
            $result = $wpdb->query($wpdb->prepare($query));

            // rimuovo i custom post e i relativi meta
            $query = "DELETE a,b,c FROM {$prefix}posts a LEFT JOIN {$prefix}term_relationships b ON (a.ID = b.object_id) LEFT JOIN {$prefix}postmeta c ON (a.ID = c.post_id) WHERE a.post_type = %s";
            $result = $wpdb->query($wpdb->prepare($query, self::SLUG));

        }
    }

    /**
     * Ajax demo function
     */
    public function hello_world_ajax() {
        echo json_encode(array('Hello', 'world'));
		wp_die(); /* previene che WordPress accodi '0' al risultato */
    }

    /**
     * Aggiunta pagina di impostazione plugin
     */
    public function add_settings_page() {
        add_options_page(
            'My custom settings page',
            'Custom settings page',
            'manage_options',
            'wpbp',
            array(&$this,'render_settings_page')
        );
    }

    /**
     * Salvataggio impostazioni
     */
    private static function add_settings() {
        //add_option('key', 'value');
    }

    /**
     * Rimozione impostazioni
     */
    private static function remove_settings() {
        //delete_option('key');
    }

    /**
     * Pagina di impostazione plugin
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) wp_die('Non possiedi i permessi per accedere a questa pagina');
        ?>
        <div class="wrap">
            <h2>Setting title</h2>
            <?php
            if (isset($_POST['submit']) && wp_verify_nonce($_POST['modify_settings_nonce'], 'modify_settings')) {
                /* opzione da salvare */
                //update_option('key', 'value');
            }
            ?>
            <form method="post">
                <?php wp_nonce_field('modify_settings', 'modify_settings_nonce') ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

}