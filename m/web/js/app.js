angular.module('starter', ['ionic', 'starter.controllers', 'starter.services', 'starter.config'])

.config(['$stateProvider', '$urlRouterProvider', '$httpProvider', '$ionicConfigProvider', function($stateProvider, $urlRouterProvider, $httpProvider, $ionicConfigProvider) {

    $stateProvider

    // setup an abstract state for the tabs directive
    .state('tab', {
        url : '/tab',
        abstract : true,
        templateUrl : 'templates/site/tabs.html'
    })

    .state('tab.home', {
        url : '/home',
        views : {
            'home' : {
                templateUrl : 'templates/site/home.html',
                controller : 'HomeCtrl'
            }
        }
    })
    
    .state('tab.school', {
        url : '/school/:id',
        views : {
            'home' : {
                templateUrl : 'templates/school/index.html',
                controller : 'SchoolCtrl'
            }
        }
    })
    
    .state('school-switch', {
        url : '/school/switch',
        templateUrl : 'templates/school/switch.html',
        controller : 'SchoolSwitchCtrl'
    })
    
    .state('store', {
        url : '/store/:id',
        templateUrl : 'templates/store/index.html',
        controller : 'StoreCtrl'
    })

    .state('tab.order', {
        url : '/order',
        views : {
            'order' : {
                templateUrl : 'templates/order/index.html',
                controller : 'OrderCtrl'
            }
        }
    })
    
    .state('order-detail', {
        cache: false,
        url : '/order/detail/:id',
        templateUrl : 'templates/order/detail.html',
        controller : 'OrderDetailCtrl',
        access: '@'
    })
    
    .state('order-create', {
        url : '/order/create/{id:[0-9]*}',
        templateUrl : 'templates/order/create.html',
        controller : 'OrderCreateCtrl',
        access: '@'
    })
    
    .state('order-create-address', {
        url : '/order/create/address/:id',
        templateUrl : 'templates/order/create-address.html',
        controller : 'OrderCreateAddressCtrl',
        access: '@'
    })
    
    .state('order-create-address-add', {
        url : '/order/create/address/add/:schoolId',
        templateUrl : 'templates/order/create-address-form.html',
        controller : 'OrderCreateAddressAddCtrl',
        access: '@'
    })
    
    .state('order-create-address-edit', {
        url : '/order/create/address/edit/:id',
        templateUrl : 'templates/order/create-address-form.html',
        controller : 'OrderCreateAddressEditCtrl',
        access: '@'
    })
    
    .state('order-create-remark', {
        url : '/order/create/remark',
        templateUrl : 'templates/order/create-remark.html',
        controller : 'OrderCreateRemarkCtrl',
        access: '@'
    })
    
    .state('order-pay', {
        cache: false,
        url : '/order/pay/:id',
        templateUrl : 'templates/order/pay.html',
        controller : 'OrderPayCtrl',
        access: '@'
    })

    .state('tab.i', {
        url : '/i',
        views : {
            'i' : {
                templateUrl : 'templates/i/index.html',
                controller : 'ICtrl'
            }
        }
    })

    .state('i-account', {
        url : '/i/account',
        templateUrl : 'templates/i/account.html',
        controller : 'IAccountCtrl',
        access: '@'
    })
    
    .state('i-account-nickname', {
        url : '/i/account/nickname',
        templateUrl : 'templates/i/account-nickname.html',
        controller : 'IAccountNicknameCtrl',
        access: '@'
    })
    
    .state('i-account-gender', {
        url : '/i/account/gender',
        templateUrl : 'templates/i/account-gender.html',
        controller : 'IAccountGenderCtrl',
        access: '@'
    })
    
    .state('i-account-mobile-verify', {
        url : '/i/account/mobile/verify',
        templateUrl : 'templates/i/account-mobile-verify.html',
        controller : 'IAccountMobileVerifyCtrl',
        access: '@'
    })
    
    .state('i-account-mobile', {
        url : '/i/account/mobile',
        templateUrl : 'templates/i/account-mobile.html',
        controller : 'IAccountMobileCtrl',
        access: '@'
    })
    
    .state('i-account-mobile-step2', {
        url : '/i/account/mobile/step2',
        templateUrl : 'templates/i/account-mobile-step2.html',
        controller : 'IAccountMobileStep2Ctrl',
        access: '@'
    })
    
    .state('i-account-email', {
        url : '/i/account/email',
        templateUrl : 'templates/i/account-email.html',
        controller : 'IAccountEmailCtrl',
        access: '@'
    })
    
    .state('i-account-email-step2', {
        url : '/i/account/email/step2',
        templateUrl : 'templates/i/account-email-step2.html',
        controller : 'IAccountEmailStep2Ctrl',
        access: '@'
    })
    
    .state('i-account-password', {
        url : '/i/account/password',
        templateUrl : 'templates/i/account-password.html',
        controller : 'IAccountPasswordCtrl',
        access: '@'
    })
    
    .state('i-account-password-step2', {
        url : '/i/account/password/step2',
        templateUrl : 'templates/i/account-password-step2.html',
        controller : 'IAccountPasswordStep2Ctrl',
        access: '@'
    })
    
    .state('i-address', {
        url : '/i/address',
        templateUrl : 'templates/i/address.html',
        controller : 'IAddressCtrl',
        access: '@'
    })
    
    .state('i-address-create', {
        url : '/i/address/create',
        templateUrl : 'templates/i/address-form.html',
        controller : 'IAddressCreateCtrl',
        access: '@'
    })
    
    .state('i-address-update', {
        url : '/i/address/:id',
        templateUrl : 'templates/i/address-form.html',
        controller : 'IAddressUpdateCtrl',
        access: '@'
    })
    
    .state('help', {
        url : '/help',
        templateUrl : 'templates/site/help.html',
        controller : 'HelpCtrl'
    })
    
    .state('help-item', {
        url : '/help/:id',
        templateUrl : 'templates/site/help-item.html',
        controller : 'HelpItemCtrl'
    })
    
    .state('more', {
        url : '/more',
        templateUrl : 'templates/site/more.html',
        controller : 'MoreCtrl'
    })
    
    .state('joinus', {
        url : '/more/joinus',
        templateUrl : 'templates/site/joinus.html',
        controller : 'JoinusCtrl'
    })
    
    .state('feedback', {
        url : '/more/feedback',
        templateUrl : 'templates/site/feedback.html',
        controller : 'FeedbackCtrl'
    })
    
    .state('signup', {
        url : '/signup',
        templateUrl : 'templates/site/signup.html',
        controller : 'SignupCtrl'
    })
    
    .state('signup-step2', {
        url : '/signup/step2',
        templateUrl : 'templates/site/signup-step2.html',
        controller : 'SignupStep2Ctrl'
    })
    
    .state('login', {
        url : '/login',
        templateUrl : 'templates/site/login.html',
        controller : 'LoginCtrl',
        access: '?'
    });

    // if none of the above states are matched, use this as the fallback
    $urlRouterProvider.otherwise('/tab/home');
    
    $httpProvider.interceptors.push('httpInterceptor');
    
    if (!$httpProvider.defaults.headers.get) {
        $httpProvider.defaults.headers.get = {};
    }
    $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
    $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
    
    $ionicConfigProvider.views.transition('ios');
    $ionicConfigProvider.views.maxCache(30);
    $ionicConfigProvider.views.forwardCache(false);
    $ionicConfigProvider.backButton.text('返回');
    $ionicConfigProvider.tabs.position('bottom');
    $ionicConfigProvider.tabs.style('standard');
    $ionicConfigProvider.templates.maxPrefetch(30);
    $ionicConfigProvider.navBar.alignTitle('center');
    $ionicConfigProvider.navBar.positionPrimaryButtons('left');
    $ionicConfigProvider.navBar.positionSecondaryButtons('right');
}]);
