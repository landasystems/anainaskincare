app.controller('RolesCtrl', function ($scope, $http, Data) {
    $scope.rowCollection = {};
    Data.get('roles').then(function(data){
        $scope.rowCollection = data.data;
    });
})
