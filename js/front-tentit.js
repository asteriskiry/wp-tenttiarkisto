/**
 * Javascripit tenttiarkiston fronttiin
 **/

/* Kurssien sorttausta varten */

jQuery(function ($)  {

    $.fn.dataTable.moment( 'DD.MM.YYYY' );

    $('#t-taulukko').DataTable({
        "pageLength": 25,
        "language": {
            "sProcessing":    "Käsitellään...",
            "sLengthMenu":    "Näytä _MENU_ kurssia",
            "sZeroRecords":   "Yhtäkään kurssia ei löytynyt",
            "sEmptyTable":    "Ei löytynyt",
            "sInfo":          "Näytetään kurssit _START_-_END_ yhteensä _TOTAL_:stä kurssista",
            "sInfoEmpty":     "Näytetään kurssit 0-0 yhteensä 0:sta kurssista",
            "infoFiltered":   "(Haettu _MAX_:stä kurssista)",
            "decimal":        ",",
            "thousands":      "",
            "sInfoPostFix":   "",
            "sSearch":        "_INPUT_",
            "searchPlaceholder": "Etsi..",
            "sUrl":           "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Ladataan...",
            "oPaginate": {
                "sFirst":    "Ensimmäinen",
                "sLast":    "Viimeinen",
                "sNext":    "Seuraava",
                "sPrevious": "Edellinen"
            },
        }
    });
});
