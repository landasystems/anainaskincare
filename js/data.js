app.factory("Data", ['$http', '$location',
    function ($http, $q, $location) {
        var serviceBase = 'api/web/';

        var obj = {};

        obj.get = function (q, object) {
            return $http.get(serviceBase + q, {
                params: object
            }).then(function (results) {
                return results.data;
            });
        };
        obj.post = function (q, object) {
            $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
            return $http.post(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.put = function (q, object) {
            return $http.put(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.delete = function (q) {
            return $http.delete(serviceBase + q).then(function (results) {
                return results.data;
            });
        };
        return obj;
    }]);
