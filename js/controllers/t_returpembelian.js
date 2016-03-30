app.controller('returPembelianCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.refresh = function () {
        $scope.form = {};
        $scope.displayed = [];
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.det = [];
        $scope.details = [];
    }
    $scope.cariPembelian = function ($query) {
        if ($query.length >= 3) {
            Data.get('pembelian/cari', {kode: $query}).then(function (data) {
                $scope.listPembelian = data.data;
            });
        }
    };
    $scope.pilihPembelian = function (form, $item, is_create) {
        form.nama_supplier = $item.nama_supplier;
        form.no_tlp = $item.no_tlp;
        form.email = $item.email;
        form.alamat = $item.alamat;
        form.klinik = $item.klinik;
        form.total = $item.total;
        form.cash = $item.cash;
        form.credit = $item.credit;
        $scope.selectedPembelian($item);
        if (is_create) {
            $scope.getCode($item.cabang_id);
        }
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

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

        Data.get('returpembelian', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    Data.get('site/session').then(function (data) {
        $scope.sCabang = data.data.user.cabang;
    });

    $scope.create = function (form) {
        $scope.refresh();
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Retur Pembelian";
        $scope.form = {};
        $scope.form.tanggal = new Date();
        $scope.form.biaya_lain = 0;
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Retur : " + form.kode;
        $scope.form = form;
        $scope.pilihPembelian(form, form.pembelian, $scope.is_create);
        $scope.subtotal();
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Lihat Retur : " + form.kode;
        $scope.form = form;
        $scope.pilihPembelian(form, form.pembelian, $scope.is_create);
        $scope.subtotal();
    };
    $scope.save = function (form, detail) {
        var data = {
            retur: form,
            returdet: detail,
        };
        var url = (form.id > 0) ? 'returpembelian/update/' + form.id : 'returpembelian/create';
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
        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('returpembelian/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selectedPembelian = function (form) {
        var id = ($scope.form.id !== undefined) ? $scope.form.id : 0;
        var data = {
            form: form,
            id: id
        };
        Data.get('returpembelian/view/', data).then(function (result) {
            $scope.details = result.details;
            var total = 0;
            angular.forEach($scope.details, function (detail) {
                detail.jumlah_retur = (detail.jumlah_retur !== null) ? parseInt(detail.jumlah_retur) : 0;
                detail.harga_retur = (detail.harga_retur != 0) ? parseInt(detail.harga_retur) : detail.harga;
                detail.sub_total_retur = detail.harga_retur * detail.jumlah_retur;
                total += detail.sub_total_retur;
            });
            $scope.form.total_biaya = total;
        });
    };

    $scope.subtotal = function () {
        var total = 0;
        var sub_total = 0;
        var biaya_lain = ($scope.form.biaya_lain !== undefined) ? parseInt($scope.form.biaya_lain) : 0;
        angular.forEach($scope.details, function (detail) {
            var jml_retur = (detail.jumlah_retur !== undefined) ? parseInt(detail.jumlah_retur) : 0;
            var harga_retur = (detail.harga_retur !== undefined) ? parseInt(detail.harga_retur) : 0;
            var diskon = 0;
            sub_total = (jml_retur * harga_retur) - (jml_retur * diskon);
            detail.sub_total_retur = sub_total;
            total += detail.sub_total_retur;
        });

        $scope.form.total_biaya = total + biaya_lain;
    };
    $scope.getCode = function (cabang) {
        Data.get('returpembelian/lastcode/' + cabang).then(function (result) {
            $scope.form.kode = result.kode;
        });
    };
});
