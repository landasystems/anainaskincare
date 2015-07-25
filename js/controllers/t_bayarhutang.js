app.controller('hutangCtrl', function ($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.openedDet = -1;
    
    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    $scope.setStatus = function () {
        $scope.openedDet = -1;
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

        Data.get('hutang/', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.history = [
            {
                id: '',
                tanggal_transaksi: '',
                status: '',
                debet: 0,
                credit: 0
            }
        ];

    };
    $scope.update = function (row) {
        $scope.form = row;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Pembayaran Hutang : " + $scope.form.kode + ' - ' + $scope.form.nama;
        $scope.detail(row);
    };
    $scope.view = function (row) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat History Pembayaran Hutang : " + row.kode + ' - ' + row.nama;
        $scope.form = row;
        $scope.detail(row);
        $scope.is_create = false;
    };
    $scope.save = function (form, detail) {
        var data = {
            form: form,
            detail: detail
        };
        var url = 'hutang/update';
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
            Data.delete('hutang/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.detail = function (form) {
        Data.get('hutang/view/' + form.pembelian_id).then(function (result) {
//            $scope.detail = result.data;
            $scope.history = result.data;
        });
    };
    $scope.addrow = function () {
        $scope.history.unshift({
            id : 0,
            debet : 0,
            credit : 0,
            status : '',
            tanggal_transaksi : '',
        });
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.history);
        if (comArr.length > 1) {
            $scope.history.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
})
