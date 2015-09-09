
app.controller('myctrl', function ($scope) {



    $scope.print = function () {
        var chart = this.chartConfig.getHighcharts();
        chart.print();
    }

    $scope.chartSeries = [
        {"name": "Some data", "data": [10]},
        {"name": "Some data 3", "data": [4],},
        {"name": "Some data 2", "data": [2],},
        {"name": "My Super Column", "data": [14],},
        {"name": "My Super Column2", "data": [2],},
        {"name": "My Super Column3", "data": [4],},
        {"name": "My Super Column4", "data": [5],},
        {"name": "My Super Column5", "data": [16],}
    ];

    $scope.chartConfig = {
        options: {
            chart: {
                type: 'bar'
            }
        },
        series: $scope.chartSeries,
        title: {
            text: 'Hello'
        },
        credits: {
            enabled: false
        },
        loading: false
    }

});