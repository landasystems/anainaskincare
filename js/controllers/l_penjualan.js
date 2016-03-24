app.controller('l_penjualanCtrl', function($scope, Data, toaster) {
    //init data;
    $scope.form = {};
    $scope.detail_laporan = false;

    $scope.sCabang = [];
    Data.get('site/session').then(function(data) {
        $scope.sCabang = data.data.user.cabang;
    });
    
    $scope.form.tanggal = {
        startDate: new Date(),
        endDate: new Date()
    };

    $scope.listkategori = [];
    Data.get('kategori/listkategori').then(function(data) {
        $scope.listkategori = data.data;
    });

    $scope.listkasir = [];
    $scope.getKasir = function(cabang_id) {
        Data.get('pengguna/listkasir', {cabang: cabang_id}).then(function(data) {
            $scope.listkasir = data.data;
        });
    }

    $scope.excel = function(form) {
        var param = {};
        param['filter'] = form;
        Data.post('laporanpenjualan/laporan', param).then(function(data) {
            window.location = 'api/web/penjualan/excel';
        });
    }

    $scope.view = function(form) {
        var param = {};
        param['filter'] = form;
        $scope.detail_laporan = true;
        Data.post('laporanpenjualan/laporan', param).then(function(data) {
            $scope.data = data.detail;
            $scope.displayed = data.data;
        });
    }
})
