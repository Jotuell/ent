(function ($) {
    window.EntCustomView = {};

    window.ent_image_cache = {};
    window.ent_image = function (id, wp_size, size) {
        var cache_id;

        size = size || '200x200';
        wp_size = wp_size || 'thumbnail';
        cache_id = id +'-'+ wp_size;

        function getMarkup(url, cache_id, size) {
            size = size.split('x');

            size[0] = size[0][size[0].length - 1] != '%' ? size[0]+'px' : size[0];
            size[1] = size[1][size[1].length - 1] != '%' ? size[1]+'px' : size[1];

            return '<div data-mu-image="'+ cache_id +'" style="width: '+ size[0] +'; height: '+ size[1] +'; background-image: url('+ url +'); background-size: cover; background-position: center;"></div>';
        }

        if (cache_id in window.ent_image_cache) {
            return getMarkup(window.ent_image_cache[cache_id], cache_id, size);
        } else {
            $.ajax({
                type: 'POST',
                url: window.ajaxurl,
                data: {
                    action: 'ent_img_src',
                    id: id,
                    size: wp_size,
                },
                dataType: 'json',
                context: this
            }).done(function(data) {
                window.ent_image_cache[cache_id] = data.url;
                $('[data-mu-image="'+ cache_id +'"]').css('background-image', 'url('+ data.url +')');
            });

            return getMarkup('', cache_id, size);
        }
    };

    window.ent_link_parse = function (link) {
        var data = {};
        var link = (link || '').split('|');
        var part;

        for (i in link) {
            if (link[i] == '') {
                continue;
            }

            part = link[i].split(':');
            data[part[0]] = decodeURIComponent(part[1]);
        }

        return data;
    };

    window.ent_link = function (link) {
        link = ent_link_parse(link);

        return '<a href="'+ link.url +'" class="button">'+ (link.title || '???') +'</a>';
    };

    function init () {
        window.EntCustomView = window.vc.shortcode_view.extend({
            tpl: false,
            $wrapper: false,
            changeShortcodeParams: function(model) {
                var params = _.extend({}, model.get('params'));

                if (!this.tpl) {
                    this.tpl = this.$el.find('[data-ent-custom-view]').html().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                }

                if (!this.$wrapper) {
                    this.$wrapper = this.$el.find('.wpb_element_wrapper');
                }

                if (_.isObject(params)) {
                    var $element = _.template(this.tpl, vc.templateOptions.custom)({
                        post_title: $('#titlewrap input').val(),
                        params: params
                    });

                    this.$wrapper.find('[data-ent-custom-view]').html($element);
                }
            }
        });
    }

    // HACK! Since it seems that wp_enqueue_script is not working…
    var checkInterval = 200;

    function check() {
        if (typeof(vc) != 'undefined') {
            init();
        } else {
            setTimeout(check, checkInterval);
        }
    }

    check();
})(jQuery);
