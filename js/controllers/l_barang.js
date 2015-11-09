app.controller('l_barangCtrl', function ($scope, Data, toaster, $stateParams) {

    //init data;
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.listStok = [];
    $scope.totalStok = 0;

    Data.get('site/session').then(function (data) {
        $scope.sCabang = data.data.user.cabang;
    });

    $scope.subtotal = function () {
        var total = 0;
        angular.forEach($scope.listStok, function (detail) {
            var jml = (detail.iStok) ? parseInt(detail.iStok) : 0;
            total += jml;
        })
        $scope.totalStok = total;
    }

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};

        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('barang', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('barang', paramRef).then(function (data) {
            window.location = 'api/web/barang/excellaporan';
        });
    }

    Data.get('barang/kategori').then(function (data) {
        $scope.sKategori = data.kategori;
    });
    Data.get('barang/satuan').then(function (data) {
        $scope.sSatuan = data.satuan;
    });

    $scope.stok = function (id) {
        Data.get('barang/getstok/' + id).then(function (data) {
            $scope.listStok = data.data;
            $scope.totalStok = data.total;
        });
    }


    $scope.view = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.stok(form.id);
    };

    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.totalStok = 0;
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
    };


    if ($stateParams.form != null) { //pengecekan jika ada pencarian, dilempar ke view
        $scope.view($stateParams.form);
    }
})
