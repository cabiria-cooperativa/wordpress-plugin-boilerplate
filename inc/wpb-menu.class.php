<?php

class WpbMenu implements Wpb {
    
    public function __construct() {
        add_shortcode('wpbmenu', array($this, 'render'));
    }

    /**
     * Attivazione plugin
     */
    public function activation(){}

    /**
     * Disattivazione plugin
     */
    public function deactivation(){}

    public function render($atts, $content = null) {
        
        extract(
            shortcode_atts(
                array(
                    'menu' => 'main_menu',
                    'class' => 'main_menu'
                ), 
                $atts, 'wpbmenu'
            ) 
        );

        return wp_nav_menu(array(
            'theme_location' => $menu,
            'menu_class'     => $class,
            'walker' => new WpbWalker()
        ));
    }
}

class WpbWalker extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth=0, $args=array(), $id = 0) {

        /* aggiungo uno span che racchiude <a> */
        $output .= '<li class="' . implode(" ", $item->classes) . '"><span class="a-container">';
        $output .= '<a href="' . $item->guid . '">' . $item->post_title . '</a>';
        $output .= '</span>'; /* N.B. non viene chiuso il <li> perché viene fatto da end_el() */
    }

    function start_lvl(&$output, $depth = 0, $args = array()) {
        /* aggiungo uno span che racchiude i sottomenu */
        $output .= '<span class="li-container"><ul class="sub-menu">';
    } 

    function end_lvl(&$output, $depth = 0, $args = array()) {
        /* chiuso lo span che racchiude i sottomenu */
        $output .= '</ul></span>';
    } 

}