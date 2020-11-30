if (typeof $ === 'undefined') var $ = jQuery;

$(function() {
    var $new    = $('.js-last-page-redirect__new');
    var $edit   = $('.js-last-page-redirect__edit');
    var $delete = $('.js-last-page-redirect__delete');

    if (getUrlParameter('added')) {
        var id = getUrlParameter('added');
        var $post = $('.post[data-id="' + id + '"]');
        $post.addClass('is-added');
    }

    $delete.click(function(e) {
        e.preventDefault();

        var $post = $(this).parents('.post:first');
        var id = $post.data('id');
        var referal_url = $post.find('.referal_url').text().trim();
        var operator = $post.find('.operator').text().trim();

        var modal = new Modal();
        modal.content = _.template(document.getElementById('js-last-page-redirect-delete__template').innerHTML)({
            id:             id,
            referal_url:    referal_url,
            operator:       operator
        });
        modal.render();
        modal.open();
    });

    $edit.click(function(e) {
        e.preventDefault();

        var $post = $(this).parents('.post:first');
        var id = $post.data('id');
        var referal_url = $post.find('.referal_url').text().trim();
        var operator = $post.find('.operator').text().trim();

        var modal = new Modal();
        modal.content = _.template(document.getElementById('js-last-page-redirect-item__template').innerHTML)({
            id:             id,
            referal_url:    referal_url,
            operator:       operator
        });
        modal.render();
        modal.open();
    });

    $new.click(function(e) {
        e.preventDefault();

        var modal = new Modal();
        modal.content = _.template(document.getElementById('js-last-page-redirect-item__template').innerHTML)({});
        modal.id = 'last-page-redirect-new-modal';
        modal.render();
        modal.open();
    });

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) {
                return sParameterName[1].replace(/\%20/g, ' ');
            }
        }
    }
});

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
        close:      close,
        content:    content,
        id:         id,
        open:       open,
        render:     render
    };
}
