<?php
/**
 * Plugin Name: Cabiria Plugin Boilerplate
 * Plugin URI: https://www.cabiria.net
 * Description: Struttura di base per un plugin
 * Version: 1.0.0
 * Author: Simone Alati
 * Author URI: https://www.cabiria.net
 * Text Domain: cabi
 */

class CabiPlugin {

    const SLUG = 'cabi-custom-post';
    
    function __construct() {
        
        $this->cpt_name = self::SLUG;
        $this->cpt_slug = self::SLUG;

		add_action('init', array($this, 'add_cpt'), 0);
        add_action('wp_enqueue_scripts', array($this, 'init'));
        add_shortcode('cabi_plugin', array($this, 'render'));

        /* azioni ajax */
        add_action('wp_ajax_nopriv_hello_world_ajax', array($this, 'hello_world_ajax'));
        add_action('wp_ajax_hello_world_ajax', array($this, 'hello_world_ajax'));

        /* attivazione e disattivazione plugin */
        register_activation_hook(__FILE__, array($this, 'activation'));
        register_deactivation_hook( __FILE__, array($this, 'deactivation'));   
    }

    function activation(){}

    function deactivation(){
		$this->remove_cpt();
	}

    function init() {
        wp_enqueue_style( 'cabi_plugin', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' , array(), mt_rand());
        wp_enqueue_script('cabi_plugin', plugin_dir_url( __FILE__ ) . 'assets/js/cabi-wordpress-plugin-boilerplate.js', array('jquery'), mt_rand(), true);
        wp_localize_script('init', 'init_ajax', array('url' => admin_url( 'admin-ajax.php' )));
    }

    function render($atts, $content = null) {
        extract(shortcode_atts(array(
            'par1' => 'Hello',
            'par2' => 'world'
            ), $atts,  'render'));
        ob_start();
		?>
        <p class="cabi_helloworld">
            <?php echo $par1 ?> <?php echo $par2 ?>!
        </p>
        <?php
        return ob_get_clean();
    }
	
	function add_cpt() {
        $labels = array(
            'name'                  => _x( 'Post Types', 'Post Type General Name', 'cabi' ),
            'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'cabi' ),
            'menu_name'             => __( 'Post Types', 'cabi' ),
            'name_admin_bar'        => __( 'Post Types', 'cabi' ),
            'archives'              => __( 'Item Archives', 'cabi' ),
            'parent_item_colon'     => __( 'Parent Item:', 'cabi' ),
            'all_items'             => __( 'All Items', 'cabi' ),
            'add_new_item'          => __( 'Add New Item', 'cabi' ),
            'add_new'               => __( 'Add New', 'cabi' ),
            'new_item'              => __( 'New Item', 'cabi' ),
            'edit_item'             => __( 'Edit Item', 'cabi' ),
            'update_item'           => __( 'Update Item', 'cabi' ),
            'view_item'             => __( 'View Item', 'cabi' ),
            'search_items'          => __( 'Search Item', 'cabi' ),
            'not_found'             => __( 'Not found', 'cabi' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'cabi' ),
            'featured_image'        => __( 'Featured Image', 'cabi' ),
            'set_featured_image'    => __( 'Set featured image', 'cabi' ),
            'remove_featured_image' => __( 'Remove featured image', 'cabi' ),
            'use_featured_image'    => __( 'Use as featured image', 'cabi' ),
            'insert_into_item'      => __( 'Insert into item', 'cabi' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'cabi' ),
            'items_list'            => __( 'Items list', 'cabi' ),
            'items_list_navigation' => __( 'Items list navigation', 'cabi' ),
            'filter_items_list'     => __( 'Filter items list', 'cabi' ),
        );
        $rewrite = array(
            'slug'                  => $this->cpt_slug,
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __( 'Post Type', 'cabi' ),
            'description'           => __( 'Post Type Description', 'cabi' ),
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
    
    function remove_cpt() {
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

    function hello_world_ajax() {
        echo json_encode(array('Hello', 'world'));
		wp_die(); /* previene che WordPress accodi '0' al risultato */
    }

}

new CabiPlugin();