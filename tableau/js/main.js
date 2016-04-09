setTimeout(function () {
  chart = c3.generate({
      data: {
          json: data,
          types: {
             nb_etudiant: 'area-spline',
             nb_doctorant: 'area-spline',
             nb_entrepreneurs: 'area-spline',
             institution: 'area-spline',
             artiste: 'area-spline',
             habitant: 'area-spline',
             visiteurs: 'area-spline',
             ID: 'area-spline',
          },
          keys: {
              x: 'Date', // it's possible to specify 'x' when category axis
              xFormat: '%d-%m-%Y',
              value: ['nb_etudiant', 'nb_doctorant', 'chercheur', 'nb_entrepreneurs', 'institution', 'visiteurs'],
          },
          // groups: [['visiteurs', 'Name']],
          // names: {
          //     name: 'Événement',
          //     visiteurs: 'Visiteurs'
          // }
      },
      axis: {
          x: {
              type: 'timeseries',
              tick: {
                  format: '%d-%m-%Y'
              }
          }
      },
      tooltip: {
          format: {
              title: function (d) { return 'Data ' + d; },
              value: function (value, ratio, id) {
                  var format = id === 'data1' ? d3.format(',') : d3.format('$');
                  return format(value);
              }
          },
          grouped: false,        
          position: function (data, width, height, element) {
              var chartOffsetX = document.querySelector("#chart").getBoundingClientRect().left,
              graphOffsetX = document.querySelector("#chart g.c3-axis-y").getBoundingClientRect().right,
              tooltipWidth = document.getElementById('tooltip').parentNode.clientWidth,
              x = (parseInt(element.getAttribute('cx')) ) + graphOffsetX - chartOffsetX - Math.floor(tooltipWidth/2),
              y = element.getAttribute('cy');
          
              y = y - height - 14;
              return {top: y, left: x}
          },
          contents: function (data, defaultTitleFormat, defaultValueFormat, color) {
              // console.log(data);
              var $$ = this, config = $$.config,
              titleFormat = config.tooltip_format_title || defaultTitleFormat,
              nameFormat = config.tooltip_format_name || function (name) { return name; },
              valueFormat = config.tooltip_format_value || defaultValueFormat,
              text, i, title, value;
              for (i = 0; i < data.length; i++) {
                  if (! (data[i] && (data[i].value || data[i].value === 0))) { continue; }

                  if (! text) {
                    title = titleFormat ? titleFormat(data[i].x) : data[i].x;
                    text = "<div id='tooltip' class='d3-tip'>";
                  }
                  // value = valueFormat(data[i].value, data[i].ratio, data[i].id, data[i].index);
                  value  = data[i].value + " : total visiteurs";
                  text += "<span class='info title'>SUPER EVENT !</span><br>";
                  text += "<span class='info'>"+ title +"</span><br>";
                  text += "<span class='value'>" + value + "</span>";
                  text += "</div>";

                  // console.log(text);

              }
              return text;
          }
      },
      zoom: {
          enabled: true
      },
      point: {
          show: true
      }
  });
}, 100);