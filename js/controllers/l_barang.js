app.controller('l_barangCtrl', function($scope, Data, toaster, FileUploader, $stateParams) {

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
        Data.get('barang', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function() {
        Data.get('barang', paramRef).then(function(data) {
            window.location = 'api/web/barang/excel';
        });
    }

    Data.get('barang/kategori').then(function(data) {
        $scope.sKategori = data.kategori;
    });
    Data.get('barang/satuan').then(function(data) {
        $scope.sSatuan = data.satuan;
    });

    $scope.stok = function (id) {
        Data.get('barang/getstok/' + id).then(function (data) {
            $scope.listStok = data.data;
            $scope.totalStok = data.total;
        });
    }

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};

        Data.get('site/session').then(function (data) {
            $scope.listStok = data.data.user.cabang;
        });

    };
    $scope.update = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nama;
        $scope.form = form;
        $scope.stok(form.id);
    };
    $scope.view = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.stok(form.id);
    };
    $scope.save = function (form, stok) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }

        var data = {
            form: form,
            stok: stok,
        }

        var url = ($scope.is_create == true) ? 'barang/create/' : 'barang/update/' + form.id;
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
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
