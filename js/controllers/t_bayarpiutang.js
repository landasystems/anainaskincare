app.controller('bayarpiutangCtrl', function($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    var paramRef;

    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.openedDet = -1;

    $scope.openDet = function($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    
    $scope.setStatus = function() {
        $scope.openedDet = -1;
    };

    Data.get('bayarpiutang/customer').then(function(data) {
        $scope.sCustomer = data.customer;
    });
    Data.get('site/session').then(function(data) {
        $scope.sCabang = data.data.user.cabang;
    });
    Data.get('bayarpiutang/kode').then(function(data) {
        $scope.list_kode = data.kode;
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
        Data.get('bayarpiutang/', param).then(function(data) {
            $scope.displayed = data.data;
//            console.log($scope.displayed);
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function() {
        Data.get('bayarpiutang', paramRef).then(function(data) {
            window.location = 'api/web/bayarpiutang/excel';
        });
    }

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
                credit: 0
            }
        ];

    };
    $scope.update = function(row) {
        $scope.form = row;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Data Piutang : " + $scope.form.kode;
        $scope.detail(row);
        $scope.detPenjualan = [
            {
                id: '',
                tanggal_transaksi: '',
                status: '',
                debet: '',
                credit: 0
            }
        ];
    };
    $scope.view = function(row) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form = row;
        $scope.detail(row);
        $scope.total();
        $scope.formtitle = "Lihat Data Piutang : " + $scope.form.kode;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
    };
    $scope.save = function(form, detPenjualan) {
        var data = {
            form: form,
            detail: detPenjualan
        };
        var url = 'bayarpiutang/update';
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
            Data.delete('bayarpiutang/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.detail = function(form) {
        Data.get('bayarpiutang/view/' + form.penjualan_id).then(function(result) {
//            $scope.detail = result.data;
            $scope.detPenjualan = result.data;
            angular.forEach($scope.detPenjualan, function(detail) {
                detail.credit = (detail.credit != undefined) ? detail.credit : 0;
                detail.debet = (detail.debet != undefined) ? detail.debet : 0;
            });
            $scope.total();

        });

    };
    $scope.addrow = function() {
        $scope.detPenjualan.unshift({
            id: '',
//            pembelian_id: ($scope.form.id != '') ? $scope.form.id : '',
            tanggal_transaksi: '',
            status: '',
            debet: 0,
            credit: 0,
        });
        $scope.total();
    };

    $scope.total = function() {
        var total_debet = 0;
        var total_credit = 0;
        angular.forEach($scope.detPenjualan, function(detail) {
            total_debet += (parseInt(detail.debet) != undefined) ? parseInt(detail.debet) : 0;
            total_credit += (parseInt(detail.credit) != undefined) ? parseInt(detail.credit) : 0;
        });
        $scope.form.total_debet = total_debet;
        $scope.form.total_credit = total_credit;
    }
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detPenjualan);
        if (comArr.length > 1) {
            $scope.detPenjualan.splice(paramindex, 1);
            $scope.total();
        } else {
            alert("Something gone wrong");
        }
    };


})
