<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth Goat Nodes</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; }
        .form-container { width: 80%; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="file"] { padding: 5px; }
        input[type="color"], input[type="checkbox"] { margin-right: 10px; }
        button { padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="form-container">
	<h2>Simple-Earth Goat Nodes Uploader Home</h2>
	<p>Use this page to upload your animals CSV (from FarmOS - Animal Assets - Export CSV (all including CSV)</p>
	<p>Optionally, you can first upload images of your animals, on <a href="upload_images.php">this page</a></p>
        <form action="process_csv.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">CSV File:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <div class="form-group">
                <label for="male_color">Male Color:</label>
                <input type="color" id="male_color" name="male_color" value="#D8BFD8" required>
            </div>
            <div class="form-group">
                <label for="female_color">Female Color:</label>
                <input type="color" id="female_color" name="female_color" value="#98FB98" required>
            </div>
            <div class="form-group">
                <input type="checkbox" id="verbose_logging" name="verbose_logging">
                <label for="verbose_logging">Enable Verbose Logging</label>
            </div>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
