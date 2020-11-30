function Modal() {
    var $app        = null;
    var content     = null;
    var id          = null;

    function close() {
        if (!$app) return false;

        $app.removeClass('is-open');

        setTimeout(function() {
            $app.remove();
            $app = null;
        }, 501);
    }

    function open() {
        if (!$app) return false;

        setTimeout(function() {
            $app.addClass('is-open');
        }, 1);
    }

    function render() {
        $app = $(_.template(document.getElementById('js-modal__template').innerHTML)({content: this.content, id: this.id}));
        $('body').append($app);

        $app.find('.js-modal__close').click(function(e) {
            e.preventDefault();

            close();
        });
    }

    return {
        content:    content,
        id:         id,
        open:       open,
        render:     render
    };
}
