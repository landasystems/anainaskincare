//app.directive('datepickerLocaldate', ['$parse', function ($parse) {
//        var directive = {
//            restrict: 'A',
//            require: ['ngModel'],
//            link: link
//        };
//        return directive;
//
//        function link(scope, element, attr, ctrls) {
//            var ngModelController = ctrls[0];
//
//            // called with a JavaScript Date object when picked from the datepicker
//            ngModelController.$parsers.push(function (viewValue) {
//                // undo the timezone adjustment we did during the formatting
//                viewValue.setMinutes(viewValue.getMinutes() - viewValue.getTimezoneOffset());
//                // we just want a local date in ISO format
//                return viewValue.toISOString().substring(0, 10);
//            });
//
//            // called with a 'yyyy-mm-dd' string to format
//            ngModelController.$formatters.push(function (modelValue) {
//                if (!modelValue) {
//                    return undefined;
//                }
//                // date constructor will apply timezone deviations from UTC (i.e. if locale is behind UTC 'dt' will be one day behind)
//                var dt = new Date(modelValue);
//                // 'undo' the timezone offset again (so we end up on the original date again)
////                dt.setMinutes(dt.getMinutes() - dt.getTimezoneOffset());
//                return dt;
//            });
//        }
//    }]);

app.controller('t_cobaCtrl', function ($scope, Data, toaster, $http, $timeout) {
    $scope.asf = new Date();
    $scope.tagTransform = function (newTag) {
        var item = {
            name: newTag,
            email: newTag.toLowerCase() + '@email.com',
            age: 'unknown',
            country: 'unknown'
        };

        return item;
    };

    $scope.person = {};
    $scope.people = [
        {name: 'Adam', email: 'adam@email.com', age: 12, country: 'United States'},
        {name: 'Amalie', email: 'amalie@email.com', age: 12, country: 'Argentina'},
        {name: 'Estefanía', email: 'estefania@email.com', age: 21, country: 'Argentina'},
        {name: 'Adrian', email: 'adrian@email.com', age: 21, country: 'Ecuador'},
        {name: 'Wladimir', email: 'wladimir@email.com', age: 30, country: 'Ecuador'},
        {name: 'Samantha', email: 'samantha@email.com', age: 30, country: 'United States'},
        {name: 'Nicole', email: 'nicole@email.com', age: 43, country: 'Colombia'},
        {name: 'Natasha', email: 'natasha@email.com', age: 54, country: 'Ecuador'},
        {name: 'Michael', email: 'michael@email.com', age: 15, country: 'Colombia'},
        {name: 'Nicolás', email: 'nicolas@email.com', age: 43, country: 'Colombia'}
    ];

    $scope.multipleDemo = {};
    $scope.multipleDemo.selectedPeople = [$scope.people[5], $scope.people[4]];
});

app.controller('DatepickerDemoCtrl', ['$scope', function ($scope) {
        $scope.today = function () {
            $scope.dt = new Date();
        };
        $scope.today();

        $scope.clear = function () {
            $scope.dt = null;
        };

        // Disable weekend selection
        $scope.disabled = function (date, mode) {
            return (mode === 'day' && (date.getDay() === 0 || date.getDay() === 6));
        };

        $scope.toggleMin = function () {
            $scope.minDate = $scope.minDate ? null : new Date();
        };
        $scope.toggleMin();

        $scope.open = function ($event) {
            $event.preventDefault();
            $event.stopPropagation();

            $scope.opened = true;
        };

        $scope.dateOptions = {
            formatYear: 'yy',
            startingDay: 1,
            class: 'datepicker'
        };

        $scope.initDate = new Date('2016-15-20');
        $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
        $scope.format = $scope.formats[0];
    }]);

app.filter('moment', function () {
    return function (input, format) {
        return moment(parseInt(input)).utc().format(format);
    };
});  