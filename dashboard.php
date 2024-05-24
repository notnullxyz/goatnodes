<?php
// Calculate disk space used by images/ directory
function getDirectorySize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

$imagesDir = 'images/';
$imagesSize = getDirectorySize($imagesDir);

// Get number of animals from the latest CSV file in uploads/ directory
$uploadsDir = 'uploads/';
$latestCsvFile = null;
$latestCsvTime = 0;

foreach (glob($uploadsDir . '*.csv') as $file) {
    if (filemtime($file) > $latestCsvTime) {
        $latestCsvTime = filemtime($file);
        $latestCsvFile = $file;
    }
}

$numAnimals = 0;

if ($latestCsvFile) {
    $csvData = array_map('str_getcsv', file($latestCsvFile));
    $numAnimals = count($csvData) - 1; // Subtract 1 to exclude header row
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple-Earth's GoatNodes</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); text-align: center; }
        .menu { display: flex; flex-wrap: wrap; justify-content: space-around; margin-top: 20px; }
        .menu button { background-color: #007bff; color: white; padding: 15px 25px; margin: 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .menu button:hover { background-color: #0056b3; }
        .menu button[title]::after { content: attr(title); display: block; font-size: 12px; color: #ccc; }
        .links { display: flex; flex-wrap: wrap; justify-content: space-around; margin-top: 20px; }
        .links a { background-color: #6c757d; color: white; padding: 15px 25px; margin: 10px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; }
        .links a:hover { background-color: #5a6268; }
        .about { margin-top: 30px; padding: 20px; background-color: #e9ecef; border-radius: 5px; }
        .dashboard-info { display: flex; justify-content: space-around; margin-top: 20px; }
        .gauge-container { display: flex; justify-content: space-around; width: 100%; }
        .gauge { width: 150px; height: 150px; background: #e6e6e6; border-radius: 50%; position: relative; margin: 10px; }
        .gauge__body { width: 100%; height: 100%; clip-path: circle(50%); background: #e6e6e6; position: relative; }
        .gauge__fill { width: 100%; height: 100%; background: #007bff; position: absolute; top: 50%; left: 50%; transform: rotate(0deg) translate(-50%, -100%); transform-origin: center bottom; transition: transform 0.2s ease-out; }
        .gauge__cover { width: 75%; height: 75%; background: white; border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Simple-Earth's GoatNodes</h1>
        <h2>FarmOS Animal Visualiser 2024</h2>
    </div>
    <div class="container">
        <div class="menu">
            <button onclick="location.href='index.php'" title="Upload CSV data or rerun current data">Data Upload</button>
            <button onclick="location.href='upload_images.php'" title="Upload images for your animals">Image Upload</button>
            <button onclick="location.href='parent_map.log'" download title="Download the latest parent map">Parent Map</button>
            <button onclick="location.href='cleanup.php'" title="Maintenance and file cleanup">Cleanup</button>
            <button onclick="location.href='information.php'" title="How to use GoatNodes">Info</button>
        </div>
		<br /><br />
        <div class="dashboard-info">
            <div class="gauge-container">
                <div class="gauge">
                    <div class="gauge__body">
                        <div class="gauge__fill" id="diskSpaceFill"></div>
                        <div class="gauge__cover" id="diskSpaceCover"></div>
                    </div>
                    <label><small>Disk Space Grazed In images/</small></label>
                </div>
                <div class="gauge">
                    <div class="gauge__body">
                        <div class="gauge__fill" id="numAnimalsFill"></div>
                        <div class="gauge__cover" id="numAnimalsCover"></div>
                    </div>
                    <label><small># Animals in Latest Data</small></label>
                </div>
            </div>
        </div>
		<br /><br />
        <div class="links">
            <a href="https://github.com/notnullxyz/goatnodes">GitHub Repo</a>
            <a href="https://farmos.org/">FarmOS</a>
            <a href="https://simple-earth.org" title="Visit Simple-Earth.org">Simple-Earth.org</a>
        </div>

        <div class="about">
            <h3>About</h3>
            <p>GoatNodes is an application designed to visualize animal data exported from FarmOS in a logical and intuitive way.
				It distinguishes between gender, location, maturity, and parent/child relationships, providing a comprehensive
				overview of your animal assets. In addition to these features, GoatNodes can display photos and includes an
				experimental family tree view. The application was created by Marlon van der Linde. 
				For more info, you can reach Marlon at marlonv AT proton DOT me.</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const imagesSize = <?php echo $imagesSize; ?>;
        const numAnimals = <?php echo $numAnimals; ?>;

        const diskSpacePercentage = Math.min(imagesSize / (500 * 1024 * 1024) * 100, 100); // Assuming 500 MB as max disk space for gauge
        const diskSpaceFill = document.getElementById('diskSpaceFill');
        const diskSpaceCover = document.getElementById('diskSpaceCover');

        diskSpaceFill.style.transform = `rotate(${diskSpacePercentage / 100 * 180}deg) translate(-50%, -100%)`;
        diskSpaceCover.textContent = `${(imagesSize / 1024 / 1024).toFixed(1)} MB`;

        const numAnimalsFill = document.getElementById('numAnimalsFill');
        const numAnimalsCover = document.getElementById('numAnimalsCover');
        const numAnimalsPercentage = Math.min(numAnimals / 100 * 100, 100); // Assuming 100 animals as max for gauge

        numAnimalsFill.style.transform = `rotate(${numAnimalsPercentage / 100 * 180}deg) translate(-50%, -100%)`;
        numAnimalsCover.textContent = numAnimals;
    });
    </script>
</body>
</html>
