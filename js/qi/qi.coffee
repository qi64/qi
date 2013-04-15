$ = jQuery
# extension
window.location.fullpaths = ->
  p = this.pathname
  until "" == (p = p.substr(0, i))
    i = p.lastIndexOf('/')
    this.protocol + '//' + this.host + p

$.qi =
  version: "0.0.2"
  all: (cfg = {})->
    @print()
    .back()
    .active()
    .timeago() # precisa vir antes do tooltip
    .bootstrap()
    .submit()
    .confirm()
    console.log("qi.all executed!") if console and cfg.debug

  print: ->
    $("[data-print]").click ->
      window.print()
      return false
    return this

  back: ->
    # @TODO pedir confirmacao se algum input de algum form foi modificado
    $("[data-back]").on 'click', ->
      window.history.back()
      return false
    return this

  active: ->
    # @TODO o que fazer quando dois menus apontam pro mesmo link?
    # @FIX nao funciona com links relativos, utilizando base
    $("a[href^='/']").each ->
      for path in window.location.fullpaths()
        if this.href == path
          $(this).closest('li').addClass('active')
    return this

  bootstrap: ->
    $('[data-select]').select2() if $.fn.select2
    $("[rel=tooltip],[data-tooltip]").tooltip() if $.fn.tooltip
    $("[data-popover]").popover() if $.fn.popover
    $('.control-group').has('[required]').addClass('required')
    return this

  timeago: ->
    # nao pode utilizar [data-timeago] pois o mesmo e utilizado internamente pelo componente
    $(".timeago").timeago() if $.fn.timeago
    return this

  submit: ->
    $('form').submit ->
      return false if $(this).data("confirm") and not confirm $(this).data("confirm")
      bt = $(@).find('[type=submit]').attr('disabled', 'disabled')
      if bt.val() then bt.val('enviando ...') else bt.text('enviando ...')
    return this

  confirm: ->
    $("a[data-confirm], button[data-confirm]").click ->
      return confirm $(this).data("confirm")
    return this
