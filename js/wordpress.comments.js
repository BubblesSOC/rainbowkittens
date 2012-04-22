(function() {
  var $, CommentAuthorField, CommentEmailField, CommentField, CommentFormField, CommentUrlField, WPComments;
  var __hasProp = Object.prototype.hasOwnProperty, __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor; child.__super__ = parent.prototype; return child; };

  $ = jQuery;

  CommentFormField = (function() {

    function CommentFormField(fieldName, isRequired) {
      this.isRequired = isRequired;
      this.container = $('#comment-form-' + fieldName);
      this.input = $('#' + fieldName);
      this.label = this.container.children('label').text();
      this.errorSpan = $('#' + fieldName + '-error');
      this.validation();
    }

    CommentFormField.prototype.validation = function() {
      var _this = this;
      return this.input.bind({
        blur: function() {
          var errorMsg;
          if (_this.isRequired && _this.input.val() === '') {
            return _this.errorSpan.text(_this.label + ' cannot be empty!');
          } else if (errorMsg = _this.additionalValidation()) {
            return _this.errorSpan.text(errorMsg);
          } else {
            return _this.errorSpan.empty();
          }
        }
      });
    };

    CommentFormField.prototype.additionalValidation = function() {
      return false;
    };

    return CommentFormField;

  })();

  CommentAuthorField = (function() {

    __extends(CommentAuthorField, CommentFormField);

    function CommentAuthorField() {
      CommentAuthorField.__super__.constructor.call(this, 'author', true);
    }

    return CommentAuthorField;

  })();

  CommentEmailField = (function() {

    __extends(CommentEmailField, CommentFormField);

    function CommentEmailField() {
      CommentEmailField.__super__.constructor.call(this, 'email', true);
    }

    CommentEmailField.prototype.additionalValidation = function() {
      return false;
    };

    return CommentEmailField;

  })();

  CommentUrlField = (function() {

    __extends(CommentUrlField, CommentFormField);

    function CommentUrlField() {
      CommentUrlField.__super__.constructor.call(this, 'url', false);
    }

    return CommentUrlField;

  })();

  CommentField = (function() {

    __extends(CommentField, CommentFormField);

    function CommentField() {
      CommentField.__super__.constructor.call(this, 'comment', true);
    }

    return CommentField;

  })();

  WPComments = (function() {

    function WPComments() {
      this.commentForm = $('#commentform');
      this.commentAuthor = new CommentAuthorField();
      this.commentEmail = new CommentEmailField();
      this.commentUrl = new CommentUrlField();
      this.commentBox = new CommentField();
      this.commentSubmit = $('#submit');
      this.bindQuoteLinks();
    }

    WPComments.prototype.hideCommenterInfo = function() {
      this.commentAuthor.container.hide();
      this.commentEmail.container.hide();
      return this.commentUrl.container.hide();
    };

    WPComments.prototype.showCommenterInfo = function() {
      this.commentAuthor.container.show();
      this.commentEmail.container.show();
      return this.commentUrl.container.show();
    };

    WPComments.prototype.bindQuoteLinks = function() {
      var _commentTextbox;
      _commentTextbox = this.commentBox.input;
      return $('a.comment-quote-link').bind('click', function(e) {
        var comment, commentBoxVal, commentId, commentQuote, cursorPos, endPos, newScrollHeight, scrollDiff, scrollHeight, scrollTop, startPos;
        e.preventDefault();
        commentId = $(this).attr("id").split("-")[3];
        comment = $(this).data("comment").replace(/\\\\/g, '\\').replace(/\\'/g, "'").replace(/\\n/g, '\n');
        commentQuote = ("[quote comment=" + commentId + "]") + comment + "[/quote]\n\n";
        startPos = _commentTextbox[0].selectionStart;
        endPos = _commentTextbox[0].selectionEnd;
        scrollTop = _commentTextbox[0].scrollTop;
        scrollHeight = _commentTextbox[0].scrollHeight;
        commentBoxVal = _commentTextbox.val();
        _commentTextbox.val(commentBoxVal.substring(0, startPos) + commentQuote + commentBoxVal.substring(endPos, commentBoxVal.length));
        cursorPos = startPos + commentQuote.length;
        _commentTextbox[0].selectionStart = cursorPos;
        _commentTextbox[0].selectionEnd = cursorPos;
        newScrollHeight = _commentTextbox[0].scrollHeight;
        if (newScrollHeight > scrollHeight) {
          scrollDiff = scrollTop + (newScrollHeight - scrollHeight);
        } else {
          scrollDiff = scrollTop + (scrollHeight - newScrollHeight);
        }
        return _commentTextbox.scrollTop(scrollDiff).focus();
      });
    };

    return WPComments;

  })();

  $(function() {
    return window.wpComments = new WPComments();
  });

  /* Quicktags edited from Alex King's Quicktags plugin: http://alexking.org/projects/js-quicktags
  */

}).call(this);
