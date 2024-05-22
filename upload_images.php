<?php
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

            // Log the upload
            $log_content = "Image uploaded: {$animal_name}.jpg (" . date('Y-m-d H:i:s') . ")\n";
            file_put_contents($log_file, $log_content, FILE_APPEND);
        }
    }

    echo "Images uploaded successfully.";
}

// Get all images in the images directory
$images = glob("images/*.{jpg,png}", GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth Goat Nodes Image Uploader</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { padding: 5px; }
        .form-group input[type="text"] { width: 100%; margin-top: 5px; }
        .form-group button { padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .form-group button:hover { background-color: #218838; }
        .grid-container { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-top: 20px; }
        .grid-item { text-align: center; }
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
    <h1>Simple-Earth Goat Nodes Image Uploader</h1>
    <p>This form is for uploading images of the goats present in the CSV. Images will be retained, or overwritten if the same name is supplied for a new image (effectively replacing a photo of your animal). All images will be converted to fit 150x150 pixels, and may appear distorted. Upload images as square as possible, and larger than 150x150px to get the best result. When completed, click back to home and upload your FarmOS animal exported CSV to see these animals appear in the output. Simple, with no warranties. =)</p>
    <form action="upload_images.php" method="post" enctype="multipart/form-data">
        <div id="file-input-container" class="form-group">
            <label>Animal Image:</label>
            <input type="file" name="images[]" accept=".jpg, .jpeg, .png" required>
            <label>Animal Name:</label>
            <input type="text" name="animal_names[]" required>
        </div>
        <button type="button" onclick="addFileInput()">Add Another Image</button>
        <button type="submit">Upload Images</button>
    </form>
    <p><a href="index.php">Home</a></p>
    
    <?php if ($images): ?>
        <h2>Currently Uploaded Images</h2>
        <div class="grid-container">
            <?php foreach ($images as $image): ?>
                <div class="grid-item">
                    <img src="<?= $image ?>" alt="<?= basename($image) ?>" style="width: auto; height: auto; max-width: 100%; max-height: 150px;">
                    <p><?= basename($image) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
