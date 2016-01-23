angular.module('starter.controllers', [])

.run(['$rootScope', '$state', '$ionicViewSwitcher', '$ionicPopup', 'Params', 'Helper', 'UserService', 'AccountService', 'CartService', 'OrderService', 'AddressService', function($rootScope, $state, $ionicViewSwitcher, $ionicPopup, Params, Helper, UserService, AccountService, CartService, OrderService, AddressService) {
    $rootScope.Params = Params;
    $rootScope.Helper = Helper;

    // Access control
    $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
        if (toState.access === '@' && UserService.isGuest) {
            event.preventDefault();
            $ionicViewSwitcher.nextDirection('forward');
            $state.go('login');
        } else if (toState.access === '?' && !UserService.isGuest) {
            event.preventDefault();
            $ionicViewSwitcher.nextDirection('back');
            Helper.back();
        }
    });
    
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
        $rootScope.previousState = fromState;
        $rootScope.previousParams = fromParams;
    });
    
    $rootScope.$on('userLoginSuccess', function (event) {
        AccountService.reload();
        OrderService.reload();
        AddressService.reload();
    });
    
    $rootScope.$on('userLogoutSuccess', function (event) {
        AccountService.destroy();
        OrderService.destroy();
        AddressService.destroy();
        CartService.destroyAll();
    });
    
    $rootScope.$on('responseError', function (event, response) {
        if (response.status == 401) {
            if (!UserService.isGuest) {
                UserService.logout();
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('login');
                
                $ionicPopup.alert({
                    title: '登录过期',
                    template: '登录过期，请您重新登录！'
                });
            }
        } else {
            $ionicPopup.alert({
                title: '请求出错',
                template: response.data.message || response.message
            });
        }
    });
}])

.controller('HomeCtrl', ['$scope', '$state', 'SchoolService', function($scope, $state, SchoolService) {
    if (SchoolService.lastSchoolId !== null) {
        $state.go('tab.school', {'id': SchoolService.lastSchoolId});
    }
    $scope.SchoolService = SchoolService;
}])

.controller('SchoolCtrl', ['$scope', '$stateParams', '$http', 'SchoolService', function($scope, $stateParams, $http, SchoolService) {
    $http.get('api/v1/school/detail', {
        params: {id: $stateParams.id}
    }).then(function(response) {
        $scope.title = response.data.name;
        $scope.stores = response.data.stores;
    });
    SchoolService.setLastSchoolId($stateParams.id);
}])

.controller('SchoolSwitchCtrl', ['$scope', 'SchoolService', function($scope, SchoolService) {
    $scope.SchoolService = SchoolService;
}])

.controller('StoreCtrl', ['$scope', '$state', '$stateParams', '$http', '$ionicModal', '$ionicPopup', '$ionicViewSwitcher', '$ionicScrollDelegate', 'UserService', 'CartService', function($scope, $state, $stateParams, $http, $ionicModal, $ionicPopup, $ionicViewSwitcher, $ionicScrollDelegate, UserService, CartService) {
    $scope.UserService = UserService;
    $scope.cart = CartService.get($stateParams.id);

    $http.get('api/v1/store/detail', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.title = response.data.store.name;
        $scope.store = response.data.store;
        $scope.categories = response.data.categories;
        $scope.goodsList = response.data.goodsList;
        
        $scope.category = 'c' + Object.keys($scope.categories)[0];

        // Goods scroll
        var cateOffset = [];
        ionic.DomUtil.ready(function() {
            var elements = document.getElementById('content-' + $stateParams.id).getElementsByClassName('cateblock');
            for (var i = elements.length - 1; i >= 0; i--) {
                cateOffset[elements[i].id] = elements[i].offsetTop;
            }
        });
        $scope.goodsScrollComplete = function() {
            var scrollTop = $ionicScrollDelegate.$getByHandle('goodsScroll').getScrollPosition().top;

            for (var id in cateOffset) {
                if (scrollTop >= cateOffset[id]) {
                    $scope.$apply(function () {
                        $scope.category = id;
                    });
                    break;
                }
            }
        };
        $scope.scrollTo = function(target) {
            var offsetTop = cateOffset[target];
            $ionicScrollDelegate.scrollTo(0, offsetTop, true);
        }
    });
    
    // Goods modal
    $ionicModal.fromTemplateUrl('modal-goods.html', {
        scope: $scope,
        animation: 'slide-in-up'
    }).then(function(modal) {
        $scope.modalGoods = modal;
    });

    // Cart modal
    $ionicModal.fromTemplateUrl('modal-cart.html', {
        scope: $scope,
        animation: 'slide-in-up'
    }).then(function(modal) {
        $scope.modalCart = modal;
    });
    
    // Open goods detail
    $scope.openGoods = function (goods) {
        if (!goods.images) {
            goods.images = [];
            $http.get('api/v1/goods/images', {
                params: {id: goods.id}
            }).then(function (response) {
                goods.images = response.data
            });
        }
        $scope.modalGoods.goods = goods;
        $scope.modalGoods.show();
    };
    
    $scope.$on('$ionicView.enter', function () {
        if (!UserService.isGuest) {
            CartService.refresh($stateParams.id);
        }
    });
    
    $scope.getCartGoodsCount = function (id) {
        if (!UserService.isGuest) {
            var goods = $scope.cart.goodsList[id];
            if (goods) {
                return goods.count;
            }
        }
        return 0;
    };
    
    var confirmLogin = function () {
        $ionicPopup.confirm({
            title: '请登录后操作，您现在要登录吗？',
            cancelText: '取消',
            okText: '确定',
        }).then(function(res) {
            if(res) {
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('login');
            }
        });
    };
    
    $scope.add = function (id) {
        if (UserService.isGuest) {
            confirmLogin();
            return;
        }
        
        CartService.add($stateParams.id, id).catch(function (response) {
            if (response.data.status === 'fail' && response.data.data.message) {
                $ionicPopup.alert({
                    title: '提示',
                    template: response.data.data.message
                });
            }
        });
    };
    
    $scope.subtract = function (id) {
        if (UserService.isGuest) {
            confirmLogin();
            return;
        }
        
        CartService.subtract($stateParams.id, id).catch(function (response) {
            if (response.data.status === 'fail' && response.data.data.message) {
                $ionicPopup.alert({
                    title: '提示',
                    template: response.data.data.message
                });
            }
        });
    };
    
    $scope.clear = function () {
        CartService.clear($stateParams.id);
    };
    
    $scope.delete = function (id) {
        CartService.delete($stateParams.id, id);
    };
}])

.controller('OrderCtrl', ['$scope', '$http', '$ionicPopup', '$ionicLoading', 'OrderService', 'UserService', function($scope, $http, $ionicPopup, $ionicLoading, OrderService, UserService) {
    $scope.OrderService = OrderService;
    $scope.UserService = UserService;
    
    $scope.refresh = function () {
        OrderService.reload().finally(function () {
            $scope.$broadcast('scroll.refreshComplete');
        });
    };
    
    $scope.moreDataCanBeLoaded = function () {
        return !OrderService.isEnd;
    };
    
    $scope.loadMore = function () {
        OrderService.loadNext().finally(function () {
            $scope.$broadcast('scroll.infiniteScrollComplete');
        });
    };
    
    $scope.removeable = function (order) {
        return order.status === 'completed' || order.status === 'cancelled';
    };
    
    $scope.remove = function (order) {
        $ionicLoading.show();
        $http.delete('api/v1/order/delete', {
            params: {id: order.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                OrderService.reload();
            } else if (response.data.status === 'fail') {
                $ionicPopup.alert({
                    title: '删除失败',
                    template: '请刷新后重试！'
                });
            }
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderDetailCtrl', ['$scope', '$stateParams', '$http', '$ionicPopup', '$ionicLoading', 'OrderService', function($scope, $stateParams, $http, $ionicPopup, $ionicLoading, OrderService) {
    $http.get('api/v1/order/detail', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.order = response.data;
    });
    
    $scope.refresh = function () {
        $http.get('api/v1/order/detail', {
            params: {id: $stateParams.id}
        }).then(function (response) {
            $scope.order = response.data;
        }).finally(function () {
            $scope.$broadcast('scroll.refreshComplete');
        });
    };
    $scope.receive = function () {
        $ionicPopup.confirm({
            title: '确认收货',
            template: '如果您已收到订单，请确认收货。',
            cancelText: '取消',
            okText: '确认',
        }).then(function(res) {
            if(res) {
                $ionicLoading.show();
                OrderService.receive($stateParams.id).then(function (response) {
                    $scope.order.status = response.data.data.status;
                    $scope.order.statusMsg = response.data.data.statusMsg;
                }).finally(function () {
                    $ionicLoading.hide();
                });
            }
        });
    };
    
    $scope.cancel = function () {
        $ionicPopup.confirm({
            title: '取消订单',
            template: '您确定要取消该订单吗？',
            cancelText: '关闭',
            okText: '取消',
            okType: 'button-assertive'
        }).then(function(res) {
            if(res) {
                $ionicLoading.show();
                OrderService.cancel($stateParams.id).then(function (response) {
                    $scope.order.status = response.data.data.status;
                    $scope.order.statusMsg = response.data.data.statusMsg;
                }).finally(function () {
                    $ionicLoading.hide();
                });
            }
        });
    };
    
    $scope.orderTimeout = function () {
        $ionicLoading.show();
        OrderService.timeout($stateParams.id).then(function (response) {
            $scope.order.status = response.data.data.status;
            $scope.order.statusMsg = response.data.data.statusMsg;
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderCreateCtrl', ['$scope', '$state', '$stateParams', '$http', '$ionicViewSwitcher', '$ionicLoading', 'Params', 'Helper', 'AccountService', 'OrderService', 'AddressService', 'MemoryStorage', function($scope, $state, $stateParams, $http, $ionicViewSwitcher, $ionicLoading, Params, Helper, AccountService, OrderService, AddressService, MemoryStorage) {
    $scope.AccountService = AccountService;
    $scope.AddressService = AddressService;
    $scope.newDownItems = {
        1: Params.newDownMsg,
        0: '不使用优惠'
    };
    
    if (!MemoryStorage.has('createOrderFormData')) {
        MemoryStorage.set('createOrderFormData', {});
    }
    $scope.data = MemoryStorage.get('createOrderFormData');
    $scope.data.payment = 'online';
    $scope.data.remark = '';
    
    $http.get('api/v1/order/create', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.realFee = $scope.fee = response.data.fee;
        $scope.store = response.data.store;
        $scope.cartGoodsList = response.data.cartGoodsList;
        $scope.preferentialItems = response.data.preferentialItems;
        $scope.bookTimeItems = response.data.bookTimeItems;
        
        if (response.data.addressList.length > 0) {
            $scope.data.addressId = response.data.addressList[0].id;
        }
        
        if (!Helper.isEmptyObject($scope.preferentialItems)) {
            $scope.data.preferential = Object.keys($scope.preferentialItems)[0];
        }
        if (!Helper.isEmptyObject($scope.bookTimeItems)) {
            $scope.data.bookTime = Object.keys($scope.bookTimeItems)[0];
        }
        
        ionic.DomUtil.ready(function() {
            $scope.updateRealFee();
        });
    });
    
    $scope.updateRealFee = function () {
        var fee = Helper.number($scope.fee);
        var realFee = fee;
        switch ($scope.data.preferential) {
            case 'down' :
                if ($scope.store.has_down && fee >= $scope.store.down_upper) {
                    realFee = realFee - $scope.store.down_val;
                }
                break;
            default:
                break;
        }
        
        if (Params.enableNewDown && $scope.data.newDown === '1' && fee >= Params.newDownUpper && AccountService.hasNewDown) {
            realFee = realFee - Params.newDownVal;

            if (realFee < 0) {
                realFee = 0;
            }
        }
        
        $scope.realFee = realFee.toFixed(2);
    };
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $scope.errors = [];
        $scope.successMsg = null;
        $http.post('api/v1/order/create', data, {
            params: {id: $stateParams.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                $scope.successMsg = '订单创建成功！';
                MemoryStorage.remove('createOrderFormData');
                OrderService.reload();
                if (data.newDown === '1') {
                    AccountService.hasNewDown = false;
                }
                $ionicViewSwitcher.nextDirection('forward');
                if (response.data.data.payment === 'online') {
                    $state.go('order-pay', {id: response.data.data.id});
                } else {
                    $state.go('order-detail', {id: response.data.data.id});
                }
            } else {
                $scope.errors = response.data.data.errors
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderCreateAddressCtrl', ['$scope', '$state', '$stateParams', '$http', '$ionicViewSwitcher', '$ionicPopup', '$ionicLoading', 'Helper', 'AddressService', 'MemoryStorage', function($scope, $state, $stateParams, $http, $ionicViewSwitcher, $ionicPopup, $ionicLoading, Helper, AddressService, MemoryStorage) {
    $scope.schoolId = $stateParams.id;
    $scope.AddressService = AddressService;
    
    if (!MemoryStorage.has('createOrderFormData')) {
        MemoryStorage.set('createOrderFormData', {});
    }
    $scope.data = MemoryStorage.get('createOrderFormData');
    
    $scope.refresh = function () {
        AddressService.reload().finally(function () {
            $scope.$broadcast('scroll.refreshComplete');
        });
    };
    
    $scope.select = function (address) {
        $scope.data.addressId = address.id;
        $ionicViewSwitcher.nextDirection('back');
        Helper.back();
    };
    
    $scope.edit = function (address) {
        $ionicViewSwitcher.nextDirection('forward');
        $state.go('order-create-address-edit', {id: address.id});
    };
    
    $scope.remove = function (address) {
        $ionicLoading.show();
        $http.delete('api/v1/address/delete', {
            params: {id: address.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.all.splice(AddressService.all.indexOf(address), 1);
            } else if (response.data.status === 'fail') {
                $ionicPopup.alert({
                    title: '删除失败',
                    template: response.data.data.message
                });
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderCreateAddressAddCtrl', ['$scope', '$stateParams', '$http', '$ionicViewSwitcher', '$ionicLoading', 'Helper', 'SchoolService', 'AddressService', function($scope, $stateParams, $http, $ionicViewSwitcher, $ionicLoading, Helper, SchoolService, AddressService) {
    $scope.title = '新增收货地址';
    $scope.data = {
        school_id: $stateParams.schoolId
    };
    $scope.SchoolService = SchoolService;
    $scope.AddressService = AddressService;
    
    $http.get('api/v1/school/buildings', {
        params: {id: $stateParams.schoolId}
    }).then(function (response) {
        $scope.buildings = response.data;
        $scope.data.building_id = Object.keys($scope.buildings)[0] || '';
    });
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/address/create', data).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.reload();
                Helper.back();
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderCreateAddressEditCtrl', ['$scope', '$stateParams', '$http', '$ionicLoading', 'Helper', 'SchoolService', 'AddressService', function($scope, $stateParams, $http, $ionicLoading, Helper, SchoolService, AddressService) {
    $scope.title = '编辑收货地址';
    $scope.SchoolService = SchoolService;
    $scope.AddressService = AddressService;
    
    $http.get('api/v1/address/detail', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.data = response.data;
        
        $http.get('api/v1/school/buildings', {
            params: {id: $scope.data.school_id}
        }).then(function (response) {
            $scope.buildings = response.data;
        });
    });
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/address/update', data, {
            params: {id: $stateParams.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.reload();
                Helper.back();
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('OrderCreateRemarkCtrl', ['$scope', '$ionicViewSwitcher', 'Helper', 'MemoryStorage', function($scope, $ionicViewSwitcher, Helper, MemoryStorage) {
    $scope.data = {};
    if (MemoryStorage.has('createOrderFormData') && MemoryStorage.get('createOrderFormData').remark) {
        $scope.data.remark = MemoryStorage.get('createOrderFormData').remark;
    }
    
    $scope.save = function (data) {
        if (!MemoryStorage.has('createOrderFormData')) {
            MemoryStorage.set('createOrderFormData', {});
        }
        MemoryStorage.get('createOrderFormData').remark = data.remark;
        $ionicViewSwitcher.nextDirection('back');
        Helper.back();
    };
}])

.controller('OrderPayCtrl', ['$scope', '$state', '$stateParams', '$http', '$ionicViewSwitcher', '$ionicPopup', '$ionicLoading', 'OrderService', function($scope, $state, $stateParams, $http, $ionicViewSwitcher, $ionicPopup, $ionicLoading, OrderService) {
    $scope.canUseWxpay = window.WeixinJSBridge != undefined;
    
    $http.get('api/v1/order/detail', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.order = response.data;
    });
    
    $scope.orderTimeout = function () {
        $ionicLoading.show();
        OrderService.timeout($stateParams.id).then(function (response) {
            $scope.order.status = response.data.data.status;
            $scope.order.statusMsg = response.data.data.statusMsg;
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
    
    $scope.pay = function (channel) {
        $ionicLoading.show();
        $http.post('api/v1/order/pay', {
            channel: channel
        }, {
            params: {id: $stateParams.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                pingpp.createPayment(response.data.data.charge, function(result, err) {
                    if (result == 'success') {
                        $ionicPopup.alert({
                            title: '恭喜支付成功！'
                        });
                        $ionicViewSwitcher.nextDirection('back');
                        $state.go('order-detail', {id: $stateParams.id});
                    } else if (result == 'fail') {
                        $ionicPopup.alert({
                            title: '支付失败！',
                            template: err.msg
                        });
                    } else if (result == 'cancel') {
                    }
                });
            } else {
                $ionicPopup.alert({
                    title: '请求失败！',
                    template: response.data.data.message
                });
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('ICtrl', ['$scope', 'UserService', 'AccountService', function($scope, UserService, AccountService) {
    $scope.UserService = UserService;
    $scope.AccountService = AccountService;
}])

.controller('IAccountCtrl', ['$scope', '$state', '$ionicPopup', '$ionicViewSwitcher', 'UserService', 'AccountService', function($scope, $state, $ionicPopup, $ionicViewSwitcher, UserService, AccountService) {
    $scope.UserService = UserService;
    $scope.AccountService = AccountService;
    
    $scope.logout = function () {
        $ionicPopup.confirm({
            title: '退出当前帐号？',
            cancelText: '取消',
            okText: '确定',
        }).then(function(res) {
            if(res) {
                UserService.logout();
                $scope.$emit('userLogoutSuccess');
                $ionicViewSwitcher.nextDirection('back');
                $state.go('tab.i');
            }
        });
    };
}])

.controller('IAccountNicknameCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, AccountService) {
    $scope.data = {
        nickname: AccountService.nickname
    };
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/i/nickname', data).then(function (response) {
            if (response.data.status === 'success') {
                AccountService.nickname = data.nickname;
                $ionicViewSwitcher.nextDirection('back');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('IAccountGenderCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, AccountService) {
    $scope.AccountService = AccountService;
    $scope.data = {
        gender: AccountService.gender
    };
    $scope.submit = function (data) {
        $http.put('api/v1/i/gender', data).then(function (response) {
            if (response.data.status === 'success') {
                AccountService.gender = data.gender;
                $ionicViewSwitcher.nextDirection('back');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        });
    }
}])

.controller('IAccountMobileVerifyCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, AccountService) {
    $scope.AccountService = AccountService;
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/i/verify-password', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.setItem('isVerifed', 'Y');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('i-account-mobile');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('IAccountMobileCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, AccountService) {
    if (window.sessionStorage.getItem('isVerifed') !== 'Y') {
        $ionicViewSwitcher.nextDirection('back');
        $state.go('i-account-mobile');
    }
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/i/mobile', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.setItem('isMsgSent', 'Y');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('i-account-mobile-step2');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('IAccountMobileStep2Ctrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, AccountService) {
    if (window.sessionStorage.getItem('isMsgSent') !== 'Y') {
        $ionicViewSwitcher.nextDirection('back');
        $state.go('i-account-mobile');
    }
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/i/mobile', data, {
            params: {step: 2}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AccountService.mobile = response.data.data.mobile;
                $ionicViewSwitcher.nextDirection('back');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('IAccountEmailCtrl', ['$scope', '$state', '$http', '$ionicPopup', '$ionicViewSwitcher', '$ionicLoading', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicPopup, $ionicViewSwitcher, $ionicLoading, $ionicLoading, AccountService) {
    $scope.AccountService = AccountService;
    $scope.remove = function () {
        $ionicPopup.confirm({
            title: '解除当前绑定？',
            cancelText: '取消',
            okText: '确定',
        }).then(function(res) {
            if(res) {
                $ionicLoading.show();
                $http.delete('api/v1/i/remove-email').then(function (response) {
                    if (response.data.status === 'success') {
                        AccountService.email = null;
                        $ionicViewSwitcher.nextDirection('back');
                        $state.go('i-account');
                    }
                }).finally(function () {
                    $ionicLoading.hide();
                });
            }
        });
    };
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/i/email', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.setItem('isEmailSent', 'Y');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('i-account-email-step2');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('IAccountEmailStep2Ctrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'AccountService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, AccountService) {
    if (window.sessionStorage.getItem('isEmailSent') !== 'Y') {
        $ionicViewSwitcher.nextDirection('back');
        $state.go('i-account-email');
    }
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/i/email', data, {
            params: {step: 2}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AccountService.email = response.data.data.email;
                $ionicViewSwitcher.nextDirection('back');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('IAccountPasswordCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading) {
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/i/verify-password', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.setItem('isVerifed', 'Y');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('i-account-password-step2');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('IAccountPasswordStep2Ctrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading) {
    if (window.sessionStorage.getItem('isVerifed') !== 'Y') {
        $ionicViewSwitcher.nextDirection('back');
        $state.go('i-account-password');
    }
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/i/password', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.removeItem('isVerifed');
                $ionicViewSwitcher.nextDirection('back');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('IAddressCtrl', ['$scope', '$http', '$ionicPopup', '$ionicLoading', 'AddressService', function($scope, $http, $ionicPopup, $ionicLoading, AddressService) {
    $scope.AddressService = AddressService;
    
    $scope.refresh = function () {
        AddressService.reload().then(function (response) {
            $scope.$broadcast('scroll.refreshComplete');
        });
    };
    
    $scope.remove = function (address) {
        $ionicLoading.show();
        $http.delete('api/v1/address/delete', {
            params: {id: address.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.all.splice(AddressService.all.indexOf(address), 1);
            } else if (response.data.status === 'fail') {
                $ionicPopup.alert({
                    title: '删除失败',
                    template: response.data.data.message
                });
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('IAddressCreateCtrl', ['$scope', '$http', '$ionicLoading', 'Helper', 'SchoolService', 'AddressService', function($scope, $http, $ionicLoading, Helper, SchoolService, AddressService) {
    $scope.title = '新增收货地址';
    $scope.SchoolService = SchoolService;
    $scope.AddressService = AddressService;
    
    $scope.data = {
        consignee: '',
        gender: Object.keys(AddressService.genderList)[0],
        cellphone: '',
        school_id: '',
        building_id: '',
        room: ''
    };

    var buildingStock = [];
    $scope.changeSchool = function (schoolId) {
        if (buildingStock[schoolId]) {
            $scope.buildings = buildingStock[schoolId];
            $scope.data.building_id = Object.keys($scope.buildings)[0] || '';
            return;
        }
        $http.get('api/v1/school/buildings', {
            params: {id: schoolId}
        }).then(function (response) {
            buildingStock[schoolId] = response.data;
            $scope.buildings = buildingStock[schoolId];
            $scope.data.building_id = Object.keys($scope.buildings)[0] || '';
        });
    };
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/address/create', data).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.reload();
                Helper.back();
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('IAddressUpdateCtrl', ['$scope', '$stateParams', '$http', '$ionicLoading', 'Helper', 'SchoolService', 'AddressService', function($scope, $stateParams, $http, $ionicLoading, Helper, SchoolService, AddressService) {
    $scope.title = '更新收货地址';
    $scope.SchoolService = SchoolService;
    $scope.AddressService = AddressService;
    
    var buildingStock = [];
    $scope.changeSchool = function (schoolId) {
        if (buildingStock[schoolId]) {
            $scope.buildings = buildingStock[schoolId];
            $scope.data.building_id = Object.keys($scope.buildings)[0] || '';
            return;
        }
        $http.get('api/v1/school/buildings', {
            params: {id: schoolId}
        }).then(function (response) {
            buildingStock[schoolId] = response.data;
            $scope.buildings = buildingStock[schoolId];
            $scope.data.building_id = Object.keys($scope.buildings)[0] || '';
        });
    };
    
    $http.get('api/v1/address/detail', {
        params: {id: $stateParams.id}
    }).then(function (response) {
        $scope.data = response.data;
        $http.get('api/v1/school/buildings', {
            params: {id: $scope.data.school_id}
        }).then(function (response) {
            buildingStock[$scope.data.school_id] = response.data;
            $scope.buildings = buildingStock[$scope.data.school_id];
        });
    });
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.put('api/v1/address/update', data, {
            params: {id: $stateParams.id}
        }).then(function (response) {
            if (response.data.status === 'success') {
                AddressService.reload();
                Helper.back();
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('HelpCtrl', ['$scope', function($scope) {
}])

.controller('HelpItemCtrl', ['$scope', '$stateParams', function($scope, $stateParams) {
    $scope.id = $stateParams.id;
}])

.controller('MoreCtrl', ['$scope', function($scope) {
}])

.controller('JoinusCtrl', ['$scope', function($scope) {
}])

.controller('FeedbackCtrl', ['$scope', '$state', '$http', '$ionicPopup', '$ionicLoading', '$ionicViewSwitcher', function($scope, $state, $http, $ionicPopup, $ionicLoading, $ionicViewSwitcher) {
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/default/feedback', data).then(function (response) {
            if (response.data.status === 'success') {
                $ionicPopup.alert({
                    title: '感谢您的反馈',
                    template: '感谢您的反馈，我们将努力为您做更好的产品。'
                });
                $ionicViewSwitcher.nextDirection('back');
                $state.go('more');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    }
}])

.controller('SignupCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading) {
    $scope.updateCaptcha = function () {
        $http.get('api/v1/default/captcha', {
            params: {refresh: 1}
        }).then(function (response) {
            $scope.captchaUrl = response.data.url;
        });
    };
    $scope.updateCaptcha();
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/default/signup', data).then(function (response) {
            if (response.data.status === 'success') {
                window.sessionStorage.setItem('signupMsgSent', 'Y');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('signup-step2');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('SignupStep2Ctrl', ['$scope', '$state', '$http', '$ionicPopup', '$ionicViewSwitcher', '$ionicLoading', 'UserService', function($scope, $state, $http, $ionicPopup, $ionicViewSwitcher, $ionicLoading, UserService) {
    if (window.sessionStorage.getItem('signupMsgSent') !== 'Y') {
        $ionicViewSwitcher.nextDirection('back');
        $state.go('signup');
    }
    
    $scope.sendMsg = function () {
        $ionicPopup.confirm({
            title: '重新发送验证码？',
            cancelText: '取消',
            okText: '确定',
        }).then(function(res) {
            if(res) {
                $http.post('api/v1/default/send-msg').then(function (response) {
                    if (response.data.status === 'success') {
                        window.sessionStorage.removeItem('signupMsgSent');
                    } else if (response.data.status === 'fail') {
                        $scope.errors = response.data.data.errors;
                    }
                });
            }
        });
    };
    
    $scope.submit = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/default/signup', data, {
            params: {step: 2}
        }).then(function (response) {
            if (response.data.status === 'success') {
                UserService.login(response.data.data.accessToken);
                $scope.$emit('userLoginSuccess');
                $ionicViewSwitcher.nextDirection('forward');
                $state.go('i-account');
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}])

.controller('LoginCtrl', ['$scope', '$state', '$http', '$ionicViewSwitcher', '$ionicLoading', 'UserService', function($scope, $state, $http, $ionicViewSwitcher, $ionicLoading, UserService) {
    $scope.login = function (data) {
        $ionicLoading.show();
        $http.post('api/v1/default/login', data).then(function (response) {
            if (response.data.status === 'success') {
                UserService.login(response.data.data.token);
                $scope.$emit('userLoginSuccess');
                if ($scope.previousState) {
                    $ionicViewSwitcher.nextDirection('back');
                    $state.go($scope.previousState.name, $scope.previousParams, {reload: true});
                } else {
                    $ionicViewSwitcher.nextDirection('forward');
                    $state.go('i-account');
                }
            } else if (response.data.status === 'fail') {
                $scope.errors = response.data.data.errors;
            }
        }).finally(function () {
            $ionicLoading.hide();
        });
    };
}]);