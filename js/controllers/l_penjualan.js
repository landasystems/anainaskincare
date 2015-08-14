app.controller('l_penjualanCtrl', function($scope, Data, toaster) {
    //init data;
    var tableStateRef;
    var paramRef;
    $scope.form = [];
    $scope.displayed = [];
    $scope.detail_laporan = false;

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
//        var offset = tableState.pagination.start || 0;
//        var limit = tableState.pagination.number || 10;
        var param = {};
        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('laporanpenjualan/', param).then(function(data) {
            $scope.form = data.detail;
            $scope.displayed = data.data;
//            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };


    Data.get('site/session').then(function(data) {
        $scope.sCabang = data.data.user.cabang;
    });

    Data.get('kategori/listkategori').then(function(data) {
        $scope.listkategori = data.data;
    });

    $scope.excel = function() {
        Data.get('penjualan', paramRef).then(function(data) {
            window.location = 'api/web/penjualan/excel';
        });
    }

    $scope.view = function(form) {
        $scope.detail_laporan = true;
    }
})
