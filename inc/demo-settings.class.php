<?php

class MySettings extends WpbSettings {
    
    /**
     * Salvataggio impostazioni
     */
    public function add_settings() {
        add_option('demo', '1');
    }

    /**
     * Rimozione impostazioni
     */
    public function remove_settings() {
        delete_option('demo');
    }

    /**
     * Aggiunta della pagina delle impostazioni
     */
    public function add_settings_page() {
        add_options_page(
            'My custom settings page',
            'Custom settings page',
            'manage_options',
            'wpb',
            array($this,'render_settings_page')
        );
    }

    /**
     * Render della pagina delle impostazioni
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) wp_die('Non possiedi i permessi per accedere a questa pagina');
        ?>
        <div class="wrap">
            <h2>My demo settings</h2>
            <?php
            if (isset($_POST['submit']) && wp_verify_nonce($_POST['modify_settings_nonce'], 'modify_settings')) {
                update_option('demo', '2');
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


