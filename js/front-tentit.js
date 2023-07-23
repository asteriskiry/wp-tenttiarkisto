/**
 * Javascripit tenttiarkiston fronttiin
 **/

/* Kurssien sorttausta varten */

jQuery(document).ready(function($) {
    $('#t-taulukko').DataTable({
        responsive: true,
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
    
    $('.pdf-link').on('click', function(e) {
        e.preventDefault();
        let src = $(this).attr('href');
        let position = {my: 'center center', at: 'center', of: window};
        let wWidth = $(window).width();
        let dWidth = wWidth * 0.9;
        let wHeight = $(window).height();
        let dHeight = wHeight * 0.9 + 40;
        let iframe = $('#riski-pdf');
        iframe.attr('src', src);
        iframe.attr('height', dHeight);
        iframe.attr('width', dWidth);
        $('#dialog').dialog({
            title: $(this).text(),
            modal: true,
            draggable: false,
            height: dHeight,
            width: dWidth,
            position: position,
            show: { effect: "fade-in", duration: 400 },
            hide: { effect: "fade-out", duration: 400 },
            open: function() {
                $('body').css('overflow', 'hidden');
            },
            close: function() {
                $('body').css('overflow', '');
            }
        });
    });
});