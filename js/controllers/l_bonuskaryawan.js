app.controller('l_bonuskaryawanCtrl', function($scope, Data, toaster) {
    $scope.exportData = function() {
        var blob = new Blob([document.getElementById('bonus').innerHTML], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
        });
        saveAs(blob, "Report-Bonus-Karyawan.xls");
    };

    $scope.detail_laporan = false;
    $scope.form = {};
    $scope.list_detail = '';
    
    $scope.form.tanggal = {
        startDate: new Date(),
        endDate: new Date()
    };

    Data.get('site/session').then(function(data) {
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
        $scope.detail_laporan = true;
        Data.post('laporan/bonus/', form).then(function(data) {
            $scope.list_detail = data.data;
            $scope.detail = data.detail;
        });
    }

});