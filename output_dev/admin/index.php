<?php require_once "./app.php"; ?>
<html>
<head>
  <link href="./css/bootstrap.css" rel="stylesheet">

  <link href="./css/bootstrap-responsive.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="../assets/js/html5shiv.js"></script>
  <![endif]-->


 <title>EimerStahl Administration</title>
  <style>
    body {
      padding-top: 60px;
      padding-bottom: 40px;
    }

    .sidebar-nav {
      padding: 9px 0;
    }

    /* Hide the content templates. */
    textarea.template {
      display:none;
    }

    #content-display {
      white-space: pre;
      border: 1px solid #cccccc;
      background-color: #ffffff;
      overflow: scroll;
    }

    .text-content {
      padding: 10px;
      font-family: monospace;
      color: #222222;
      line-height: 1.3;
      font-size: 12px;
      width: 100%;
      min-height: 400px;
    }

    .alert {
      display: none;
      font-size: 14px;
    }

    #file-listing h5 {
      margin: 0 0 0 10px;
    }

    .mode-content-view,
    .mode-content-edit,
    .mode-content-create,
    .mode-content-delete {
      display: none;
    }
    /* style upload fields cross browser*/
    .upload-label {
      overflow:hidden;
    }
    .upload-label input[type="file"] {
      position: fixed;
      top: -1000px;
    }
    .upload-label.error {
      border-color: #ff0000;
    }
    .upload-button {
      display: block;
      margin-top: 10px;
    }
    .validation-errors {
      margin-top: 10px;
    }
    #content-display {
      background: #ccc;
    }

  </style>
</head>

<body>

   <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="brand" href="/admin/">EimerStahl Administration</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <!--<li><a id="nav-content-edit" class="application-nav" href="/admin">Edit</a></li>-->
              <li><a id="nav-content-create" href="create" class="application-nav">Create</a></li>
              <li><a  id="nav-purge-modal"href="#purgeModal" class="application-nav" data-toggle="modal" >Clear Site Cache</a></li>
              <li><a id="nav-content-rebuild" href="rebuild" class="application-nav">Rebuild</a></li>
              <li><a href="/" target="_blank">Live site</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav" id="file-listing">
            <h5>Content list</h5>
            <ul class="nav nav-list">
              <!-- Content is loaded from POST on page load. -->
            </ul>
          </div><!--/.well -->
        </div><!--/span-->


        <div class="span9">
          <div class="hero-unit">
            <div id="action-message" class="alert  ">

              <strong></strong>
              <span></span>
            </div>
            <button name="upload-files" id="upload-files" class="btn btn-success upload-files">Upload Files</button>
            <div id="fileform"></div>
            <div id="files-uploaded" style="display:none;">
            <h4>Filenames:</h4>
            </div>
          </div>
          <div class="hero-unit">




            <div class="control-group mode-content-view">
              <div class="controls">
                <button name="button1id" class="btn btn-success content-edit action-edit">Edit</button>
                <button name="button2id" class="btn btn-danger content-delete action-delete">delete</button>
              </div>
            </div>
            <div id='content-display' class="text-content mode-content-view"></div>
            <form id="delete-content-form" action="./app.php" method="post" class="mode-content-delete" >
              <h3>Delete Page: are you sure?</h3>
              <div class="control-group">
                <div class="controls">
                  <button name="button1id" class="btn btn-success content-save action-delete">delete</button>

                </div>
              </div>
              <!-- File Button -->
              <input type="hidden" value="delete" name="action">
              <input type="hidden" value="" name="file" id="content-file-path-delete">
            </form>

            <form id="edit-content-form" action="./app.php" method="post" class="mode-content-edit">
              <h3>Edit Page</h3>
              <div class="control-group">
                <div class="controls">
                  <button name="button1id" class="btn btn-success content-save action-edit">Save</button>
                  <button name="button2id" class="btn btn-danger content-edit-cancel">Cancel</button>
                </div>
              </div>
              <!-- File Button -->
              <input type="hidden" value="edit" name="action">
              <input type="hidden" value="" name="file" id="content-file-path">

              <div class="controls">
                <textarea id="content-textarea" class="text-content" name="content"></textarea>
              </div>
            </form> <!-- #edit-content-form -->


            <form id="add-content-form" action="./app.php" method="post" class="mode-content-create">
              <input type="hidden" value="create" name="action">
              <h3>Create Page</h3>
              <div class="control-group">
                  <p class="help-block">Enter the filename for the the post that you are
                    creating.</p>
                <label class="control-label" for="contentTitle">Post filename</label>
                <div class="controls">
                  <input id="textinput" name="contentTitle" type="text" placeholder="Enter your file name." class="input-xlarge">
                </div>
              </div>

              <!-- Select Basic -->
              <div class="control-group">
                <p class="help-block">Select which page type you are creating.</p>
                <label class="control-label" for="selectbasic">Select content type</label>
                <div class="controls">
                  <!-- hard coded for only these two content types -->
                  <select id="content-type" name="contentType" class="input-xlarge">
                      <option value="/news/">News</option>
                      <option value="/lawyers/">Lawyers</option>
                  </select>
                </div>
              </div>



              <div class="controls">
              <p class="help-block">Edit the YAML data and body content for your post.</p>

                <textarea id="content-add-textarea" class="text-content" name="content"></textarea>
              </div>

              <div class="control-group">
                <div class="controls">
                  <button name="button1id" class="btn btn-success content-create-save">Save</button>
                  <button name="button2id" class="btn btn-danger content-create-cancel">Cancel</button>
                </div>
              </div>
              <textarea id="content-add-textarea-default" class="template" name="template">---
layout: post

image:
title:
author:
source:
date: SS:MM:HH DD-MM-YYYY
teaser:
pdf:
---
Page content goes here.
              </textarea>
             <textarea id="content-add-textarea-lawyer" class="template" name="template">---
layout: profile

title:
image:
phone:
email:
vcard:
awards:
---
Page content goes here

              </textarea>

            </form><!-- #add-content-form -->



        </div><!--/span-->
      </div><!--/row-->
      </div>

      <hr>

      <footer>
        <p>&copy; <a href="http://palantir.net">Palantir.net</a> 2014</p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- purgeModal -->
<div id="purgeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3 id="myModalLabel">Clear Site Cache</h3>
  </div>
  <div class="modal-body">
    <p>Are you sure? This will clear the live site. You should only do this when you are ready to rebuild the site. </p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn btn-primary" id="nav-content-deletesite">Clear Site Cache</button>
  </div>
</div>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script src="./js/bootstrap.js"></script>
  <script src="./js/jquery.form.min.js"></script>
  <script src="./js/jquery.validate.min.js"></script>
  <script src="./js/additional-methods.min.js"></script>
  <script src="./js/app.js"></script>

</body>
</html>
