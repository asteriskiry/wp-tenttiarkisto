<?php

/**
 * Plugin Name: WP Tenttiarkisto
 * Description: Tenttiarkisto
 * Plugin URI: https://asteriski.fi
 * Author: Maks Turtiainen, Asteriski ry
 * Version: 1.3
 * Author URI: https://github.com/asteriskiry
 * License: MIT
 * Collaboration: Roosa Virta, Asteriski ry
 **/

if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-tentit.php');
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php');

/* Poistetaan listauksesta quick edit */

function wptent_remove_quick_edit($actions)
{
    global $typenow;
    if ($typenow == 'tentit') {
        unset($actions['inline hide-if-no-js']);
    }
    
    return $actions;
}

add_filter('post_row_actions', 'wptent_remove_quick_edit');