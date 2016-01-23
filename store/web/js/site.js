$(function() {

    var baseUrl = $('meta[name=baseurl]').attr('content');
    
    $('#side-menu').metisMenu();

    var element = $('#side-menu li.active').parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
    
    var count = parseInt($('.audio-order').attr('data-count'));
    setInterval(function () {
        $.ajax({
            url : baseUrl + '/order/count',
            type : 'post',
            dataType : 'json',
            success : function (data) {
                if (data.count > count) {
                    $('.audio-order')[0].play();
                }
                count = data.count;
            }
        });
    }, 30000);
});