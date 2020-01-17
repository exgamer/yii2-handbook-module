$(document).ready(function() {
    var interactiveClass = 'yii2-handbook-dynamic-elements-interactive-mode';
    var manageClass = 'yii2-handbook-dynamic-elements-manage-control';
    $(document).on('click', '.' + manageClass + '.' + interactiveClass, function () {
        var url = $(this).attr('data-url');
        window.open(url, '_blank');
    });

    $('.yii2-handbook-dynamic-elements-manage-panel input[type="checkbox"]').change(function(){
        var value = $(this).is(':checked');
        var url = $(this).attr('data-url');
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                value: value
            },
            success: function() {
                $('.' + manageClass).each(function() {
                    var self = $(this);
                    if(value === true) {
                        self.addClass(interactiveClass);
                    } else {
                        self.removeClass(interactiveClass);
                    }
                });
            }
        })
    });
});