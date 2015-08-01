app.controller('pembelianCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.pembeliandet = [
        {
            id: '',
//            pembelian_id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
            diskon: '',
            sub_total: ''
        }
    ];
    $scope.datepickerOptions = {
        language: "id",
        autoclose: true,
        weekStart: 0
    }

    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.cariSupplier = function ($query) {
        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.listSupplier = data.data;
            });
        }
    };
    Data.get('pembelian/kliniklist').then(function (data) {
        $scope.listKlinik = data.listKlinik;
    });
    $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {nama: $query}).then(function (data) {
                $scope.listProduk = data.data;
            });
        }
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.pilih = function (detail, $item){
        detail.harga = $item.harga_beli_terakhir;
        detail.diskon = $item.diskon;
    };
    $scope.pilihSupplier = function (form, $item){
        form.no_tlp = $item.no_tlp;
        form.email = $item.email;
        form.alamat = $item.alamat;
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

        Data.get('pembelian', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Pembelian";
        $scope.form = {};
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
        $scope.formtitle = "Edit Pembelian : " + form.kode;
        $scope.form = form;
        $scope.det = {};
        $scope.getDetail(form.id);
        $scope.bayar();
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Lihat Pembelian : " + $scope.form.kode;
        $scope.form = form;
        $scope.det = {};
        $scope.getDetail(form.id);
    };
    $scope.getDetail = function(id){
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
//    $scope.selectedSupplier = function (sup_id) {
//        Data.get('pembelian/selectedsupplier/' + sup_id).then(function (result) {
//            $scope.form.no_tlp = result.selected.no_tlp;
//            $scope.form.email = result.selected.email;
//            $scope.form.alamat = result.selected.alamat;
//        });
//    };
//    $scope.selectedProduk = function (detail) {
//        $scope.detail = detail;
//        Data.get('pembelian/selectedproduk/' + detail.produk_id).then(function (result) {
//            $scope.detail.diskon = parseInt(result.selected.diskon);
//            $scope.detail.harga = parseInt(result.selected.harga_beli_terakhir);
//        });
//    };
    $scope.addrow = function () {
        $scope.pembeliandet.unshift({
            id: '',
//            pembelian_id: ($scope.form.id != '') ? $scope.form.id : '',
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
        var credit = (parseInt($scope.form.credit) !== null) ? parseInt($scope.form.credit) : 0;
        var kembalian = cash - total + credit;
        $scope.form.kembalian = (kembalian> 0) ? kembalian : 0;
    }
})
