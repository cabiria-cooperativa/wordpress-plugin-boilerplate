<?php

abstract class WpbCustomPost implements Wpb {

    abstract function __construct();

    /**
     * Attivazione plugin
     */
    public function activation(){}

    /**
     * Disattivazione plugin
     */
    abstract function deactivation();

    /**
     * Aggiunta custom post
     */
	public function add_cpt($slug, $args) {
        register_post_type($slug, $args);
    }

    /**
     * Rimozione custom post
     */
    public function remove_cpt($slug) {
        global $wpdb;
        global $wp_post_types;

        $prefix = $wpdb->prefix;
        if (post_type_exists($slug)) {

            // deregistro il cpt
            unset($wp_post_types[$slug]);

            // rimuovo la pagina di menu
            remove_menu_page($slug);

            // recupero le revisioni del custom post
            $rows = $wpdb->get_results ("SELECT ID FROM {$prefix}posts WHERE post_type = '" . $slug . "'");
            $ids = '';
            for ($i = 0; $i < count($rows); $i++) {
                $ids .= $rows[$i]->ID . ',';
            }
            $ids = substr($ids, 0, -1);

            //rimuovo le revisioni
            $query = "DELETE FROM {$prefix}posts WHERE post_type = 'revision' and post_parent IN (%$)";

            $result = $wpdb->query($wpdb->prepare($query, array($ids)));

            // rimuovo i custom post e i relativi meta
            $query = "DELETE a,b,c FROM {$prefix}posts a LEFT JOIN {$prefix}term_relationships b ON (a.ID = b.object_id) LEFT JOIN {$prefix}postmeta c ON (a.ID = c.post_id) WHERE a.post_type = %s";
            $result = $wpdb->query($wpdb->prepare($query, array($slug)));

        }
    }

}


