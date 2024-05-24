<?php
//index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth Goat Nodes CSV Uploader</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input[type="file"],
        .form-group input[type="color"],
        .form-group input[type="checkbox"],
        .form-group input[type="submit"] {
            padding: 10px;
            margin-top: 5px;
        }
        .table-container {
            margin-top: 20px;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th,
        .table-container td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .table-container th {
            background-color: #28a745;
            color: white;
        }
    </style>

</head>
<body>
    <div class="header">
        <h1>Simple-Earth Goat Nodes CSV Uploader</h1>
    </div>
    <div class="container">
        <form action="process_csv.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Upload CSV:</label>
                <input type="file" name="csv_file" id="csv_file" required>
            </div>
            <div class="form-group">
                <label for="male_color">Male Color:</label>
                <input type="color" name="male_color" id="male_color" value='#7eb4cf' required>
            </div>
            <div class="form-group">
                <label for="female_color">Female Color:</label>
                <input type="color" name="female_color" id="female_color" value='#ab84a9' required>
            </div>
            <div class="form-group">
                <label for="verbose_logging">
                    <input type="checkbox" name="verbose_logging" id="verbose_logging">
                    Enable Verbose Logging
                </label>
            </div>
            <div class="form-group">
                <input type="submit" value="Upload CSV">
            </div>
        </form>

        <div class="table-container">
            <h2>Previously Uploaded CSV's</h2>
            <table>
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>Upload Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $files = glob("uploads/*.csv");
                    if (count($files) > 0) {
                        foreach ($files as $file) {
                            $filename = basename($file);
                            $upload_time = date("Y-m-d H:i:s", filemtime($file));
                            echo "<tr>
                                    <td>$filename</td>
                                    <td>$upload_time</td>
                                    <td>
                                        <form action='process_csv.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='csv_file_path' value='$file'>
                                            <input type='hidden' name='male_color' value='#7eb4cf'> <!-- Default male color -->
                                            <input type='hidden' name='female_color' value='#ab84a9'> <!-- Default female color -->
                                            <button type='submit'>Reprocess</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No CSV files found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
