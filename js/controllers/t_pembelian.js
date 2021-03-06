app.controller('pembelianCtrl', function ($scope, Data, toaster) {
//init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.pembeliandet = [
        {
            id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
            diskon: '',
            sub_total: ''
        }
    ];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.tagSup = false;
    $scope.read = false;
    $scope.tagSupplier = function (sup) {
        var item = {
            nama: sup,
        };
        $scope.tagSup = true;
        return item;
    };
    $scope.getkode_cabang = function (form, is_create) {
        if (is_create) {
            Data.get('pembelian/lastcode/', form.cabang).then(function (data) {
                $scope.form.kode = data.kode;
                $scope.form.cabang_id = form.cabang.id;
            });
        }
    };
    $scope.cariSupplier = function ($query) {
        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.listSupplier = data.data;
            });
        }
    };
    Data.get('site/session').then(function (data) {
        $scope.sCabang = data.data.user.cabang;
    });
    $scope.cariProduk = function ($query, $cabang) {
        if ($query.length >= 3) {
            Data.get('barang/cari?type=Barang', {nama: $query, cabang: $cabang.id}).then(function (data) {
                $scope.listProduk = data.data;
                angular.forEach($scope.listProduk, function (detail) {
                    detail.diskon = (detail.diskon != undefined) ? detail.diskon : 0;
                });
            });
        }
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.pilih = function (detail, $item) {
        detail.harga = $item.harga_beli_terakhir;
        detail.diskon = $item.diskon;
        $scope.calculate();
    };
    $scope.pilihSupplier = function (form, $item) {
        form.kode_cust = $item.kode;
        form.no_tlp = $item.no_tlp;
        form.email = $item.email;
        form.alamat = $item.alamat;
        $scope.read = false;
        if ($scope.tagSup == true) {
            $scope.tagSup = false;
        }
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

        Data.get('pembelian', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.create = function () {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Pembelian";
        $scope.form = {};
        $scope.form.tanggal = new Date();
        $scope.form.status = 'clear';
        $scope.pembeliandet = [
            {
                id: '',
                barang: [],
                jumlah: 0,
                harga: 0,
                diskon: 0,
                sub_total: 0
            }
        ];
        $scope.form.cash = 0;
        $scope.form.credit = 0;
        $scope.form.kembalian = 0;
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.form = form;
        $scope.formtitle = "Edit Pembelian : " + form.kode;
        $scope.det = {};
        $scope.getDetail(form.id);
        $scope.bayar();
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.form = form;
        $scope.formtitle = "Lihat Pembelian : " + $scope.form.kode;
        $scope.det = {};
        $scope.getDetail(form.id);
        $scope.bayar();
    };
    $scope.getDetail = function (id) {
        Data.get('pembelian/view/' + id).then(function (data) {
            $scope.pembeliandet = data.detail;
            $scope.form.supplier = data.supplier;
            $scope.calculate();
        });
    }
    $scope.save = function (form, detail) {
        var data = {
            pembelian: form,
            pembeliandet: detail,
        };
        var url = (form.id > 0) ? 'pembelian/update/' + form.id : 'pembelian/create';
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
            Data.delete('pembelian/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.addrow = function () {
        $scope.pembeliandet.unshift({
            id: '',
            barang: [],
            jumlah: 0,
            harga: 0,
            diskon: 0,
            sub_total: 0,
        });
        $scope.calculate();
        $scope.bayar();
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.pembeliandet);
        if (comArr.length > 1) {
            $scope.pembeliandet.splice(paramindex, 1);
            $scope.calculate();
            $scope.bayar();
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.calculate = function () {
        var total = 0;
        var total_belanja = 0;
        var total_diskon = 0;
        angular.forEach($scope.pembeliandet, function (detail) {
            var jml = parseInt(detail.jumlah);
            var harga = parseInt(detail.harga);
            var diskon = parseInt(detail.diskon);
            var sub_total = (jml * harga) - (jml * diskon);
            detail.sub_total = sub_total;
            total += sub_total;
            total_belanja += harga * jml;
            total_diskon += diskon * jml;
        });
        $scope.form.total = total;
        $scope.form.total_belanja = total_belanja;
        $scope.form.diskon = total_diskon;
    };
    $scope.bayar = function () {
        var cash = (parseInt($scope.form.cash) !== null) ? parseInt($scope.form.cash) : 0;
        var total = (parseInt($scope.form.total) !== null) ? parseInt($scope.form.total) : 0;
        var credit = total - cash;
        $scope.form.credit = (credit >= 0) ? credit : 0;
        var kembalian = cash - (total + $scope.form.credit);
        $scope.form.kembalian = kembalian;
    };
    $scope.excel = function () {
        Data.get('pembelian', paramRef).then(function (data) {
            window.location = 'api/web/pembelian/excel';
        });
    };
});
