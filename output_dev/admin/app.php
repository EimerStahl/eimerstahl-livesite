<?php

/**
 * @file
 * Controller for the Sculpin administration.
 *
 * @copyright (c) Copyright 2014 Palantir.net
 */

/* *********************************************** */
/* Configuration                                   */
/* *********************************************** */


// @NOTE this is set relative to the actual directory, not the web path.
// This may need to be updated in different environments.
define('CONTENT_PATH', '../../source');

// These are the content paths that can be edited or added to.
$editable_content_paths = array(
  'Landing Pages' => '/',
  'News' => '/news/',
  'Lawyers' => '/lawyers/'
);

// Upload path for non-assigned content. Not sure is this will be needed long
// term.
$asset_path = '/assets';

// Path to the sculpin binary.
define('SCULPIN_PATH', '/usr/home/eimerstahl/bin/sculpin/bin/sculpin');

define('ALERT_SUCCESS', 'success');
define('ALERT_INFO', 'info');
define('ALERT_WARNING', 'warning');
define('ALERT_DANGER', 'danger');


/* *********************************************** */
/* Application                                     */
/* *********************************************** */

// Controller.
if (! empty($_POST['action'])) {
  switch ($_POST['action']) {
    case 'list':
      $output = filesList($editable_content_paths);
      break;
    case 'get':
      $output = fileGet($_POST);
      break;
    case 'edit':
      $output = fileEdit($_POST);
      break;
    case 'delete':
      $output = fileDelete($_POST);
      break;
    case 'deleteSite':
      $output = siteDelete($_POST);
      break;
    case 'create':
      $output = fileCreate($_POST);
      break;
    case 'upload':
      $output = fileUpload($_POST);
      break;
    case 'rebuild':
      $output = sculpinGenerate();
      break;
  }
  print $output;
  exit();
}



/**
 * Get a complete list of files
 *
 * @param array $editable_content_paths
 *   Array directories to read from.
 *
 * @return array
 *   Array of files by directory.
 */
function filesGet($editable_content_paths) {
  $files = array();
  $excluded_files = array('.', '..', '.DS_Store', 'news.html', 'lawyers.md');

  foreach ($editable_content_paths as $name => $path) {
    $directory[$name] = array();
    $contents = scandir(CONTENT_PATH . $path);
    foreach ($contents as $file) {
      if (! in_array($file, $excluded_files) ) {
        if (! is_dir(CONTENT_PATH . $path . $file)) {
          $directory[$name][$path . $file] = $file;
        }
      }
    }
  }
  return $directory;
}


/**
 * Display a list of file names.
 *
 * @NOTE this doesn't produce JSON so that it can be reused at the HTML level.
 */
function filesList($editable_content_paths) {
  $directory = filesGet($editable_content_paths);
  $output = '';
  foreach ($directory as $name => $files) {
    $output .= "<li class='nav-header'>$name</li>\n";
    foreach ($files as $path => $file) {
      $output .= "<li><a class='file' href='$path'>$file</a></li>\n";
    }
  }
  return $output;
}


// Get a file.
// @TODO ensure that this file is not outside of the content directory.
// @NOTE security risk!

function fileGet($data)  {
  $path = escapeshellcmd($data['file']);
  $content = file_get_contents(CONTENT_PATH . $path);
  $return = array(
    'content' => $content,
    'file' => $path
  );
  return json_encode($return);
}

//File upload function for both edit and create
function fileUpload($data) {
    // Check for uploads with this post.
    // This could be cleaned up to be less repititious - Bill
  if (! empty($_FILES['uploadFile'])) {
    $destination = CONTENT_PATH . '/assets/';

    // Get the file extension of $_FILES['uploadFile']['tmp_name']
    // @TODO do this with mimetypes $_FILES['file']['type'] ?

    // @TODO restrict file mimetypes ?
    // @TODO check for upload errors
    $extension = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    switch ($extension) {
      case 'pdf':
        $destination .= 'PDFs';
        break;
      case 'jpg':
      case 'gif':
      case 'png':
      case 'jpeg':
        $destination .= 'imgs';
        break;
      case 'vcf':
      case 'vcard':
        $destination .= 'vcard';
        break;
      default:
        $destination .= 'other';
        break;
    }
    // Set the complete destination.
    $data['uploadFile'] = $destination . '/' . $_FILES['uploadFile']['name'];
    move_uploaded_file($_FILES['uploadFile']['tmp_name'], $data['uploadFile']);
  }
  if (! empty($_FILES['uploadFileBio'])) {
    //profile images
    $destination = CONTENT_PATH . '/assets/';
    $extension = pathinfo($_FILES['uploadFileBio']['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    switch ($extension) {
      case 'jpg':
      case 'gif':
      case 'png':
      case 'jpeg':
        $destination .= 'imgs/profile';
        break;
    }
    // Set the complete destination.
    $data['uploadFileBio'] = $destination . '/' . $_FILES['uploadFileBio']['name'];
    move_uploaded_file($_FILES['uploadFileBio']['tmp_name'], $data['uploadFileBio']);
  }
  if (! empty($_FILES['uploadFileAward'])) {
    //Award images
    $destination = CONTENT_PATH . '/assets/';
    $extension = pathinfo($_FILES['uploadFileAward']['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    switch ($extension) {
      case 'jpg':
      case 'gif':
      case 'png':
      case 'jpeg':
        $destination .= 'imgs/profile/awards';
        break;
    }
    // Set the complete destination.
    $data['uploadFileAward'] = $destination . '/' . $_FILES['uploadFileAward']['name'];
    move_uploaded_file($_FILES['uploadFileAward']['tmp_name'], $data['uploadFileAward']);
  }
  if (! empty($_FILES['uploadSidebar'])) {
    //media Sidebar images
    $destination = CONTENT_PATH . '/assets/';
    $extension = pathinfo($_FILES['uploadSidebar']['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    switch ($extension) {
      case 'jpg':
      case 'gif':
      case 'png':
      case 'jpeg':
        $destination .= 'imgs/media';
        break;
    }
    // Set the complete destination.
    $data['uploadSidebar'] = $destination . '/' . $_FILES['uploadSidebar']['name'];
    move_uploaded_file($_FILES['uploadSidebar']['tmp_name'], $data['uploadSidebar']);
  }
  if (! empty($_FILES['uploadFileInline'])) {
    //Inline images
    $destination = CONTENT_PATH . '/assets/';
    $extension = pathinfo($_FILES['uploadFileInline']['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    switch ($extension) {
      case 'jpg':
      case 'gif':
      case 'png':
      case 'jpeg':
        $destination .= 'imgs/inline';
        break;
    }
    // Set the complete destination.
    $data['uploadFileInline'] = $destination . '/' . $_FILES['uploadFileInline']['name'];
    move_uploaded_file($_FILES['uploadFileInline']['tmp_name'], $data['uploadFileInline']);
  }
  if (empty($_FILES['uploadFile']) && empty($_FILES['uploadFile']) && empty($_FILES['uploadFileInline']) && empty($_FILES['uploadFileBio']) && empty($_FILES['uploadFileAward']) && empty($_FILES['uploadSidebar'])) {
     $message = array(
    'level' => ALERT_WARNING,
    'type' => 'Error!',
    'content' => "No files uploaded."
    );

  } else {
    $message = array(
    'level' => ALERT_SUCCESS,
    'type' => 'Success!',
    'content' => "File was successfully uploaded."
    );
  };
  $return = array(
    'message' => $message,
  );
  return json_encode($return);
}
/**
 * Edit a specified file.
 *
 * @TODO ensure that this file is not outside of the content directory.
 * @NOTE security risk!
 *
 *
 * @param array $data
 * @return sting
 */
function fileEdit($data, $path = FALSE) {
  if (! $path){
    $path = CONTENT_PATH . $data['file'];
  }

  // @TODO strip out nefarious content!
  if (! file_put_contents($path, $data['content'])) {
    $message = array(
      'level' => ALERT_DANGER,
      'type' => 'Failure',
      'content' => pathinfo($path, PATHINFO_FILENAME) . " could not be updated."
    );
  }
  else {
    // Read the file back after it is saved.
    if (! $content = file_get_contents(CONTENT_PATH . $path)) {
      $message = array(
        'level' => ALERT_DANGER,
        'type' => 'Failure',
        'content' => pathinfo($path, PATHINFO_FILENAME) . " could not be updated."
      );
    }
    else {
      $message = array(
        'level' => ALERT_SUCCESS,
        'type' => 'Success!',
        'content' => pathinfo($path, PATHINFO_FILENAME) . " was successfully updated."
      );
    }
  }

  $return = array(
    'message' => $message,
    'content' => $content,
  );
  return json_encode($return);
}
function siteDelete() {

  $lawyersDir = "../../output_dev/lawyers/";
  $mediaDir = "../../output_dev/news/";
    system('/bin/rm -rf ' . escapeshellarg($lawyersDir));
    system('/bin/rm -rf ' . escapeshellarg($mediaDir));
    //TODO: check for errors here
    $message = array(
      'level' => ALERT_SUCCESS,
      'type' => 'Success!',
      'content' => "Files were deleted"
    );

     $return = array(
    'message' => $message,
  );
  return json_encode($return);

}
function fileDelete($data, $path = FALSE) {
$filename = $data['file'];
$deleteFile = CONTENT_PATH . $data['file'];
  if (file_exists($deleteFile)) {
    if (! unlink($deleteFile))
    {
        $message = array(
          'level' => ALERT_WARNING,
          'type' => 'Error!',
          'content' => $filename . " was not successfully deleted."
        );
    }
    else {
      $message = array(
          'level' => ALERT_SUCCESS,
          'type' => 'Success!',
          'content' => $filename . " was successfully deleted."
        );
    }
  } else {
    $message = array(
          'level' => ALERT_WARNING,
          'type' => 'Error!',
          'content' => $filename . " does not exist."
        );
  }





  $return = array(
    'message' => $message,
  );
  return json_encode($return);
}

/**
 * Create a new file.
 *
 * @TODO consider security concerns.
 * @TODO how to relate uploads with post.
 *
 * @param type $data
 */
function fileCreate($data) {
  // Set the destination based on the type of content.
  $destination = CONTENT_PATH . $data['contentType'];
  // Use a date stamp for a title if somebody forgot a title.
  if (empty($data['contentTitle'])) {
    $data['contentTitle'] = date('Y-m-d', time());
  }
  // Create a unique file name
  $path = file_create_filename($data['contentTitle'] . '.md', $destination);
  $data['file'] = pathinfo($path, PATHINFO_FILENAME);



  // Now save the file
  return fileEdit($data, $path);
}



/**
 * Regenerate the sculpin files.
 * @return type
 */
function sculpinGenerate() {
  // @TODO this should probably be a defined value.
  chdir('../../');
  // Using the current path to the php executable to ensure that Sculpin executes
  // with the same configuration as the current script.
  $command = PHP_BINDIR . '/php ' . SCULPIN_PATH . ' generate 2>&1';

  $stream = popen($command,"r");
  while (! feof($stream)) {
    $content = trim(fgets($stream, 4096));
    if ($content) {
      $output[] = $content;
    }
  }
  pclose($stream);

  // Error handling.
  // @TODO this should really be more sophisiticated. Unfortunately getting the
  // return value of $command doesn't work because of popen(). Unclear on how
  // to find errors in the return outside of just searching.

  $text = implode("\n", $output);
  if (strstr($text, 'Error')) {
    $error = TRUE;
  }

  if (empty($error)) {
    $message = array(
      'level' => ALERT_SUCCESS,
      'type' => 'Success!',
      'content' => "Sculpin has regenerated all of its files."
    );
  }
  else {
    $message = array(
      'level' => ALERT_DANGER,
      'type' => 'Error!',
      'content' => "Sculpin failed to regenerate files. This could be a syntax
        error in a file you edited."
    );
  }


  $return = array(
    'message' => $message,
    'content' => implode("\n", $output)
  );

  return json_encode($return);

}


/**
 * Create a unique filename.
 *
 * @NOTE taken from Drupal 7
 *
 * @param type $basename
 * @param type $directory
 * @return string
 */
function file_create_filename($basename, $directory) {
  // Strip control characters (ASCII value < 32). Though these are allowed in
  // some filesystems, not many applications handle them well.
  $basename = preg_replace('/\p{Cc}+/u', '_', $basename);

  //spaces to hyphens
  $basename = str_replace(array(' '), '-', $basename);
  if (substr(PHP_OS, 0, 3) == 'WIN') {
    // These characters are not allowed in Windows filenames
    $basename = str_replace(array(':', '*', '?', '"', '<', '>', '|'), '_', $basename);
  }

  // A URI or path may already have a trailing slash or look like "public://".
  if (substr($directory, -1) == '/') {
    $separator = '';
  }
  else {
    $separator = '/';
  }

  $destination = $directory . $separator . $basename;

  if (file_exists($destination)) {
    // Destination file already exists, generate an alternative.
    $pos = strrpos($basename, '.');
    if ($pos !== FALSE) {
      $name = substr($basename, 0, $pos);
      $ext = substr($basename, $pos);
    }
    else {
      $name = $basename;
      $ext = '';
    }

    $counter = 0;
    do {
      $destination = $directory . $separator . $name . '_' . $counter++ . $ext;
    } while (file_exists($destination));
  }

  return $destination;
}
