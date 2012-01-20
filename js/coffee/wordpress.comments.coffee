# Compile from theme folder: coffee -c -o js/ js/coffee/
# Quicktags edited from Alex King's Quicktags plugin: http://alexking.org/projects/js-quicktags
$ = jQuery
    
class QuickTagButton
  @element
  @title
  @display
  @idAppend
  @tagStart
  @tagEnd
  @isTagOpen
  
  constructor: (@title, @display, @idAppend, @tagStart, @tagEnd) ->
    @element = $('<button class="quicktag" id="quicktag-' + @idAppend + '" title="' + @title + '">' + @display + '</button>').appendTo(wpComments.qtContainer)
    @isTagOpen = false
    @bindClick()
  bindClick: ->
    @element.bind "click", (e) =>
      e.preventDefault()
      if @idAppend == 'link' && !@isTagOpen
        wpComments.qtLinkDialog.dialog "option", "buttons", {
          Ok: =>
            @saveLink()
          Cancel: =>
            wpComments.qtLinkDialog.dialog('close')
        }
        wpComments.qtLinkDialog.dialog('open')
      else
        @insertTag()
  insertTag: ->
    commentBoxVal = wpComments.commentBox.val()
    startPos = wpComments.commentBox[0].selectionStart
    endPos = wpComments.commentBox[0].selectionEnd
    if startPos != endPos
      # Selection
      selection = commentBoxVal.substring(startPos, endPos)
      if @idAppend == 'code'
        # Escape the selection to be used between the code tags
        selection = Wordpress.htmlspecialchars(selection)
      cursorPos = endPos + @tagStart.length + @tagEnd.length
      wpComments.commentBox[0].selectionStart = cursorPos
      wpComments.commentBox[0].selectionEnd = cursorPos
      wpComments.commentBox.val(
        commentBoxVal.substring(0, startPos) +
        @tagStart +
        selection +
        @tagEnd +
        commentBoxVal.substring(endPos, commentBoxVal.length)
      ).focus()
    else if @idAppend == 'code'
      wpComments.qtCodeDialog.dialog "option", "buttons", {
        Ok: =>
          codeBoxVal = Wordpress.htmlspecialchars(wpComments.qtCodeBox.val())
          wpComments.commentBox.val(
            commentBoxVal.substring(0, startPos) +
            @tagStart +
            codeBoxVal +
            @tagEnd +
            commentBoxVal.substring(endPos, commentBoxVal.length)
          )
          cursorPos = endPos + @tagStart.length + codeBoxVal.length + @tagEnd.length
          wpComments.commentBox[0].selectionStart = cursorPos
          wpComments.commentBox[0].selectionEnd = cursorPos
          wpComments.qtCodeDialog.dialog('close')
        Cancel: =>
          wpComments.qtCodeDialog.dialog('close')
      }
      wpComments.qtCodeDialog.dialog('open')
    else
      if @isTagOpen
        # Close Tag
        @element.text(@display)
        @isTagOpen = false
        tag = @tagEnd
        @removeTagFromStack()
      else
        # Open Tag
        @element.text('/' + @display)
        @isTagOpen = true
        tag = @tagStart
        wpComments.qtStack.push(this)
      cursorPos = endPos + tag.length
      wpComments.commentBox[0].selectionStart = cursorPos
      wpComments.commentBox[0].selectionEnd = cursorPos
      wpComments.commentBox.val(
        commentBoxVal.substring(0, startPos) +
        tag +
        commentBoxVal.substring(endPos, commentBoxVal.length)
      ).focus()
  saveLink: ->
    link = wpComments.qtLinkInput.val()
    if link == 'http://' or link == ''
      # Error
      wpComments.qtLinkError.show()
    else
      # Shorten Link
      # $.ajax {
      #   url: "/shorten.php?url=" + link,
      #   context: this,
      #   success: (response) ->
      #     if response.status_txt == "INVALID_URI"
      #       wpComments.qtLinkError.show()
      #     else
      #       if response.status_code == 200
      #         link = response.data.url
      #       @tagStart = '<a href="' + link + '">'
      #       @insertTag()
      #       wpComments.qtLinkDialog.dialog('close')
      #   error: ->
      #     @tagStart = '<a href="' + link + '">'
      #     @insertTag()
      #     wpComments.qtLinkDialog.dialog('close')
      # }
      @tagStart = '<a href="' + link + '">'
      @insertTag()
      wpComments.qtLinkDialog.dialog('close')
  removeTagFromStack: ->
    for qtButton, index in wpComments.qtStack
      if this == qtButton
        wpComments.qtStack.splice(index, 1)
        return

window.wpComments =
  init: ->
    @commentBox = $("#comment")
    @qtContainer = $('<div id="quicktags" />').insertBefore(@commentBox)
    @qtStack = []
    
    @qtBold = new QuickTagButton(
      'bold/strong',
      'b',
      'bold',
      '<strong>',
      '</strong>'
    )
    
    @qtItalic = new QuickTagButton(
      'italic/emphasis',
      'i',
      'italic',
      '<em>',
      '</em>'
    )
    
    @qtIns = new QuickTagButton(
      'underline/insert',
      'u',
      'underline',
      '<ins>',
      '</ins>'
    )
    
    @qtDel = new QuickTagButton(
      'strike/delete',
      'del',
      'delete',
      '<del>',
      '</del>'
    )
    
    @qtLink = new QuickTagButton(
      'link',
      'link',
      'link',
      '<a>',
      '</a>'
    )
    
    @qtLinkDialog = $('#quicktag-link-dialog').dialog({
      autoOpen: false,
      draggable: false,
      modal: true,
      resizable: false,
      title: "Insert Link",
      open: =>
        @qtLinkInput.focus()
      close: =>
        @qtLinkInput.val('http://')
        @qtLinkError.hide()
        @commentBox.focus()
    })
    @qtLinkInput = $('#quicktag-link-url')
    @qtLinkError = $('#quicktag-link-error')
    
    @qtCode = new QuickTagButton(
      'code',
      'code',
      'code',
      '<code>',
      '</code>'
    )
    
    @qtCodeDialog = $('#quicktag-code-dialog').dialog({
      autoOpen: false,
      draggable: false,
      modal: true,
      resizable: false,
      title: "Insert Code",
      open: =>
        @qtCodeBox.focus()
      close: =>
        @qtCodeBox.val('')
        @commentBox.focus()
    })
    @qtCodeBox = $('#quicktag-code-box')
    
    $('<button class="quicktag" id="quicktag-close" title="Close Tags">Close Tags</button>')
      .appendTo(@qtContainer)
      .bind "click", (e) =>
        e.preventDefault()
        while @qtStack.length != 0
          @commentBox[0].selectionStart = @commentBox[0].selectionEnd
          @qtStack.pop().insertTag()
      
    $("a.comment-quote-link").bind "click", (e) ->
      # Test & make work in IE!
      e.preventDefault()
      commentId = $(this).attr("id").split("-")[3] # comment-quote-link-{commentId}
      comment = $(this).data("comment")
        .replace(/\\\\/g, '\\')
        .replace(/\\'/g, "'")
        .replace(/\\n/g, '\n')
      commentQuote = "[quote comment=#{commentId}]" + comment + "[/quote]\n\n"
      startPos = wpComments.commentBox[0].selectionStart
      endPos = wpComments.commentBox[0].selectionEnd
      scrollTop = wpComments.commentBox[0].scrollTop
      scrollHeight = wpComments.commentBox[0].scrollHeight
      commentBoxVal = wpComments.commentBox.val()
      # If text is selected it will be replaced with quote
      wpComments.commentBox
        .val(
          commentBoxVal.substring(0, startPos) + 
          commentQuote + 
          commentBoxVal.substring(endPos, commentBoxVal.length)
        )
      cursorPos = startPos + commentQuote.length
      wpComments.commentBox[0].selectionStart = cursorPos
      wpComments.commentBox[0].selectionEnd = cursorPos
      newScrollHeight = wpComments.commentBox[0].scrollHeight
      if newScrollHeight > scrollHeight
        scrollDiff = scrollTop + (newScrollHeight - scrollHeight)
      else
        scrollDiff = scrollTop + (scrollHeight - newScrollHeight)
      wpComments.commentBox
        .scrollTop(scrollDiff)
        .focus()

$ ->
  wpComments.init()