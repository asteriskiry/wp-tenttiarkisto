<?php

/**
 * Template Name: Pöytäkirjat-single
 **/

get_header();
/**
 * Asteriski WP teemaa varten
 */
?>

<header class="page-header">
    <div class="overlay-dark"></div>
    <div class="container breadcrumbs-wrapper">
        <div class="breadcrumbs d-flex flex-column justify-content-center">
            <h3><?php wp_title(''); ?></h3>
        </div>
    </div>
</header>

<div class="tentit-single">
<script>
jQuery(function ($)  {
    $(window).load(function() {
        $('#loadOverlay').fadeOut('slow');
    })
})
</script>
<?php

/* Loop joka hakee tiedot */

if ( have_posts() ) : while ( have_posts() ) : the_post();
    global $post;

    /* Tallennetaan tiedot muuttujiin kannasta */

	$pdf_id = (int) carbon_get_post_meta($post->ID, 't_file_id');
	$pdfurl = wp_get_attachment_url($pdf_id);
    $slug = get_permalink();
	$pm = carbon_get_post_meta( $post->ID, 't_paivamaara' );
    $kurssi = get_the_terms( $post->ID, 'kurssi' );
    $post_type = get_post_type();
    if ( $post_type )
    {
        $post_type_data = get_post_type_object( $post_type );
        $post_type_slug = $post_type_data->rewrite['slug'];
    }

    /* Generoidaan HTML */
	echo '<a class="hvr-grow" href="' . $pdfurl . '" download>Lataa tentti <i class="fa fa-paperclip"></i></a>';

	echo '<div class="t-single-meta-content">';
	echo '<table>';
	echo '<tr>';
	echo '<td><strong>Kurssi</strong></td><td>' . $kurssi[0]->name . '</td>';
	echo '</tr><tr>';
	echo '<td><strong>Päivämäärä</strong></td><td>' . $pm . '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';

	echo '<iframe id="riski-pdf" width="100%" src="'.$pdfurl.'"></iframe>';

	echo '<div class="t-pagination">';
	echo '<div class="t-buttons-left">';
	echo previous_post_link('%link', '<i class="fa fa-chevron-left"></i> Edellinen');
	echo '</div>';
	echo '<div class="t-buttons-right">';
	echo next_post_link('%link', 'Seuraava <i class="fa fa-chevron-right"></i>');
	echo '</div>';
	echo '</div>';

    echo '<div class="t-buttons">';
    echo '<a href="' . get_site_url() . '/' . $post_type_slug . '"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Takaisin selailuun</a>';
    echo '</div>';

    echo '</div>';
    echo '</div>';
endwhile; endif;

get_footer();
