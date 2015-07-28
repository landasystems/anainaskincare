app.controller('l_bonuskaryawanCtrl', function($scope, Data, toaster) {

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.list_detail = '';
    $scope.tanggal = {startDate: null, endDate: null};
//    $scope.listpegawai = {};

    
    Data.get('site/session').then(function (data) {
        $scope.listcabang = data.data.user.cabang;
    });

    Data.get('pegawai/listpegawai').then(function(data) {
        $scope.listpegawai = data.data;
    });

    $scope.ubah_pegawai = function(cabang) {
        Data.get('pegawai/listpegawaicabang/?id=' + cabang, '').then(function(data) {
            $scope.listpegawai = data.data;
        });
    }

    $scope.view = function(form) {
//        console.log(form.tanggal);
        $scope.detail_laporan = true;
        Data.post('laporan/bonus/', form).then(function(data) {
            $scope.list_detail = data.data;
            $scope.detail = data.detail;
        });
    }

//    $scope.excel = function(form) {
//        Data.post('laporan/bonus/?is_excel', form, {responseType: 'arraybuffer'}).then(function(data) {
////            window.location = 'api/web/laporan/bonus/?is_excel';
//            var file = new Blob([data], {type: 'application/pdf'});
//            saveAs(file, 'filename.pdf');
//        });
//    }

});