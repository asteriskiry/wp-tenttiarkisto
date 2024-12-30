<?php

/**
 * Tenttiarkisto
 **/

/* Custom post type "Tentit" rekisteröinti */

function wpark_t_register_post_type()
{
    $singular = 'Tentti';
    $plural = 'Tentit';
    $slug = 'tentit';

    $labels = array(
        'name'                  => $plural,
        'singular_name'         => $singular,
        'add_name'              => 'Lisää uusi',
        'add_new_item'          => 'Lisää uusi ' . $singular,
        'edit'                  => 'Muokkaa',
        'edit_item'             => 'Muokkaa tenttiä',
        'new_item'              => 'Uusi ' . $singular,
        'view'                  => 'Näytä ' . $singular,
        'view_item'             => 'Näytä ' . $singular,
        'search_term'           => 'Etsi tenttiä',
        'parent'                => 'Vanhempi ' . $singular,
        'not_found'             => 'Tenttejä ei löytynyt',
        'not_found_in_trash'    => 'Tenttejä ei löytynyt roskakorista',
        'menu_name'             => 'Tenttiarkisto'
    );

    $args = array(
        'label'                 => $plural,
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
		'show_ui'               => true,
		'delete_with_user'      => false,
		'show_in_rest' => true,
		'rest_base' => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'has_archive'           => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'show_in_admin_bar'     => true,
		'menu_position'         => 10,
		'menu_icon'             => 'dashicons-welcome-learn-more',
		'can_export'            => true,
        'query_var'             => true,
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
		'hierarchical'          => false,
        'capabilities'       => array(
            'publish_posts' => 'publish_exams',
            'edit_posts' => 'edit_exams',
            'edit_others_posts' => 'edit_others_exams',
            'delete_posts' => 'delete_exams',
            'delete_others_posts' => 'delete_others_exams',
            'read_private_posts' => 'read_private_exams',
            'edit_post' => 'edit_exam',
            'delete_post' => 'delete_exam',
            'read_post' => 'read_exam'
        ),
        'taxonomies'            => array( 'kurssi', ),
        'rewrite'               => array(
            'slug'                  => 'tenttiarkisto',
            'with_front'            => true,
        ),
        'supports'              => array(
            'title',
            //'comments',
            // 'editor',
            // 'custom-fields',
        )
    );
    register_post_type($slug, $args);
}
add_action('init', 'wpark_t_register_post_type');

/* Custom taxonomyn "Kurssit" rekisteröinti tenteille */

function wpark_t_register_taxonomy_kurssi()
{
    $plural = 'Kurssit';
    $singular = 'Kurssi';

    $labels = array(
        'name'                       => $singular,
        'singular_name'              => $singular,
        'search_items'               => 'Etsi kurssia',
        'popular_items'              => 'Suositut kurssit',
        'all_items'                  => 'Kaikki kurssit',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Muokkaa kurssia',
        'update_item'                => 'Päivitä ' . $singular,
        'add_new_item'               => 'Lisää uusi ' . $singular,
        'new_item_name'              => 'Nimeä ' . $singular,
        'separate_items_with_commas' => 'Erottele ' . $plural . ' pilkuilla',
        'add_or_remove_items'        => 'Lisää tai poista kursseja',
        'choose_from_most_used'      => 'Valitse suosituimmista kursseista',
        'not_found'                  => 'Kursseja ei löytynyt',
        'menu_name'                  => $plural,
    );

    $args = array(
        'hierarchical'          => true,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'kurssi' ),
        'capabilities'          => array(
            'manage_terms' => 'manage_courses',
            'edit_terms' => 'edit_courses',
            'delete_terms' => 'delete_courses',
            'assign_terms' => 'assign_courses'
        ),
		/** @link wpark_t_taxonomy_meta_box */
        'meta_box_cb'           => 'wpark_t_taxonomy_meta_box',
    );
    register_taxonomy('kurssi', 'tentit', $args);
}
add_action('init', 'wpark_t_register_taxonomy_kurssi');

/* Perus tenttiarkistokäyttäjä ei nää kun omat mediansa */

function wpb_show_current_user_attachments($query)
{
    $user_id = get_current_user_id();
    $userRole = wp_get_current_user()->roles[0];
    if ($userRole == 'exam_role') {
        $query['author'] = $user_id;
    }
    return $query;
}
// Maybe not necessary anymore ? - 2024 m
//add_filter('ajax_query_attachments_args', 'wpb_show_current_user_attachments');

/* Lisää käyttäjäroolit tenttien ja kurssien lisäämiseen */

function wpark_t_add_roles()
{
    add_role(
        'exam_role',
        'Tenttiarkistokäyttäjä',
        array(
            // tentit
            'edit_exams' => true,
            'edit_others_exams' => true,
            'delete_exams' => true,
            'delete_others_exams' => true,
            'read_private_exams' => true,
            'edit_exam' => true,
            'delete_exam' => true,
            'read_exam' => true,
            // kurssit
            'manage_courses' => true,
            'edit_courses' => true,
            'assign_courses' => true,
            // muut
            'upload_files' => true
        )
    );
}

add_action('init', 'wpark_t_add_roles');

/* Lisätään custom capabilitit vakiorooleille */

function wpark_t_add_caps()
{
    $editor = get_role('editor');
    $administrator = get_role('administrator');
    $author = get_role('author');

    $capabilities = array(
		'publish_exams',
		'edit_exams',
		'edit_others_exams',
		'delete_exams',
		'delete_others_exams',
		'read_private_exams',
		'edit_exam',
		'delete_exam',
		'read_exam'
	);

    foreach ($capabilities as $cap) {
        if(!empty($administrator)) $administrator->add_cap($cap);
        if(!empty($editor)) $editor->add_cap($cap);
        if(!empty($author)) $author->add_cap($cap);
    }
}
add_action('init', 'wpark_t_add_caps');

/* Lähetetään mailia sillon kun joku postaa tentin
 * Myös tentin postanneelle kun opintomateriaalivastaava hyväksyy
 * */

function wpark_t_send_email($ID, $post)
{
    // Opintomateriaalivastaavan email
    $opintomateriaalivastaava = get_option("opintomateriaalivastaava");

    // Lähetä opintomateriaalivastaavalle
    $edit_link                = get_edit_post_link($post->ID, '');
    $username                 = get_userdata($post->post_author);
    $username_last_edit       = get_the_modified_author();
    $subject                  = '[*] Uusi tentti odottaa hyväksymistäsi';
    $message                  = 'Uusi tentti odottaa hyväksymistäsi.';
    $message                 .= "\r\n\r\n";
    $message                 .= 'Lisääjä' . ': ' . $username->user_login . "\r\n";
    $message                 .= 'Otsikko' . ': ' . $post->post_title . "\r\n";
    $message                 .= 'Viimeinen muokkaaja' . ': ' . $username_last_edit . "\r\n";
    $message                 .= 'Viimeisen muokkauksen pvm' . ': ' . $post->post_modified;
    $message                 .= "\r\n\r\n";
    $message                 .= 'Julkaise tästä' . ': ' . $edit_link . "\r\n";
    wp_mail($opintomateriaalivastaava, $subject, $message);
}
add_action('pending_tentit', 'wpark_t_send_email', 10, 3);

/* Lisäyssivun taxonomioiden meta boxit */

function wpark_t_taxonomy_meta_box($post, $meta_box_properties)
{
	$myvals = get_post_meta(get_the_ID());

	foreach($myvals as $key=>$val)
	{
		echo $key . ' : ' . $val[0] . '<br/>';
	}
    $taxonomy = $meta_box_properties['args']['taxonomy'];
    $tax = get_taxonomy($taxonomy);
    $terms = get_terms($taxonomy, array('hide_empty' => 0));
    $name = 'tax_input[' . $taxonomy . ']';
    $postterms = get_the_terms($post->ID, $taxonomy);
    $current = ($postterms ? array_pop($postterms) : false);
    $current = ($current ? $current->term_id : 0); ?>

    <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

        <input type="text" class="kurssihaku" id="kurssi-input" onkeyup="wparkFilter()" placeholder="Hae kurssia..">
        <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
            <input name="tax_input[<?php echo $taxonomy; ?>][]" value="0" type="hidden">
            <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:symbol" class="categorychecklist form-no-clear">

<?php
    foreach ($terms as $term) {
        $id = $taxonomy.'-'.$term->term_id; ?>

        <li id="<?php echo $id?>">
            <label class="selectit"><input required value="<?php echo $term->term_id; ?>" name="tax_input[<?php echo $taxonomy; ?>][]" id="in-<?php echo $id; ?>"<?php if ($current === (int)$term->term_id) {
            ?>checked="checked"<?php
        } ?> type="radio"><div class="taxitem"><?php echo $term->name; ?></div></label>
            </li>
<?php
    } ?>
            </ul>
        </div>
    </div>
<?php
}


/* Templojen lataus */

function wpark_t_load_archive_templates($original_template)
{
	if (is_tax('kurssi')) {
		return plugin_dir_path(__FILE__) . 'templates/kurssit-archive.php';
	}
	if (is_post_type_archive('tentit') || is_search()) {
		return plugin_dir_path(__FILE__) . 'templates/tentit-archive.php';
	}

	return $original_template;
}

add_action('archive_template', 'wpark_t_load_archive_templates');

function wpark_t_load_singular_templates($original_template)
{
	if  (is_singular('tentit')) {
		return plugin_dir_path(__FILE__) . 'templates/tentit-single.php';
	}

	return $original_template;
}

add_action('singular_template', 'wpark_t_load_singular_templates');
add_action('single_template', 'wpark_t_load_singular_templates');
