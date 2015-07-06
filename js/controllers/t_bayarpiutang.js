app.controller('bayarpiutangCtrl', function($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
   
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
//            console.log(data.detail);
        });
    };



    $scope.addDetail = function() {
        var newDet = {
            type: '',
            jumlah: '',
            diskon: '',
            pegawai_terapis_id: '',
            pegawai_dokter_id: '',
        }
        $scope.detPenjualan.unshift(newDet);
    };
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detBom);
        if (comArr.length > 1) {
            $scope.detPenjualan.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
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
            type: '',
            jumlah: '',
            diskon: '',
            pegawai_terapis_id: '',
            pegawai_dokter_id: '',
        }
    ];

    };
    $scope.update = function(row) {
        $scope.form = row;
        Data.get('bayarpiutang/view/' + row.id).then(function(data) {
//            $scope.form = data.data;
            $scope.detPenjualan = data.detail;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.is_create = true;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

        })
    };
    $scope.view = function(form) {
         Data.get('bayarpiutang/view/' + form.id).then(function(data) {
//            $scope.form = data.data;
            $scope.detPenjualan = data.detail;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
        })
    };
    $scope.save = function(form, detail) {
     
        var url = (form.id > 0) ? 'bayarpiutang/update/' + form.id : 'bayarpiutang/create'
        Data.post(url, form).then(function(result) {
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
            Data.delete('penjualan/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
