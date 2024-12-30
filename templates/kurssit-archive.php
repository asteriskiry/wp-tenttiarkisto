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
    $pdf_id = (int) carbon_get_post_meta($post->ID, 'gt_file_id');
	$slug = get_permalink();
    $pm = carbon_get_post_meta( $post->ID, 't_paivamaara' );

    /* HTML: dynaamiset kentät*/
    echo '<tr class="item">';
    echo '<td><a class="hvr-link" href="' . get_the_permalink() . '">' . $title . '</a></td>';
    echo '<td> ' . $pm  . '</td>';
    echo '</tr>';
endwhile;
echo '</tbody>';
echo '</table>';
	?>

	<?php
echo '</div>';
endif;

get_footer();