# Compile from theme folder: coffee -c -o js/ js/coffee/
$ = jQuery

window.Wordpress =
  init: ->
    $("#archives li.year").bind "click", (e) ->
      e.preventDefault()
      $(this)
        .find("ol.months")
        .toggle('fast')
        .toggleClass('active')
        
  htmlspecialchars: (str) ->
    str
      .replace(/&(?!(#(X|x)?[0-9A-Fa-f]{2,5}|[A-Za-z]{2,8});)/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;')

$ ->
  Wordpress.init()