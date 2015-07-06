app.controller('bayarpiutangCtrl', function($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.total = 0;

    $scope.datepickerOptions = {
        language: 'id',
        autoclose: true,
        weekStart: 0
    }

    Data.get('bayarpiutang/customer').then(function(data) {
        $scope.sCustomer = data.customer;
    });
    Data.get('bayarpiutang/cabang').then(function(data) {
        $scope.sCabang = data.cabang;
    });
    Data.get('bayarpiutang/kode').then(function(data) {
        $scope.list_kode = data.kode;
    });

    $scope.getpenjualan = function(wo) {
//        alert(wo);
        Data.get('bayarpiutang/det_penjualan/' + wo).then(function(data) {
            $scope.form = data.penjualan;
            $scope.form.penjualan_id = wo;
            $scope.detPenjualan = data.detail;
            $scope.total = data.total;
            
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

        Data.get('bayarpiutang/', param).then(function(data) {
            $scope.displayed = data.data;
//            console.log($scope.displayed);
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detPenjualan = [
            {
                id: '',
                tanggal_transaksi: '',
                status: '',
                debet: '',
                credit: ''
            }
        ];

    };
     $scope.update = function (row) {
        $scope.form = row;
        $scope.getpenjualan(row);
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

//        })
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        $scope.getpenjualan(form);
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

//        })
    };
    $scope.save = function (form, detPenjualan) {
        var data = {
            form: form,
            detail: detPenjualan
        };
        var url = 'bayarpiutang/create';
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
    $scope.cancel = function() {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }

        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('bayarpiutang/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.addrow = function () {
        $scope.detPenjualan.unshift({
            id: '',
//            pembelian_id: ($scope.form.id != '') ? $scope.form.id : '',
            tanggal_transaksi: '',
            status: '',
            debet: '',
            credit: '',
        });
    };
     $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detPenjualan);
        if (comArr.length > 1) {
            $scope.detPenjualan.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };


})
