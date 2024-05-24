<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_image'])) {
        unlink($_POST['delete_image']);
        $message = "Image deleted: " . htmlspecialchars($_POST['delete_image']);
    } elseif (isset($_POST['delete_all_images'])) {
        array_map('unlink', glob("images/*.{jpg,png}", GLOB_BRACE));
        $message = "All images deleted.";
    } elseif (isset($_POST['delete_upload'])) {
        unlink($_POST['delete_upload']);
        $message = "Upload deleted: " . htmlspecialchars($_POST['delete_upload']);
    } elseif (isset($_POST['delete_all_uploads'])) {
        array_map('unlink', glob("uploads/*.csv"));
        $message = "All uploads deleted.";
    } elseif (isset($_POST['delete_log'])) {
        unlink($_POST['delete_log']);
        $message = "Log deleted: " . htmlspecialchars($_POST['delete_log']);
    }
}

// Get all images and uploads
$images = glob("images/*.{jpg,png}", GLOB_BRACE);
$uploads = glob("uploads/*.csv");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoatNodes Cleanup</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .grid-container { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; margin-top: 20px; }
        .grid-item { text-align: center; background-color: #f8f9fa; border: 1px solid #ccc; border-radius: 5px; padding: 10px; }
        .grid-item img { width: auto; height: auto; max-width: 100%; max-height: 150px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        .form-group button { padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .form-group button:hover { background-color: #c82333; }
        .alert { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #28a745; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>GoatNodes Cleanup</h1>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        <?php if (isset($message)): ?>
            <div class="alert"><?= $message ?></div>
        <?php endif; ?>

        <h2>Images</h2>
        <form action="cleanup.php" method="post">
            <button type="submit" name="delete_all_images">Delete All Images</button>
        </form>
        <div class="grid-container">
            <?php if ($images): ?>
                <?php foreach ($images as $image): ?>
                    <div class="grid-item">
                        <img src="<?= $image ?>" alt="<?= basename($image) ?>">
                        <p><?= basename($image) ?></p>
                        <form action="cleanup.php" method="post">
                            <input type="hidden" name="delete_image" value="<?= $image ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No images found.</p>
            <?php endif; ?>
        </div>

        <h2>Uploads</h2>
        <form action="cleanup.php" method="post">
            <button type="submit" name="delete_all_uploads">Delete All Uploads</button>
        </form>
        <div class="grid-container">
            <?php if ($uploads): ?>
                <?php foreach ($uploads as $upload): ?>
                    <div class="grid-item">
                        <p><?= basename($upload) ?></p>
                        <form action="cleanup.php" method="post">
                            <input type="hidden" name="delete_upload" value="<?= $upload ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No uploads found.</p>
            <?php endif; ?>
        </div>

        <h2>Logs</h2>
        <div class="grid-container">
            <?php
            $logs = ['parent_map.log', 'goatnodes.log'];
            foreach ($logs as $log): 
                if (file_exists($log)): ?>
                    <div class="grid-item">
                        <p><?= $log ?></p>
                        <a href="<?= $log ?>" download>Download</a>
                        <form action="cleanup.php" method="post">
                            <input type="hidden" name="delete_log" value="<?= $log ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endif;
            endforeach; ?>
        </div>
    </div>
</body>
</html>
