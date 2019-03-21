<?php

/**
 * Tenttiarkisto
 **/

/* Custom post type "Tentit" rekisteröinti */

function wpark_t_register_post_type() {

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
        // 'capabilities'       => array(),
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

    register_post_type( $slug, $args );
}
add_action( 'init', 'wpark_t_register_post_type' );

/* Custom taxonomyn "Kurssit" rekisteröinti tenteille */

function wpark_t_register_taxonomy_kurssi() {

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
        'meta_box_cb'           => 'wpark_t_taxonomy_meta_box',
    ); 
    register_taxonomy( 'kurssi', 'tentit', $args );
}
add_action('init', 'wpark_t_register_taxonomy_kurssi');

/* Lisäyssivun taxonomioiden meta boxit */

function wpark_t_taxonomy_meta_box($post, $meta_box_properties) {
    $taxonomy = $meta_box_properties['args']['taxonomy'];
    $tax = get_taxonomy($taxonomy);
    $terms = get_terms($taxonomy, array('hide_empty' => 0));
    $name = 'tax_input[' . $taxonomy . ']';
    $postterms = get_the_terms( $post->ID, $taxonomy );
    $current = ($postterms ? array_pop($postterms) : false);
    $current = ($current ? $current->term_id : 0);
?>

<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

    <input type="text" class="kurssihaku" id="kurssi-input" onkeyup="wparkFilter()" placeholder="Hae kurssia..">
    <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
        <input name="tax_input[<?php echo $taxonomy; ?>][]" value="0" type="hidden">            
        <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:symbol" class="categorychecklist form-no-clear">

<?php
    foreach($terms as $term){
    $id = $taxonomy.'-'.$term->term_id; ?>

        <li id="<?php echo $id?>">
        <label class="selectit"><input required value="<?php echo $term->term_id; ?>" name="tax_input[<?php echo $taxonomy; ?>][]" id="in-<?php echo $id; ?>"<?php if( $current === (int)$term->term_id ){?>checked="checked"<?php } ?> type="radio"><div class="taxitem"><?php echo $term->name; ?></div></label>
        </li>
<?php   } ?>
        </ul>
    </div>
</div>
<?php
}

/* Tenttien lisäyssivun meta box (pvm, helppi) */

function wpark_t_add_metaboxes() {

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

function wpark_t_callback( $post ) {
    wp_nonce_field( basename( __FILE__  ), 'wpark_t_nonce' );
    $wpark_t_stored_meta = get_post_meta( $post->ID );   
?>

<div class="meta-row">
    <div class="meta-th">
        <label for="t-paivamaara" class="t-row-title">Tentin päivämäärä</label>
    </div>
    <div class="meta-td">
        <input type="text" pattern="[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}" class="t-row-content datepicker" required size=8  name="t_paivamaara" id="t-paivamaara" value="<?php if ( ! empty ( $wpark_t_stored_meta['t_paivamaara'] ) ) echo esc_attr( $wpark_t_stored_meta['t_paivamaara'][0]  ); ?>"/>
    </div>
</div>

<?php

}

function wpark_t_help_callback( $post ) {
    echo '<div class="meta-help">Jos et ole ihan varma mitä teet, katso <a href="' . admin_url( 'edit.php?post_type=tentit&page=t-ohjeet' ) . '">ohjeet</a></div>';
}

/* Metatietojen tallennus */

function wpark_t_meta_save( $post_id ) {
    $is_autosave = wp_is_post_autosave( $post_id  );
    $is_revision = wp_is_post_revision( $post_id  );
    $is_valid_nonce = ( isset ( $_POST[ 'wpark_t_nonce' ] ) && wp_verify_nonce( $_POST[ 'wpark_t_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    if ( isset ( $_POST[ 't_paivamaara' ] ) ) {
        update_post_meta( $post_id, 't_paivamaara', sanitize_text_field( $_POST[ 't_paivamaara' ] ) );
    }

    $t_title = array();
    $t_title['ID'] = $post_id;
    $kurssi = get_the_terms( $post_id, 'kurssi' );

    if ( get_post_type() == 'tentit' ) {
        $t_title['post_title'] = $kurssi[0]->name . ' - ' . get_post_meta( $post_id, 't_paivamaara', true );
    }

    remove_action( 'save_post', 'wpark_t_meta_save' );
    wp_update_post($t_title);
    add_action( 'save_post', 'wpark_t_meta_save' );
}

add_action( 'save_post', 'wpark_t_meta_save' );

/* Templojen lataus */

function wpark_t_load_templates( $original_template ) {
    
    if(is_tax('kurssi')) {
        return plugin_dir_path( __FILE__ ) . 'templates/kurssit-archive.php';
    }    
    if ( get_query_var( 'post_type' ) !== 'tentit' ) {
        return $original_template;
    }
    if ( is_archive() || is_search() ) {
        return plugin_dir_path( __FILE__ ) . 'templates/tentit-archive.php';
    } elseif(is_singular('tentit')) {
        return plugin_dir_path( __FILE__ ) . 'templates/tentit-single.php';
    } else {
        return get_page_template();
    }
    return $original_template;
}
add_action( 'template_include', 'wpark_t_load_templates' );

/* Ohjeet-sivu */

function wpark_t_add_help_page() {

    add_submenu_page( 
        'edit.php?post_type=tentit',
        'Tenttiarkiston ohjeet',
        'Ohjeet',
        'manage_options',
        't-ohjeet',
        'wpark_t_help_cb'
    );
}

add_action( 'admin_menu', 'wpark_t_add_help_page' ); 

function wpark_t_help_cb() {
?>
    <div class="help-page">
        <h1>Ohjeet tenttiarkiston hallintaan</h1>
        <h3>Tentin lisääminen</h3>
        <p>Sinulla tulisi olla tentistä PDF-tiedosto tietokoneellasi ennen aloitusta</p>
        <ol type="1">    
            <li>Ohjeita</li>
        </ol>

        <h3>Kurssien hallinta</h3>
        <p>Ohjeita</p>

    </div>

<?php 
}


