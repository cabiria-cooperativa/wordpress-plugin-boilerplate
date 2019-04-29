<?php

class wpbpdemo extends wpbp {

    const SLUG = 'wpbpdemo';

    public function __construct() {
        parent::__construct();
        add_shortcode('wpdb_render', array($this, 'render'));
    }

    /**
     * Shortcode per il rendering
     */
    public static function render($atts, $content = null) {
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

}

new wpbpdemo();


