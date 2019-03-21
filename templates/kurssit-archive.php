<?php 

/**
 * Template Name: Kurssit-archive
 **/

get_header();

echo '<div id="kurssit-archive">';
echo '<h1 class="customtitle">' . get_the_terms( $post->ID, 'kurssi' )[0]->name . '</h1>';

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
    <table id="t-k-taulukko" class="row-border">
        <thead>
            <tr class="t-k-rivi">	
                <th class="t-k-indeksit">Tentti </th>
                <th class="t-k-indeksit">Päivämäärä </th>
            </tr>
        </thead>
    <tbody>

<?php
while ( $pk_by_year->have_posts() ) : $pk_by_year->the_post();

    global $post;
    $title = get_the_title();
    $custom_pdf_data = get_post_meta($post->ID, 'custom_pdf_data');
    /* Kommentoitu $slug sitä varten jos halutaan valikosta suoraan liitteeseen */
    //$slug = $custom_pdf_data[0]['src'];
    $slug = get_permalink();
    $pm = get_post_meta( $post->ID, 't_paivamaara', true );
    $thumbnail = $custom_pdf_data[0]['tnMed']; 

    /* HTML: dynaamiset kentät*/
    echo '<tr class="item">';
    echo '<td><div class="tooltip"><a class="hvr-grow-custom-smaller" href="' . $slug . '">' . $title . '</a><img class="tooltipimg" src="' . $thumbnail  . '"></div></td>';
    echo '<td> ' . $pm  . '</td>';
    echo '</tr>';
endwhile;
echo '</tbody>';
echo '</table>';
echo '<div class="t-k-buttons">';
echo '<a href="' . get_site_url() . '/tenttiarkisto"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Takaisin selailuun</a>'; 
echo '</div>';
echo '</div>';
endif;

get_footer();
