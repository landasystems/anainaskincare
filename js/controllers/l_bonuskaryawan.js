app.controller('l_bonuskaryawanCtrl', function($scope, Data, toaster) {
    
    $scope.form = {};
    
    Data.get('cabang/listcabang').then(function(data) {
        $scope.listcabang = data.data;
    });
    
    Data.get('pegawai/listpegawai').then(function(data) {
        $scope.listpegawai = data.data;
    });
});