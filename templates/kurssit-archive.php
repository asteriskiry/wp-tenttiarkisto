<?php

/**
 * Template Name: Kurssit-archive
 **/

get_header();

/**
 * Asteriski WP teemaa varten
 */
?>

<?php
echo '<h3  class="t-heading">' . get_the_terms( $post->ID, 'kurssi' )[0]->name . '</h3>';
echo '<p><a href="' . get_site_url() . '/tenttiarkisto"><i class="fa fa-arrow-left" aria-hidden="true"></i> Takaisin tenttiarkistoon</a></p>';
$args_by_year = array(
    'post_type' 		=> 'tentit',
    'posts_per_page'        => -1,
    'tax_query' 		=> array(
        array(
            'taxonomy' => 'kurssi',
            'field' => 'slug',
            'terms' => get_the_terms( $post->ID, 'kurssi' )[0]->name,
        ),
    ),
);

$pk_by_year = new WP_Query( $args_by_year );
if ( $pk_by_year-> have_posts() ) :
?>
<div id='tentit'>
    <table id="t-taulukko" class="row-border">
        <thead>
            <tr class="t-rivi">
                <th class="t-indeksit">Tentti </th>
                <th class="t-indeksit">Päivämäärä </th>
            </tr>
        </thead>
    <tbody>

<?php
while ( $pk_by_year->have_posts() ) : $pk_by_year->the_post();

    global $post;
    $title = get_the_title();
    $custom_pdf_data = get_post_meta($post->ID, 'custom_pdf_data');
    $pdfurl = $custom_pdf_data[0]['src'];
    $slug = get_permalink();
    $pm = get_post_meta( $post->ID, 't_paivamaara', true );
    $thumbnail = $custom_pdf_data[0]['tnMed'];

    /* HTML: dynaamiset kentät*/
    echo '<tr class="item">';
    echo '<td><a class="hvr-grow-custom-smaller pdf-link" href="' . $pdfurl . '">' . $title . '</a></td>';
    echo '<td> ' . $pm  . '</td>';
    echo '</tr>';
endwhile;
echo '</tbody>';
echo '</table>';
	?>
	<div id='dialog' style='display:none'>
		<div>
			<iframe id="riski-pdf" width='100%' height='100%' src=''></iframe>
		</div>
	</div>
	<?php
echo '</div>';
endif;

get_footer();