<?php

abstract class WpbSettings implements Wpb {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
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
        $this->remove_settings();
    }
    
    /**
     * Salvataggio impostazioni
     */
    abstract function add_settings();

    /**
     * Rimozione impostazioni
     */
    abstract function remove_settings();

    /**
     * Aggiunta della pagina delle impostazioni
     */
    abstract function add_settings_page();

}


