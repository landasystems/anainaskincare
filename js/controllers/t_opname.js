app.controller('t_opnameCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope._view = false;
    $scope.detProduk = [];
    $scope.tanggal = new Date();

    $scope.getkode = function (id) {
        Data.get('opname/kode_cabang/' + id).then(function (data) {
            $scope.form.kode = data.kode;
            $scope.form.cabang_id = id;
        });
    };

    $scope.listcabang = [];
    Data.get('site/session').then(function (data) {
        $scope.listcabang = data.data.user.cabang;
    });

    $scope.listkategori = [];
    Data.get('kategori/listkategori').then(function (data) {
        $scope.listkategori = data.data;
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
        Data.get('opname/', param).then(function (data) {
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

    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('satuan/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.create = function (form, detail) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Stok Opname Bulanan";
        $scope.form = {};
        $scope.detProduk = [{}];
        $scope.form.tanggal = new Date();
    };

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Opname Stok Bulanan : " + form.kode;
        $scope._view = true;
        $scope.selected(form.id);
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Stok Opname Bulanan : " + form.kode;
        $scope._view = true;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.selected(form.id);
    };

    $scope.save = function (form, detail, is_temp) {
        form.is_tmp = is_temp;

        var data = {
            form: form,
            detailopname: detail,
        };

        var url = 'opname/create';
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

    $scope.viewDet = function (form) {
        if (typeof form.cabang_id == "undefined" || form.cabang_id == '') {
            toaster.pop('info', "Perhatian", 'Silahkan pilih cabang');
        } else if (typeof form.kategori_id == "undefined" || form.kategori_id == '') {
            toaster.pop('info', "Perhatian", 'Silahkan pilih kategori');
        } else {
            $scope._view = true;
            Data.post('opname/getproduk/', form).then(function (data) {
                $scope.detProduk = data.data;
            });
        }
    };

    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.detProduk = [{}];
    };

    $scope.selected = function (id) {
        Data.get('opname/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.detProduk = data.detail;

        });
    };

});

