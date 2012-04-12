class eSort
  reverse: 1
  attr: ($el) -> $.trim $el.text().toLowerCase()
  cmp: (a, b) -> if a > b then 1 else -1
  sort: (sel)-> sel.children().sort (a, b)=> @sorter $(a), $(b)
  sorter: (a, b) -> @reverse * @cmp(@attr(a), @attr(b))

jQuery.fn.esort = (options) ->
  @.html ($.extend new eSort(), options).sort(@)