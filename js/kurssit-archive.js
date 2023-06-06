/**
 * Javascripit kurssien archive-sivuille
 **/

/* Tenttien sorttausta varten */

jQuery(function ($)  {
    $('#t-k-taulukko').DataTable({
        "pageLength": 10,
        "language": {
            "sProcessing":    "Käsitellään...",
            "sLengthMenu":    "Näytä _MENU_ tenttiä",
            "sZeroRecords":   "Yhtäkään tenttiä ei löytynyt",
            "sEmptyTable":    "Ei löytynyt",
            "sInfo":          "Näytetään tentit _START_-_END_ yhteensä _TOTAL_:stä tentistä",
            "sInfoEmpty":     "Näytetään tentit 0-0 yhteensä 0:sta tentistä",
            "infoFiltered":   "(Haettu _MAX_:stä tentistä)",
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
