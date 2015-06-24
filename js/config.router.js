'use strict';

/**
 * Config for the router
 */
angular.module('app')
        .run(
                ['$rootScope', '$state', '$stateParams',
                    function ($rootScope, $state, $stateParams) {
                        $rootScope.$state = $state;
                        $rootScope.$stateParams = $stateParams;
                    }
                ]
                )
        .config(
                ['$stateProvider', '$urlRouterProvider',
                    function ($stateProvider, $urlRouterProvider) {

                        $urlRouterProvider
                                .otherwise('/app/dashboard');
                        $stateProvider
                                .state('app', {
                                    abstract: true,
                                    url: '/app',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('app.dashboard', {
                                    url: '/dashboard',
                                    templateUrl: 'tpl/dashboard.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {

                                            }]
                                    }
                                })
                                .state('app.pegawai', {
                                    url: '/pegawai',
                                    templateUrl: 'tpl/m_pegawai/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pegawai.js');
                                            }]
                                    }
                                })
                                // others
                                .state('access', {
                                    url: '/access',
                                    template: '<div ui-view class="fade-in-right-big smooth"></div>'
                                })
                                .state('access.signin', {
                                    url: '/signin',
                                    templateUrl: 'tpl/page_signin.html',
                                    resolve: {
                                        deps: ['uiLoad',
                                            function (uiLoad) {
                                                return uiLoad.load(['js/controllers/signin.js']);
                                            }]
                                    }
                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
                                })
                                //master
                                .state('master', {
                                    url: '/master',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('master.roles', {
                                    url: '/roles',
                                    templateUrl: 'tpl/m_roles/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/roles.js');
                                            }]
                                    }
                                })
                                .state('master.satuan', {
                                    url: '/satuan',
                                    templateUrl: 'tpl/m_satuan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function ($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/satuan.js');
                                            }]
                                    }
                                })
                                .state('master.pengguna', {
                                    url: '/pengguna',
                                    templateUrl: 'tpl/m_user/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna.js');
                                            }]
                                    }
                                })
                                .state('master.klinik', {
                                    url: '/klinik',
                                    templateUrl: 'tpl/m_klinik/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/klinik.js');
                                            }]
                                    }
                                })
                                .state('master.customer', {
                                    url: '/customer',
                                    templateUrl: 'tpl/m_customer/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/customer.js');
                                            }]
                                    }
                                })
                                .state('master.supplier', {
                                    url: '/supplier',
                                    templateUrl: 'tpl/m_supplier/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/supplier.js');
                                            }]
                                    }
                                })
                                .state('master.karyawan', {
                                    url: '/karyawan',
                                    templateUrl: 'tpl/m_karyawan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/karyawan.js');
                                            }]
                                    }
                                })
                                .state('master.kategori', {
                                    url: '/kategori',
                                    templateUrl: 'tpl/m_kategori/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/kategori.js');
                                            }]
                                    }
                                })
                                .state('master.barang', {
                                    url: '/barang',
                                    templateUrl: 'tpl/m_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/barang.js');
                                            }]
                                    }
                                })
                    }
                ]
                );
