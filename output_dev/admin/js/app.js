/**
 * @file
 * Javascript controllers for Sculping administration.
 *
 * @copyright (c) Copyright 2014 Palantir.net
 */

$(document).ready(function() {

  // Welcome our users.
  var message = {
    level: 'info',
    type: 'Welcome!',
    content: ' Select a file from sidebar to edit or create from the menu to create new content'
  };
  showMessage(message);


  // Display the file list on page load.
  sidebarBuild();


  /**
   * Trigger the edit mode.
   */
  $('button.content-edit').click(function() {
    // Set a message that they are now creating content
    var message = {
      level: 'warning',
      type: 'Editing:',
      content: 'You are editing: ' + $('#content-file-path').val()
    };
    showMessage(message);
    modeContentEdit();
    return false;
  });
  /**
   * Trigger the delete mode.
   */
  $('button.content-delete').click(function() {
    // Set a message that they are now creating content
    var message = {
      level: 'warning',
      type: 'Editing:',
      content: 'You are deleting: ' + $('#content-file-path').val()
    };
    showMessage(message);
    modeContentDelete();
    return false;
  });

  /**
   * Edit content form submit trigger.
   */
  $('button.content-edit-save').click(function() {
    $(this).parent('form').trigger('submit');
  });


  /**
   * Submit the edit content form.
   */
  $('#edit-content-form').ajaxForm({
    dataType: 'json',
    success: function(data) {
      // Set edit mode.
      $('#nav-content-edit').trigger('click');
      // Append the new content.
      $('#content-display').html(data.content);
      // Switch to the view mode.
      modeContentView();
      showMessage(data.message);
      scrollMessage();
    }
  });
  $('#delete-content-form').ajaxForm({
    dataType: 'json',
    success: function(data) {
      showMessage(data.message);
      $('.mode-content-delete').hide();
      //rebuild sidebar to remove deleted page
      sidebarBuild();
      scrollMessage();
    }
  });
  //purge site files
  //launch modal
  $('#nav-purge-modal').click( function() {
    $('#purgeModal').modal("show");
  });
  $('#nav-content-deletesite').click(function() {
    $.post('app.php', {action: 'deleteSite'}, function(data){
        data = JSON.parse(data);
        $('#content-display').html(data.content);
        showMessage(data.message);
        $('#content-display').show();
        //close the modal
        $('#purgeModal').modal("hide");
      });
  });


  function showUploadForm() {
    $('#fileform').load('fileform.html', function() {
      $('#upload-files').hide();

      //file upload needs ro return filename
      var filename = '';
      var filepath = '';
      //File queue to show files that are ready to upload
      function updateFileQueue(filename, queue) {
        var queueElement = '#upload-ready '+ queue;
        $(queueElement).html(filename);
        $('#upload-ready').show();
      }
      $('#filebutton').change(function(){
            filepath = $(this).val();
            //split out filename
            filename = "filename: "+filepath.split("\\").pop();
            updateFileQueue(filename, ".queue1");
      });
      $('#filebutton2').change(function(){
            filepath = $(this).val();
            //split out filename
            filename = "filename: "+filepath.split("\\").pop();
            updateFileQueue(filename, ".queue2");
      });
      $('#filebutton3').change(function(){
            filepath = $(this).val();
            //split out filename
            filename = "filename: "+filepath.split("\\").pop();
            updateFileQueue(filename, ".queue3");
      });

      //Requirement: display entire path for inline images
      $('.upload-file-inline').change(function(){
          filepath = $(this).val();
            //split out filename
            filename = filepath.split("\\").pop();
            var filenamePath = "/assets/imgs/inline/"+filename;
            updateFileQueue(filenamePath, ".queue4");
      });
            $('#filebutton5').change(function(){
            filepath = $(this).val();
            //split out filename
            filename = "filename: "+filepath.split("\\").pop();
            updateFileQueue(filename, ".queue5");
      });
      //form validation
      jQuery.validator.addMethod("nospace", function(value, element) {
        return value.indexOf(" ") < 0 ;
      }, "filename should not have spaces");
      //Method to check against regex
      jQuery.validator.addMethod("DontAccept", function(value, element, param) {
        return value.match( new RegExp("." + param + "$"));
      }, "filename should not have symbols or punctuation");
      $("#add-file-form").validate({
        errorPlacement: function(error, element) {
          var ErrorDiv = $('.validation-errors');
            error.appendTo(ErrorDiv);
            element.parent('label').addClass("error");
            $('.validation-errors').show();
        },
        rules: {
                uploadFile: {
                  nospace: true,
                  extension: "pdf|vcard|vcf"
                },
                uploadFileBio: {
                  nospace: true,
                  extension: "gif|jpg|jpeg|png"
                },
                uploadFileAward: {
                  nospace: true,
                  extension: "gif|jpg|jpeg|png"
                },
                uploadFileInline: {
                  nospace: true,
                  extension: "gif|jpg|jpeg|png"
                },
                uploadSidebar: {
                  nospace: true,
                  extension: "gif|jpg|jpeg|png"
                }
              },
              messages: {
                uploadFile: {
                  extension: "pdf or vCard: This filetype is not accepted"
                },
                uploadFileBio: {
                  extension: "Bio Photo: This filetype is not accepted"
                },
                uploadFileAward: {
                  extension: "Award: This filetype is not accepted"
                },
                uploadFileInline: {
                  extension: "Inline: This filetype is not accepted"
                },
                uploadSidebar: {
                  extension: "Inline: This filetype is not accepted"
                }
              }
      });
      //load queues into variables
      var q1 = '',
          q2 = '',
          q3 = '',
          q4 = '';
          q5 = '';
      $('#upload-button').click( function() {
        q1 = $('.queue1').text();
        q2 = $('.queue2').text();
        q3 = $('.queue3').text();
        q4 = $('.queue4').text();
        q5 = $('.queue5').text();
      });
      // submit the file upload form
      $('#add-file-form').ajaxForm({
        dataType: 'json',
        success: function(data) {
          // take files listed in queue and move them into files uploaded list.
          if (q1.length > 0) {
          $('#files-uploaded').append("<p>"+q1+"</p>").html();
          }
          if (q2.length > 0) {
            $('#files-uploaded').append("<p>"+q2+"</p>").html();
          }
          if(q3.length > 0) {
            $('#files-uploaded').append("<p>"+q3+"</p>").html();
          }
          if (q4.length > 0) {
            $('#files-uploaded').append("<p>Use this code for inline image:<br>![Image title here]<br>("+q4+")</p>").html();
          }
          if(q5.length > 0) {
            $('#files-uploaded').append("<p>"+q5+"</p>").html();
          }
          $('#files-uploaded').show();
          $('#upload-success').show();

          showMessage(data.message);
          scrollMessage();
          $('#add-file-form').hide();
          $('#upload-another').click( function() {
            refreshUploadForm();
          });
        }
      });
    });
  }
  function refreshUploadForm(){
    showUploadForm();
  }
  $('#upload-files').click( function() {
    showUploadForm();
  });

  /**
   * Cancel a content edit action.
   */
  $('button.content-edit-cancel').click(function() {
    modeContentView();
    var message = {
      level: 'info',
      type: 'Cancled.',
      content: 'You are viewing: ' + $('#content-file-path').val()
    };
    showMessage(message);
    return false;
  });


  /**
   * Content creation.
   */
  $('#nav-content-create').click(function(){
    $('.nav li').removeClass('active');
    $(this).parent('li').addClass('active');
    //clear any previously entered filename
    modeContentCreate();
    $('#textinput').val("");
    $('#content-type').val("/news/");
    // Copy the template into the content area.
    $('#content-add-textarea').val($('#content-add-textarea-default').val());
    // Make sure this is identified as new.
    $('#content-add-textarea').attr('data-file', 'new');
    //listeners for selectbox for template type
    $('#content-type').change(function() {
      if ($(this).val() === "/lawyers/") {
        $('#content-add-textarea').val($('#content-add-textarea-lawyer').val());
      } else if ($(this).val() === "/news/") {
        $('#content-add-textarea').val($('#content-add-textarea-default').val());
      }
    });


    // Set a message that they are now creating content
    var message = {
      level: 'info',
      type: 'Creating:',
      content: 'You are adding new content.'
    };
    showMessage(message);
  });

  /**
   * Save new content form trigger.
   */
  $('button.content-create-save').click(function() {
    $(this).parent('form').trigger('submit');
  });

  /**
   * Submit the content create form.
   */
  $('#add-content-form').ajaxForm({
    dataType: 'json',
    success: function(data) {
      // Set edit mode.
      $('#nav-content-edit').trigger('click');
      // Append the new content.
      $('#content-display').html(data.content);
      //update the edit textarea
      $('#content-textarea').html(data.content);
      // Switch to the view mode.
      //Make Create nav no longer active
      $('#nav-content-create').parent('li').removeClass('active');
      // @NOTE this should be aware of any previous mode as well.
      modeContentView();

      showMessage(data.message);

      //remove ability to edit from here since it breaks.
      //TODO: shall we fix this to immediately go back and edit?
      $('.mode-content-view').hide();
      //rebuild sidebar to show new page
      sidebarBuild();
      scrollMessage();
    }
  });


    // Disable menu navigation for the moment.
    $('a.application-nav').click(function() {
      return false;
    });


    /**
     * Rebuild Sculpin.
     */
    $('#nav-content-rebuild').click(function() {
      $.post('app.php', {action: 'rebuild'}, function(data){
        data = JSON.parse(data);
        $('#content-display').html(data.content);
        showMessage(data.message);
        $('#content-display').show();
      });
    });

});



/**
 * Utility function to display a message.
 *
 * level: success, info, warning, danger
 */
function showMessage(message) {
  $('#action-message').attr('class', 'alert  alert-dismissable alert-' + message.level);
  $('#action-message strong').html(message.type);
  $('#action-message span').html(message.content);
  $('#action-message').show();
}
//scroll to message area
function scrollMessage() {
      $('html, body').animate({
        scrollTop: $("#action-message").offset().top -100
    }, 500);
}
/**
 * Transition to the editing mode.
 */
function modeContentView() {
  $('.mode-content-view').show();
  $('.mode-content-edit').hide();
  $('.mode-content-create').hide();
  $('.mode-content-delete').hide();
}

/**
 * Transition to the editing mode.
 */
function modeContentEdit() {
  $('.mode-content-view').hide();
  $('.mode-content-edit').show();
  $('.mode-content-create').hide();
  $('.mode-content-delete').hide();
}

/**
 * Transition to the editing mode.
 */
function modeContentCreate() {
  $('.mode-content-view').hide();
  $('.mode-content-edit').hide();
  $('.mode-content-create').show();
  $('.mode-content-delete').hide();
}

function modeContentDelete() {
  $('.mode-content-view').hide();
  $('.mode-content-edit').hide();
  $('.mode-content-create').hide();
  $('.mode-content-delete').show();
}
/**
 * Fetch the file content from the specified path.
 *
 * @param string path
 */
function loadFile(path) {
  // Fetch the file.
  $.post('app.php', {action: 'get', file: path}, function(data) {
    data = JSON.parse(data);
    // Put content into the text display.
    $('#content-display').html(data.content);
    // Store the data in the edit form.
    $('#content-textarea').val(data.content);
    // Add the current file path to the editor for use when saving.
    $('#content-file-path').val(data.file);
    //file path for delete file
    $('#content-file-path-delete').val(data.file);
    // Set the view mode.
    modeContentView(data);
    var message = {
      level: 'info',
      type: 'Viewing:',
      content: ' ' + path
    };
    showMessage(message);
    scrollMessage();
    // hide delete button for top level landing pages
    if ((data.file === '/contact.md') || (data.file === '/clients.md') || (data.file === '/firm.md' ) || (data.file === '/index.md')){
      $('button.content-delete').hide();
    } else {
      $('button.content-delete').show();
    }
  });
}


/**
 * Build the list of files in the sidebar.
 */
function sidebarBuild() {
  $.post('./app.php', {action: 'list'}, function(content) {
    $('#file-listing ul').html(content);
    sidebarAttachLinks();
  });
}


/**
 * Attach links on the sidebar.
 */
function sidebarAttachLinks() {
  $('#file-listing li a.file').click(function() {
    // Set this to be the active item.
    $('#file-listing li.active').removeClass('active');
    $(this).parent('li').addClass('active');
    var path = $(this).attr('href');
    loadFile(path);
    return false;
  });
}
