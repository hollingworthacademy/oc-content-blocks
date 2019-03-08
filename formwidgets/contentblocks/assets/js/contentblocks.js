+function ($) { "use strict";
    var Base      = $.oc.foundation.base,
        BaseProto = Base.prototype

    var ContentBlocks = function (element, options) {
        this.$el        = $(element)
        this.$container = this.$el.find('.contentblocks-container:first-child')
        this.options    = options || {}
        this.$items     = $(options.blockContainer, this.$el)

        $.oc.foundation.controlUtils.markDisposable(element)
        Base.call(this)
        this.init()
    }

    ContentBlocks.prototype = Object.create(BaseProto)
    ContentBlocks.prototype.constructor = ContentBlocks

    ContentBlocks.prototype.init = function () {
        this.$items.sortable({
            itemSelector: 'div.contentblocks-block',
            handle: this.options.blockHandle,
            placeholder: '<div class="placeholder"></div>',
            distance: 5,
            nested: false,
        })

        this.$el.one('dispose-control', this.proxy(this.dispose))        
    }

    ContentBlocks.prototype.dispose = function () {
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('oc.ContentBlocks')

        this.$el     = null
        this.options = null
        this.$items  = null

        BaseProto.dispose.call(this)
    }

    ContentBlocks.prototype.addBlock = function (type) {
        var $container = this.$container

        this.$el.request(this.options.addHandler, {
            data: {
                type: type
            },
            success: function (data) {
                $(data.result).appendTo($container).hide().slideDown(250)
                this.success(data)
            }
        })
    }

    ContentBlocks.prototype.removeBlock = function (blockId) {
        $(blockId, this.$container).slideUp(250, function () {
            $(this).remove()
        })
    }

    ContentBlocks.DEFAULTS = {
        addHandler: null
    }

    // jQuery Plugin
    // =============

    var old = $.fn.contentblocks

    $.fn.contentblocks = function (option) {
        var args = Array.prototype.slice.call(arguments, 1), items, result

        items = this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.ContentBlocks')
            var options = $.extend({}, ContentBlocks.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data) $this.data('oc.ContentBlocks', (data = new ContentBlocks(this, options)))
            if (typeof option == 'string') result = data[option].apply(data, args)
            if (typeof result != 'undefined') return false
        })

        return result ? result : items
    }

    $.fn.contentblocks.Constructor = ContentBlocks

    $.fn.contentblocks.noConflict = function () {
        $.fn.contentblocks = old
        return this
    }

    // Data API
    // ========
    $(document).render(function () {
        $('[data-control="contentblocks"]').contentblocks()
    })

}(window.jQuery);