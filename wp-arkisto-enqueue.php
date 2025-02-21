<?php

/**
 * CSS-tyylien ja javascriptin rekisteröinti ja lataus
 **/

function wpark_admin_enqueue_scripts()
{
    global $pagenow, $typenow;

    /* Rekisteröidään admin-puolen scriptit ja tyylit */

    wp_register_style('jquery-style', plugins_url('assets/jquery-ui-theme-asteriski/jquery-ui.css', __FILE__));
    wp_register_script('w3js', plugins_url('assets/w3.js', __FILE__), true);
    wp_register_style('wpark-t-admin-css', plugins_url('css/admin-tentit.css', __FILE__));
    wp_register_script('wpark-t-admin-js', plugins_url('js/admin-tentit.js', __FILE__), array( 'jquery', 'jquery-ui-datepicker', 'media-upload' ), true);
    wp_register_script('wpark-t-pdf-uploader', plugin_dir_url(__FILE__) . 'js/admin-tentit-uploader.js', array('jquery', 'media-upload'), '0.0.2', true);

    /* Ladataan tenttiarkiston admin-puolelle */

    if (($pagenow == 'post.php' || $pagenow == 'post-new.php') && $typenow == 'tentit') {
        wp_enqueue_media();
        wp_enqueue_style('wpark-t-admin-css');
        wp_enqueue_script('wpark-t-admin-js');
        wp_enqueue_style('jquery-style');
        wp_enqueue_script('wpark-t-pdf-uploader');
        wp_localize_script('wpark-t-pdf-uploader', 'pdfUploads', array( 'pdfdata' => get_post_meta(get_the_ID(), 'custom_pdf_data', true) ));
    }

    if (get_current_screen() ->taxonomy === "kurssi") {
        wp_enqueue_style('wpark-t-admin-css');
    }
}

add_action('admin_enqueue_scripts', 'wpark_admin_enqueue_scripts');

function wpark_front_enqueue_scripts()
{

    /* Rekisteröidään frontin scriptit ja tyylit */

    wp_register_style('tentti-datatables-css', plugins_url('assets/datatables.min.css', __FILE__), [], TENTIT_VERSION);
    wp_register_script('tentti-datatables-js', plugins_url('assets/datatables.min.js', __FILE__), array( 'jquery' ), TENTIT_VERSION,true);
    wp_register_script('font-awesome', plugins_url('assets/fontawesome-all.js', __FILE__), TENTIT_VERSION,true);
    wp_register_style('font-awesome-legacy', plugins_url('assets/Font-Awesome-legacy/css/font-awesome.min.css', __FILE__), [], TENTIT_VERSION);
	wp_register_style('jquery-style', plugins_url('assets/jquery-ui-theme-asteriski/jquery-ui.css', __FILE__), [], TENTIT_VERSION);

    wp_register_style('wpark-t-front-css', plugins_url('css/front-tentit.css', __FILE__), [], TENTIT_VERSION);
    wp_register_script('wpark-t-front-js', plugins_url('js/front-tentit.js', __FILE__), array(
        'jquery',
        'jquery-ui-core',
        'jquery-ui-dialog',
        'jquery-ui-button',
        'jquery-ui-position',
        'tentti-datatables-js',
    ), TENTIT_VERSION, true);
    wp_register_script('wpark-t-kurssit-js', plugins_url('js/kurssit-archive.js', __FILE__), array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-dialog',
		'jquery-ui-button',
		'jquery-ui-position',
		'tentti-datatables-js',
	), TENTIT_VERSION,true);

    if (is_singular('tentit') || is_post_type_archive('tentit') || is_tax('kurssi')) {
        wp_deregister_style('jquery-ui-base-dialog');

        wp_enqueue_style('tentti-datatables-css');
        wp_enqueue_script('tentti-datatables-js');

        wp_enqueue_style('font-awesome-legacy');
        wp_enqueue_script('font-awesome');

        wp_enqueue_script('wpark-t-front-js');
        wp_enqueue_style('wpark-t-front-css');
    }

}

add_action('wp_enqueue_scripts', 'wpark_front_enqueue_scripts');