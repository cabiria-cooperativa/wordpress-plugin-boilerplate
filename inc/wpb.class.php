<?php

interface Wpb {

    public function activation();
    public function deactivation();

}

class WpbFactory {
    
    public function __construct($plugin_file, Wpb $plugin) {
        
        $this->plugin_file = $plugin_file;
        $this->plugin = $plugin;
    	
        add_action('wp_enqueue_scripts', array($this, 'scripts_and_styles'));

        /* TODO - da testare */
        add_action('wp_ajax_nopriv_hello_world_ajax', array($this, 'hello_world_ajax'));
        add_action('wp_ajax_hello_world_ajax', array($this, 'hello_world_ajax'));

        register_activation_hook($this->plugin_file, array($this, 'activation'));
        register_deactivation_hook($this->plugin_file, array($this, 'deactivation'));
    }

    public function activation() {
        $this->plugin->activation();
    }

    public function deactivation() {
        $this->plugin->deactivation();
    }

    public function scripts_and_styles() {
        wp_enqueue_style( 'wpb', plugin_dir_url($this->plugin_file) . 'assets/css/style.css' , array(), mt_rand());
        wp_enqueue_script('wpb', plugin_dir_url($this->plugin_file) . 'assets/js/wpb.js', array('jquery'), mt_rand(), true);
        wp_localize_script('init', 'init_ajax', array('url' => admin_url('admin-ajax.php')));
    }

    /* TODO - da testare */
    public function hello_world_ajax() {
        echo json_encode(array('Hello', 'world'));
        wp_die();
    }

}