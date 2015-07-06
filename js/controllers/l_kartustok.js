app.controller('l_kartustokCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.laporan = '';
//    $scope.listpegawai = {};

    Data.get('cabang/listcabang').then(function(data) {
        $scope.listcabang = data.data;
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