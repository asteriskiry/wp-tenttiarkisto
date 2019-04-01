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
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'exclude_from_search'   => false,
        'show_in_nav_menus'     => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_admin_bar'     => true,
        'menu_position'         => 10,
        'menu_icon'             => 'dashicons-welcome-learn-more',
        'can_export'            => true,
        'delete_with_user'      => false,
        'hierarchical'          => false,
        'has_archive'           => true,
        'query_var'             => true,
        'capability_type'       => 'post',
        'map_meta_cap'          => true,
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
        'map_meta_cap' => true,
        'taxonomies'            => array( 'kurssi', ),
        'rewrite'               => array(
            'slug'                  => 'tenttiarkisto',
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => false,
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
    $slug = 'kurssi';

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
add_filter('ajax_query_attachments_args', 'wpb_show_current_user_attachments');

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

    $capabilities = array("edit_exams", "edit_others_exams", "delete_exams", "delete_others_exams", "read_private_exams", "edit_exam", "delete_exam", "read_exam", "publish_exams", "manage_courses", "edit_courses", "assign_courses", "delete_courses");
    foreach ($capabilities as $cap) {
        $administrator->add_cap($cap);
        $editor->add_cap($cap);
        $author->add_cap($cap);
    }
}
add_action('init', 'wpark_t_add_caps');

/* Lähetetään mailia sillon kun joku postaa tentin
 * Myös tentin postanneelle kun opintomateriaalivastaava hyväksyy
 * */

function wpark_t_send_email($ID, $post)
{
    if ( get_post_type( $post ) == 'tentit' )
    {
        // Opintomateriaalivastaavan email
        $opintomateriaalivastaava = get_option("opintomateriaalivastaava");

        // Lähetä opintomateriaalivastaavalle
        if ('pending' === $new_status && 'new' === $old_status && user_can($post->post_author, 'edit_exams') && ! user_can($post->post_author, 'publish_posts')) {
            $edit_link                = get_edit_post_link($post->ID, '');
            $preview_link             = get_permalink($post->ID) . '&preview=true';
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
            $result = wp_mail($opintomateriaalivastaava, $subject, $message);
        }
         // Lähetä tentin postaajalle ilmoitus hyväksymisestä
        elseif ('pending' === $old_status && 'publish' === $new_status && user_can($post->post_author, 'edit_exams') && ! user_can($post->post_author, 'publish_exams')) {
            $username = get_userdata($post->post_author);
            $url      = get_permalink($post->ID);
            $subject  = '[*] Lähettämäsi tentti hyväksytty';
            $message  = 'Lähettämäsi tentti on nyt hyväksytty tenttiarkistoon!' . "\r\n\r\n";
            $message .= $url;
            $result = wp_mail($username->user_email, $subject, $message);
        }
    }
}
add_action('pending_tentit', 'wpark_t_send_email', 10, 3);

/* Lisäyssivun taxonomioiden meta boxit */

function wpark_t_taxonomy_meta_box($post, $meta_box_properties)
{
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

/* Tenttien lisäyssivun meta box (pvm, helppi) */

function wpark_t_add_metaboxes()
{
    add_meta_box(
        'wpark_t_meta',
        'Tentin tiedot',
        'wpark_t_callback',
        'tentit',
        'normal',
        'high'
    );

    add_meta_box(
        'wpark_t_help',
        'Tiedote',
        'wpark_t_help_callback',
        'tentit',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'wpark_t_add_metaboxes');

/* Lisäyssivun html:n generointi */

function wpark_t_callback($post)
{
    wp_nonce_field(basename(__FILE__), 'wpark_t_nonce');
    $wpark_t_stored_meta = get_post_meta($post->ID); ?>

<div class="meta-row">
    <div class="meta-th">
        <label for="t-paivamaara" class="t-row-title">Tentin päivämäärä</label>
    </div>
    <div class="meta-td">
        <input type="text" pattern="[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}" class="t-row-content datepicker" required size=8  name="t_paivamaara" id="t-paivamaara" value="<?php if (! empty($wpark_t_stored_meta['t_paivamaara'])) {
        echo esc_attr($wpark_t_stored_meta['t_paivamaara'][0]);
    } ?>"/>
    </div>
</div>

<?php
}

function wpark_t_help_callback($post)
{
    echo '<div class="meta-help">Lisättyäsi tentin opintomateriaalivastaava hyväksyy tentin pikimmiten.</div>';
}

/* Metatietojen tallennus */

function wpark_t_meta_save($post_id)
{
    $is_autosave = wp_is_post_autosave($post_id);
    $is_revision = wp_is_post_revision($post_id);
    $is_valid_nonce = (isset($_POST[ 'wpark_t_nonce' ]) && wp_verify_nonce($_POST[ 'wpark_t_nonce' ], basename(__FILE__))) ? 'true' : 'false';

    if ($is_autosave || $is_revision || !$is_valid_nonce) {
        return;
    }
    if (isset($_POST[ 't_paivamaara' ])) {
        update_post_meta($post_id, 't_paivamaara', sanitize_text_field($_POST[ 't_paivamaara' ]));
    }

    $t_title = array();
    $t_title['ID'] = $post_id;
    $kurssi = get_the_terms($post_id, 'kurssi');

    if (get_post_type() == 'tentit') {
        $t_title['post_title'] = $kurssi[0]->name . ' - ' . get_post_meta($post_id, 't_paivamaara', true);
    }

    if (isset($_POST[ 'custom_pdf_data' ])) {
        $pdf_data = json_decode(stripslashes($_POST[ 'custom_pdf_data' ]));
        if (is_object($pdf_data[0])) {
            $pdf_data = array( 'id' => intval($pdf_data[0]->id), 'src' => esc_url_raw($pdf_data[0]->src), 'tnBig' => esc_url_raw($pdf_data[0]->tnBig), 'tnMed' => esc_url_raw($pdf_data[0]->tnMed), 'tnSmall' => esc_url_raw($pdf_data[0]->tnSmall) );
        } else {
            $pdf_data = [];
        }
        update_post_meta($post_id, 'custom_pdf_data', $pdf_data);
    }
}
add_action('save_post', 'wpark_t_meta_save');

/* Muuta otsikko metadatan perusteella */

function wpark_filter_post_data( $data, $postarr ) {
    if($data['post_type'] == 'tentit' && isset($_POST['t_paivamaara']))
    {
        $pvm = date('d.m.Y', strtotime($_POST[ 't_paivamaara' ]));
        $data['post_title'] = $pvm;
    }
    return $data;
}
add_filter( 'wp_insert_post_data' , 'wpark_filter_post_data' , '99', 2 );

/* Templojen lataus */

function wpark_t_load_templates($original_template)
{
    if (is_tax('kurssi')) {
        return plugin_dir_path(__FILE__) . 'templates/kurssit-archive.php';
    }
    if (get_query_var('post_type') !== 'tentit') {
        return $original_template;
    }
    if (is_archive() || is_search()) {
        return plugin_dir_path(__FILE__) . 'templates/tentit-archive.php';
    } elseif (is_singular('tentit')) {
        return plugin_dir_path(__FILE__) . 'templates/tentit-single.php';
    } else {
        return get_page_template();
    }
    return $original_template;
}
add_action('template_include', 'wpark_t_load_templates');

/* Uploaderin HTML */

function register_metaboxes()
{
    add_meta_box(
        'pdf_t_uploader_metabox',
        'Tentin tiedosto',
        'pdf_t_uploader_callback',
        'tentit',
        'normal'
    );
}
add_action('add_meta_boxes', 'register_metaboxes');

function pdf_t_uploader_callback($post_id)
{
    wp_nonce_field(basename(__FILE__), 'custom_pdf_t_nonce'); ?>

<div id="metabox_wrapper">
    <img id="pdf-tag"></img>
    <input type="hidden" id="pdf-hidden" name="custom_pdf_data">
    <input type="button" id="pdf-upload-button" class="button" value="Lisää tentti">
    <input type="button" id="pdf-delete-button" class="button" value="Poista tentti">
</div>

<?php
}

/* Astetukset-sivu */

function wpark_t_add_help_page()
{
    add_submenu_page(
        'edit.php?post_type=tentit',
        'Tenttiarkiston asetukset',
        'Asetukset',
        'manage_options',
        't-settings',
        'wpark_t_settings_cb'
    );
}

add_action('admin_menu', 'wpark_t_add_help_page');

function wpark_t_settings_cb()
{
    if (isset($_POST["update_settings"])) {
        $opintomateriaalivastaava = esc_attr($_POST["opintomateriaalivastaava"]);
        update_option("opintomateriaalivastaava", $opintomateriaalivastaava);
        ?>
        <div id="message" class="updated">Settings saved</div>
        <?php
    } else {
        $opintomateriaalivastaava = get_option("opintomateriaalivastaava");
    }
    ?>
<div class="help-page">
    <h1>Tenttiarkiston asetukset</h1>
    <form method="POST" action="">
    <label for="opintomateriaalivastaava">
        Opintomateriaalivastaavan email:
    </label>
    <input type="text" name="opintomateriaalivastaava" value="<?php echo $opintomateriaalivastaava;?>" />
    <input type="hidden" name="update_settings" value="Y" />
    <input type="submit" value="Tallenna" class="button-primary"/>
    </form>
    <p>
    </p>
</div>

<?php
}
