app.controller('l_kartustokCtrl', function($scope, Data, toaster) {
    $scope.exportData = function() {
        var blob = new Blob([document.getElementById('exportable').innerHTML], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
        });
        saveAs(blob, "Report-Kartu-Stok.xls");
    };

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.laporan = '';

    Data.get('site/session').then(function(data) {
        $scope.listcabang = data.data.user.cabang;
    });

    Data.get('kategori/listkategori').then(function(data) {
        $scope.listkategori = data.data;
    });

    $scope.view = function(form) {
        $scope.detail_laporan = true;
        Data.post('laporan/kartustok/', form).then(function(data) {
            $scope.laporan = data.data;
            $scope.list_detail = data.detail;
            console.log(data);
        });
    }

});