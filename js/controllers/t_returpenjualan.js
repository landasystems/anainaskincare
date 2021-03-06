app.controller('r_penjualanCtrl', function ($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    var paramRef;
    $scope.refresh = function () {
        $scope.form = {};
        $scope.displayed = [];
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.penjualan = []
        $scope.detPenjualan = []
        $scope.form.biaya_lain = 0;
        $scope.callServer(tableStateRef);
    }

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
        Data.get('returpenjualan/', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('returpenjualan', paramRef).then(function (data) {
            window.location = 'api/web/returpenjualan/excel';
        });
    }
    $scope.create = function (form) {
        $scope.refresh();
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Retur Penjualan";
        $scope.form = {};
        $scope.form.tanggal = new Date();
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Edit Retur Penjualan : " + $scope.form.kode;
        $scope.selected(form);

    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Edit Retur Penjualan : " + $scope.form.kode;
        $scope.selected(form);
    };
    $scope.save = function (form, detail) {
        var data = {
            retur_penjualan: form,
            retur_penjualandet: detail,
        };
        var url = (form.id > 0) ? 'returpenjualan/update/' + form.id : 'returpenjualan/create';
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
            Data.delete('returpenjualan/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    //select2 product
    $scope.cariKode = function ($query) {
        if ($query.length >= 3) {
            Data.get('penjualan/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
    $scope.getkodepenjualan = function (wo) {
        Data.post('returpenjualan/det_kodepenjualan/', wo).then(function (data) {
            $scope.penjualan = data.penjualan;
            $scope.detPenjualan = data.detail;
            $scope.form.kode = data.kode;
            $scope.form.penjualan = data.penjualan;
            $scope.form.biaya_lain = 0;
            angular.forEach($scope.detPenjualan, function (detail) {
                detail.jumlah_retur = (detail.jumlah_retur !== null) ? detail.jumlah_retur : 0;
                detail.diskon = 0;
                detail.harga = (detail.harga !== null) ? detail.harga : detail.harga_awal;
            })
        });
    };
    $scope.selected = function (form) {
        Data.get('returpenjualan/view/' + form.id).then(function (data) {
            $scope.form = data.data;
            $scope.penjualan = data.penjualan;
            $scope.detPenjualan = data.detail;
            angular.forEach($scope.detPenjualan, function (detail) {
                detail.jumlah_retur = (detail.jumlah_retur !== null) ? detail.jumlah_retur : 0;
            })
        });
    }

    $scope.total = function () {
        var total = 0;
        var total_retur = 0;
        var diskon = 0;
        var diskon_retur = 0;
        var lain = parseInt($scope.form.biaya_lain);
        angular.forEach($scope.detPenjualan, function (detail) {
            diskon_retur += detail.jumlah_retur * detail.diskon_awal;
            total_retur += detail.jumlah_retur * detail.harga;
        })
        $scope.form.total = (total_retur - diskon_retur) + lain;
        $scope.form.belanja = (total - diskon);
        $scope.form.total_belanja = total_retur;
        $scope.form.total_diskon = diskon_retur;

    }
    $scope.bayar = function () {
        var total = parseInt($scope.form.total);
        var cash = parseInt($scope.form.cash);
        var diskon = parseInt($scope.form.total_diskon);
        var credit = (total - diskon) - cash;
        $scope.form.credit = (credit > 0) ? credit : 0;
    }
})
