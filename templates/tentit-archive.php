<?php

/**
 * Template Name: Tentit-archive
 **/

get_header();
/**
 * Asteriski WP teemaa varten
 */
?>

<h3 class="t-heading"><?php wp_title(''); ?></h3>
<?php
$args = array( 'hide_empty=0' );

$terms = get_terms( 'kurssi', $args );
if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    $count = count( $terms );
    $yhtmaara = 0;
?>
<div class="tentit">
    <table id="t-taulukko" class="row-border">
        <thead>
            <tr class="t-rivi">
                <th class="t-indeksit">Kurssi </th>
                <th class="t-indeksit">Tenttejä </th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ( $terms as $term ) {
        $slug = esc_url( get_term_link( $term ) );
        $kurssi = $term->name;
        $maara = $term->count;
        $yhtmaara = $yhtmaara + $maara;
        echo '<tr class="item">';
        echo '<td><a class="hvr-grow-custom-smaller" href="' . $slug . '">' . $kurssi . '</a></td>';
        echo '<td> ' . $maara  . '</td>';
        echo '</tr>';
    }
    $opintomateriaalivastaava = get_option("opintomateriaalivastaava");
    echo '</tbody>';
    echo '</table>';
    echo 'Tenttiarkistossa on yhteensä ' . $yhtmaara . ' tenttiä';

    echo carbon_get_theme_option('tentit_ohjeistus') ? wpautop(carbon_get_theme_option('tentit_ohjeistus')) :'<p><h4>Miten lisätä uusi tentti tenttiarkistoon?</h4></p>
    <ol>
        <li>Skannaa ensin paperitentti tietokoneelle (tai ota kuva)</li>
        <li><a href="https://asteriski.fi/register">Rekisteröidy</a> tai <a href="https://asteriski.fi/wp-admin">kirjaudu sisään</a></li>
        <li>Valitse vasemmasta yläkulmasta "Uusi" -> "Tentti"</li>
        <li>Täytä tiedot (pvm, kurssi, tiedosto) ja paina "Lähetä arvio"</li>
        <li>Jos oikeaa kurssia ei löydy pääset lisäämään niitä vasemmalta kohdasta "Kurssit"</li>
        <li>Opintomateriaalivastaava hyväksyy tentin</li>
    </ol>
    <p>Voit myös lähettää tentin sähköpostilla opintomateriaalivastaavalle </p>';
    echo '</div>';
    echo '</div>';
}

get_footer();