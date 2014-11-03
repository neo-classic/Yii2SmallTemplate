$(document).ready(function() {
    $('body').on('click', '.confirmLink', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var grid = $(this).attr('data-grid');

        if (confirm('Точно удалить?')) {
            $.get(url, function() {
                $.pjax.reload({container:'#'+grid});
            });
        }
        return false;
    });
});