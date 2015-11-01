$(document).ready(function () {
    //var currentCount = $('form > fieldset > fieldset').length;
    var max = $('form > fieldset > fieldset').length;
    $('.domain-add').click(function () {
                $('form > fieldset > fieldset > input').each(function (index) {
            var name = $(this).attr('name');
            var start_i = name.indexOf('[', 0) + 1;
            var end_i = name.indexOf(']', start_i);

            var i = name.substr(start_i, end_i - start_i);

            if (i > max) max = i;
        });
        max++;

        var template = $('form > fieldset > span').data('template');
        template = template.replace(/__index__/g, max);
        $('form > fieldset').append(template);

        //currentCount++;
        //var template = $('form > fieldset > span').data('template');
        //template = template.replace(/__index__/g, currentCount);
        //
        //$('form > fieldset').append(template);
        return false;
    });
});
$(document).on('click', '.domain-remove', function () {
    var currentCount = $('form > fieldset > fieldset').length;
    if (currentCount != 1) $(this).parent().remove();

    return false;
});

