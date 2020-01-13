$(document).ready(function() {
    $('.yii2-handbook-seo-manage-control, .yii2-handbook-seo-manage-panel').click(function () {
       var url = $(this).attr('data-url');
        var win = window.open(url, '_blank');
        // win.focus();
    });
});