app.controller('rolesCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    
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

        Data.get('roles', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {"akses": {"master": {
                cabang: false,
                customer: false,
                supplier: false,
                karyawan: false,
                satuan: false,
                kategori: false,
                barang: false,
                user: false,
                roles: false,
            }, "transaksi": {
                stok_masuk: false,
                stok_keluar: false,
            }, "laporan": {
                kartu_stok: false,
                bonus_karyawan: false,
                laba_rugi: false,
            }}};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.form.akses = JSON.parse($scope.form.akses);
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
        if (!$scope.is_view){ //hanya waktu edit cancel, di load table lagi
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

    $scope.checkMaster = function () {
        if ($scope.selectMaster) {
            $scope.selectMaster = true;
        } else {
            $scope.selectMaster = false;
        }
        
        angular.forEach($scope.form.akses.master, function ($value, $key) {
            $scope.form.akses.master[$key] = $scope.selectMaster;
        });
    };
    $scope.checkTransaksi = function () {
        if ($scope.selectTransaksi) {
            $scope.selectTransaksi = true;
        } else {
            $scope.selectTransaksi = false;
        }
        
        angular.forEach($scope.form.akses.transaksi, function ($value, $key) {
            $scope.form.akses.transaksi[$key] = $scope.selectTransaksi;
        });
    };
    $scope.checkLaporan = function () {
        if ($scope.selectLaporan) {
            $scope.selectLaporan = true;
        } else {
            $scope.selectLaporan = false;
        }
        
        angular.forEach($scope.form.akses.laporan, function ($value, $key) {
            $scope.form.akses.laporan[$key] = $scope.selectLaporan;
        });
    };

})
