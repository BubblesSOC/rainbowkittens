(function() {
  var $;

  $ = jQuery;

  window.Wordpress = {
    init: function() {
      return $("#archives li.year").bind("click", function(e) {
        e.preventDefault();
        return $(this).find("ol.months").toggle('fast').toggleClass('active');
      });
    },
    htmlspecialchars: function(str) {
      return str.replace(/&(?!(#(X|x)?[0-9A-Fa-f]{2,5}|[A-Za-z]{2,8});)/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
  };

  $(function() {
    return Wordpress.init();
  });

}).call(this);
