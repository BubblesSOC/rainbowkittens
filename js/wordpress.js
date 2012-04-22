(function() {
  var $, htmlspecialchars, loadSocialWidget;

  $ = jQuery;

  loadSocialWidget = function(selector, action) {
    return $.get(wpAjax.url, {
      action: action
    }, function(html) {
      return $(function() {
        return $(selector).html(html);
      });
    });
  };

  loadSocialWidget('#raptr ul', 'brp-print-games');

  loadSocialWidget('#twitter ul', 'bsp-print-tweets');

  loadSocialWidget('#flickr ul', 'bsp-print-photos');

  loadSocialWidget('#lastfm ul', 'bsp-print-tracks');

  loadSocialWidget('#github ul', 'bsp-print-repos');

  htmlspecialchars = function(str) {
    return str.replace(/&(?!(#(X|x)?[0-9A-Fa-f]{2,5}|[A-Za-z]{2,8});)/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  };

  $(function() {
    return $("#archives li.year").click(function(e) {
      e.preventDefault();
      return $(this).find("ol.months").toggle('fast').toggleClass('active');
    });
  });

}).call(this);
