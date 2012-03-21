$ = jQuery
$.fn.esort = (options)->
  opts = $.extend {}, $.fn.esort.defaults, options
  r = (n)-> n
  cmp = (a, b) -> if a > b then r(1) else r(-1)
  sorter = (a, b) -> cmp opts.attr(a), opts.attr(b)
  if opts.reverse then r = (n)-> n * -1 
  @html @children().sort(sorter)
  return this

$.fn.esort.defaults =
  reverse: false
  attr: (el) -> $.trim $(el).text().toLowerCase()
