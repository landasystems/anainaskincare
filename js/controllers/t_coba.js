app.controller('t_cobaCtrl', function ($scope, Data, toaster) {

//    $scope.produk_id = "aaa";
    $scope.produk = {
        minimumInputLength: 3,
        allowClear: true,
        ajax: {
            url: "api/web/barang/cari/",
            dataType: 'json',
            data: function (term) {
                return {
                    kata: term,
                };
            },
            results: function (data, page) {
                return {
                    results: data.data
                };
            }
        },
        formatResult: function (object) {
            return object.nama;
        },
        formatSelection: function (object) {
            return object.nama;
        },
//        id: function(data) {
//            return data.id
//        },
        initSelection: function (element, callback) {
            var data = [];
            $(element.val()).each(function () {
                data.push({id: "11", text: "satu"});
            });
            callback(data);
        },
    };
});

