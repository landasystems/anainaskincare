angular.module('app')
        .run(
                ['$rootScope', '$state', '$stateParams', 'Data',
                    function($rootScope, $state, $stateParams, Data) {
                        $rootScope.$state = $state;
                        $rootScope.$stateParams = $stateParams;
                        //pengecekan login
                        $rootScope.$on("$stateChangeStart", function(event, toState) {
                            var globalmenu = ['app.dashboard', 'master.userprofile', 'access.signin', 'transaksi.coba'];
                            Data.get('site/session').then(function(results) {
                                if (typeof results.data.user != "undefined") {
                                    $rootScope.user = results.data.user;
                                    if (results.data.user.akses[(toState.name).replace(".", "_")]) { // jika punya hak akses, return true

                                    } else {
                                        if (globalmenu.indexOf(toState.name) >= 0) { //menu global menu tidak di redirect

                                        } else {
                                            $state.go("access.forbidden");
                                        }
                                    }
                                } else {
                                    $state.go("access.signin");
                                }
                            });
                        });
                    }
                ]
                )
        .config(
                ['$stateProvider', '$urlRouterProvider',
                    function($stateProvider, $urlRouterProvider) {

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
                                            function($ocLazyLoad) {

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
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/site.js').then(
                                                        );
                                            }]
                                    }
                                })
                                .state('access.404', {
                                    url: '/404',
                                    templateUrl: 'tpl/page_404.html'
                                })
                                .state('access.forbidden', {
                                    url: '/forbidden',
                                    templateUrl: 'tpl/page_forbidden.html'
                                })
                                //master
                                .state('master', {
                                    url: '/master',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('master.pegawai', {
                                    url: '/pegawai',
                                    templateUrl: 'tpl/m_pegawai/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pegawai.js');
                                            }]
                                    }
                                })
                                .state('master.roles', {
                                    url: '/roles',
                                    templateUrl: 'tpl/m_roles/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/roles.js');
                                            }]
                                    }
                                })
                                .state('master.satuan', {
                                    url: '/satuan',
                                    templateUrl: 'tpl/m_satuan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/satuan.js');
                                            }]
                                    }
                                })
                                .state('master.user', {
                                    url: '/pengguna',
                                    templateUrl: 'tpl/m_user/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna.js');
                                            }]
                                    }
                                })
                                .state('master.userprofile', {
                                    url: '/profile',
                                    templateUrl: 'tpl/m_user/profile.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/pengguna_profile.js');
                                            }]
                                    }
                                })
                                .state('master.cabang', {
                                    url: '/cabang',
                                    templateUrl: 'tpl/m_cabang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('js/controllers/cabang.js');
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
                                    params: {'form': null},
                                    templateUrl: 'tpl/m_barang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load('angularFileUpload').then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/barang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                // Transaksi
                                .state('transaksi', {
                                    url: '/trans',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('transaksi.stokmasuk', {
                                    url: '/masuk',
                                    templateUrl: 'tpl/t_masuk/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_masuk.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.stokkeluar', {
                                    url: '/keluar',
                                    templateUrl: 'tpl/t_keluar/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_keluar.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.coba', {
                                    url: '/coba',
                                    templateUrl: 'tpl/t_coba/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load([]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_coba.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.bayarhutang', {
                                    url: '/bayarhutang',
                                    templateUrl: 'tpl/t_bayarhutang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_bayarhutang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.returpembelian', {
                                    url: '/returpembelian',
                                    templateUrl: 'tpl/t_returpembelian/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_returpembelian.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.pembelian', {
                                    url: '/pembelian',
                                    templateUrl: 'tpl/t_pembelian/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_pembelian.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.penjualan', {
                                    url: '/penjualan',
                                    templateUrl: 'tpl/t_penjualan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_penjualan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.bayarpiutang', {
                                    url: '/bayarpiutang',
                                    templateUrl: 'tpl/t_bayarpiutang/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_bayarpiutang.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('transaksi.returpenjualan', {
                                    url: '/returpenjualan',
                                    templateUrl: 'tpl/t_returpenjualan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker']).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/t_returpenjualan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })

                                // Transaksi
                                .state('laporan', {
                                    url: '/laporan',
                                    templateUrl: 'tpl/app.html'
                                })
                                .state('laporan.kartustok', {
                                    url: '/kartustok',
                                    templateUrl: 'tpl/l_kartustok/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker', ]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/l_kartustok.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('laporan.bonuskaryawan', {
                                    url: '/bonuskaryawan',
                                    templateUrl: 'tpl/l_bonuskaryawan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker', ]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/l_bonuskaryawan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('laporan.labarugi', {
                                    url: '/labarugi',
                                    templateUrl: 'tpl/l_labarugi/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker', ]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/l_labarugi.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('laporan.kartustatus', {
                                    url: '/kartustatus',
                                    templateUrl: 'tpl/l_kartustatus/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker', ]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/l_kartustatus.js');
                                                        }
                                                );
                                            }]
                                    }
                                })
                                .state('laporan.penjualan', {
                                    url: '/lpenjualan',
                                    templateUrl: 'tpl/l_penjualan/index.html',
                                    resolve: {
                                        deps: ['$ocLazyLoad',
                                            function($ocLazyLoad) {
                                                return $ocLazyLoad.load(['daterangepicker', ]).then(
                                                        function() {
                                                            return $ocLazyLoad.load('js/controllers/l_penjualan.js');
                                                        }
                                                );
                                            }]
                                    }
                                })


                    }
                ]
                );
