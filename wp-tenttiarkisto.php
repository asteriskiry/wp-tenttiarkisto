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
			Field::make( 'date', 't_paivamaara', 'Tentin päivämäärä' ),
			Field::make( 'file', 'gt_file_id', 'Tentin tiedosto' ),
			Field::make( 'html', 'crb_html' )
				->set_html('<p>Lisättyäsi tentin opintomateriaalivastaava hyväksyy tentin pikimmiten.</p>'),



		]);
}

add_action('carbon_fields_after_save_post', function ($post_id) {
	$paivamaara = carbon_get_post_meta($post_id, 't_paivamaara', true);
	$kurssi = get_the_terms($post_id, 'kurssi');

	if (get_post_type($post_id) == 'tentit' && $paivamaara && !empty($kurssi[0])) {
		$new_title = $kurssi[0]->name . ' - ' . $paivamaara;
		wp_update_post([
			'ID' => $post_id,
			'post_title' => $new_title,
		]);
	}
});

add_action('init', function () {
	if(get_option('t_pvm_migration_done')){
		return;
	}
	$args = [
		'post_type' => 'tentit',
		'post_status' => 'any',
		'posts_per_page' => - 1,
	];

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();

			// Get the current meta value
			$meta_value = get_post_meta($post_id, 't_paivamaara', true);
			update_post_meta($post_id, 'bu_t_paivamaara', $meta_value);

			// Check if the value matches the d.m.Y format
			if (preg_match('/^\d{1,2}\.\d{1,2}\.\d{4}$/', $meta_value)) {
				// Convert d.m.Y to Y-m-d
				$parts = explode('.', $meta_value);
				$normalized_date = sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);

				// Update the meta value
				update_post_meta($post_id, '_t_paivamaara', $normalized_date);
			}
		}
		wp_reset_postdata();
		update_option('t_pvm_migration_done', 1);
	}
});

add_action('init', function () {
	if(get_option('t_file_migration_done')){
		return;
	}

	$args = [
		'post_type' => 'tentit',
		'post_status' => 'any',
		'posts_per_page' => - 1,
	];

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();

			// Get the current meta value
			$meta_value = get_post_meta($post_id, 'custom_pdf_data', true);
			update_post_meta($post_id, 'bu_custom_pdf_data', $meta_value);

			// Check if the id exists
			if (!empty($meta_value['id'])) {

				// Update the meta value
				update_post_meta($post_id, '_t_file_id', $meta_value['id']);
			}
		}
		wp_reset_postdata();
		update_option('t_file_migration_done', 1);
	}
});
