<!DOCTYPE html>
<html>
<head>
  <title>Simple WYSIWYG Editor with File Upload</title>
  <style>
    .editor {
      width: 100%;
      min-height: 200px; /* Set minimum height */
      border: 1px solid #ccc;
      padding: 10px;
      box-sizing: border-box;
      overflow-y: auto; /* Add scrollbar if content exceeds height */
    }
    .toolbar {
      margin-bottom: 10px;
    }
    .toolbar button,label {
      margin-right: 0.1rem;
      background:transparent;
      border-radius:0.5rem;
      border:none;
      cursor:pointer;
      color:black;
      transition: 0.3s all ease;
    }
    .toolbar button:hover,label:hover {

    }
    .toolbar input[type="file"]{
        display:none;
    }
  </style>
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <div class="toolbar">
    <button onclick="formatText('bold')"><i class='bx bx-bold'></i></button>
    <button onclick="formatText('italic')"><i class='bx bx-italic'></i></button>
    <button onclick="formatText('underline')"><i class='bx bx-underline'></i></button>
    <label for="upload_img">
        <button><i class='bx bx-image-alt' ></i></button>
        <input type="file" name="upload_img" accept="image/*" onchange="uploadImage(this)">
    </label>
    <label for="upload_vid">
        <button><i class='bx bx-video'></i></button>
        <input type="file" name="upload_vid" accept="video/*" onchange="uploadVideo(this)">
    </label>
  </div>
  <div contenteditable="true" class="editor" id="editor"></div>
  <?php
    $conn = require __DIR__ . "/database.php";
    // First query
    $stmt = $conn->prepare("SELECT following_user FROM follow_details WHERE requested_user = 'metherealrudra' AND request_condition = 't'");
    // $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $all_users[] = 'metherealrudra';
    while ($row = $result->fetch_assoc()) {
        $all_users[] = $row['following_user'];
    }
    echo count($all_users)."<br>";
    foreach($all_users as $user){
      $stmt = $conn->prepare("SELECT * FROM post_details WHERE username = ? ORDER BY post_id DESC");
      $stmt->bind_param("s", $user);
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()) {
          $posts[] = $row;
      }
    }
    foreach($posts as $p){
      echo $p['post_id']."<br>" ;
    }


  ?>
  <script>
    function formatText(command) {
      document.execCommand(command, false, null);
    }

    function uploadImage(input) {
      var file = input.files[0];
      var reader = new FileReader();
      reader.onloadend = function() {
        var image = document.createElement("img");
        image.src = reader.result;
        image.style.maxWidth = "50%"; // Set maximum width
        document.getElementById("editor").appendChild(image);
      }
      if (file) {
        reader.readAsDataURL(file);
      }
    }

    function uploadVideo(input) {
      var file = input.files[0];
      var reader = new FileReader();
      reader.onloadend = function() {
        var video = document.createElement("video");
        video.src = reader.result;
        video.controls = true;
        video.width = "100%";
        video.height = "200";
        document.getElementById("editor").appendChild(video);
      }
      if (file) {
        reader.readAsDataURL(file);
      }
    }

    // Adjust editor height based on content
    var editor = document.getElementById("editor");
    editor.addEventListener("input", function() {
      editor.style.height = "auto";
      editor.style.height = Math.max(editor.scrollHeight, 200) + "px";
    });
  </script>
</body>
</html>
