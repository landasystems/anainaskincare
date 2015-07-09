app.controller('t_cobaCtrl', function ($scope, Data, toaster) {
    

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

});

