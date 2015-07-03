app.controller('pembelianCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.pembeliandet = [
        {
            produk : '',
            jumlah : '',
            harga : '',
            diskon : '',
            subtotal : '',
            
        }
    ];
    $scope.is_edit = false;
    $scope.is_view = false;

    Data.get('pembelian/supplierlist').then(function(data) {
        $scope.listSupplier= data.listSupplier;
    });
    Data.get('pembelian/kliniklist').then(function(data) {
        $scope.listKlinik= data.listKlinik;
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

        Data.get('pembelian', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Pembelian";
        $scope.form = {};
        $scope.pembeliandet = [];
        $scope.det = {};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Pembelian : " + form.kode + " - " + form.nama;
        $scope.form = form;
        $scope.det = {};
        Data.get('pembelian/detail/'+form.id).then(function (data) {
            $scope.pembeliandet = data.data;
        });
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Pembelian : " + form.kode + " - " + form.nama;
        $scope.form = form;
        $scope.det = {};
        Data.get('pembelian/detail/'+form.id).then(function (data) {
            $scope.pembeliandet = data.data;
        });
    };
    $scope.save = function (form) {
        var url = (form.id > 0) ? 'pembelian/update/' + form.id : 'pembelian/create';
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

    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('pembelian/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selectedSupplier = function(sup_id){
        Data.get('pembelian/selectedsupplier/' + sup_id).then(function (result) {
                $scope.form.no_tlp = result.selected.no_tlp;
                $scope.form.email = result.selected.email;
                $scope.form.alamat = result.selected.alamat;
            });
    }
    $scope.addrow = function(){
        $scope.pembeliandet.push({
            produk : $scope.det.produk,
            jumlah : $scope.det.jumlah,
            harga : $scope.det.harga,
            diskon : $scope.det.diskon,
            subtotal : $scope.det.subtotal,
        });
        $scope.det.produk = '';
        $scope.det.jumlah = '';
        $scope.det.harga = '';
        $scope.det.diskon = '';
        $scope.det.subtotal = '';
    }
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.pembeliandet);
        if (comArr.length > 1) {
            $scope.pembeliandet.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

})
