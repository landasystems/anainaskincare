app.controller('t_masukCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

    $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/carilagi', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
    
    $scope.pilih = function (detail, $item){
        detail.harga = $item.harga_beli_terakhir;
    }

    $scope.datepickerOptions = {
        language: 'id',
        autoclose: true,
        weekStart: 0
    }

    $scope.detsmasuk = [
        {
            stok_masuk_id: '',
            produk: '',
            jumlah: '',
            harga: '',
            sub_total: '0'
        }];


    $scope.addDetail = function () {
        var newDet = {
            stok_masuk_id: '',
            produk: '',
            jumlah: '',
            harga: '',
        }
        $scope.detsmasuk.unshift(newDet);
    };

    $scope.getkode = function (id) {
        Data.get('stokmasuk/kode_cabang/' + id).then(function (data) {
            $scope.form.kode = data.kode;
            $scope.form.cabang_id = id;

        });
    };

    //subtotal

    //total
    $scope.subtotal = function () {
        var total = 0;
        var sub_total = 0;
        angular.forEach($scope.detsmasuk, function (detail) {
            var jml = (detail.jumlah) ? parseInt(detail.jumlah) : 0;
            var hrg = (detail.harga) ? parseInt(detail.harga) : 0;
            sub_total = (jml * hrg);
            detail.sub_total = sub_total;
            total += sub_total;
        })
        $scope.form.total = total;
    }
//    $scope.form.total=total();

    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detsmasuk);
        if (comArr.length > 1) {
            $scope.detsmasuk.splice(paramindex, 1);
            $scope.subtotal();
        } else {
            alert("Something gone wrong");
        }
    };


    $scope.cabang = {
        minimumInputLength: 3,
        allowClear: true,
    }


    Data.get('stokmasuk/cabang').then(function (data) {
        $scope.listcabang = data.data;
    });

    Data.get('stokmasuk/product').then(function (data) {
        $scope.list_produk = data.data;
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
        Data.get('stokmasuk/', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.excel = function () {
        Data.get('stokmasuk', paramRef).then(function (data) {
            window.location = 'api/web/stokmasuk/excel';
        });
    }

    $scope.create = function (form, detail) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Persediaan Masuk";
        $scope.form = {};
        $scope.form.tanggal = moment().format('DD-MM-YYYY');
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form = form;
        $scope.formtitle = "Edit Persediaan Masuk : " + $scope.form.kode;
        $scope.selected(form.id);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form = form;
        $scope.formtitle = "Persediaan Masuk : " + $scope.form.kode;
        $scope.selected(form.id);

    };
    $scope.save = function (form, detail) {
        var data = {
            stokmasuk: form,
            detailsmasuk: detail,
        };

        var url = (form.id > 0) ? 'stokmasuk/update/' + form.id : 'stokmasuk/create';
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
        $scope.detsmasuk = [{}];
    };

    $scope.trash = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
            row.is_deleted = 1;
            Data.post('stokmasuk/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function (row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('stokmasuk/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('stokmasuk/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id) {
        Data.get('stokmasuk/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.detsmasuk = data.detail;

        });
        $scope.subtotal();
    }

});

