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

use Carbon_Fields\Container;
use Carbon_Fields\Field;

if (!defined('ABSPATH')) {
	exit;
}
const TENTIT_VERSION = '1.0.0'; // For forcing css updates
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-tentit.php');
require_once(plugin_dir_path(__FILE__) . 'wp-arkisto-enqueue.php');

add_action('carbon_fields_register_fields', 'crb_attach_theme_options');
function crb_attach_theme_options() {
	Container::make('theme_options', __('Asetukset'))
		->set_page_parent('edit.php?post_type=tentit')
		->set_page_menu_title('Tenttiarkiston asetukset')
		->add_fields([
			Field::make('text', 'opintomateriaalivastaava', 'Opintomateriaalivastaavan sähköposti'),
			Field::make('rich_text', 'tentit_ohjeistus', 'Tenttien lisäys ohjeistus'),
		]);
	Container::make('post_meta', 'Tentin tiedot')
		->where('post_type', '=', 'tentit')
		->add_fields([
			Field::make('date', 't_paivamaara', 'Tentin päivämäärä'),
			Field::make('file', 't_file_id', 'Tentin tiedosto'),
			Field::make('html', 'crb_html')
				->set_html('<p>Lisättyäsi tentin opintomateriaalivastaava hyväksyy tentin pikimmiten.</p>'),

		]);
}

add_action('wp_insert_post', 'update_tentit_title', 20, 1);

function update_tentit_title($post_id) {
	if (get_post_type($post_id) !== 'tentit') {
		return;
	}
	// Prevent recursion
	remove_action('wp_insert_post', 'update_tentit_title', 20);

	$paivamaara = carbon_get_post_meta($post_id, 't_paivamaara');
	$kurssi = get_the_terms($post_id, 'kurssi');

	if (!empty($paivamaara) && !empty($kurssi) && is_array($kurssi)) {
		$new_title = $kurssi[0]->name . ' - ' . $paivamaara;

		$current_title = get_the_title($post_id);
		if ($current_title !== $new_title) {
			wp_update_post([
				'ID' => $post_id,
				'post_title' => $new_title,
			]);
		}
	}
	add_action('wp_insert_post', 'update_tentit_title', 20);
}