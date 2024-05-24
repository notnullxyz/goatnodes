<?php

// disable caching and stuff, so we can always see the latest images
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['images'])) {
    $log_file = 'goatnodes.log';

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $animal_name = $_POST['animal_names'][$key];
        $image_info = getimagesize($tmp_name);
        $image_type = $image_info[2];

        // Check if the file is a jpg or png
        if ($image_type == IMAGETYPE_JPEG || $image_type == IMAGETYPE_PNG) {
            $target_file = "images/{$animal_name}.jpg";

            // Convert to JPG and resize the image
            if ($image_type == IMAGETYPE_JPEG) {
                $image = imagecreatefromjpeg($tmp_name);
            } else {
                $image = imagecreatefrompng($tmp_name);
            }

            $resized_image = imagescale($image, 150, 150);
            imagejpeg($resized_image, $target_file);

            imagedestroy($image);
            imagedestroy($resized_image);

            // Log the upload$addr = $_SERVER["REMOTE_ADDR"];
			$date = new DateTime();
			$date = $date->format("Y/m/d h:i:s:u");
			$addr = $_SERVER["REMOTE_ADDR"];
			$filesize = filesize($target_file);
			$message = $date . ': src ' . $addr .': ' . "Image uploaded -> {$animal_name}.jpg size: {$filesize} bytes";
			file_put_contents($log_file, $message . PHP_EOL, FILE_APPEND);
        }
    }

    echo "<div class='alert alert-success'>Images uploaded successfully.</div>";
}

// mung up all the images in the images/ dir
$images = glob("images/*.{jpg,png}", GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth Goat Nodes Image Uploader</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input[type="file"],
        .form-group input[type="text"],
        .form-group button { padding: 10px; width: 100%; margin-top: 5px; }
        .form-group button { background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .form-group button:hover { background-color: #218838; }
        .grid-container { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-top: 20px; }
        .grid-item { text-align: center; background-color: #f8f9fa; border: 1px solid #ccc; border-radius: 5px; padding: 10px; }
        .grid-item img { width: auto; height: auto; max-width: 100%; max-height: 150px; border-radius: 5px; }
        .alert { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #28a745; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
    <script>
        function addFileInput() {
            const container = document.getElementById('file-input-container');
            const div = document.createElement('div');
            div.classList.add('form-group');
            div.innerHTML = `
                <label>Animal Image:</label>
                <input type="file" name="images[]" accept=".jpg, .jpeg, .png" required>
                <label>Animal Name:</label>
                <input type="text" name="animal_names[]" required>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <div class="header">
        <h1>Simple-Earth Goat Nodes Image Uploader</h1>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        <p>This is the image uploader only.<br> Note: </p>
        <ul>
            <li>After upload, the file will be named [animal name].jpg - provide accurate animal name as per CSV/Data</li>
            <li>Images will be resized to 150x150px. For better looking results upload 1:1 aspect ratio (square) images no smaller than 150 x 150 pixels</li>
            <li>Images will be retained in a storage directory. Uploading a new image with the same name will overwrite the previous</li>
            <li>Images are not strictly required. A white square placeholder will be shown if no image is available or names mismatch</li>
            <li>GoatNodes is not yet multi-tenanted. To avoid images mixing with other users, run isolated instances.</li>
            <li>There is no security here. Add your own, or risk someone replacing all your goat photos with something unsavoury</li>
            <li>There are no warranties. This software is as-is. =)</li>
        </ul>    
        <form action="upload_images.php" method="post" enctype="multipart/form-data">
            <div id="file-input-container" class="form-group">
                <label>Animal Image:</label>
                <input type="file" name="images[]" accept=".jpg, .jpeg, .png" required>
                <label>Animal Name:</label>
                <input type="text" name="animal_names[]" required>
            </div>
            <button type="button" onclick="addFileInput()">Add Another Image</button>
            <button type="submit">Upload</button>
        </form>
        <p><a href="index.php">Home</a></p>
        
        <?php if ($images): ?>
            <h2>Currently Uploaded Images</h2>
            <div class="grid-container">
                <?php foreach ($images as $image): ?>
                    <div class="grid-item">
                        <img src="<?= $image ?>" alt="<?= basename($image) ?>">
                        <p><?= basename($image) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
