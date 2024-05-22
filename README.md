# goatnodes
A simple, ugly processor of FarmOS animal asset exports (in CSV) to visualise breeding information like dams, sires, childen, birthdates etc in a grid. Includes an image uploader and D3 based tree. 

# For now, just do this:
- Obtain these files...
- Put them in a web accesible directory with some php8.x love
- Hit dashboard.php or go directly to index.php to upload a CSV (see below)
- If the images/ or uploads/ directories aren't created, or doesn't work - create them and make sure they writable by the web server

# CSV file requirements
You need an exported CSV from the animals assets page at your farmOS instance, usually here: /taxonomy/term/11/assets?name=&is_location=All&is_fixed=All&status=All&items_per_page=50
This will get up to 50 animals, including archived status ones (handy for reference animals in the system) in ALL locations (if you have many enclosures or camps)

Make sure the CSV contains the neccesary fields:
 ($important_fields = ['name', 'status', 'archived', 'location', 'parent', 'birthdate', 'is_castrated', 'sex'];)

In the latest FarmOS build 3.2.2, you can select the columns you want to export (select the above columns). If not, export everything to be safe.

Go to index.php (directly, or via the dashboard) and uploaded your CSV
The processor will also generate a parent_map.log
You should be presented with a grid of the animals, and a link the to D3 family tree (created from the parent_map.log file)

