app.controller('rolesCtrl', function ($scope, Data, toaster, $state) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

    $scope.listcabang = [];
    Data.get('cabang/listcabang').then(function (data) {
        $scope.listcabang = data.data;
    });

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
        Data.get('roles', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('roles', paramRef).then(function (data) {
            window.location = 'api/web/roles/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.akses = {};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
        Data.get('cabang/akses/' + form.id).then(function (data) {
            $scope.form.cabang = data.data;
        });
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
        Data.get('cabang/akses/' + form.id).then(function (data) {
            $scope.form.cabang = data.data;
        });
    };
    $scope.save = function (form) {
        var url = (form.id > 0) ? 'roles/update/' + form.id : 'roles/create';
        form.akses = JSON.stringify(form.akses);
        Data.post(url, form).then(function (result) {
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

        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.trash = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
            row.is_deleted = 1;
            Data.post('roles/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function (row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('roles/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('roles/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.checkAll = function (module, valueCheck) {
        var akses = {
            "master_cabang": false,
            "master_customer": false,
            "master_supplier": false,
            "master_pegawai": false,
            "master_satuan": false,
            "master_kategori": false,
            "master_barang": false,
            "master_user": false,
            "master_roles": false,
            "transaksi_stokmasuk": false,
            "transaksi_stokkeluar": false,
            "transaksi_pembelian": false,
            "transaksi_bayarhutang": false,
            "transaksi_returpembelian": false,
            "transaksi_penjualan": false,
            "transaksi_bayarpiutang": false,
            "transaksi_returpenjualan": false,
            "transaksi_terimabarang": false,
            "transaksi_kirimbarang": false,
            "laporan_kartustok": false,
            "laporan_bonuskaryawan": false,
            "laporan_labarugi": false,
            "laporan_kartustatus": false,
            "laporan_penjualan": false,
            "laporan_barang": false,
        }

        angular.forEach(akses, function ($value, $key) {
            if ($key.indexOf(module) >= 0)
                $scope.form.akses[$key] = valueCheck;
        });
    };

})
