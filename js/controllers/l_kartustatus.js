app.controller('l_kartustatusCtrl', function ($scope, Data, toaster) {
    $scope.exportData = function () {
        var blob = new Blob([document.getElementById('omset').innerHTML], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
        });
        saveAs(blob, "Kartu-Status.xls");
    };

    $scope.detail_laporan = false;
    $scope.laporan = {};
    $scope.form = {};

    Data.get('site/session').then(function (data) {
        $scope.listcabang = data.data.user.cabang;
    });

    $scope.cariCustomer = function ($query) {
        if ($query.length >= 3) {
            Data.get('customer/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.view = function (form) {
        $scope.detail_laporan = true;
        Data.post('kartustatus/index', form).then(function (data) {
            $scope.laporan = data.data;
        });
    }

});