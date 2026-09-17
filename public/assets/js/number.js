function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function display_number(number, decimals=3, dec_point=",", thousands_sep=" ") {
    const nbr = number_format(number, decimals, dec_point, thousands_sep);
    if (nbr === '0' || nbr === '0.0' || nbr === '0,0') {
        return '0';
    }
    let nb = nbr.split(dec_point);
    if (nb.length === 1) {
        return nb[0];
    }
    if (nb.length > 1 && parseInt(nb[1]) === 0) {
        return nb[0];
    }
    let part2 = +('0.' + nb[1]);
    return nb[0] + ',' + part2.toString().split('.')[1];
}