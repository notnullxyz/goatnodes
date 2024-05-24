<?php

$verbose = false;
$log_file = 'goatnodes.log';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if (isset($_POST['verbose_logging'])) {
		$verbose = true;
		logToFile('logging enabled');
	}
	
    if (isset($_POST['csv_file_path'])) {
        $target_file = $_POST['csv_file_path'];
    } else {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["csv_file"]["name"]);
        $uploadOk = 1;
        $csvFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a CSV
        if ($csvFileType != "csv") {
			if ($verbose) { logToFile("CSV type not right: " . $csvFileType . " and file: " . $target_file); }
            echo "Sorry, only CSV files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
			if ($verbose) { logToFile("CSV upload not OK for type " . $csvFileType . " and file: " . $target_file); }
            echo "Problem uploading that file.";
            exit;
        } else {
            if (!move_uploaded_file($_FILES["csv_file"]["tmp_name"], $target_file)) {
				if ($verbose) { logToFile("File move did not go well - from " . $_FILES["csv_file"]["tmp_name"] . " to " . $target_file); }
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
			if ($verbose) {	logToFile("Uploaded file OK: " . $_FILES["csv_file"]["name"]); } 
            echo "The file ". htmlspecialchars(basename($_FILES["csv_file"]["name"])). " has been uploaded.";
        }
    }

    // Process the CSV file
    $csv = array_map('str_getcsv', file($target_file));
    $header = array_map('trim', $csv[0]);
    $data = array_slice($csv, 1);

    // Important fields to retain
    $important_fields = ['name', 'status', 'archived', 'location', 'parent', 'birthdate', 'is_castrated', 'sex'];
    $field_indices = array_flip($header);
    $important_indices = array_intersect_key($field_indices, array_flip($important_fields));

    $animals = [];
    foreach ($data as $row) {
        $animal = [];
        foreach ($important_indices as $field => $index) {
            $animal[$field] = trim($row[$index]);
        }
        // Handle empty parent field
        if (empty($animal['parent'])) {
			if ($verbose) {	logToFile("Empty Parent Field Encountered for " . $animal['name'] . " - action: setting defaults"); }
            $animal['parent'] = 'Jane Doe|John Buck';
        }
        $animal['age'] = date_diff(new DateTime($animal['birthdate']), new DateTime('now'))->format('%y years, %m months');
        $animals[] = $animal;
    }

    // Build parent map
    $parent_map = [];
	if ($verbose) {	logToFile("Building the parent mapping"); }
    foreach ($animals as $animal) {
        $parents = explode('|', $animal['parent']);
        foreach ($parents as $parent) {
            if (!isset($parent_map[$parent])) {
                $parent_map[$parent] = ['children' => []];
            }
            $parent_map[$parent]['children'][] = $animal['name'];
        }
    }

    // Log parent map
    $parent_map_file = 'parent_map.log';
    $log_content = "Parent-Child Mapping (" . date('Y-m-d H:i:s') . ")\n";
    foreach ($parent_map as $parent => $info) {
        $log_content .= $parent . ' -> ' . implode(', ', $info['children']) . "\n";
    }
    file_put_contents($parent_map_file, $log_content);
	if ($verbose) {	logToFile("Logged parent mapping to " . $parent_map_file); }

    // Render the HTML output
	if ($verbose) {	logToFile("Going to render output now..."); }
    $html_content = render_output($animals, $parent_map, $parent_map_file, $_POST['male_color'], $_POST['female_color'], basename($target_file));
	if ($verbose) {	logToFile("Output rendered. Echo'ing..."); }
    
    // Display the content
    echo $html_content;
   
}

function render_output($animals, $parent_map, $parent_map_file, $male_color, $female_color, $filename) {
	global $verbose;
    ob_start(); // Start output buffering
    ?>
	
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>GoatNodes Animals</title>
		<style>
			body { font-family: Helvetica, Arial, sans-serif; }
			.tooltip {
				position: relative;
				display: inline-block;
				border-bottom: 1px dotted black; /* If you want a dotted underline for tooltip text */
			}

			.tooltip .tooltiptext {
				visibility: hidden;
				width: auto;
				background-color: black;
				color: #fff;
				text-align: center;
				border-radius: 9px;
				padding: 10px;
				position: absolute;
				z-index: 1;
				bottom: 150%;
				left: 50%;
				margin-left: -60px;
				opacity: 0;
				transition: opacity 0.5s;
			}

			.tooltip:hover .tooltiptext {
				visibility: visible;
				opacity: 0.85;
			}
		</style>
	</head>
	
	
	<h1>Simple-Earth.org Goat Nodes</h1>
    <p>File: <?= htmlspecialchars($filename) ?><br>
    Date: <?= date('Y-m-d H:i:s') ?><br>
    Total Goats: <?= count($animals) ?><br>
    Active Goats: <?= count(array_filter($animals, fn($a) => $a['status'] !== 'archived')) ?><br>
    Archived Goats: <?= count($animals) - count(array_filter($animals, fn($a) => $a['status'] !== 'archived')) ?></p>
        
    Visual Family Tree: <a href='visualize_family_tree.php' target='_blank'>SVG</a><br>
    Parent Mapping: <a href='<?php echo $parent_map_file ?>' download='<?php echo $parent_map_file ?>'><?php echo $parent_map_file ?></a></p>
	
    <?php
    $locations = array_unique(array_column($animals, 'location'));
    $locations = array_diff($locations, [null, '']);
    $locations[] = 'Far, far away'; // Add unknown location at the end

    foreach ($locations as $location) {
		if ($verbose) {	logToFile("Scouting location - " . $location); }
        $location_animals = array_filter($animals, fn($a) => $a['location'] === $location || (!$a['location'] && $location === 'Far, far away'));
        if (empty($location_animals)) continue;

        ?>
        <h2 style='border: 2px dashed #000; padding: 10px;'>Location: <?= htmlspecialchars($location ?: 'Far, far away') ?></h2>
        <div style='display: flex; flex-wrap: wrap; gap: 10px; border: 2px dashed #000; padding: 10px;'>
        <?php
        foreach ($location_animals as $animal) {
			if ($verbose) {	logToFile("Wrangling Animal: " . $animal['name']); }
            $color = $animal['sex'] === 'M' ? $male_color : ($animal['sex'] === 'F' ? $female_color : '#ccc');
            $icon = $animal['sex'] === 'M' ? '<span class="tooltip">&#9794;<span class="tooltiptext">Gender is male: buck,bull,rooster,boar,stallion,drake etc</span></span>' 
				: ($animal['sex'] === 'F' ? '<span class="tooltip">&#9792;<span class="tooltiptext">Gender is female: doe,cow,hen,sow,mare,hen etc</span></span>' 
				: '<span class="tooltip">&#176;<span class="tooltiptext">unspecified</span></span>');
            $birthdate = $animal['birthdate'] ? date('m/Y', strtotime($animal['birthdate'])) : 'N/A';
			
			$castrated_icon = $animal['is_castrated'] == true ? '<span class="tooltip">&#9737;<span class="tooltiptext">Animal is Castrated: whether, steer, capon, barrow, gelding, hokie etc</span></span>'
				: '<span class="tooltip">&#9738;<span class="tooltiptext">Uncastrated</span></span>';
						
            $age = $animal['age'] ?? 'N/A'; // Use the calculated age
            $age_icon = ($age < 1) ? '<span class="tooltip">&#9744;<span class="tooltiptext">Not yet at recommended breeding age</span></span>' 
				: (($age > 6) ? '<span class="tooltip">&#9765;<span class="tooltiptext">Almost at end of breeding age</span></span>' 
				: '<span class="tooltip">&#9752;<span class="tooltiptext">Prime breeding age</span></span>');
            $archived_icon = $animal['status'] === 'archived' ? '<span class="tooltip">&#9842;<span class="tooltiptext">Archived/terminated/sold/reference animal</span></span>' : '';

            $children_list = !empty($parent_map[$animal['name']]['children']) ? "<ul><li>" . implode("</li><li>", $parent_map[$animal['name']]['children']) . "</li></ul>" : 'None';
            $parents_list = $animal['parent'] !== '[R] Jane Doe|[R] John Buck' ? implode(' & ', explode('|', $animal['parent'])) : 'None';

            $image_path = "images/" . htmlspecialchars($animal['name']) . ".jpg";
            $image_html = file_exists($image_path) ? "<img src='$image_path' alt='" . htmlspecialchars($animal['name']) . "' style='width: 100%; height: auto;'>" : "<div style='width: 100%; height: 0; padding-bottom: 100%; background-color: #eee;'></div>";

            ?>
            <div style='background-color: <?= $color ?>; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: calc(25% - 10px);'>
                <div style='display: flex; justify-content: space-between;'>
                    <div>
                        <strong><?= htmlspecialchars($animal['name']) ?></strong> <?= $icon ?> <?= $age_icon ?> <?= $archived_icon ?> <?= $castrated_icon ?><br>
                        Birthdate: <?= $birthdate ?><br>
                        Age: <?= $age ?><br>
                        Parents: <?= $parents_list ?><br>
                        Children: <?= $children_list ?>
                    </div>
                    <div style='width: 30%;'><?= $image_html ?></div>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
    }
    
    if ($verbose) {
        logToFile("Report = CSV Processed OK");
        logToFile("Report = File: $filename");
        logToFile("Report = Total Goats: " . count($animals));
        logToFile("Report = Active Goats: " . count(array_filter($animals, fn($a) => $a['status'] !== 'archived')));
        logToFile("Report = Archived Goats: " . (count($animals) - count(array_filter($animals, fn($a) => $a['status'] !== 'archived'))));
    }

    $output = ob_get_clean(); // Get the buffered content
    return $output;
}

// log to a file if verbose was enabled, with date string, IP,  and an End of Line included.
function logToFile($message) {
	global $log_file;
	$addr = $_SERVER["REMOTE_ADDR"];
	$date = new DateTime();
	$date = $date->format("Y/m/d h:i:s:u");
	$message = $date . ': src ' . $addr .': ' . $message;
	file_put_contents($log_file, $message . PHP_EOL, FILE_APPEND);
}

?>
