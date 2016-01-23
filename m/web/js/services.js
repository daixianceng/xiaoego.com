angular.module('starter.services', [])

.directive('mobile', function() {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            ctrl.$validators.mobile = function(modelValue, viewValue) {
                if (/^1[3|4|5|7|8][0-9]{9}$/.test(viewValue)) {
                    return true;
                }
                return false;
            };
        }
    };
})

.directive('password', function() {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            ctrl.$validators.password = function(modelValue, viewValue) {
                if (/^\S{6,32}$/.test(viewValue)) {
                    return true;
                }
                return false;
            };
        }
    };
})

// <countdown time="1445341653552" format="ss" on-finish="handle()"></countdown>
.directive('countdown', ['$interval', function ($interval) {
    var formatCountdown = function (t, format) {
        var days, hours, minutes, seconds, result;
        
        days = Math.floor(t / 86400);
        t -= days * 86400;
        hours = Math.floor(t / 3600) % 24;
        t -= hours * 3600;
        minutes = Math.floor(t / 60) % 60;
        t -= minutes * 60;
        seconds = t % 60;
        
        result = format.replace('dd', ('0' + days).slice(-2));
        result = result.replace('d', days);
        result = result.replace('hh', ('0' + hours).slice(-2));
        result = result.replace('h', hours);
        result = result.replace('mm', ('0' + minutes).slice(-2));
        result = result.replace('m', minutes);
        result = result.replace('ss', ('0' + seconds).slice(-2));
        result = result.replace('s', seconds);
        
        return result;
    };
    return {
        restrict: 'E',
        link: function (scope, element, attrs) {
            var future = new Date(Number(attrs.time));
            var update = function () {
                var diff = Math.floor((future.getTime() - (new Date()).getTime()) / 1000);
                if (diff < 0) {
                    $interval.cancel(stop);
                    if (attrs.onFinish) {
                        scope.$eval(attrs.onFinish);
                    }
                } else {
                    element.text(formatCountdown(diff, attrs.format));
                }
            };
            var stop = $interval(update, 1000);
            update();
            element.on('$destroy', function() {
                $interval.cancel(stop);
            });
        }
    };
}])

.constant('$ionicLoadingConfig', {
    template: 'Loading...'
})

.constant('Helper', {
    back: function () {
        window.history.back();
    },
    isEmptyObject: function (obj) {
        for (var name in obj) {
            return false;
        }
        return true;
    },
    number: function (val) {
        return Number(val);
    },
    generateRandomString: function (length) {
        return Math.round((Math.pow(36, length + 1) - Math.random() * Math.pow(36, length))).toString(36).slice(1);
    }
})

.constant('MemoryStorage', {
    _data: {},
    get: function (key) {
        return this._data[key];
    },
    set: function (key, value) {
        this._data[key] = value;
    },
    has: function (key) {
        return this._data[key] != undefined;
    },
    remove: function (key) {
        delete this._data[key];
    },
    getAll: function () {
        return this._data;
    },
    destroy: function () {
        this._data = {};
    }
})

.factory('httpInterceptor', ['$rootScope', '$q', function($rootScope, $q) {
    return {
        'request': function(config) {
            config.withCredentials = true;
            return config;
        },
        'requestError': function(rejection) {
            return $q.reject(rejection);
        },
        'response': function(response) {
            return response;
        },
        'responseError': function(rejection) {
            $rootScope.$emit('responseError', rejection);
            return $q.reject(rejection);
        }
    };
}])

.factory('UserService', ['$http', function($http) {
    var o = {
        isGuest: true,
        login: function (accessToken) {
            this.isGuest = false;
            this.setCredentials(accessToken);
        },
        logout: function () {
            this.isGuest = true;
            this.destroyCredentials();
        },
        setCredentials: function (accessToken) {
            $http.defaults.headers.common['X-Auth-Token'] = accessToken;
            window.localStorage.setItem('accessToken', accessToken);
        },
        destroyCredentials: function () {
            delete $http.defaults.headers.common['X-Auth-Token'];
            window.localStorage.removeItem('accessToken');
        },
        init: function () {
            var accessToken = window.localStorage.getItem('accessToken');
            if (accessToken) {
                this.isGuest = false;
                this.setCredentials(accessToken);
            }
        }
    };
    
    o.init();
    
    return o;
}])

.factory('AccountService', ['$http', '$q', 'UserService', function($http, $q, UserService) {
    var o = {
        mobile: null,
        nickname: null,
        gender: null,
        email: null,
        hasNewDown: false,
        genderList: {
            male: '男',
            woman: '女',
            other: '保密'
        },
        getSymbolMobile: function () {
            if (this.mobile === null) {
                return null;
            } else {
                var mobile = this.mobile + '';
                return mobile.slice(0, 3) + '****' + mobile.slice(7, 11);
            }
        },
        getGenderMsg: function () {
            if (this.gender === null) {
                return null;
            } else {
                return this.genderList[this.gender];
            }
        },
        reload: function () {
            return $q(function (resolve, reject) {
                $http.get('api/v1/i/profile').then(function (response) {
                    if (response.data.status === 'success') {
                        o.mobile = response.data.data.mobile;
                        o.nickname = response.data.data.nickname;
                        o.gender = response.data.data.gender;
                        o.email = response.data.data.email;
                        o.hasNewDown = response.data.data.hasNewDown;
                        
                        resolve(response);
                    } else {
                        UserService.logout();
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        destroy: function () {
            this.mobile = null;
            this.nickname = null;
            this.gender = null;
            this.email = null;
        },
        init: function () {
            if (!UserService.isGuest) {
                this.reload();
            }
        }
    };
    
    o.init();
    
    return o;
}])

.factory('CartService', ['$http', '$q', function($http, $q) {
    var o = {
        all: [],
        add: function (storeId, goodsId) {
            return $q(function (resolve, reject) {
                $http.post('api/v1/cart/add', {
                    goodsId: goodsId
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        o.refresh(storeId);
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        subtract: function (storeId, goodsId) {
            return $q(function (resolve, reject) {
                $http.post('api/v1/cart/subtract', {
                    goodsId: goodsId
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        o.refresh(storeId);
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        clear: function (storeId) {
            return $q(function (resolve, reject) {
                $http.put('api/v1/cart/clear', {
                    storeId: storeId
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        var cart = o.get(storeId);
                        cart.goodsList = [];
                        cart.length = 0;
                        cart.volume = '0.00';
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        delete: function (storeId, goodsId) {
            return $q(function (resolve, reject) {
                $http.delete('api/v1/cart/delete', {
                    params: {id: goodsId}
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        o.refresh(storeId);
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        refresh: function (storeId) {
            return $q(function (resolve, reject) {
                $http.get('api/v1/cart/index', {
                    params: {id: storeId}
                }).then(function (response) {
                    var cart = o.get(storeId);
                    cart.goodsList = response.data.goodsList;
                    cart.length = response.data.length;
                    cart.volume = response.data.volume;
                    resolve(response);
                }, function (response) {
                    reject(response);
                });
            });
        },
        get: function (storeId) {
            if (!this.all[storeId]) {
                this.all[storeId] = {
                    goodsList: [],
                    length: 0,
                    volume: '0.00'
                };
            }
            
            return this.all[storeId];
        },
        destroyAll: function () {
            this.all.length = 0;
        }
    };
    
    return o;
}])

.factory('SchoolService', ['$http', function($http) {
    var o = {
        all: [],
        lastSchoolId: null,
        setLastSchoolId: function (id) {
            this.lastSchoolId = id;
            window.localStorage.setItem('lastSchoolId', id);
        },
        getSchoolName: function (id) {
            return this.all[id];
        },
        init: function () {
            $http.get('api/v1/school/all').then(function (response) {
                o.all = response.data;
            });
            
            var lastSchoolId = window.localStorage.getItem('lastSchoolId');
            if (lastSchoolId) {
                this.lastSchoolId = lastSchoolId;
            }
        }
    };
    
    o.init();
    
    return o;
}])

.factory('OrderService', ['$http', '$q', 'UserService', function($http, $q, UserService) {
    var o = {
        _links: [],
        _meta: [],
        items: [],
        isEnd: true,
        statusList: {
            unshipped: '未发货',
            shipped: '配送中',
            unpaid: '待付款',
            completed: '订单完成',
            cancelled: '订单取消'
        },
        getStatusMsg: function (status) {
            return this.statusList[status];
        },
        getOne: function (id) {
            for (var i in this.items) {
                if (this.items[i].id == id) {
                    return this.items[i];
                }
            }
            
            return null;
        },
        receive: function (id) {
            return $q(function (resolve, reject) {
                $http.put('api/v1/order/receive', {}, {
                    params: {id: id}
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        var order = o.getOne(id);
                        if (order) {
                            order.status = response.data.data.status;
                        }
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        cancel: function (id) {
            return $q(function (resolve, reject) {
                $http.put('api/v1/order/cancel', {}, {
                    params: {id: id}
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        var order = o.getOne(id);
                        if (order) {
                            order.status = response.data.data.status;
                        }
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        timeout: function (id) {
            return $q(function (resolve, reject) {
                $http.put('api/v1/order/timeout', {}, {
                    params: {id: id}
                }).then(function (response) {
                    if (response.data.status === 'success') {
                        var order = o.getOne(id);
                        if (order) {
                            order.status = response.data.data.status;
                        }
                        resolve(response);
                    } else {
                        reject(response);
                    }
                }, function (response) {
                    reject(response);
                });
            });
        },
        reload: function () {
            return $q(function (resolve, reject) {
                $http.get('api/v1/order/index').then(function (response) {
                    o._links = response.data._links;
                    o._meta = response.data._meta;
                    o.items = response.data.items;
                    o.isEnd = o._meta.currentPage >= o._meta.pageCount;
                    resolve(response);
                }, function (response) {
                    reject(response);
                });
            });
        },
        loadNext: function () {
            return $q(function (resolve, reject) {
                if (o.isEnd) {
                    reject('pageEnd');
                } else {
                    $http.get(o._links.next.href).then(function (response) {
                        o._links = response.data._links;
                        o._meta = response.data._meta;
                        o.items = o.items.concat(response.data.items);
                        o.isEnd = o._meta.currentPage == o._meta.pageCount;
                        resolve(response);
                    }, function (response) {
                        reject(response);
                    });
                }
            });
        },
        destroy: function () {
            this._links.length = 0;
            this._meta.length = 0;
            this.items.length = 0;
        },
        init: function () {
            if (!UserService.isGuest) {
                this.reload();
            }
        }
    };
    
    o.init();
    
    return o;
}])

.factory('AddressService', ['$http', '$q', 'UserService', function($http, $q, UserService) {
    var o = {
        all: [],
        genderList: {
            male: '帅哥',
            woman: '美女',
        },
        getGenderMsg: function (gender) {
            return this.genderList[gender];
        },
        getBySchoolId: function (id) {
            var list = [];
            for (var i in this.all) {
                if (this.all[i].school_id == id) {
                    if (this.all[i].is_default === '1') {
                        list.unshift(this.all[i]);
                    } else {
                        list.push(this.all[i]);
                    }
                }
            }
            
            return list;
        },
        getOne: function (id) {
            for (var i in this.all) {
                if (this.all[i].id == id) {
                    return this.all[i];
                }
            }
            
            return null;
        },
        reload: function () {
            return $q(function (resolve, reject) {
                $http.get('api/v1/address/all').then(function (response) {
                    o.all = response.data;
                    resolve(response);
                }, function (response) {
                    reject(response);
                });
            });
        },
        destroy: function () {
            this.all.length = 0;
        },
        init: function () {
            if (!UserService.isGuest) {
                this.reload();
            }
        }
    };
    
    o.init();
    
    return o;
}])

;