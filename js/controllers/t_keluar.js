app.controller('t_keluarCtrl', function($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

    $scope.datepickerOptions = {
        language: 'id',
        autoclose: true,
        weekStart: 0
    }

    $scope.detskeluar =
            {
                stok_keluar_id: '',
                produk_id: '',
                jumlah: '',
                harga: '',
            };

//    $scope.produk = {
//        minimumInputLength: 3,
//        allowClear: false,
//        ajax: {
//            url: "api/web/stokkeluar/product/",
//            dataType: 'json',
//            data: function(term) {
//                return {
//                    kata: term,
//                };
//            },
//            results: function(data, page) {
//                return {
//                    results: data.produk
//                };
//            }
//        },
//        formatResult: function(object) {
//            return object.produk;
//        },
//        formatSelection: function(object) {
//            return object.produk;
//        },
//        id: function(data) {
//            return data.produk
//        },
//        initSelection : function(element, callback) {
//            var obj = {id: 1, text: 'whatever value'};
//            callback(obj);
//        },
//    };

    $scope.addDetail = function() {
        var newDet = {
            stok_keluar_id: '',
            produk_id: '',
            jumlah: '',
            harga: '',
        }
        $scope.detskeluar.unshift(newDet);
    };

    //subtotal

    //total
    $scope.total = function() {
        var total = 0;
        angular.forEach($scope.detskeluar, function(detail) {
            total += detail.jumlah * detail.harga;
        })
        $scope.form.total = total;

    }
//    $scope.form.total=total();

    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detskeluar);
        if (comArr.length > 1) {
            $scope.detskeluar.splice(paramindex, 1);
            $scope.total();
        } else {
            alert("Something gone wrong");
        }
    };


    $scope.cabang = {
        minimumInputLength: 3,
        allowClear: true,
    }


    Data.get('stokkeluar/cabang').then(function(data) {
        $scope.listcabang = data.data;
    });

    Data.get('stokkeluar/product').then(function(data) {
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

        Data.get('stokkeluar/', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form, detail) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Persediaan Keluar";
        $scope.form = {};
        $scope.detskeluar = [{}];


    };
    $scope.update = function(id) {
        Data.get('stokkeluar/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.detskeluar = data.detail;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode + " - " + $scope.form.nama;

        })
    };
    $scope.view = function(id) {
        Data.get('stokkeluar/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.detskeluar = data.detail;

            $scope.is_edit = true;
            $scope.is_view = false;
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
        console.log($scope.form.tanggal);
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

});

