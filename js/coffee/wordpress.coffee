# Compile from theme folder: coffee -c -o js/ js/coffee/
$ = jQuery

loadSocialWidget = (selector, action) ->
  $.get wpAjax.url, { action: action }, (html) ->
    $ ->
      $(selector).html html
    
loadSocialWidget '#raptr ul', 'brp-print-games'
loadSocialWidget '#twitter ul', 'bsp-print-tweets'
loadSocialWidget '#flickr ul', 'bsp-print-photos'
loadSocialWidget '#github ul', 'bsp-print-repos'
loadSocialWidget '#tumblr ul', 'bsp-print-posts'

# twttr.anywhere (T) ->
#   T('#twitter a.tweep').hovercards
#     infer: true

htmlspecialchars = (str) ->
  str
    .replace(/&(?!(#(X|x)?[0-9A-Fa-f]{2,5}|[A-Za-z]{2,8});)/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')

$ ->
  $("#archives li.year").click (e) ->
    e.preventDefault()
    $(this)
      .find("ol.months")
      .toggle('fast')
      .toggleClass('active')