app.controller('penjualanCtrl', function ($scope, Data, toaster) {
    //init data;
    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detail = [
        {
            type: ''
        }
    ];

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
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
        Data.get('penjualan/', param).then(function (data) {
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
        $scope.form.credit = "0";
        $scope.form.cash = "0";
        $scope.form.atm = "0";
        $scope.form.status = 'Selesai',
                $scope.retrive = {};
        $scope.detPenjualan = [
            {
                type: '',
                jumlah: '1',
                diskon: '0',
                harga: '0',
                sub_total: '0',
            }];
        $scope.form.tanggal = new Date();
        $scope.form.is_deleted = 0;
    };
    $scope.update = function (row) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Edit Data Penjualan : " + row.kode;
        $scope.selected(row.id);
        $scope.form = row;
        $scope.form.tanggal = new Date(row.tanggal);

    };
    $scope.view = function (row) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data Penjualan : " + row.kode;
        $scope.selected(row.id);
//        $scope.form = row;
        $scope.form.tanggal = new Date(row.tanggal);
    };
    $scope.save = function (form, detail) {
        var data = {
            penjualan: form,
            penjualandet: detail,
        };
        var url = (form.id > 0) ? 'penjualan/update/' + form.id : 'penjualan/create'
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.view(result.data);
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
            Data.delete('penjualan/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    Data.get('site/session').then(function (data) {
        $scope.sCabang = data.data.user.cabang;
    });

    $scope.getcustomer = function (wo) {
        Data.get('penjualan/nm_customer/' + wo).then(function (data) {
            $scope.retrive.no_tlp = data.data.no_tlp;
            $scope.retrive.email = data.data.email;
            $scope.retrive.alamat = data.data.alamat;
            $scope.form.customer_id = wo;
            $scope.form.credit = 0;
        });
    };
    $scope.getkode_cabang = function (form) {
        Data.get('penjualan/kode_cabang/' + form.cabang.id).then(function (data) {
            $scope.form.kode = data.kode;
            $scope.list_dokter = data.dokter;
            $scope.list_terapis = data.terapis;
        });
    };
    $scope.excel = function () {
        Data.get('penjualan', paramRef).then(function (data) {
            window.location = 'api/web/penjualan/excel';
        });
    }
    //select2 product
    $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {nama: $query}).then(function (data) {
                $scope.resultsProduk = data.data;
            });
        }
    }
    $scope.cariCustomer = function ($query) {
        if ($query.length >= 3) {
            Data.get('customer/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
                console.log(data.data);
            });
        }
    }
    //retrive lebih dari 1 tabel
    $scope.pilihCustomer = function (form, $item) {
        form.no_tlp = $item.no_tlp;
        form.email = $item.email;
        form.alamat = $item.alamat;
    }
    $scope.pilih = function (detail, $item) {
        detail.harga = ($item.harga_jual != null) ? $item.harga_jual : 0;
        detail.type = $item.type;
        detail.diskon = ($item.diskon != null) ? $item.diskon : 0;
        detail.fee_dokter = $item.fee_dokter;
        detail.fee_terapis = $item.fee_terapis;
        $scope.total();
    }
    $scope.getproduk = function (detail) {
        $scope.detail = detail;
        Data.get('penjualan/det_produk/' + detail.produk_id).then(function (data) {
            $scope.detail.type = data.produk.type;
            $scope.detail.harga = data.produk.harga_jual;
            $scope.detail.diskon = data.produk.diskon;
            $scope.detail.fee_terapis = data.produk.fee_terapis;
            $scope.detail.fee_dokter = data.produk.fee_dokter;
            $scope.form.credit = 0;
        });
    };
    //selected
    $scope.selected = function (id) {
        Data.get('penjualan/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.list_dokter = data.dokter;
            $scope.list_terapis = data.terapis;
            $scope.form.cabang_id = data.data.cabang_id.id;
            $scope.form.no_tlp = data.data.customers.no_tlp;
            $scope.form.email = data.data.customers.email;
            $scope.form.alamat = data.data.customers.alamat;
            $scope.form.customers = data.data.customers;
            $scope.form.user = data.data.user;
            $scope.detPenjualan = data.detail;
            angular.forEach($scope.detPenjualan, function (detail) {
                detail.diskon = (detail.diskon != null) ? detail.diskon : 0;
            })
            $scope.total();
        });
    }
    $scope.addDetail = function () {
        var newDet = {
            id: '',
            type: '',
            jumlah: '1',
            diskon: '0',
            harga: '0',
            sub_total: '0',
        }
        $scope.total();
        $scope.detPenjualan.unshift(newDet);
    };
    $scope.total = function () {
        var total = 0;
        var diskon = 0;
        var total_diskon = 0;
        var total_harga = 0;
        angular.forEach($scope.detPenjualan, function (detail) {
            diskon += detail.jumlah * detail.diskon;
            total += detail.jumlah * detail.harga;
            total_diskon += parseInt(detail.diskon);
            total_harga += parseInt(detail.harga);
        });
        $scope.form.total = (total - diskon);
        $scope.form.belanja = (total - diskon);
        $scope.form.total_diskon = diskon;
        $scope.form.total_harga = total;
        $scope.detail.sub_total = (total - diskon);
    }

    $scope.calcDiskonHarga = function (detail) {
        detail.diskon = (parseInt(detail.diskonpersen) * parseInt(detail.harga)) / 100;
    }
    $scope.calcDiskonPersen = function (detail) {
        detail.diskonpersen = (parseInt(detail.diskon) / parseInt(detail.harga)) * 100;
    }


    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detPenjualan);
        $scope.total();
        if (comArr.length > 1) {
            $scope.detPenjualan.splice(paramindex, 1);
            $scope.total();
        } else {
            alert("Something gone wrong");
        }

    };
    $scope.bayar = function () {
        var total = parseInt($scope.form.total);
        var cash = parseInt($scope.form.cash);
        var credit = total - cash;
        $scope.form.credit = (credit > 0) ? credit : 0;
    }
    $scope.detail.type = {
        allowClear: true,
    }
})
