$ = jQuery
$.fn.extend
  plugin: (options)->
    self = $.fn.plugin
    opts = $.extend {}, self.default_options, options
    $(@).each (i, el) ->
      self.init el, opts

$.extend $.fn.plugin,
  default_options:
    color: 'red'
  init: (el, opts) ->
    @opts = opts
    console.log @
