app.controller('l_bonuskaryawanCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.list_detail = '';
//    $scope.listpegawai = {};

    Data.get('cabang/listcabang').then(function(data) {
        $scope.listcabang = data.data;
    });

    Data.get('pegawai/listpegawai').then(function(data) {
        $scope.listpegawai = data.data;
    });

    $scope.date = {startDate: null, endDate: null};

    $scope.ubah_pegawai = function(cabang) {
        Data.get('pegawai/listpegawaicabang/?id=' + cabang, '').then(function(data) {
            $scope.listpegawai = data.data;
        });
    }

    $scope.view = function(form) {
        $scope.detail_laporan = true;
        Data.post('laporan/bonus/', form).then(function(data) {
            $scope.list_detail = data.data;
            $scope.detail = data.detail;
        });
    }

});