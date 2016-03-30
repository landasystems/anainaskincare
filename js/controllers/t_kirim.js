app.controller('kirimCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.detTransfer = [];

    Data.get('site/session').then(function (data) {
        $scope.sCabang = data.data.user.cabang;
    });

    Data.get('cabang/listcabang').then(function (data) {
        $scope.lCabang = data.data;
    });

    $scope.cariProduk = function ($query, cabang) {
        if ($query.length >= 3) {
            Data.post('barang/carilagi', {nama: $query, cabang: cabang}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    //subtotal
    $scope.subtotal = function () {
        var total = 0;
        var sub_total = 0;
        angular.forEach($scope.detTransfer, function (detail) {
            var jml = (detail.jumlah) ? parseInt(detail.jumlah) : 0;
            var hrg = (detail.harga) ? parseInt(detail.harga) : 0;
            sub_total = (jml * hrg);
            detail.sub_total = sub_total;
            total += sub_total;
        })
        $scope.form.total = total;
    };


    $scope.addDetail = function () {
        var newDet = {
            stok_keluar_id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
            sub_total: 0
        }

        $scope.detTransfer.unshift(newDet);
    };



    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detTransfer);
        if (comArr.length > 1) {
            $scope.detTransfer.splice(paramindex, 1);
            $scope.subtotal();
        } else {
            alert("Something gone wrong");
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

        Data.get('transfer/kirim', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_transfer = new Date();
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nota;
        $scope.form = form;
        $scope.form.tgl_transfer = new Date(form.tgl_transfer);
        $scope.selected(form.id);
    };

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nota;
        $scope.form = form;
        $scope.selected(form.id);
    };

    $scope.selected = function (id) {
        Data.get('transfer/view/' + id).then(function (result) {
            $scope.form = result.data;
            $scope.form.tgl_transfer = new Date(result.data.tgl_transfer);
            $scope.form.tgl_terima = new Date(result.data.tgl_terima);
            $scope.detTransfer = result.details;
            $scope.subtotal();
        });
    }

    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail,
        }

        var url = (form.id > 0) ? 'transfer/update/' + form.id : 'transfer/create';
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
            Data.delete('transfer/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
