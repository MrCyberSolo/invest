<?php
session_start();
include('includes/connect.php');
include('includes/check-login.php');

$userid_admin = $_SESSION['userid'];
$role = 'admin';

if (isset($_GET['userid'])) {
    $userid = $_GET['userid'];
} else {
    header("Location: chat_details.php?userid=" . urlencode($userid_admin));
    exit();
}

$targetFile = ""; // initialize to empty

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender = $_POST['sender'];
    $message = trim($_POST["message"] ?? "");

    // Handle image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $targetDir = "../asupport/chat/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageTmp = $_FILES["image"]["tmp_name"];
        $imageName = $_FILES["image"]["name"];

        // Validate MIME type & Image Integrity using getimagesize
        $image_info = @getimagesize($imageTmp);
        $allowed_mime_types = ["image/jpeg", "image/png", "image/gif", "image/webp", "image/bmp"];
        
        $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed_extensions = ["jpg", "jpeg", "png", "gif", "webp", "bmp"];

        if ($image_info !== false && in_array($imageFileType, $allowed_extensions) && in_array($image_info['mime'], $allowed_mime_types)) {
            $secureImageName = time() . "_" . bin2hex(random_bytes(8)) . "." . $imageFileType;
            $targetFile = $targetDir . $secureImageName;

            if (move_uploaded_file($imageTmp, $targetFile)) {
                // Store only image path in DB (relative to your web root)
                $message = ""; // Clear message if image uploaded
            } else {
                $targetFile = ""; // reset if move fails
            }
        } else {
            $targetFile = ""; // reset if extension not allowed or malicious file
        }
    }
    $modifiedPath = substr($targetFile, 3);
    if ($message !== "" || $targetFile !== "") {
        $stmt = $con->prepare("INSERT INTO chat (userid, sender, message, image,status) VALUES (?, ?, ?, ?,'1')");
        $stmt->bind_param("ssss", $userid, $sender, $message, $modifiedPath);
        $stmt->execute();
        $stmt->close();
       // Now update all previous messages for this user
        $updateStmt = $con->prepare("UPDATE chat SET status = '1' WHERE userid = ?");
        $updateStmt->bind_param("s", $userid);
        $updateStmt->execute();
        $updateStmt->close();
    }
}

// Fetch chat history
$stmt = $con->prepare("SELECT * FROM chat WHERE userid = ? ORDER BY timestamp ASC");
$stmt->bind_param("s", $userid);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Chat Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.12.1/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  body {
    background-color: #121212;
    color: #ffffff;
    font-family: Arial, sans-serif;
    height: 100vh;
    display: flex;
    flex-direction: column;
    margin: 0;
  }
  .chat-header {
    background-color: #1f1f1f;
    padding: 1rem;
    text-align: center;
    font-weight: bold;
    font-size: 1.2rem;
    border-bottom: 1px solid #333;
  }
  .chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  .chat-message {
    max-width: 75%;
    padding: 0.6rem 1rem;
    border-radius: 15px;
    word-wrap: break-word;
  }
  .sent {
    align-self: flex-end;
    background-color: #0d6efd;
    color: #fff;
    border-bottom-right-radius: 0;
  }
  .received {
    align-self: flex-start;
    background-color: #2c2c2c;
    color: #eee;
    border-bottom-left-radius: 0;
  }
  .chat-input {
    background-color: #1f1f1f;
    padding: 0.75rem;
    border-top: 1px solid #333;
  }
  .chat-input .form-control {
    background-color: #2c2c2c;
    color: white;
    border: none;
  }
  .chat-input .form-control::placeholder {
    color: #aaa;
  }
  .chat-input .btn {
    border-radius: 50%;
  }
  .chat-image {
    max-width: 200px;
    border-radius: 10px;
    margin-top: 5px;
  }
</style>
</head>
<body>

  <div class="chat-header">
    Chat with Support (<?= ucfirst(htmlspecialchars($role)) ?>)
  </div>

  <div class="chat-body" id="chatBox">
  <?php while ($row = $messages->fetch_assoc()): ?>
    <div class="chat-message <?= ($row['sender'] === 'user' ? 'sent' : 'received') ?>">
      <?php if (!empty($row['message'])): ?>
        <div><?= nl2br(htmlspecialchars($row['message'])) ?></div>
      <?php endif; ?>
      <?php if (!empty($row['image'])): ?>
        <img src="../<?= htmlspecialchars($row['image']) ?>" class="chat-image" alt="Image" />
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
  </div>

  <!-- Chat Input Form -->
  <form method="post" class="chat-input" enctype="multipart/form-data">
    <div class="d-flex align-items-center">
      <input type="hidden" name="sender" value="<?= htmlspecialchars($role) ?>" />
      <input type="text" name="message" id="msgInput" class="form-control me-2 rounded-pill" placeholder="Type your message..." autocomplete="off" />
      
      <!-- Hidden Image Input -->
      <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;" />
      
      <!-- Image Icon Trigger -->
      <label for="imageInput" class="btn btn-outline-light me-2 mb-0" title="Send Image">
        <i class="bi bi-image"></i>
      </label>

      <button class="btn btn-primary" type="submit">
        <i class="bi bi-send-fill"></i>
      </button>
    </div>
  </form>

  <script>
    // Scroll to bottom on page load
    const chatBox = document.getElementById("chatBox");
    chatBox.scrollTop = chatBox.scrollHeight;

    // Auto-submit when image selected
    document.getElementById('imageInput').addEventListener('change', function () {
      if (this.files.length > 0) {
        this.form.submit();
      }
    });
  </script>

</body>
</html>
