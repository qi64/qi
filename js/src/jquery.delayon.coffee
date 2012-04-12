jQuery.fn.delayOn = (delay, events, handler) ->
  self = @
  forward_arguments = Array.prototype.slice.call(arguments)
  forward_arguments.shift()
  forward_arguments[1] = (e)->
    clearTimeout self._delayOnTimeout
    realEvent = -> handler.apply self, e
    self._delayOnTimeout = setTimeout realEvent, delay

  $(@).on.apply @, forward_arguments
