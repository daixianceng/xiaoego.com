$(function() {

    $('#side-menu').metisMenu();

    var element = $('#side-menu li.active').parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});