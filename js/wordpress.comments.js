(function() {
  var $, QuickTagButton;

  $ = jQuery;

  QuickTagButton = (function() {

    QuickTagButton.element;

    QuickTagButton.title;

    QuickTagButton.display;

    QuickTagButton.idAppend;

    QuickTagButton.tagStart;

    QuickTagButton.tagEnd;

    QuickTagButton.isTagOpen;

    function QuickTagButton(title, display, idAppend, tagStart, tagEnd) {
      this.title = title;
      this.display = display;
      this.idAppend = idAppend;
      this.tagStart = tagStart;
      this.tagEnd = tagEnd;
      this.element = $('<button class="quicktag" id="quicktag-' + this.idAppend + '" title="' + this.title + '">' + this.display + '</button>').appendTo(wpComments.qtContainer);
      this.isTagOpen = false;
      this.bindClick();
    }

    QuickTagButton.prototype.bindClick = function() {
      var _this = this;
      return this.element.bind("click", function(e) {
        e.preventDefault();
        if (_this.idAppend === 'link' && !_this.isTagOpen) {
          wpComments.qtLinkDialog.dialog("option", "buttons", {
            Ok: function() {
              return _this.saveLink();
            },
            Cancel: function() {
              return wpComments.qtLinkDialog.dialog('close');
            }
          });
          return wpComments.qtLinkDialog.dialog('open');
        } else {
          return _this.insertTag();
        }
      });
    };

    QuickTagButton.prototype.insertTag = function() {
      var commentBoxVal, cursorPos, endPos, selection, startPos, tag;
      var _this = this;
      commentBoxVal = wpComments.commentBox.val();
      startPos = wpComments.commentBox[0].selectionStart;
      endPos = wpComments.commentBox[0].selectionEnd;
      if (startPos !== endPos) {
        selection = commentBoxVal.substring(startPos, endPos);
        if (this.idAppend === 'code') {
          selection = Wordpress.htmlspecialchars(selection);
        }
        cursorPos = endPos + this.tagStart.length + this.tagEnd.length;
        wpComments.commentBox[0].selectionStart = cursorPos;
        wpComments.commentBox[0].selectionEnd = cursorPos;
        return wpComments.commentBox.val(commentBoxVal.substring(0, startPos) + this.tagStart + selection + this.tagEnd + commentBoxVal.substring(endPos, commentBoxVal.length)).focus();
      } else if (this.idAppend === 'code') {
        wpComments.qtCodeDialog.dialog("option", "buttons", {
          Ok: function() {
            var codeBoxVal;
            codeBoxVal = Wordpress.htmlspecialchars(wpComments.qtCodeBox.val());
            wpComments.commentBox.val(commentBoxVal.substring(0, startPos) + _this.tagStart + codeBoxVal + _this.tagEnd + commentBoxVal.substring(endPos, commentBoxVal.length));
            cursorPos = endPos + _this.tagStart.length + codeBoxVal.length + _this.tagEnd.length;
            wpComments.commentBox[0].selectionStart = cursorPos;
            wpComments.commentBox[0].selectionEnd = cursorPos;
            return wpComments.qtCodeDialog.dialog('close');
          },
          Cancel: function() {
            return wpComments.qtCodeDialog.dialog('close');
          }
        });
        return wpComments.qtCodeDialog.dialog('open');
      } else {
        if (this.isTagOpen) {
          this.element.text(this.display);
          this.isTagOpen = false;
          tag = this.tagEnd;
          this.removeTagFromStack();
        } else {
          this.element.text('/' + this.display);
          this.isTagOpen = true;
          tag = this.tagStart;
          wpComments.qtStack.push(this);
        }
        cursorPos = endPos + tag.length;
        wpComments.commentBox[0].selectionStart = cursorPos;
        wpComments.commentBox[0].selectionEnd = cursorPos;
        return wpComments.commentBox.val(commentBoxVal.substring(0, startPos) + tag + commentBoxVal.substring(endPos, commentBoxVal.length)).focus();
      }
    };

    QuickTagButton.prototype.saveLink = function() {
      var link;
      link = wpComments.qtLinkInput.val();
      if (link === 'http://' || link === '') {
        return wpComments.qtLinkError.show();
      } else {
        this.tagStart = '<a href="' + link + '">';
        this.insertTag();
        return wpComments.qtLinkDialog.dialog('close');
      }
    };

    QuickTagButton.prototype.removeTagFromStack = function() {
      var index, qtButton, _len, _ref;
      _ref = wpComments.qtStack;
      for (index = 0, _len = _ref.length; index < _len; index++) {
        qtButton = _ref[index];
        if (this === qtButton) {
          wpComments.qtStack.splice(index, 1);
          return;
        }
      }
    };

    return QuickTagButton;

  })();

  window.wpComments = {
    init: function() {
      var _this = this;
      this.commentBox = $("#comment");
      this.qtContainer = $('<div id="quicktags" />').insertBefore(this.commentBox);
      this.qtStack = [];
      this.qtBold = new QuickTagButton('bold/strong', 'b', 'bold', '<strong>', '</strong>');
      this.qtItalic = new QuickTagButton('italic/emphasis', 'i', 'italic', '<em>', '</em>');
      this.qtIns = new QuickTagButton('underline/insert', 'u', 'underline', '<ins>', '</ins>');
      this.qtDel = new QuickTagButton('strike/delete', 'del', 'delete', '<del>', '</del>');
      this.qtLink = new QuickTagButton('link', 'link', 'link', '<a>', '</a>');
      this.qtLinkDialog = $('#quicktag-link-dialog').dialog({
        autoOpen: false,
        draggable: false,
        modal: true,
        resizable: false,
        title: "Insert Link",
        open: function() {
          return _this.qtLinkInput.focus();
        },
        close: function() {
          _this.qtLinkInput.val('http://');
          _this.qtLinkError.hide();
          return _this.commentBox.focus();
        }
      });
      this.qtLinkInput = $('#quicktag-link-url');
      this.qtLinkError = $('#quicktag-link-error');
      this.qtCode = new QuickTagButton('code', 'code', 'code', '<code>', '</code>');
      this.qtCodeDialog = $('#quicktag-code-dialog').dialog({
        autoOpen: false,
        draggable: false,
        modal: true,
        resizable: false,
        title: "Insert Code",
        open: function() {
          return _this.qtCodeBox.focus();
        },
        close: function() {
          _this.qtCodeBox.val('');
          return _this.commentBox.focus();
        }
      });
      this.qtCodeBox = $('#quicktag-code-box');
      $('<button class="quicktag" id="quicktag-close" title="Close Tags">Close Tags</button>').appendTo(this.qtContainer).bind("click", function(e) {
        var _results;
        e.preventDefault();
        _results = [];
        while (_this.qtStack.length !== 0) {
          _this.commentBox[0].selectionStart = _this.commentBox[0].selectionEnd;
          _results.push(_this.qtStack.pop().insertTag());
        }
        return _results;
      });
      return $("a.comment-quote-link").bind("click", function(e) {
        var comment, commentBoxVal, commentId, commentQuote, cursorPos, endPos, newScrollHeight, scrollDiff, scrollHeight, scrollTop, startPos;
        e.preventDefault();
        commentId = $(this).attr("id").split("-")[3];
        comment = $(this).data("comment").replace(/\\\\/g, '\\').replace(/\\'/g, "'").replace(/\\n/g, '\n');
        commentQuote = ("[quote comment=" + commentId + "]") + comment + "[/quote]\n\n";
        startPos = wpComments.commentBox[0].selectionStart;
        endPos = wpComments.commentBox[0].selectionEnd;
        scrollTop = wpComments.commentBox[0].scrollTop;
        scrollHeight = wpComments.commentBox[0].scrollHeight;
        commentBoxVal = wpComments.commentBox.val();
        wpComments.commentBox.val(commentBoxVal.substring(0, startPos) + commentQuote + commentBoxVal.substring(endPos, commentBoxVal.length));
        cursorPos = startPos + commentQuote.length;
        wpComments.commentBox[0].selectionStart = cursorPos;
        wpComments.commentBox[0].selectionEnd = cursorPos;
        newScrollHeight = wpComments.commentBox[0].scrollHeight;
        if (newScrollHeight > scrollHeight) {
          scrollDiff = scrollTop + (newScrollHeight - scrollHeight);
        } else {
          scrollDiff = scrollTop + (scrollHeight - newScrollHeight);
        }
        return wpComments.commentBox.scrollTop(scrollDiff).focus();
      });
    }
  };

  $(function() {
    return wpComments.init();
  });

}).call(this);
