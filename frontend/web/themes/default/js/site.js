Zhai = {
    baseUrl: '',
    cart : {
        length : 0,
        volume : 0,
        storeId : 0,
        isRest : 0,
        atLeast : 0,
        
        add : function (id, callback) {
            $.ajax({
                url : Zhai.baseUrl + '/cart/add',
                type : 'post',
                dataType : 'json',
                data : {goodsId : id},
                success : function (data) {
                    if (data.status === 'ok') {
                        if ((typeof callback) === 'function') {
                            callback(data);
                        }
                    } else if (data.msg) {
                        alert(data.msg);
                    }
                },
                error : function () {}
            });
        },
        
        subtract : function (id, callback) {
            $.ajax({
                url : Zhai.baseUrl + '/cart/subtract',
                type : 'post',
                dataType : 'json',
                data : {goodsId : id},
                success : function (data) {
                    if (data.status === 'ok') {
                        if ((typeof callback) === 'function') {
                            callback(data);
                        }
                    } else if (data.msg) {
                        alert(data.msg);
                    }
                },
                error : function () {}
            });
        },
        
        refresh : function () {
            $.ajax({
                url : Zhai.baseUrl + '/cart/refresh',
                type : 'post',
                dataType : 'json',
                data : {storeId : this.storeId},
                success : function (data) {
                    if (data.status === 'ok') {
                        $('.shopping-list').empty().html(data.html);
                        
                        $('.shopping-list li').each(function (i) {
                            var $item = $(this);
                            var goodsId = parseInt($item.attr('data-goodsId'));
                            var $sale = $('.sale-item[data-goodsid="' + goodsId + '"]');
                            var callback = function (data) {
                                $sale.find('.stock span').text(data.surplus);
                                $sale.find('.quantity-count').text(data.cart);
                                $sale.find('.quantity').attr('data-max', data.surplus);
                                
                                $sale.find('.quantity-minus').prop('disabled', data.cart < 1);
                                $sale.find('.quantity-plus').prop('disabled', data.cart >= data.surplus);
                                
                                Zhai.cart.refresh();
                            }
                            
                            $item.find('.quantity-minus').click(function () {
                                Zhai.cart.subtract(goodsId, callback);
                            });
                            $item.find('.quantity-plus').click(function () {
                                Zhai.cart.add(goodsId, callback);
                            });
                            $item.find('.delete button').click(function () {
                                Zhai.cart.del(goodsId);
                            });
                        });
                        
                        Zhai.quantity.refreshAll();
                        Zhai.cart.length = parseInt(data.length);
                        Zhai.cart.volume = data.volume;
                        $('.shopping-bottom .price span').text(Zhai.cart.volume);
                        if (!Zhai.cart.isRest) {
                            if (Zhai.cart.atLeast > Zhai.cart.volume) {
                                $('.shopping-btn a').addClass('disabled').text('还差' + (Zhai.cart.atLeast - Zhai.cart.volume).toFixed(2) + '元');
                            } else {
                                $('.shopping-btn a').html('选好了 <i class="fa fa-long-arrow-right"></i>');
                                if (Zhai.cart.length < 1) {
                                    $('.shopping-btn a').addClass('disabled');
                                } else {
                                    $('.shopping-btn a').removeClass('disabled');
                                }
                            }
                        }
                        $('.shopping-list .link-view').click(Zhai.detail.viewHandle);
                    }
                },
                error : function () {}
            });
        },
        
        clear : function () {
            $.ajax({
                url : Zhai.baseUrl + '/cart/clear',
                type : 'post',
                dataType : 'json',
                data : {storeId : this.storeId},
                success : function (data) {
                    if (data.status === 'ok') {
                        Zhai.cart.refresh();
                        $('.wrapper .quantity-count').text('0');
                        Zhai.quantity.refreshAll();
                    }
                },
                error : function () {}
            });
        },
        
        del : function (id) {
            $.ajax({
                url : Zhai.baseUrl + '/cart/delete',
                type : 'post',
                dataType : 'json',
                data : {goodsId : id},
                success : function (data) {
                    if (data.status === 'ok') {
                        Zhai.cart.refresh();
                    }
                },
                error : function () {}
            });
        },
        
        init : function () {
            this.storeId = parseInt($('.shopping-cart').attr('data-storeId'));
            this.isRest = parseInt($('.shopping-cart').attr('data-isRest'));
            this.atLeast = parseFloat($('.shopping-cart').attr('data-least'));
        }
    },
    
    user : {
        isGuest : true,
        
        showLogin : function () {
            $('#modal-login').modal('show');
        },
        
        init : function () {
            this.isGuest = Boolean($('html').attr('data-isGuest'));
        }
    },
    
    detail : {
        goodsId : 0,
        cart : 0,
        surplus : 0,
        
        refreshQuantity : function () {
            var $quantity = $('#modal-goods .quantity');
            $quantity.find('.quantity-minus').prop('disabled', this.cart < 1);
            $quantity.find('.quantity-plus').prop('disabled', this.cart >= this.surplus);
        },
        
        load : function (id) {
            $.ajax({
                url : Zhai.baseUrl + '/goods/detail?id=' + id,
                type : 'post',
                dataType : 'json',
                success : function (data) {
                    if (data.status === 'ok') {
                        $('#modal-goods h4').text(data.name);
                        $('#modal-goods p').text(data.description);
                        $('#modal-goods .price span').text(data.price);
                        $('#modal-goods .quantity-count').text(data.cart);
                        $('#modal-goods .modal-header').css('background-image', 'url(' + data.image + ')');
                        
                        Zhai.detail.goodsId = id;
                        Zhai.detail.cart = data.cart;
                        Zhai.detail.surplus = data.surplus;
                        Zhai.detail.refreshQuantity();
                    }
                },
                error : function () {}
            });
        },
        
        show : function () {
            $('#modal-goods').modal('show');
        },
        
        viewHandle : function () {
            var goodsId = parseInt($(this).attr('data-goodsId'));
            Zhai.detail.load(goodsId);
            Zhai.detail.show();
            return false;
        },
        
        init : function () {
            var callback = function (data) {
                var $sale = $('.sale-item[data-goodsid="' + Zhai.detail.goodsId + '"]');
                $sale.find('.stock span').text(data.surplus);
                $sale.find('.quantity-count').text(data.cart);
                $sale.find('.quantity').attr('data-max', data.surplus);
                $('#modal-goods .quantity-count').text(data.cart);
                
                Zhai.detail.cart = data.cart;
                Zhai.detail.surplus = data.surplus;
                Zhai.detail.refreshQuantity();
                
                $sale.find('.quantity-minus').prop('disabled', data.cart < 1);
                $sale.find('.quantity-plus').prop('disabled', data.cart >= data.surplus);
                
                Zhai.cart.refresh();
            };
            
            $('#modal-goods .quantity-minus').click(function () {
                Zhai.cart.subtract(Zhai.detail.goodsId, callback);
            });
            $('#modal-goods .quantity-plus').click(function () {
                Zhai.cart.add(Zhai.detail.goodsId, callback);
            });
            $('.link-view').click(Zhai.detail.viewHandle);
        }
    },
    
    quantity : {
        refreshAll : function () {
            $('.wrapper .quantity').each(function (i) {
                var $quantity = $(this);
                var max = parseInt($quantity.attr('data-max'));
                var count = parseInt($quantity.find('.quantity-count').text());
                
                $quantity.find('.quantity-minus').prop('disabled', count < 1);
                $quantity.find('.quantity-plus').prop('disabled', count >= max);
            });
        },
    },
    
    goods : {
        traversalHandle : function (i) {
            var $item = $(this);
            var $quantity = $item.find('.quantity');
            var goodsId = parseInt($item.attr('data-goodsId'));
            var callback = function (data) {
                $item.find('.stock span').text(data.surplus);
                $item.find('.quantity-count').text(data.cart);
                $quantity.attr('data-max', data.surplus);
                
                $quantity.find('.quantity-minus').prop('disabled', data.cart < 1);
                $quantity.find('.quantity-plus').prop('disabled', data.cart >= data.surplus);
                
                Zhai.cart.refresh();
            }
            
            $item.find('.quantity-minus').click(function () {
                if (Zhai.user.isGuest) {
                    Zhai.user.showLogin();
                    return false;
                }
                
                Zhai.cart.subtract(goodsId, callback);
            });
            $item.find('.quantity-plus').click(function () {
                if (Zhai.user.isGuest) {
                    Zhai.user.showLogin();
                    return false;
                }
                
                Zhai.cart.add(goodsId, callback);
            });
        }
    },
    
    init : function () {
        this.baseUrl = $('meta[name=baseurl]').attr('content');
        this.user.init();
        this.cart.init();
        this.detail.init();
    }
};

$(function () {
    Zhai.init();
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $('.shopping-clear').click(function () {
        if (!Zhai.user.isGuest) {
            Zhai.cart.clear();
        }
        return false;
    });
    
    $('.sale-item').each(Zhai.goods.traversalHandle);
    
    $('.link-cate').click(function () {
        var $target = $($(this).attr('data-target'));
        if ($target.length == 1) {
            $(window).scrollTop($target.offset().top);
            return false;
        }
        
        return true;
    });
    
    var isClosed = true;
    var shoppingToggle = function () {
        if (isClosed) {
            $('.shopping-toggle button').addClass('active');
            $('.shopping-cart').removeClass('shopping-cart-closed');
            $('<div class="modal-backdrop backdrop-cart fade in"></div>').click(shoppingToggle).appendTo(document.body);
        } else {
            $('.shopping-toggle button').removeClass('active');
            $('.shopping-cart').addClass('shopping-cart-closed');
            $(".backdrop-cart").remove();
        }
        
        isClosed = !isClosed;
    }
    $('.shopping-toggle button').click(shoppingToggle);
    
    $('.totop a').click(function () {
        $(window).scrollTop(0);
        return false;
    });
    
    var searchClosed = true;
    var searchToggle = function () {
        if (searchClosed) {
            $('.tool-searchbox').css('display', 'block').addClass('active');
            $('<div class="modal-backdrop backdrop-searchbox fade in"></div>').click(searchToggle).appendTo(document.body);
        } else {
            $('.tool-searchbox').removeClass('active');
            setTimeout(function () {
                $('.tool-searchbox').css('display', 'none');
            }, 400);
            $(".backdrop-searchbox").remove();
        }
        
        searchClosed = !searchClosed;
        return false;
    }
    $('.tool-search a').click(searchToggle);
})