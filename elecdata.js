function refreshChart(dataUrl, chartId, instantGraphFrom, instantGraphTo) {
    $.post(
      dataUrl,
      {from:instantGraphFrom, to:instantGraphTo},
      function(data) {
        drawHighChart(data);
      }
    );
}

function drawHighChart(data) {
  $.each(data, function() {
    this[0] = Date.parse(this[0]);
  });

  $('#graph-elec-instant').highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
              text: ''
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
               min: 0
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },

            series: [{
                type: 'area',
                name: 'Consommation',
                data: data
            }]
        });
}
