<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth Goat Nodes Dashboard</title>
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
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 20px;
            text-align: center;
            width: 300px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card h2 {
            color: #28a745;
        }
        .card p {
            color: #333;
        }
        .card a {
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
        }
        .about {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 20px;
            text-align: left;
        }
        .about h2 {
            color: #28a745;
        }
        .about p {
            color: #333;
        }
        .footer {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Simple-Earth Goat Nodes Dashboard</h1>
    </div>

    <div class="container">
        <div class="card">
            <h2>Image Uploader</h2>
            <p>Upload images for your goats.</p>
            <a href="upload_images.php">Go to Image Uploader</a>
        </div>
        <div class="card">
            <h2>Data Upload</h2>
            <p>Upload your FarmOS animal data CSV.</p>
            <a href="index.php">Go to Data Upload</a>
        </div>
        <div class="card">
            <h2>Parent Map</h2>
            <p>Download the latest parent map file.</p>
            <a href="parent_map.log" download>Download Parent Map</a>
        </div>
    </div>

    <div class="container">
        <div class="about">
            <h2>About</h2>
            <p>This software is designed to help manage and visualize goat data from FarmOS. It allows you to upload images of your goats, import animal data from FarmOS, and generate a parent map to visualize family relationships. The system is simple to use, but comes with no warranties.</p>
        </div>
    </div>

    <div class="footer">
        &copy; 2024 Simple-Earth Self Reliance
    </div>
</body>
</html>
