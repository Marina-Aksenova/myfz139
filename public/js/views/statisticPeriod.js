$(document).ready(function() {
    var period = $('select').val();

    if(period == '5'){
        $("input[name='dateCalendarFirst']").parent().show();
        $("input[name='dateCalendarLast']").parent().show();
    } else
    {
        $("input[name='dateCalendarFirst']").parent().hide();
        $("input[name='dateCalendarLast']").parent().hide();
    }
});

$(document).on('click', 'select', function() {
    var period = $('select').val();

    if(period == '5'){
        $("input[name='dateCalendarFirst']").parent().show();
        $("input[name='dateCalendarLast']").parent().show();
    } else
    {
        $("input[name='dateCalendarFirst']").parent().hide();
        $("input[name='dateCalendarLast']").parent().hide();
    }
});

$(function () {
    $("input[name='dateCalendarFirst']").datepicker({
        maxDate: 0,
        dateFormat: "dd.mm.yy"
    });

    $("input[name='dateCalendarLast']").datepicker({
        maxDate: 0,
        dateFormat: "dd.mm.yy"
    });
});