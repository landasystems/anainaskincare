app.controller('l_labarugiCtrl', function($scope, Data, toaster) {
    $scope.exportData = function() {
        var blob = new Blob([document.getElementById('omset').innerHTML], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
        });
        saveAs(blob, "Report-Omset.xls");
    };

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.laporan = '';

    Data.get('site/session').then(function(data) {
        $scope.listcabang = data.data.user.cabang;
    });

    $scope.view = function(form) {
        $scope.detail_laporan = true;
        Data.post('laporan/labarugi/', form).then(function(data) {
            $scope.laporan = data.data;
            console.log(data);
        });
    }

});