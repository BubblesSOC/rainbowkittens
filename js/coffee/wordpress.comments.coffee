# Compile from theme folder: coffee -c -o js/ js/coffee/
$ = jQuery

class CommentFormField
  constructor: (fieldName, @isRequired) ->
    @container = $('#comment-form-' + fieldName)
    @input = $('#' + fieldName)
    @label = @container.children('label').text()
    @errorSpan = $('#' + fieldName + '-error')
    @validation()    
  validation: ->
    @input.bind
      blur: =>
        if @isRequired && @input.val() == ''
          @errorSpan.text(@label + ' cannot be empty!')
        else if errorMsg = @additionalValidation()
          @errorSpan.text(errorMsg)
        else
          @errorSpan.empty()         
  additionalValidation: ->
    # Can be overridden
    false
          
class CommentAuthorField extends CommentFormField
  constructor: ->
    super 'author', true
    
class CommentEmailField extends CommentFormField
  constructor: ->
    super 'email', true  
  additionalValidation: ->
    # Ref: is_email() : wp-includes/formatting.php
    false
    
class CommentUrlField extends CommentFormField
  constructor: ->
    super 'url', false
    
class CommentField extends CommentFormField
  constructor: ->
    super 'comment', true
    

class WPComments
  constructor: ->
    @commentForm = $('#commentform')
    @commentAuthor = new CommentAuthorField()
    @commentEmail = new CommentEmailField()
    @commentUrl = new CommentUrlField()
    @commentBox = new CommentField()
    @commentSubmit = $('#submit')
    @bindQuoteLinks()
  hideCommenterInfo: ->
    @commentAuthor.container.hide()
    @commentEmail.container.hide()
    @commentUrl.container.hide()
  showCommenterInfo: ->
    @commentAuthor.container.show()
    @commentEmail.container.show()
    @commentUrl.container.show()
  bindQuoteLinks: ->
    _commentTextbox = @commentBox.input
    $('a.comment-quote-link').bind 'click', (e) ->
      # Test & make work in IE!
      e.preventDefault()
      commentId = $(this).attr("id").split("-")[3] # comment-quote-link-{commentId}
      comment = $(this).data("comment")
        .replace(/\\\\/g, '\\')
        .replace(/\\'/g, "'")
        .replace(/\\n/g, '\n')
      commentQuote = "[quote comment=#{commentId}]" + comment + "[/quote]\n\n"
      startPos = _commentTextbox[0].selectionStart
      endPos = _commentTextbox[0].selectionEnd
      scrollTop = _commentTextbox[0].scrollTop
      scrollHeight = _commentTextbox[0].scrollHeight
      commentBoxVal = _commentTextbox.val()
      # If text is selected it will be replaced with quote
      _commentTextbox.val(
        commentBoxVal.substring(0, startPos) + 
        commentQuote + 
        commentBoxVal.substring(endPos, commentBoxVal.length)
      )
      cursorPos = startPos + commentQuote.length
      _commentTextbox[0].selectionStart = cursorPos
      _commentTextbox[0].selectionEnd = cursorPos
      newScrollHeight = _commentTextbox[0].scrollHeight
      if newScrollHeight > scrollHeight
        scrollDiff = scrollTop + (newScrollHeight - scrollHeight)
      else
        scrollDiff = scrollTop + (scrollHeight - newScrollHeight)
      _commentTextbox
        .scrollTop(scrollDiff)
        .focus()


$ ->
  window.wpComments = new WPComments()    

### Quicktags edited from Alex King's Quicktags plugin: http://alexking.org/projects/js-quicktags ###    
# class QuickTagButton
#   @element
#   @title
#   @display
#   @idAppend
#   @tagStart
#   @tagEnd
#   @isTagOpen
#   
#   constructor: (@title, @display, @idAppend, @tagStart, @tagEnd) ->
#     @element = $('<button class="quicktag" id="quicktag-' + @idAppend + '" title="' + @title + '">' + @display + '</button>').appendTo(wpComments.qtContainer)
#     @isTagOpen = false
#     @bindClick()
#   bindClick: ->
#     @element.bind "click", (e) =>
#       e.preventDefault()
#       if @idAppend == 'link' && !@isTagOpen
#         wpComments.qtLinkDialog.dialog "option", "buttons", {
#           Ok: =>
#             @saveLink()
#           Cancel: =>
#             wpComments.qtLinkDialog.dialog('close')
#         }
#         wpComments.qtLinkDialog.dialog('open')
#       else
#         @insertTag()
#   insertTag: ->
#     commentBoxVal = wpComments.commentBox.val()
#     startPos = wpComments.commentBox[0].selectionStart
#     endPos = wpComments.commentBox[0].selectionEnd
#     if startPos != endPos
#       # Selection
#       selection = commentBoxVal.substring(startPos, endPos)
#       if @idAppend == 'code'
#         # Escape the selection to be used between the code tags
#         selection = Wordpress.htmlspecialchars(selection)
#       cursorPos = endPos + @tagStart.length + @tagEnd.length
#       wpComments.commentBox[0].selectionStart = cursorPos
#       wpComments.commentBox[0].selectionEnd = cursorPos
#       wpComments.commentBox.val(
#         commentBoxVal.substring(0, startPos) +
#         @tagStart +
#         selection +
#         @tagEnd +
#         commentBoxVal.substring(endPos, commentBoxVal.length)
#       ).focus()
#     else if @idAppend == 'code'
#       wpComments.qtCodeDialog.dialog "option", "buttons", {
#         Ok: =>
#           codeBoxVal = Wordpress.htmlspecialchars(wpComments.qtCodeBox.val())
#           wpComments.commentBox.val(
#             commentBoxVal.substring(0, startPos) +
#             @tagStart +
#             codeBoxVal +
#             @tagEnd +
#             commentBoxVal.substring(endPos, commentBoxVal.length)
#           )
#           cursorPos = endPos + @tagStart.length + codeBoxVal.length + @tagEnd.length
#           wpComments.commentBox[0].selectionStart = cursorPos
#           wpComments.commentBox[0].selectionEnd = cursorPos
#           wpComments.qtCodeDialog.dialog('close')
#         Cancel: =>
#           wpComments.qtCodeDialog.dialog('close')
#       }
#       wpComments.qtCodeDialog.dialog('open')
#     else
#       if @isTagOpen
#         # Close Tag
#         @element.text(@display)
#         @isTagOpen = false
#         tag = @tagEnd
#         @removeTagFromStack()
#       else
#         # Open Tag
#         @element.text('/' + @display)
#         @isTagOpen = true
#         tag = @tagStart
#         wpComments.qtStack.push(this)
#       cursorPos = endPos + tag.length
#       wpComments.commentBox[0].selectionStart = cursorPos
#       wpComments.commentBox[0].selectionEnd = cursorPos
#       wpComments.commentBox.val(
#         commentBoxVal.substring(0, startPos) +
#         tag +
#         commentBoxVal.substring(endPos, commentBoxVal.length)
#       ).focus()
#   saveLink: ->
#     link = wpComments.qtLinkInput.val()
#     if link == 'http://' or link == ''
#       # Error
#       wpComments.qtLinkError.show()
#     else
#       @tagStart = '<a href="' + link + '">'
#       @insertTag()
#       wpComments.qtLinkDialog.dialog('close')
#   removeTagFromStack: ->
#     for qtButton, index in wpComments.qtStack
#       if this == qtButton
#         wpComments.qtStack.splice(index, 1)
#         return
        
# window.wpComments =
#   init: ->
#     @commentBox = $("#comment")
#     @qtContainer = $('<div id="quicktags" />').insertBefore(@commentBox)
#     @qtStack = []
#     
#     @qtBold = new QuickTagButton(
#       'bold/strong',
#       'b',
#       'bold',
#       '<strong>',
#       '</strong>'
#     )
#     
#     @qtItalic = new QuickTagButton(
#       'italic/emphasis',
#       'i',
#       'italic',
#       '<em>',
#       '</em>'
#     )
#     
#     @qtIns = new QuickTagButton(
#       'underline/insert',
#       'u',
#       'underline',
#       '<ins>',
#       '</ins>'
#     )
#     
#     @qtDel = new QuickTagButton(
#       'strike/delete',
#       'del',
#       'delete',
#       '<del>',
#       '</del>'
#     )
#     
#     @qtLink = new QuickTagButton(
#       'link',
#       'link',
#       'link',
#       '<a>',
#       '</a>'
#     )
#     
#     @qtLinkDialog = $('#quicktag-link-dialog').dialog({
#       autoOpen: false,
#       draggable: false,
#       modal: true,
#       resizable: false,
#       title: "Insert Link",
#       open: =>
#         @qtLinkInput.focus()
#       close: =>
#         @qtLinkInput.val('http://')
#         @qtLinkError.hide()
#         @commentBox.focus()
#     })
#     @qtLinkInput = $('#quicktag-link-url')
#     @qtLinkError = $('#quicktag-link-error')
#     
#     @qtCode = new QuickTagButton(
#       'code',
#       'code',
#       'code',
#       '<code>',
#       '</code>'
#     )
#     
#     @qtCodeDialog = $('#quicktag-code-dialog').dialog({
#       autoOpen: false,
#       draggable: false,
#       modal: true,
#       resizable: false,
#       title: "Insert Code",
#       open: =>
#         @qtCodeBox.focus()
#       close: =>
#         @qtCodeBox.val('')
#         @commentBox.focus()
#     })
#     @qtCodeBox = $('#quicktag-code-box')
#     
#     $('<button class="quicktag" id="quicktag-close" title="Close Tags">Close Tags</button>')
#       .appendTo(@qtContainer)
#       .bind "click", (e) =>
#         e.preventDefault()
#         while @qtStack.length != 0
#           @commentBox[0].selectionStart = @commentBox[0].selectionEnd
#           @qtStack.pop().insertTag()
#       
#     $("a.comment-quote-link").bind "click", (e) ->
#       # Test & make work in IE!
#       e.preventDefault()
#       commentId = $(this).attr("id").split("-")[3] # comment-quote-link-{commentId}
#       comment = $(this).data("comment")
#         .replace(/\\\\/g, '\\')
#         .replace(/\\'/g, "'")
#         .replace(/\\n/g, '\n')
#       commentQuote = "[quote comment=#{commentId}]" + comment + "[/quote]\n\n"
#       startPos = wpComments.commentBox[0].selectionStart
#       endPos = wpComments.commentBox[0].selectionEnd
#       scrollTop = wpComments.commentBox[0].scrollTop
#       scrollHeight = wpComments.commentBox[0].scrollHeight
#       commentBoxVal = wpComments.commentBox.val()
#       # If text is selected it will be replaced with quote
#       wpComments.commentBox
#         .val(
#           commentBoxVal.substring(0, startPos) + 
#           commentQuote + 
#           commentBoxVal.substring(endPos, commentBoxVal.length)
#         )
#       cursorPos = startPos + commentQuote.length
#       wpComments.commentBox[0].selectionStart = cursorPos
#       wpComments.commentBox[0].selectionEnd = cursorPos
#       newScrollHeight = wpComments.commentBox[0].scrollHeight
#       if newScrollHeight > scrollHeight
#         scrollDiff = scrollTop + (newScrollHeight - scrollHeight)
#       else
#         scrollDiff = scrollTop + (scrollHeight - newScrollHeight)
#       wpComments.commentBox
#         .scrollTop(scrollDiff)
#         .focus()