<?php

class MyCustomPost extends WpbCustomPost {

    const SLUG = 'my-custom-post';

    public function __construct() {
        add_action('init', array($this, 'add_new_cpt'), 0);
    }

    /**
     * Aggiunta custom post
     */
	public function add_new_cpt() {
        $labels = array(
            'name'                  => _x( 'Post Types', 'Post Type General Name', 'wpb' ),
            'singular_name'         => _x( 'Post Type', 'Post Type Singular Name', 'wpb' ),
            'menu_name'             => __( 'Post Types', 'wpb' ),
            'name_admin_bar'        => __( 'Post Types', 'wpb' ),
            'archives'              => __( 'Item Archives', 'wpb' ),
            'parent_item_colon'     => __( 'Parent Item:', 'wpb' ),
            'all_items'             => __( 'All Items', 'wpb' ),
            'add_new_item'          => __( 'Add New Item', 'wpb' ),
            'add_new'               => __( 'Add New', 'wpb' ),
            'new_item'              => __( 'New Item', 'wpb' ),
            'edit_item'             => __( 'Edit Item', 'wpb' ),
            'update_item'           => __( 'Update Item', 'wpb' ),
            'view_item'             => __( 'View Item', 'wpb' ),
            'search_items'          => __( 'Search Item', 'wpb' ),
            'not_found'             => __( 'Not found', 'wpb' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'wpb' ),
            'featured_image'        => __( 'Featured Image', 'wpb' ),
            'set_featured_image'    => __( 'Set featured image', 'wpb' ),
            'remove_featured_image' => __( 'Remove featured image', 'wpb' ),
            'use_featured_image'    => __( 'Use as featured image', 'wpb' ),
            'insert_into_item'      => __( 'Insert into item', 'wpb' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpb' ),
            'items_list'            => __( 'Items list', 'wpb' ),
            'items_list_navigation' => __( 'Items list navigation', 'wpb' ),
            'filter_items_list'     => __( 'Filter items list', 'wpb' ),
        );
        $rewrite = array(
            'slug'                  => self::SLUG,
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        );
        $args = array(
            'label'                 => __( 'Post Type', 'wpb' ),
            'description'           => __( 'Post Type Description', 'wpb' ),
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
        parent::add_cpt(self::SLUG, $args);
    }

    /**
     * Rimozione custom post
     */
    public function deactivation() {
        parent::remove_cpt(self::SLUG);
    }

}


