app.controller('l_bonuskaryawanCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.list_detail = '';

    Data.get('cabang/listcabang').then(function(data) {
        $scope.listcabang = data.data;
    });

    Data.get('pegawai/listpegawai').then(function(data) {
        $scope.listpegawai = data.data;
    });

    $scope.view = function(form) {
        $scope.detail_laporan = true;
        Data.get('laporan/bonus/', form).then(function(data) {
            $scope.list_detail = data.data;
            $scope.total = data.total;
        });
    }

});