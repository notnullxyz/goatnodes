<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoatNodes Information</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .back-link { display: inline-block; margin-bottom: 15px; color: #28a745; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #28a745; }
    </style>
</head>
<body>
    <div class="header">
        <h1>GoatNodes Information</h1>
    </div>
    <div class="container">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>

        <div class="section">
            <h2>What is GoatNodes</h2>
            <p>GoatNodes is a straightforward, yet effective application designed to process animal export CSV files from FarmOS.
			It provides visual insights into your herds, their locations, and relationships. Each animal is displayed in a grid according to its location,
			along with details about its parents and offspring. Originally created for Simple-Earth's Nigerian Dwarf Goat herds,
			GoatNodes is versatile enough to work with any animal type, provided your FarmOS data is well-maintained.
			Remember, the accuracy of the visualizations depends on the quality of the data you input.</p>
        </div>

        <div class="section">
            <h2>Using GoatNodes</h2>
            <p>Using GoatNodes should be quick and simple. To ensure everything works smoothly, make sure you extract the information correctly from FarmOS by following these steps:</p>
				<ul>
				<li>If you don't know how to export a CSV from FarmOS, see <i>"Exporting data from FarmOS"</i> below.</li>
				<li>Go to the animal photos pages, at the <a href="upload_images.php">Image Uploader</a> to add your animal photos. See <i>Directories: images/ and uploads/</i> below</li>
				<li>Go to GoatNodes' <a href="index.php">Data Upload page</a>, browse and select your CSV file, choose the colors and options you prefer, and click 'Upload CSV'.</li>
				<li>You should now see a grid of your animals, organized by location, with their information displayed.</li>
				<li>If you don't see your animal photos yet, then you should upload them manually using the Image Upload page of GoatNodes. These images can not be transported from FarmOS using the CSV</li>
				<li>There will be links to the family tree on the visual grid page (but this is quite experimental)</li>
				<li>Other links to the parent log file, etc will be available</li>
				<li>For a deeper look into things, enable logging on the data upload page. This will create and append to a log file on disk.</li>
				</ul>

		<p>If anything seems off, it could be due to data issues in the CSV or possibly a bug in GoatNodes.</p>
        </div>

        <div class="section">
            <h2>Directories: images/ and uploads/</h2>
            <p>During the use of GoatNodes, the directories named uploads/ and images/ will be created to store the following data:</p>
			<ol>
			<li><b>uploads/</b> will contain all uploaded CSV files until you clean them up. This allows GoatNodes to regenerate visuals for historical and previous datasets.</li>
			<li><b>images/</b> will store photos of your animals, if you choose to upload them.</li>
			</ol>
			<p>For the best results, choose photos that are as square as possible since GoatNodes will resize them
			to 150x150 pixels to fit into the animal grid. On the image upload page, you can upload as many photos as
			you'd like and name them after your animals. It is crucial that the name you provide matches the name of your
			animal in FarmOS so that GoatNodes can correctly associate the image with the animal.
			Uploading a new image with the same animal name will overwrite the existing one.
			You can manage the data in these directories by visiting the Cleanup page.</p>
        </div>

        <div class="section">
            <h2>Exporting data from FarmOS</h2>
			<ul><li>Log in to your FarmOS installation.</li>
				<li>Navigate to your animal assets -> animals view, or go directly to this page on your instance: <u>/taxonomy/term/11/assets/animal?status=All</u> (setting status=All ensures that even archived animals are exported).</li>
				<li>Once you see all your animals on the list, select them all.</li>
				<li>Scroll down to the Action dropdown menu, select Export CSV, and click Apply to Selected.</li>
				<li>For FarmOS versions prior to 3.2.2, double-check the exported data for accuracy.</li>
				<li>In FarmOS 3.2.2, you'll be presented with an option to select columns. GoatNodes requires the following columns: 'name', 'status', 'archived', 'location', 'parent', 'birthdate', 'is_castrated', 'sex' - ensure they are selected.</li>
				<li>Once the CSV is generated, download it.</li>
				</ul>
            <p>On earlier versions of FarmOS, there's some issues with fields like 'notes' that can break the CSV. If you have data discrepancies, check the CSV and fix it before uploading to FarmOS and retrying.
			It's best to make sure all your animals are listed and select for this export. Reference animals (external animals that you breed with) can be added to your FarmOS as well, to improve visibility.</p>
        </div>

        <div class="section">
            <h2>Understanding the Output</h2>
            <p>The output is simple to read. Your animal grid is divided into locations, as you have them set in FarmOS. Animal genders are colour coded, and also marked by symbols. 
			Once all development is completed on the symbols code, this section will explain them properly.</p>
        </div>

        <div class="section">
            <h2>Making GoatNodes Better</h2>
            <p>GoatNodes can, no doubt, be polished a lot. Feel free to find it on GitHub, or look for the link on the <a href="dashboard.php">dashboard</a> page. </p>
        </div>

        <div class="section">
            <h2>Support the developers in some way...</h2>
            <p>Improving the code and pushing changed back to the repo on GitHub is a great way to help it advance. Another way is to send some coffee money to the ionvolved parties... :)</p>
        </div>
    </div>
</body>
</html>
