<?php
// Load the parent map from the log file
$title = "Simple-Earth.org Family Visualisation";
$note = "This is seriously experimental. Double check the data";
$parent_map = [];
$log_file = 'parent_map.log';
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $lines = explode("\n", $log_content);
    foreach ($lines as $line) {
        if (strpos($line, '->') !== false) {
            list($parent, $children) = explode(' -> ', $line);
            $parent_map[trim($parent)] = array_map('trim', explode(',', $children));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title?></title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; }
        .node rect { fill: #fff; stroke: steelblue; stroke-width: 3px; }
        .node.inbreeding rect { fill: #ffcccc; } /* Highlight nodes with inbreeding */
        .node text { font: 12px sans-serif; }
        .link { fill: none; stroke: #000; stroke-width: 3px; } /* Make lines more prominent */
        .controls { margin-bottom: 20px; }
        .controls button { margin-right: 10px; }
    </style>
    <script src="https://d3js.org/d3.v7.min.js"></script>
</head>
<body>
    <h1><?php echo $title?></h1>
    <p><?php echo $note?></p>
    <div class="controls">
        <button id="increase-spacing">Increase Spacing</button>
        <button id="decrease-spacing">Decrease Spacing</button>
    </div>
    <div id="tree-container" style="width: 100%; height: 800px;"></div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var parentMap = <?php echo json_encode($parent_map); ?>;

            var data = { name: "Root", children: [] };
            var nodesMap = {};

            Object.keys(parentMap).forEach(function(parent) {
                var parentNode = nodesMap[parent] || { name: parent, children: [] };
                parentMap[parent].forEach(function(child) {
                    var childNode = nodesMap[child] || { name: child, children: [] };
                    parentNode.children.push(childNode);
                    nodesMap[child] = childNode;
                });
                nodesMap[parent] = parentNode;
            });

            data.children = Object.values(nodesMap).filter(node => node.name !== "Root");

            // Initial settings
            var nodeWidth = 100;
            var nodeHeight = 50;
            var horizontalSpacing = 200;
            var verticalSpacing = 100;

            var width = document.getElementById("tree-container").clientWidth;
            var height = 800;

            var svg = d3.select("#tree-container").append("svg")
                .attr("width", width)
                .attr("height", height)
                .call(d3.zoom().on("zoom", function(event) {
                    svg.attr("transform", event.transform);
                }))
                .append("g")
                .attr("transform", "translate(50,50)");

            var tree = d3.tree().nodeSize([verticalSpacing, horizontalSpacing]);
            var root = d3.hierarchy(data, function(d) { return d.children; });

            function detectInbreeding(node) {
                let inbreedingDetected = false;
                if (node.children) {
                    node.children.forEach(child => {
                        if (child.children && child.children.some(grandchild => grandchild.data.name === node.data.name)) {
                            inbreedingDetected = true;
                        }
                        inbreedingDetected = inbreedingDetected || detectInbreeding(child);
                    });
                }
                node.data.inbreeding = inbreedingDetected;
                return inbreedingDetected;
            }

            detectInbreeding(root);

            function update() {
                tree(root);

                svg.selectAll(".link").remove();
                svg.selectAll(".node").remove();

                svg.selectAll(".link")
                    .data(root.descendants().slice(1))
                    .enter().append("path")
                    .attr("class", "link")
                    .attr("d", function(d) {
                        return "M" + d.x + "," + d.y
                            + "C" + d.x + "," + (d.y + d.parent.y) / 2
                            + " " + d.parent.x + "," + (d.y + d.parent.y) / 2
                            + " " + d.parent.x + "," + d.parent.y;
                    });

                var node = svg.selectAll(".node")
                    .data(root.descendants())
                    .enter().append("g")
                    .attr("class", function(d) { return "node" + (d.data.inbreeding ? " inbreeding" : ""); })
                    .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

                node.append("rect")
                    .attr("width", nodeWidth)
                    .attr("height", 30)
                    .attr("x", -nodeWidth / 2)
                    .attr("y", -15);

                node.append("text")
                    .attr("dy", ".35em")
                    .attr("text-anchor", "middle")
                    .text(function(d) { return d.data.name; });
            }

            document.getElementById("increase-spacing").addEventListener("click", function() {
                horizontalSpacing += 20;
                verticalSpacing += 20;
                tree.nodeSize([verticalSpacing, horizontalSpacing]);
                update();
            });

            document.getElementById("decrease-spacing").addEventListener("click", function() {
                if (horizontalSpacing > 40 && verticalSpacing > 40) {
                    horizontalSpacing -= 20;
                    verticalSpacing -= 20;
                    tree.nodeSize([verticalSpacing, horizontalSpacing]);
                    update();
                }
            });

            update();
        });
    </script>
</body>
</html>
