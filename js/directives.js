/*pagination text*/
angular.module('app')
        .directive('pageSelect', function() {
            return {
                restrict: 'E',
                template: '<input type="text" class="select-page" ng-model="inputPage" ng-change="selectPage(inputPage)">',
                link: function(scope, element, attrs) {
                    scope.$watch('currentPage', function(c) {
                        scope.inputPage = c;
                    });
                }
            }
        });

/*fullscreen*/
angular.module('app')
        .directive('uiFullscreen', ['uiLoad', '$document', '$window', function(uiLoad, $document, $window) {
                return {
                    restrict: 'AC',
                    template: '<i class="fa fa-expand fa-fw text"></i><i class="fa fa-compress fa-fw text-active"></i>',
                    link: function(scope, el, attr) {
                        el.addClass('hide');
                        uiLoad.load('vendor/libs/screenfull.min.js').then(function() {
                            // disable on ie11
                            if (screenfull.enabled && !navigator.userAgent.match(/Trident.*rv:11\./)) {
                                el.removeClass('hide');
                            }
                            el.on('click', function() {
                                var target;
                                attr.target && (target = $(attr.target)[0]);
                                screenfull.toggle(target);
                            });
                            $document.on(screenfull.raw.fullscreenchange, function() {
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
angular.module('ui.validate', []).directive('uiValidate', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            var validateFn, validators = {},
                    validateExpr = scope.$eval(attrs.uiValidate);

            if (!validateExpr) {
                return;
            }

            if (angular.isString(validateExpr)) {
                validateExpr = {validator: validateExpr};
            }

            angular.forEach(validateExpr, function(exprssn, key) {
                validateFn = function(valueToValidate) {
                    var expression = scope.$eval(exprssn, {'$value': valueToValidate});
                    if (angular.isObject(expression) && angular.isFunction(expression.then)) {
                        // expression is a promise
                        expression.then(function() {
                            ctrl.$setValidity(key, true);
                        }, function() {
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
                    scope.$watch(watch, function() {
                        angular.forEach(validators, function(validatorFn) {
                            validatorFn(ctrl.$modelValue);
                        });
                    });
                    return;
                }

                //array - update all validators on change of any expression
                if (angular.isArray(watch))
                {
                    angular.forEach(watch, function(expression) {
                        scope.$watch(expression, function()
                        {
                            angular.forEach(validators, function(validatorFn) {
                                validatorFn(ctrl.$modelValue);
                            });
                        });
                    });
                    return;
                }

                //object - update appropriate validator
                if (angular.isObject(watch))
                {
                    angular.forEach(watch, function(expression, validatorKey)
                    {
                        //value is string - look after one expression
                        if (angular.isString(expression))
                        {
                            scope.$watch(expression, function() {
                                validators[validatorKey](ctrl.$modelValue);
                            });
                        }

                        //value is array - look after all expressions in array
                        if (angular.isArray(expression))
                        {
                            angular.forEach(expression, function(intExpression)
                            {
                                scope.$watch(intExpression, function() {
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

angular.module('ui.select2', []).value('uiSelect2Config', {}).directive('uiSelect2', ['uiSelect2Config', '$timeout', function(uiSelect2Config, $timeout) {
        var options = {};
        if (uiSelect2Config) {
            angular.extend(options, uiSelect2Config);
        }
        return {
            require: 'ngModel',
            priority: 1,
            compile: function(tElm, tAttrs) {
                var watch,
                        repeatOption,
                        repeatAttr,
                        isSelect = tElm.is('select'),
                        isMultiple = angular.isDefined(tAttrs.multiple);

                // Enable watching of the options dataset if in use
                if (tElm.is('select')) {
                    repeatOption = tElm.find('optgroup[ng-repeat], optgroup[data-ng-repeat], option[ng-repeat], option[data-ng-repeat]');

                    if (repeatOption.length) {
                        repeatAttr = repeatOption.attr('ng-repeat') || repeatOption.attr('data-ng-repeat');
                        watch = jQuery.trim(repeatAttr.split('|')[0]).split(' ').pop();
                    }
                }

                return function(scope, elm, attrs, controller) {
                    // instance-specific options
                    var opts = angular.extend({}, options, scope.$eval(attrs.uiSelect2));

                    /*
                     Convert from Select2 view-model to Angular view-model.
                     */
                    var convertToAngularModel = function(select2_data) {
                        var model;
                        if (opts.simple_tags) {
                            model = [];
                            angular.forEach(select2_data, function(value, index) {
                                model.push(value.id);
                            });
                        } else {
                            model = select2_data;
                        }
                        return model;
                    };

                    /*
                     Convert from Angular view-model to Select2 view-model.
                     */
                    var convertToSelect2Model = function(angular_data) {
                        var model = [];
                        if (!angular_data) {
                            return model;
                        }

                        if (opts.simple_tags) {
                            model = [];
                            angular.forEach(
                                    angular_data,
                                    function(value, index) {
                                        model.push({'id': value, 'text': value});
                                    });
                        } else {
                            model = angular_data;
                        }
                        return model;
                    };

                    if (isSelect) {
                        // Use <select multiple> instead
                        delete opts.multiple;
                        delete opts.initSelection;
                    } else if (isMultiple) {
                        opts.multiple = true;
                    }

                    if (controller) {
                        // Watch the model for programmatic changes
                        scope.$watch(tAttrs.ngModel, function(current, old) {
                            if (!current) {
                                return;
                            }
                            if (current === old) {
                                return;
                            }
                            controller.$render();
                        }, true);
                        controller.$render = function() {
                            if (isSelect) {
                                elm.select2('val', controller.$viewValue);
                            } else {
                                if (opts.multiple) {
                                    controller.$isEmpty = function(value) {
                                        return !value || value.length === 0;
                                    };
                                    var viewValue = controller.$viewValue;
                                    if (angular.isString(viewValue)) {
                                        viewValue = viewValue.split(',');
                                    }
                                    elm.select2(
                                            'data', convertToSelect2Model(viewValue));
                                    if (opts.sortable) {
                                        elm.select2("container").find("ul.select2-choices").sortable({
                                            containment: 'parent',
                                            start: function() {
                                                elm.select2("onSortStart");
                                            },
                                            update: function() {
                                                elm.select2("onSortEnd");
                                                elm.trigger('change');
                                            }
                                        });
                                    }
                                } else {
                                    if (angular.isObject(controller.$viewValue)) {
                                        elm.select2('data', controller.$viewValue);
                                    } else if (!controller.$viewValue) {
                                        elm.select2('data', null);
                                    } else {
                                        elm.select2('val', controller.$viewValue);
                                    }
                                }
                            }
                        };

                        // Watch the options dataset for changes
                        if (watch) {
                            scope.$watch(watch, function(newVal, oldVal, scope) {
                                if (angular.equals(newVal, oldVal)) {
                                    return;
                                }
                                // Delayed so that the options have time to be rendered
                                $timeout(function() {
                                    elm.select2('val', controller.$viewValue);
                                    // Refresh angular to remove the superfluous option
                                    controller.$render();
                                    if (newVal && !oldVal && controller.$setPristine) {
                                        controller.$setPristine(true);
                                    }
                                });
                            });
                        }

                        // Update valid and dirty statuses
                        controller.$parsers.push(function(value) {
                            var div = elm.prev();
                            div
                                    .toggleClass('ng-invalid', !controller.$valid)
                                    .toggleClass('ng-valid', controller.$valid)
                                    .toggleClass('ng-invalid-required', !controller.$valid)
                                    .toggleClass('ng-valid-required', controller.$valid)
                                    .toggleClass('ng-dirty', controller.$dirty)
                                    .toggleClass('ng-pristine', controller.$pristine);
                            return value;
                        });

                        if (!isSelect) {
                            // Set the view and model value and update the angular template manually for the ajax/multiple select2.
                            elm.bind("change", function(e) {
                                e.stopImmediatePropagation();

                                if (scope.$$phase || scope.$root.$$phase) {
                                    return;
                                }
                                scope.$apply(function() {
                                    controller.$setViewValue(
                                            convertToAngularModel(elm.select2('data')));
                                });
                            });

                            if (opts.initSelection) {
                                var initSelection = opts.initSelection;
                                opts.initSelection = function(element, callback) {
                                    initSelection(element, function(value) {
                                        var isPristine = controller.$pristine;
                                        controller.$setViewValue(convertToAngularModel(value));
                                        callback(value);
                                        if (isPristine) {
                                            controller.$setPristine();
                                        }
                                        elm.prev().toggleClass('ng-pristine', controller.$pristine);
                                    });
                                };
                            }
                        }
                    }

                    elm.bind("$destroy", function() {
                        elm.select2("destroy");
                    });

                    attrs.$observe('disabled', function(value) {
                        elm.select2('enable', !value);
                    });

                    attrs.$observe('readonly', function(value) {
                        elm.select2('readonly', !!value);
                    });

                    if (attrs.ngMultiple) {
                        scope.$watch(attrs.ngMultiple, function(newVal) {
                            attrs.$set('multiple', !!newVal);
                            elm.select2(opts);
                        });
                    }

                    // Initialize the plugin late so that the injected DOM does not disrupt the template compiler
                    $timeout(function() {
                        elm.select2(opts);

                        // Set initial value - I'm not sure about this but it seems to need to be there
                        elm.select2('data', controller.$modelValue);
                        // important!
                        controller.$render();

                        // Not sure if I should just check for !isSelect OR if I should check for 'tags' key
                        if (!opts.initSelection && !isSelect) {
                            var isPristine = controller.$pristine;
                            controller.$pristine = false;
                            controller.$setViewValue(
                                    convertToAngularModel(elm.select2('data'))
                                    );
                            if (isPristine) {
                                controller.$setPristine();
                            }
                            elm.prev().toggleClass('ng-pristine', controller.$pristine);
                        }
                    });
                };
            }
        };
    }]);