app.controller('penjualanCtrl', function($scope, Data, toaster) {


    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detail = [
        {
            type: ''
        }
    ];
    $scope.detPenjualan = [
        {
            type: '',
            jumlah: '',
            diskon: '',
            pegawai_terapis_id: '',
            pegawai_dokter_id: '',
        }
    ];
    $scope.datepickerOptions = {
        language: 'id',
        autoclose: true,
        weekStart: 0
    }

    Data.get('penjualan/customer').then(function(data) {
        $scope.sCustomer = data.customer;
    });
    Data.get('penjualan/cabang').then(function(data) {
        $scope.sCabang = data.cabang;
    });
    Data.get('penjualan/produk').then(function(data) {
        $scope.sProduk = data.produk;
    });
    $scope.getcustomer = function(wo) {
        Data.post('penjualan/nm_customer/', wo).then(function(data) {
            $scope.form = data.customer;
            $scope.form.customer_id = wo;

        });
    };
    $scope.getproduk = function(detail) {
        $scope.detail = detail;
        Data.get('penjualan/det_produk/' + detail.produk_id).then(function(data) {
            $scope.detail.type = data.produk.type;
            $scope.detail.harga = data.produk.harga_jual;
            $scope.detail.diskon = data.produk.diskon;
//            alert(data.produk.type);
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
 $scope.total();
        $scope.detPenjualan.unshift(newDet);
        
    };
      $scope.total = function() {
        var total = 0;
        var diskon = 0;
        angular.forEach($scope.detPenjualan, function(detail) {
            diskon += detail.jumlah * detail.diskon;
            total += detail.jumlah * detail.harga;
        });
        $scope.form.total = (total - diskon);
        $scope.form.belanja = (total - diskon);
        $scope.form.total_belanja = total;
        $scope.detail.sub_total = (total - diskon);
        $scope.form.total_diskon = diskon;
        $scope.bayar();

    }
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detPenjualan);
         $scope.total();
        if (comArr.length > 1) {
            $scope.detPenjualan.splice(paramindex, 1);
            $scope.total();
        } else {
            alert("Something gone wrong");
        }
        
    };
  
    $scope.bayar = function() {
        var total = parseInt($scope.form.total);
        var cash = parseInt($scope.form.cash);
//        alert(cash);
        var credit = total - cash;
//        alert(credit);
        $scope.form.credit = (credit > 0) ? credit : 0;
//        $scope.total();

    }
    $scope.detail.type = {
        allowClear: true,
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

        Data.get('penjualan/', param).then(function(data) {
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
        console.log(row);
        $scope.form = row;
        Data.get('penjualan/view/' + row.id).then(function(data) {
//            $scope.form = data.data;
            $scope.detPenjualan = data.detail;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.is_create = true;
            $scope.formtitle = "Edit Persediaan Keluar : " + $scope.form.kode;

        })
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
    };
    $scope.save = function(form, detail) {
        var data = {
            penjualan: form,
            penjualandet: detail,
        };
        var url = (form.id > 0) ? 'penjualan/update/' + form.id : 'penjualan/create'
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
            Data.delete('penjualan/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
