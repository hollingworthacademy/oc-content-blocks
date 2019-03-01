+function ($) { "use strict";
    var Base      = $.oc.foundation.base,
        BaseProto = Base.prototype

    var ContentBlocks = function (element, options) {
        this.$el     = $(element)
        this.options = options || {}
        this.$items  = $(options.blockContainer, this.$el)

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

        this.$el.on('ajaxDone', '> [data-contentblocks-dialog] [data-contentblocks-add]', this.proxy(this.onAddBlock))
        this.$el.on('ajaxDone', '> .contentblocks-container > .contentblocks-block > .contentblocks-handle > [data-contentblocks-remove]', this.proxy(this.onRemoveBlock))
        this.$el.one('dispose-control', this.proxy(this.dispose))        
    }

    ContentBlocks.prototype.dispose = function () {
        this.$el.off('ajaxDone', '> [data-contentblocks-dialog] [data-contentblocks-add]', this.proxy(this.onAddBlock))
        this.$el.off('ajaxDone', '> .contentblocks-container > .contentblocks-block > .contentblocks-handle > [data-contentblocks-remove]', this.proxy(this.onRemoveBlock))
        this.$el.off('dispose-control', this.proxy(this.dispose))
        this.$el.removeData('oc.ContentBlocks')

        this.$el     = null
        this.options = null
        this.$items  = null

        BaseProto.dispose.call(this)
    }

    ContentBlocks.prototype.onAddBlock = function (ev, context, data) {
        var $container = this.$el.find('> .contentblocks-container')
        $(data.result).appendTo($container).hide().slideDown(250)
    }

    ContentBlocks.prototype.onRemoveBlock = function (ev) {
        $(ev.target).closest('.contentblocks-block').slideUp(250, function () {
            $(this).remove()
        })
    }

    ContentBlocks.DEFAULTS = {
        
    }

    // jQuery Plugin
    // =============

    var old = $.fn.ContentBlocks

    $.fn.ContentBlocks = function (option) {
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

    $.fn.ContentBlocks.Constructor = ContentBlocks

    $.fn.ContentBlocks.noConflict = function () {
        $.fn.ContentBlocks = old
        return this
    }

    // Data API
    // ========
    $(document).render(function () {
        $('[data-control="contentblocks"]').ContentBlocks()
    })

}(window.jQuery);