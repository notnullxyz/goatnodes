<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["csv_file"]["name"]);
    $uploadOk = 1;
    $csvFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	$parentMapFilename = 'parent_map.log';

    // save the user time and pain by making sure we have a csv, at least
    if ($csvFileType != "csv") {
        echo "Only CSV files are allowed (farmOS animal assets export)";
        $uploadOk = 0;
    }

    // if anything marks an upload issue, then we will have a 0 here.
    if ($uploadOk == 0) {
        echo "The file was not uploaded. Not OK.";
		
    // try to get the file uploaded.
    } else {
        if (move_uploaded_file($_FILES["csv_file"]["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars(basename($_FILES["csv_file"]["name"])). " has been uploaded.";

            // csv parsing into array
            $csv = array_map('str_getcsv', file($target_file));
            $header = array_map('trim', $csv[0]);
            $data = array_slice($csv, 1);

            // retention fields. We don't need all of the fields hanging around
            $important_fields = ['name', 'status', 'archived', 'location', 'parent', 'birthdate', 'is_castrated', 'sex'];
            $field_indices = array_flip($header);
            $important_indices = array_intersect_key($field_indices, array_flip($important_fields));

            $animals = [];
            foreach ($data as $row) {
                $animal = [];
                foreach ($important_indices as $field => $index) {
                    $animal[$field] = trim($row[$index]);
                }
                // in the case of external or reference animals, there are sometimes empty/unknown parents
                if (empty($animal['parent'])) {
                    $animal['parent'] = 'Jane Doe|John Buck';
                }
                $animals[] = $animal;
            }

            // work out the parent mapping
            $parent_map = [];
            foreach ($animals as $animal) {
                $parents = explode('|', $animal['parent']);
                foreach ($parents as $parent) {
                    if (!isset($parent_map[$parent])) {
                        $parent_map[$parent] = ['children' => []];
                    }
                    $parent_map[$parent]['children'][] = $animal['name'];
                }
            }

            // write parent map to log
            $log_file = $parentMapFilename;
            $log_content = "Parent-Child Mapping (" . date('Y-m-d H:i:s') . ")\n";
            foreach ($parent_map as $parent => $info) {
                $log_content .= $parent . ' -> ' . implode(', ', $info['children']) . "\n";
            }
            file_put_contents($log_file, $log_content);

            // make pretty output
            echo render_output($animals, $parent_map, $parentMapFilename, $_POST['male_color'], $_POST['female_color'], $_POST['verbose_logging'] ?? false, $_FILES["csv_file"]["name"]);
        } else {
            echo "This file could not be uploaded (possibly due to directory permissions, etc";
        }
    }
}

function render_output($animals, $parent_map, $parentMapFilename, $male_color, $female_color, $verbose_logging, $filename) {
    $output = "<a href='index.php'><h1>Simple-Earth.org Goat Nodes</h1></a>";
    $output .= "<h2>marlonv@proton.me</h2>";
    $output .= "<p>File: " . htmlspecialchars($filename) . "<br>";
    $output .= "Date: " . date('Y-m-d H:i:s') . "<br>";
    $output .= "Total Goats: " . count($animals) . "<br>";
    $active_count = count(array_filter($animals, fn($a) => $a['status'] !== 'archived'));
    $archived_count = count($animals) - $active_count;
    $output .= "Active Goats: $active_count<br>";
    $output .= "Archived Goats: $archived_count<br>";
    
    // link to the D3.js parent map visual and map log
    $output .= "Visual Family Tree: <a href='visualize_family_tree.php' target='_blank'>SVG</a><br>";
    $output .= "Parent Mapping: <a href='$parentMapFilename' download='$parentMapFilename'>$parentMapFilename</a></p>";

    $locations = array_unique(array_column($animals, 'location'));
    $locations = array_diff($locations, [null, '']);
    $locations[] = 'Far, far away'; // unknown locations and terminated animals at the end.(reference only)

    foreach ($locations as $location) {
        $location_animals = array_filter($animals, fn($a) => $a['location'] === $location || (!$a['location'] && $location === 'Far, far away'));
        if (empty($location_animals)) continue;
        
        $output .= "<h3 style='border: 1px dashed #000; padding: 8px;'>Location: " . htmlspecialchars($location ?: 'Far, far away') . "</h3>";
        $output .= "<div style='display: flex; flex-wrap: wrap; gap: 10px; border: 2px dashed #000; padding: 10px;'>";
        foreach ($location_animals as $animal) {
            // node colors and symbols based on the 'sex' field:
            $color = $animal['sex'] === 'M' ? $male_color : ($animal['sex'] === 'F' ? $female_color : '#ccc');
            $icon = $animal['sex'] === 'M' ? '&#9794;' : ($animal['sex'] === 'F' ? '&#9792;' : '&#176;');
			
			// format the birthdate, and also add some age based symbols.
			// @todo - put these ages as config params at the top of the file
            $birthdate = $animal['birthdate'] ? date('m/Y', strtotime($animal['birthdate'])) : 'N/A';
	    //$age = $animal['birthdate'] ? floor((time() - strtotime($animal['birthdate'])) / (365.25*24*60*60)) : 'N/A';
	    
	    if ($birthdate != 'N/A') {
		    $age = date_diff(DateTime::createFromFormat('m/Y', $birthdate), new DateTime('now'))->format('%y years, %m months');
	    } else {
		    $age = '*';
	    }
            $age_icon = ($age < 1) ? '&#9744;' : (($age > 6) ? '&#9765;' : '&#9752;');
            
	    // if this animal is status=archived (meaning, sold or dead or belonging to someone else... add a little symbol)
	    $archived_icon = $animal['status'] === 'archived' ? '&#9842;' : '';

            $children_list = !empty($parent_map[$animal['name']]['children']) ? "<ul><li>" . implode("</li><li>", $parent_map[$animal['name']]['children']) . "</li></ul>" : 'None';
            $parents_list = $animal['parent'] !== 'Jane Doe|John Buck' ? implode(' & ', explode('|', $animal['parent'])) : 'None';

            // check for the availability of an image names after the animal.jpg in images/ and if it's there, we show it.
	    // @todo - automatically resizes images to save space and time and layout
            $image_path = "images/" . htmlspecialchars($animal['name']) . ".jpg";
            $image_html = file_exists($image_path) ? "<img src='$image_path' alt='" . htmlspecialchars($animal['name']) . "' style='width: 100%; height: auto;'>" : "<div style='width: 100%; height: 0; padding-bottom: 100%; background-color: #eee;'></div>";

            $output .= "<div style='background-color: $color; padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: calc(25% - 10px);'>";
            $output .= "<div style='display: flex; justify-content: space-between;'>";
            $output .= "<div>";
            $output .= "<strong>" . htmlspecialchars($animal['name']) . "</strong> $icon $age_icon $archived_icon<br>";
            $output .= "Birthdate: $birthdate<br>";
            $output .= "Age: $age<br>";
            $output .= "Parents: $parents_list<br>";
            $output .= "Children: $children_list</div>";
            $output .= "<div style='width: 30%;'>$image_html</div>";
            $output .= "</div></div>";
        }
        $output .= "</div>";
    }
    
    if ($verbose_logging) {
        $log_file = 'goatnodes.log';
        $log_content = "CSV Processed (" . date('Y-m-d H:i:s') . ")\n";
        $log_content .= "File: $filename\n";
        $log_content .= "Total Goats: " . count($animals) . "\n";
        $log_content .= "Active Goats: $active_count\n";
        $log_content .= "Archived Goats: $archived_count\n";
        file_put_contents($log_file, $log_content, FILE_APPEND);
    }

    return $output;
}
?>
