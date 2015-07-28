app.controller('t_keluarCtrl', function($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.cariProduk = function($query, cabang) {
        if ($query.length >= 3) {
            Data.post('barang/carilagi', {nama: $query, cabang: cabang}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }
    $scope.detskeluar = [
        {
            stok_keluar_id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
            sub_total: '0'
        }];


    //subtotal
    $scope.subtotal = function() {
        var total = 0;
        var sub_total = 0;
        angular.forEach($scope.detskeluar, function(detail) {
            var jml = (detail.jumlah) ? parseInt(detail.jumlah) : 0;
            var hrg = (detail.harga) ? parseInt(detail.harga) : 0;
            sub_total = (jml * hrg);
            detail.sub_total = sub_total;
            total += sub_total;
        })
        $scope.form.total = total;
    }


    $scope.addDetail = function() {
        var newDet = {
            stok_keluar_id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
            sub_total: 0
        }

        $scope.detskeluar.unshift(newDet);
    };



    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detskeluar);

        if (comArr.length > 1) {
            $scope.detskeluar.splice(paramindex, 1);
            $scope.subtotal();
        } else {
            alert("Something gone wrong");
        }
    };


    $scope.cabang = {
        minimumInputLength: 3,
        allowClear: true,
    }


    Data.get('site/session').then(function (data) {
        $scope.listcabang = data.data.user.cabang;
    });

    Data.get('stokkeluar/product').then(function(data) {
        $scope.list_produk = data.data;
    });

    $scope.getkode = function(id) {
        Data.get('stokkeluar/kode_cabang/' + id).then(function(data) {
            $scope.form.kode = data.kode;
            $scope.form.cabang_id = id;
        });
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
        Data.get('stokkeluar/', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function() {
        Data.get('stokkeluar', paramRef).then(function(data) {
            window.location = 'api/web/stokkeluar/excel';
        });
    }

    $scope.create = function(form, detail) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Persediaan Keluar";
        $scope.form = {};
//        $scope.form.tanggal = moment().format('DD-MM-YYYY');
        $scope.form.tanggal = "aa";
    };

    $scope.update = function(id) {
        Data.get('stokkeluar/view/' + id).then(function(data) {
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.form = data.data;
            $scope.detskeluar = data.detail;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode + " - " + $scope.form.nama;

        })
    };
    $scope.view = function(id) {
        Data.get('stokkeluar/view/' + id).then(function(data) {
            $scope.is_edit = true;
            $scope.is_view = true;
            $scope.form = data.data;
            $scope.detskeluar = data.detail;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode + " - " + $scope.form.nama;

        })
    };
    $scope.save = function(form, detail) {
        var data = {
            stokkeluar: form,
            detailskeluar: detail,
        };

        var url = (form.id > 0) ? 'stokkeluar/update/' + form.id : 'stokkeluar/create';
        Data.post(url, data).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function() {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.detskeluar = [{}];
    };

    $scope.trash = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
            row.is_deleted = 1;
            Data.post('stokkeluar/update/' + row.id, row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function(row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('stokkeluar/update/' + row.id, row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('stokkeluar/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


    $scope.selected = function(id) {
        Data.get('stokkeluar/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.detskeluar = data.detail;

        });
        $scope.subtotal();
    }

});

