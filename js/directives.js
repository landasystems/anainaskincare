/*pagination text*/
angular.module('app')
        .directive('pageSelect', function () {
            return {
                restrict: 'E',
                template: '<input type="text" class="select-page" ng-model="inputPage" ng-change="selectPage(inputPage)">',
                link: function (scope, element, attrs) {
                    scope.$watch('currentPage', function (c) {
                        scope.inputPage = c;
                    });
                }
            }
        });

/*fullscreen*/
angular.module('app')
        .directive('uiFullscreen', ['uiLoad', '$document', '$window', function (uiLoad, $document, $window) {
                return {
                    restrict: 'AC',
                    template: '<i class="fa fa-expand fa-fw text"></i><i class="fa fa-compress fa-fw text-active"></i>',
                    link: function (scope, el, attr) {
                        el.addClass('hide');
                        uiLoad.load('vendor/libs/screenfull.min.js').then(function () {
                            // disable on ie11
                            if (screenfull.enabled && !navigator.userAgent.match(/Trident.*rv:11\./)) {
                                el.removeClass('hide');
                            }
                            el.on('click', function () {
                                var target;
                                attr.target && (target = $(attr.target)[0]);
                                screenfull.toggle(target);
                            });
                            $document.on(screenfull.raw.fullscreenchange, function () {
                                if (screenfull.isFullscreen) {
                                    el.addClass('active');
                                } else {
                                    el.removeClass('active');
                                }
                            });
                        });
                    }
                };
            }]);

/*validation*/
angular.module('ui.validate', []).directive('uiValidate', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, elm, attrs, ctrl) {
            var validateFn, validators = {},
                    validateExpr = scope.$eval(attrs.uiValidate);

            if (!validateExpr) {
                return;
            }

            if (angular.isString(validateExpr)) {
                validateExpr = {validator: validateExpr};
            }

            angular.forEach(validateExpr, function (exprssn, key) {
                validateFn = function (valueToValidate) {
                    var expression = scope.$eval(exprssn, {'$value': valueToValidate});
                    if (angular.isObject(expression) && angular.isFunction(expression.then)) {
                        // expression is a promise
                        expression.then(function () {
                            ctrl.$setValidity(key, true);
                        }, function () {
                            ctrl.$setValidity(key, false);
                        });
                        return valueToValidate;
                    } else if (expression) {
                        // expression is true
                        ctrl.$setValidity(key, true);
                        return valueToValidate;
                    } else {
                        // expression is false
                        ctrl.$setValidity(key, false);
                        return valueToValidate;
                    }
                };
                validators[key] = validateFn;
                ctrl.$formatters.push(validateFn);
                ctrl.$parsers.push(validateFn);
            });

            function apply_watch(watch)
            {
                //string - update all validators on expression change
                if (angular.isString(watch))
                {
                    scope.$watch(watch, function () {
                        angular.forEach(validators, function (validatorFn) {
                            validatorFn(ctrl.$modelValue);
                        });
                    });
                    return;
                }

                //array - update all validators on change of any expression
                if (angular.isArray(watch))
                {
                    angular.forEach(watch, function (expression) {
                        scope.$watch(expression, function ()
                        {
                            angular.forEach(validators, function (validatorFn) {
                                validatorFn(ctrl.$modelValue);
                            });
                        });
                    });
                    return;
                }

                //object - update appropriate validator
                if (angular.isObject(watch))
                {
                    angular.forEach(watch, function (expression, validatorKey)
                    {
                        //value is string - look after one expression
                        if (angular.isString(expression))
                        {
                            scope.$watch(expression, function () {
                                validators[validatorKey](ctrl.$modelValue);
                            });
                        }

                        //value is array - look after all expressions in array
                        if (angular.isArray(expression))
                        {
                            angular.forEach(expression, function (intExpression)
                            {
                                scope.$watch(intExpression, function () {
                                    validators[validatorKey](ctrl.$modelValue);
                                });
                            });
                        }
                    });
                }
            }
            // Support for ui-validate-watch
            if (attrs.uiValidateWatch) {
                apply_watch(scope.$eval(attrs.uiValidateWatch));
            }
        }
    };
});
