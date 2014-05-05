(function($) {
    $('.jobs a').on('click', function(e) {
        e.preventDefault();

        var $iframe;
        if (!$('.wrapper iframe').length) {
            $iframe = $('<iframe src="" width="100%" height="400px"></iframe>');
            $('.wrapper').append($iframe);
        } else {
            $iframe = $('.wrapper iframe');
        }

        $iframe.prop('src', this.href);

    });

    if ((undefined !== default_command) && default_command) {
        $('.jobs a.job-'+default_command).click();
    }
})(jQuery);
