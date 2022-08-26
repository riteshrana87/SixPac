var Dashboard = (function () {
    /** Load graph for registered users - code start here **/
    var userRegisterGraph = function () {
        var year = $("#reg_year").val();

        am4core.ready(function () {
            am4core.disposeAllCharts();
            am4core.addLicense("ch-custom-attribution");
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            var chart = am4core.create("userRegisterGraph", am4charts.XYChart);
            chart.colors.step = 2;

            chart.legend = new am4charts.Legend();
            chart.legend.position = "bottom";
            chart.legend.paddingBottom = 20;
            chart.legend.labels.template.maxWidth = 95;

            chart.legend.labels.template.fill = am4core.color("#fff");

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            xAxis.dataFields.category = "month";
            xAxis.renderer.cellStartLocation = 0.1;
            xAxis.renderer.cellEndLocation = 0.9;
            xAxis.renderer.grid.template.location = 0;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;

            xAxis.renderer.labels.template.fill = am4core.color("#A0CA92");
            yAxis.renderer.labels.template.fill = am4core.color("#A0CA92");

            function createSeries(value, name, color) {
                var series = chart.series.push(new am4charts.ColumnSeries());
                series.dataFields.valueY = value;
                series.dataFields.categoryX = "month";
                series.name = name;

                series.columns.template.tooltipText =
                    "{name} {dateX}: {valueY}";

                series.columns.template.stroke = am4core.color("#fff"); // red outline
                series.columns.template.fill = color; // green fill

                series.columns.template.column.cornerRadiusTopLeft = 4;
                series.columns.template.column.cornerRadiusTopRight = 4;

                series.events.on("hidden", arrangeColumns);
                series.events.on("shown", arrangeColumns);

                var bullet = series.bullets.push(new am4charts.LabelBullet());
                bullet.interactionsEnabled = false;
                bullet.dy = 30;
                // bullet.label.text = "{valueY}";
                bullet.label.fill = am4core.color("#ffffff");

                return series;
            }

            submitcall(
                superadmin_url + "/dashboard/get-year-wise-user",
                { year: year },
                function (output) {
                    //console.log(output);
                    chart.data = output;
                }
            );

            createSeries("admin", "Admin", "#718776");
            createSeries("business", "Business", "#936c83");
            createSeries("consumer", "Consumer", "#69729d");

            function arrangeColumns() {
                var series = chart.series.getIndex(0);

                var w =
                    1 -
                    xAxis.renderer.cellStartLocation -
                    (1 - xAxis.renderer.cellEndLocation);
                if (series.dataItems.length > 1) {
                    var x0 = xAxis.getX(
                        series.dataItems.getIndex(0),
                        "categoryX"
                    );
                    var x1 = xAxis.getX(
                        series.dataItems.getIndex(1),
                        "categoryX"
                    );
                    var delta = ((x1 - x0) / chart.series.length) * w;
                    if (am4core.isNumber(delta)) {
                        var middle = chart.series.length / 2;

                        var newIndex = 0;
                        chart.series.each(function (series) {
                            if (!series.isHidden && !series.isHiding) {
                                series.dummyData = newIndex;
                                newIndex++;
                            } else {
                                series.dummyData = chart.series.indexOf(series);
                            }
                        });
                        var visibleCount = newIndex;
                        var newMiddle = visibleCount / 2;

                        chart.series.each(function (series) {
                            var trueIndex = chart.series.indexOf(series);
                            var newIndex = series.dummyData;

                            var dx =
                                (newIndex - trueIndex + middle - newMiddle) *
                                delta;

                            series.animate(
                                { property: "dx", to: dx },
                                series.interpolationDuration,
                                series.interpolationEasing
                            );
                            series.bulletsContainer.animate(
                                { property: "dx", to: dx },
                                series.interpolationDuration,
                                series.interpolationEasing
                            );
                        });
                    }
                }
            }
        }); // end am4core.ready()
    };

    $("#reg_year").on("change", function () {
        return userRegisterGraph();
    });
    /** Load graph for registered users - code end here **/

    /** Add record code start here **/
    var add = function () {};
    /** Add record code end here **/

    /** Edit record code start here **/
    var edit = function () {};
    /** Edit record code end here **/

    return {
        init: function () {
            userRegisterGraph();
        },
        add: function () {
            add();
        },
        edit: function () {
            edit();
        },
    };
})();
