/*
 * Configuration globale de DataTables pour tout le site.
 *
 * Active FixedHeader sur toutes les tables (customer.js, cars.js, containers.js,
 * bl.js, invoice.js, client.js, auth.js, t1.js, loading.js) sans modifier
 * chacun de leurs appels .DataTable(...) : $.fn.dataTable.defaults est fusionné
 * dans la config de chaque table au moment de son initialisation.
 *
 * headerOffset = hauteur des barres fixes du dessus (.header + .page-header,
 * cf. theme.css) pour que l'en-tête collé se positionne juste en dessous et
 * ne soit pas caché derrière. Ces barres ne sont fixes qu'à partir de 768px
 * (voir @media only screen and (min-width: 768px) html.fixed dans theme.css) ;
 * en dessous, rien n'est fixe donc l'offset doit être nul.
 */
$.extend(true, $.fn.dataTable.defaults, {
    fixedHeader: {
        header: true,
        headerOffset: window.matchMedia('(min-width: 768px)').matches ? 110 : 0
    }
});
