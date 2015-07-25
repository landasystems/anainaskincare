app.controller('l_labarugiCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.laporan = '';
//    $scope.listpegawai = {};

    Data.get('site/session').then(function (data) {
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