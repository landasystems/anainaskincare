app.controller('r_penjualanCtrl', function($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.penjualan = []
    $scope.datepickerOptions = {
        language: 'id',
        autoclose: true,
        weekStart: 0
    }

    Data.get('returpenjualan/customer').then(function(data) {
        $scope.sCustomer = data.customer;
    });
    Data.get('returpenjualan/cabang').then(function(data) {
        $scope.sCabang = data.cabang;
    });
    Data.get('returpenjualan/kodepenjualan').then(function(data) {
        $scope.listkodepenjualan = data.listkode;
    });

    $scope.cabang = {
        minimumInputLength: 3,
        allowClear: true,
    }

    Data.get('returpenjualan/produk').then(function(data) {
        $scope.sProduk = data.produk;
    });
    $scope.getkodepenjualan = function(id) {
        Data.get('returpenjualan/det_kodepenjualan/' + id).then(function(data) {
            $scope.penjualan = data.penjualan;
            $scope.form.penjualan_id = id;
            $scope.detPenjualan = data.detail;
            angular.forEach($scope.detPenjualan, function(detail) {
                detail.jumlah_retur = 0;
            })

        });
    };

    $scope.total = function() {
        var total = 0;
        var total_retur = 0;
        var diskon = 0;
        var diskon_retur = 0;
        angular.forEach($scope.detPenjualan, function(detail) {

            diskon_retur += detail.jumlah_retur * detail.diskon;
            total_retur += detail.jumlah_retur * detail.harga;
            var sub_total = (total_retur) - (diskon_retur);
            detail.sub_total = sub_total;
        })
        $scope.form.total = (total_retur - diskon_retur);
        $scope.form.belanja = (total - diskon);
        $scope.form.total_belanja = total_retur;
        $scope.form.total_diskon = diskon_retur;

    }
    $scope.bayar = function() {
        var total = parseInt($scope.form.total);
        var cash = parseInt($scope.form.cash);
        var diskon = parseInt($scope.form.total_diskon);
        var credit = (total - diskon) - cash;
        $scope.form.credit = (credit > 0) ? credit : 0;

    }
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

        Data.get('returpenjualan/', param).then(function(data) {
            $scope.displayed = data.data;
//            console.log($scope.displayed);
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};


    };
    $scope.update = function(row) {
        $scope.form = row;
        Data.get('returpenjualan/view/' + row.id).then(function(data) {
            $scope.form = data.data;
            $scope.detPenjualan = data.detail;
            $scope.penjualan = data.penjualan;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.is_create = true;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

        })
    };
    $scope.view = function(form) {
        Data.get('returpenjualan/view/' + row.id).then(function(data) {
            $scope.form = data.data;
            $scope.detPenjualan = data.detail;
            $scope.penjualan = data.penjualan;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.is_create = true;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

        })
    };
    $scope.save = function(form, detail) {
        var data = {
            retur_penjualan: form,
            retur_penjualandet: detail,
        };
        var url = 'returpenjualan/create'
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
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('returpenjualan/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
