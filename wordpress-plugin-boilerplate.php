<?php
/**
 * Plugin Name: Plugin Boilerplate
 * Plugin URI: https://www.cabiria.net
 * Description: Struttura di base per un plugin
 * Version: 1.2.0
 * Author: Simone Alati
 * Author URI: https://www.cabiria.net
 * Text Domain: prefix_plugin
 */

if (!defined('WPINC')) die;

prefix_plugin::init();

class prefix_plugin {

    private static $instance;

    const SLUG = 'prefix_plugin';

    public static function init() {
        if (self::$instance == null) self::$instance = new prefix_plugin();
    }

    private function __construct() {

        /* custom post */
        // $this->cpt_name = self::SLUG;
        // $this->cpt_slug = self::SLUG;
        // add_action('init', array(&$this, 'add_cpt'), 0);     

        /* azioni */        	
        add_action('wp_enqueue_scripts', array(&$this, 'scripts_and_styles'));
        add_action('admin_menu', array(&$this, 'add_settings_page'));
        add_action('wp_ajax_nopriv_hello_world_ajax', array(&$this, 'hello_world_ajax'));
        add_action('wp_ajax_hello_world_ajax', array(&$this, 'hello_world_ajax'));

        /* shortcode */
        add_shortcode('prefix_render', array(&$this, 'render'));

        /* attivazione e disattivazione */
        register_activation_hook(__FILE__, array(&$this, 'activation'));
        register_deactivation_hook( __FILE__, array(&$this, 'deactivation'));
    }

    /**
     * Attivazione plugin
     */
    public function activation(){
        $this->add_settings();
    }

    /**
     * Disattivazione plugin
     */
    public function deactivation(){
		//$this->remove_cpt();
        $this->remove_settings();
	  }

    /**
     * Aggiunta CSS e javascript
     */
    public function scripts_and_styles() {
        wp_enqueue_style( 'prefix_plugin', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' , array(), mt_rand());
        wp_enqueue_script('prefix_plugin', plugin_dir_url( __FILE__ ) . 'assets/js/wordpress-plugin-boilerplate.js', array('jquery'), mt_rand(), true);
        wp_localize_script('init', 'init_ajax', array('url' => admin_url('admin-ajax.php')));
    }

    /**
     * Shortcode per il rendering
     */
    public function render($atts, $content = null) {
        extract(shortcode_atts(array(
            'par1' => 'Hello',
            'par2' => 'world'
            ), $atts,  'render'));
        ob_start();
		?>
        <p class="helloworld">
            <?php echo $par1 ?> <?php echo $par2 ?>!
        </p>
        <?php
        return ob_get_clean();
    }

    /**
     * Aggiunta custom post
     */
	public function add_cpt() {
        $labels = array(
            'name'                  => _x( 'Post Types', 'Post Type General Name', 'prefix_plugin' ),
            'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'prefix_plugin' ),
            'menu_name'             => __( 'Post Types', 'prefix_plugin' ),
            'name_admin_bar'        => __( 'Post Types', 'prefix_plugin' ),
            'archives'              => __( 'Item Archives', 'prefix_plugin' ),
            'parent_item_colon'     => __( 'Parent Item:', 'prefix_plugin' ),
            'all_items'             => __( 'All Items', 'prefix_plugin' ),
            'add_new_item'          => __( 'Add New Item', 'prefix_plugin' ),
            'add_new'               => __( 'Add New', 'prefix_plugin' ),
            'new_item'              => __( 'New Item', 'prefix_plugin' ),
            'edit_item'             => __( 'Edit Item', 'prefix_plugin' ),
            'update_item'           => __( 'Update Item', 'prefix_plugin' ),
            'view_item'             => __( 'View Item', 'prefix_plugin' ),
            'search_items'          => __( 'Search Item', 'prefix_plugin' ),
            'not_found'             => __( 'Not found', 'prefix_plugin' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'prefix_plugin' ),
            'featured_image'        => __( 'Featured Image', 'prefix_plugin' ),
            'set_featured_image'    => __( 'Set featured image', 'prefix_plugin' ),
            'remove_featured_image' => __( 'Remove featured image', 'prefix_plugin' ),
            'use_featured_image'    => __( 'Use as featured image', 'prefix_plugin' ),
            'insert_into_item'      => __( 'Insert into item', 'prefix_plugin' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'prefix_plugin' ),
            'items_list'            => __( 'Items list', 'prefix_plugin' ),
            'items_list_navigation' => __( 'Items list navigation', 'prefix_plugin' ),
            'filter_items_list'     => __( 'Filter items list', 'prefix_plugin' ),
        );
        $rewrite = array(
            'slug'                  => $this->cpt_slug,
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __( 'Post Type', 'prefix_plugin' ),
            'description'           => __( 'Post Type Description', 'prefix_plugin' ),
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
        register_post_type( $this->cpt_name, $args );
    }

    /**
     * Rimozione custom post
     */
    private function remove_cpt() {
        global $wpdb;
        global $wp_post_types;

        $prefix = $wpdb->prefix;
        if (post_type_exists($this->cpt_name)) {

            // deregistro il cpt
            unset($wp_post_types[$this->cpt_name]);

            // rimuovo la pagina di menu
            remove_menu_page($this->cpt_slug);

            // recupero le revisioni del custom post
            $rows = $wpdb->get_results ("SELECT ID FROM {$prefix}posts WHERE post_type = '{$this->cpt_slug}'");
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
            $result = $wpdb->query($wpdb->prepare($query, $this->cpt_slug));

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
            'prefix_plugin',
            array(&$this,'render_settings_page')
        );
    }

    /**
     * Salvataggio impostazioni
     */
    private function add_settings() {
        //add_option('key', 'value');
    }

    /**
     * Rimozione impostazioni
     */
    private function remove_settings() {
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

