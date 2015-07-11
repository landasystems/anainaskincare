
app.controller('t_cobaCtrl', function ($scope, Data, toaster, $http, $timeout) {
//    $scope.address = {selected:"Aaaa"};
//    $scope.refreshAddresses = function (address) {
//        var params = {address: address, sensor: false};
//        return $http.get(
//                'http://maps.googleapis.com/maps/api/geocode/json',
//                {params: params}
//        ).then(function (response) {
//            $scope.addresses = response.data.results;
//        });
//    };



    // LOGIC IS HERE!!!
    //-------------------------------
    //-------------------------------
//    $scope.availableColors = [];
//    $http.get('api/web/barang/cari').then(
//            function (response) {
//                $scope.availableColors = response.data.data;
//                $scope.multipleDemo.colors = ['Blue', 'Red'];
//                console.log(response)
//            },
//            function () {
//                console.log('ERROR!!!');
//            }
//    );

    $scope.funcAsync = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.multipleDemo = {};
    $scope.form = {};
//    $scope.form.barang_id = {"id":"1","nama":"Sabun"};



});

