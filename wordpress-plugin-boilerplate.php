<?php
/**
 * Plugin Name: WPB - WordPress Plugin Boilerplate
 * Plugin URI: https://github.com/cabiria-cooperativa/wordpress-plugin-boilerplate
 * Description: Struttura di base per un plugin
 * Version: 1.7.0
 * Author: Simone Alati
 * Author URI: https://www.simonealati.it
 * Text Domain: wpb
 */

if (!defined('WPINC')) die;

/* classi core */
include_once __DIR__ . '/inc/wpb.class.php';
include_once __DIR__ . '/inc/wpb-settings.class.php';
include_once __DIR__ . '/inc/wpb-custompost.class.php';
include_once __DIR__ . '/inc/wpb-menu.class.php';

/* classi demo */
include_once __DIR__ . '/inc/demo-settings.class.php';
include_once __DIR__ . '/inc/demo-custompost.class.php';

WpbFactory::getInstance(__FILE__, new MySettings());
WpbFactory::getInstance(__FILE__, new MyCustomPost());