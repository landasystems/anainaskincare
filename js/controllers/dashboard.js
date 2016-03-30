app.controller('dashboardCtrl', function ($scope, Data, toaster) {

//    $scope.grafik = function () {
    Data.post('site/penjualan').then(function (data) {
        var hr = data.kategori;
        var dataPen = data.data;
        var judul = data.title;
        $scope.penjualan = {
            title: {
                text: judul,
                x: -20 //center
            },
//            subtitle: {
//                text: '',
//                x: -20
//            },
            xAxis: {
                categories: hr
            },
            yAxis: {
                title: {
                    text: 'Total Penjualan'
                },
                plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
            },
            tooltip: {
                valueSuffix: '°C'
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: dataPen
        }
    });
//    }
})
