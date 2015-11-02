app.controller('customerCtrl', function ($scope, Data, toaster, FileUploader, $modal) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

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
        Data.get('customer', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('customer', paramRef).then(function (data) {
            window.location = 'api/web/customer/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Customer";
        $scope.form = {};
        $scope.form.tanggal_lahir = new Date();
        $scope.form.is_deleted = '0';
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Customer : " + form.kode + " - " + form.nama;
        $scope.form = form;
        $scope.gambar = ($scope.form.foto == null) ? [] : $scope.form.foto;
        $scope.form.tanggal_lahir = new Date(form.tanggal_lahir);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Customer : " + form.kode + " - " + form.nama;
        $scope.form = form;
        $scope.gambar = ($scope.form.foto == null) ? [] : $scope.form.foto;
    };
    $scope.save = function (form) {
        var url = (form.id > 0) ? 'customer/update/' + form.id : 'customer/create';
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
            Data.post('customer/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function (row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('customer/update/' + row.id, row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('customer/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'customer/upload/?folder=customer',
        formData: [],
        removeAfterUpload: true,
    });

    $scope.uploadGambar = function (form) {
        $scope.uploader.uploadAll();
    };

    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            var x = '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            if (!x) {
                toaster.pop('error', "Jenis gambar tidak sesuai");
            }
            return x;
        }
    });

    uploader.filters.push({
        name: 'sizeFilter',
        fn: function (item) {
            var xz = item.size < 2097152;
            if (!xz) {
                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 2 MB");
            }
            return xz;
        }
    });

    $scope.gambar = [];

    uploader.onSuccessItem = function (fileItem, response) {
        if (response.answer == 'File transfer completed') {
            $scope.gambar.unshift({name: response.name});
            $scope.form.foto = $scope.gambar;
        }
    };

    uploader.onBeforeUploadItem = function (item) {
        item.formData.push({
            kode: $scope.form.kode,
        });
    };

    $scope.removeFoto = function (paramindex, namaFoto) {
        var comArr = eval($scope.gambar);
        Data.post('customer/removegambar', {kode: $scope.form.kode, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modal = function (kd_customer, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/customer/' + kd_customer + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };

})
