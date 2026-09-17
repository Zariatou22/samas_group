$(function () {
    // Graphique de suivi des BL : conteneurs enregistrés par mois (graphique à barres)
    const monthStatChart = $("#chartBl");
    if (monthStatChart.html() !== undefined) {
        const months = JSON.parse(decodeURIComponent(monthStatChart.data('months')));
        const barData = [{
            data: months.map(function (d, i) { return [i, d.data]; }),
            color: '#4C6EF5',
            bars: {
                show: true,
                barWidth: 0.6,
                align: 'center',
                lineWidth: 0,
                fillColor: {colors: [{opacity: 0.85}, {opacity: 0.85}]}
            }
        }];
        monthStatChart.css('position', 'relative');
        monthStatChart.find('.bar-value-label').remove();
        const plot = $.plot(monthStatChart, barData, {
            xaxis: {
                ticks: months.map(function (d, i) { return [i, d.label.substring(0, 4)]; }),
                tickLength: 0,
                font: {size: 11, color: '#495057'}
            },
            yaxis: {
                min: 0,
                tickDecimals: 0,
                font: {size: 11}
            },
            grid: {
                hoverable: true,
                clickable: true,
                borderWidth: 1,
                borderColor: '#eee',
                margin: {top: 22}
            },
            legend: {show: false},
            tooltip: true,
            tooltipOpts: {
                content: function (label, x, y, item) {
                    const d = item ? months[item.dataIndex] : null;
                    return (d ? d.label : label) + ': ' + y + ' conteneur(s)';
                },
                defaultTheme: false
            }
        });
        // Affiche le nombre au-dessus de chaque barre (y compris les mois à 0)
        months.forEach(function (d, i) {
            const o = plot.pointOffset({x: i, y: d.data});
            $('<div class="bar-value-label"></div>')
                .css({
                    position: 'absolute',
                    left: (o.left - 15) + 'px',
                    top: (o.top - 18) + 'px',
                    width: '30px',
                    textAlign: 'center',
                    fontSize: '11px',
                    fontWeight: 'bold',
                    color: '#495057',
                    pointerEvents: 'none'
                })
                .text(d.data)
                .appendTo(monthStatChart);
        });
    }
});
