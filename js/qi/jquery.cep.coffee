$ = jQuery

class CEP
  constructor: (@el, opts)->
    @$el = $(@el)
    @cache = {}
    @lastCep = null
    @$el.on 'keyup', @search
    @$el.on 'change', @search
    @$el.on 'blur', @search
    @search()

  search: =>
    return unless @valid() and @changed()
    @send()

  valid: => @cep().length is 8
  changed: => @cep() != @lastCep

  cep: => @el.value.replace(/\D/g, "")

  clean: =>
    console.log 'clean'
    $('#form-bairro,#form-cidade,#form-logradouro,#form-tipo_logradouro,#form-uf,#form-endereco').val('')

  disable: =>
    console.log 'disable'
    $('#form-cep,#form-bairro,#form-cidade,#form-logradouro,#form-tipo_logradouro,#form-uf,#form-endereco').attr('readonly', 'readonly')

  enable: =>
    console.log 'enable'
    $('#form-cep,#form-bairro,#form-cidade,#form-logradouro,#form-tipo_logradouro,#form-uf,#form-endereco').removeAttr('readonly')

  send: =>
    @disable()
    cep = @cep()
    if @cache[cep]
      @enable()
      @success @cache[cep]
    else
      xhr = $.getJSON "http://cep.appspot.com/#{cep}.json?callback=?"
      xhr.success @success
      xhr.error -> console.log 'error'
      xhr.complete -> console.log 'complete'

    @lastCep = cep

  success: (json)=>
    return unless json.ok
    @clean()
    $('#form-bairro').val json.bairro
    $('#form-cidade').val json.cidade
    $('#form-logradouro').val json.logradouro
    $('#form-tipo_logradouro').val json.tipo_logradouro
    $('#form-uf').val json.uf
    $('#form-endereco').val "#{json.tipo_logradouro} #{json.logradouro}"

$.fn.extend
  cep: (options) ->
    opts = $.extend {}, self.default_options, options
    $(this).each (i, el) ->
      new CEP(el, opts)

$ -> $("input[data-cep]").cep()