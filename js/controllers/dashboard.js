app.controller('dashboardCtrl', function ($scope, Data, toaster) {
    $scope.form = {};
    $scope.bln = [{
            "id": "01",
            "bulan": "Januari",
        }, {
            "id": "02",
            "bulan": "Februari",
        }, {
            "id": "03",
            "bulan": "Maret",
        }, {
            "id": "04",
            "bulan": "April",
        }, {
            "id": "05",
            "bulan": "Mei",
        }, {
            "id": "06",
            "bulan": "Juni",
        }, {
            "id": "07",
            "bulan": "Juli",
        }, {
            "id": "08",
            "bulan": "Agustus",
        }, {
            "id": "09",
            "bulan": "September",
        }, {
            "id": "10",
            "bulan": "Oktober",
        }, {
            "id": "11",
            "bulan": "November",
        }, {
            "id": "12",
            "bulan": "Desember",
        },
    ];
    $scope.grafik = function () {
        Data.post('site/penjualan', $scope.form).then(function (data) {
            var hr = data.kategori;
            var dataPen = data.data;
            var judul = data.title;
            $scope.penjualan = {
                title: {
                    text: judul,
                    x: -20 //center
                },
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
    }

    $scope.grafik();
})
