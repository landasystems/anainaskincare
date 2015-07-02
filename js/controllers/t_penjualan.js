app.controller('penjualanCtrl', function($scope, Data, toaster) {
    

    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    
     Data.get('penjualan/customer').then(function(data) {
        $scope.sCustomer = data.customer;
    });
     Data.get('penjualan/cabang').then(function(data) {
        $scope.sCabang = data.cabang;
    });
     $scope.getcustomer = function(wo) {
        
        Data.post('penjualan/nm_customer/', wo).then(function(data) {
            $scope.sCustomer = data.customer;
//            $scope.form.nm_customer = data.customer.nama;
            $scope.form.no_telp = data.customer;
//            $scope.form.email = data.customer;
//            $scope.form.alamat = data.customer;
//            alert(data.customer);
    console.log($scope.sCustomer);

        });
    };
    
    $scope.produk = {
        minimumInputLength: 3,
        allowClear: true,
        initSelection: function(el, fn) {
        },
        ajax: {
            url: "api/web/penjualan/produk/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.produk,
                };
            }
        },
        formatResult: function(object) {
            return object.produk;
        },
        formatSelection: function(object) {
            return object.produk;
        },
        id: function(data) {
            return data.id
        }
    };
    
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detPenjualan = [
        {
            produk_id: '',
            jumlah: '',
            diskon: '',
            pegawai_terapis_id: '',
            pegawai_dokter_id: '',
        }
    ];
       $scope.addDetail = function() {
        var newDet = {
            produk_id: '',
            jumlah: '',
            diskon: '',
            pegawai_terapis_id: '',
            pegawai_dokter_id: '',
        }
        $scope.detPenjualan.push(newDet);
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

        Data.get('penjualan', param).then(function(data) {
            $scope.displayed = data.data;
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
      
    };
    $scope.update = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nama;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama;
        $scope.form = form;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'penjualan/create/' : 'penjualan/update/' + form.id;
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
      if (!$scope.is_view){ //hanya waktu edit cancel, di load table lagi
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
