app.controller('l_kartustokCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.laporan = '';
//    $scope.listpegawai = {};

    Data.get('cabang/listcabang').then(function(data) {
        $scope.listcabang = data.data;
    });

    $scope.view = function(form) {
        $scope.detail_laporan = true;
        Data.post('laporan/kartustok/', form).then(function(data) {
            $scope.laporan = data.data;
            console.log(data);
        });
    }

});