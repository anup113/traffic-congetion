<?php
require("phpsqlsearch_dbinfo.php");
header("Content-type: text/xml");

// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("map_info");
$parnode = $dom->appendChild($node);

// Opens a connection to a mySQL server
$connection=mysql_connect ($db_host, $db_username, $db_pass);
if (!$connection) {
  die("Not connected : " . mysql_error());
}

// Set the active mySQL database
$db_selected = mysql_select_db($db_name, $connection);
if (!$db_selected) {
  die ("Can\'t use db : " . mysql_error());
}

// Search the rows in the markers table
$query = sprintf("SELECT address, name, lat, lng, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM map_info HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20",
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($radius));
$result = mysql_query($query);

$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}



// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name", $row['name']);
  $newnode->setAttribute("address", $row['address']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("distance", $row['distance']);
}

// echo $dom->saveXML();
?>