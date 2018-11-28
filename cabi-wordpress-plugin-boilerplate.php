<?php
/**
 * Plugin Name: Cabiria Plugin Boilerplate
 * Plugin URI: https://www.cabiria.net
 * Description: Struttura di base per un plugin
 * Version: 1.0.0
 * Author: Cabiria
 * Author URI: https://www.cabiria.net
 * Text Domain: cabi
 */

class CabiPlugin {
    
    function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'init'));
        add_shortcode('cabi_plugin', array($this, 'render'));   
    }

    function init() {
        wp_enqueue_style( 'cabi_plugin', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' , array(), '1');
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

}

new CabiPlugin();