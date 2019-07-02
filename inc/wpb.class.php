<?php

interface Wpb {

    public function activation();
    public function deactivation();

}

class WpbFactory {

    private static $plugin_file;
    private static $plugin;
    private static $instance = null;
    
    private function __construct($plugin_file, Wpb $plugin) {

        self::$plugin_file = $plugin_file;
        self::$plugin = $plugin;

        if (self::$instance == null) {
            
            add_action('wp_enqueue_scripts', array($this, 'scripts_and_styles'));
            add_action('wp_ajax_nopriv_hello_world_ajax', array($this, 'hello_world_ajax'));
            add_action('wp_ajax_hello_world_ajax', array($this, 'hello_world_ajax'));
            register_activation_hook(self::$plugin_file, array($this, 'activation'));
            register_deactivation_hook(self::$plugin_file, array($this, 'deactivation'));
            
        }
    }

    public static function getInstance($plugin_file, Wpb $plugin) {
        self::$instance = new WpbFactory($plugin_file, $plugin);
        return self::$instance;
    }

    public function activation() {
        self::$plugin->activation();
    }

    public function deactivation() {
        self::$plugin->deactivation();
    }

    public function scripts_and_styles() {
        wp_enqueue_style( 'wpb', plugin_dir_url(self::$plugin_file) . 'assets/css/style.css' , array(), mt_rand());
        wp_enqueue_script('wpb', plugin_dir_url(self::$plugin_file) . 'assets/js/wpb.js', array('jquery'), mt_rand(), true);
        wp_localize_script('wpb', 'init_ajax', array('url' => admin_url('admin-ajax.php')));
    }

    public function hello_world_ajax() {
        echo json_encode(array('Hello', 'world'));
        wp_die();
    }

}