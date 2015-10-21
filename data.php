<?php

// Set required headers for json output
header("Content-type: application/json");

// Open mysql connection
if(!$link = mysql_connect("localhost", "root", "")) {
  die("Could not connect to MySQL database.");
}

if(!mysql_select_db("db_consoelec", $link)) {
  die("Could not select db_consoelec database.");
}

// Set request parameters
if(isset($_POST["from"])) {
  $dateFrom = DateTime::createFromFormat("m/d/Y H:i", $_POST["from"]);
} else {
  $dateFrom = new DateTime();
  $dateFrom->add(DateInterval::createFromDateString("-1 hour"));
}

if(isset($_POST["to"])) {
  $dateTo = DateTime::createFromFormat("m/d/Y H:i", $_POST["to"]);
} else {
  $dateTo = new DateTime();
}

// Compute precision
if(!isset($_POST["precision"]) || $_POST["precision"] == "auto")
{
  $dateInterval = $dateTo->getTimestamp() - $dateFrom->getTimestamp();
  switch($dateInterval) {
    case ($dateInterval <= 43200): // 12 hours => 1 second
      $precision = 1; break;
    case ($dateInterval <= 172800): // 2 days => 30 second
      $precision = 30; break;
    case ($dateInterval <= 345600): // 4 days => 1 minute
      $precision = 60; break;
    case ($dateInterval <= 691200): // 8 days => 2 minute
      $precision = 120; break;
    case ($dateInterval <= 1382400): // 16 days => 5 minutes
      $precision = 300; break;
    case ($dateInterval <= 2592000): // 30 days => 10 minutes
      $precision = 600; break;
    case ($dateInterval <= 5184000): // 60 days => 20 minutes
      $precision = 1200; break;
    case ($dateInterval <= 7776000): // 90 days => 30 minutes
      $precision = 1800; break;
    case ($dateInterval <= 15552000): // 180 days => 1 hour
      $precision = 3600; break;
    default:
      $precision = 86400; break; // default => 1 day
  }
} else {
  if(!is_numeric($_POST["precision"])) die("Require integer precision.");
  $precision = $_POST["precision"];
}

// Retrieve data from database
$sql = "SELECT date, AVG(consommation) as consommation FROM ConsoElec WHERE date > '" . $dateFrom->format('Y-m-d H:i:s') ."' AND date < '" . $dateTo->format('Y-m-d H:i:s') ."' group by UNIX_TIMESTAMP(date) DIV $precision order by date";

$result = mysql_query($sql, $link);

if (!$result) {
    die("DB Error, could not query the database\nMySQL Error: " . mysql_error());
}

$data = array();

while($row = mysql_fetch_assoc($result))
{
  $item = array();
  array_push($item, DateTime::createFromFormat("Y-m-d H:i:s", $row["date"])->format('c'));
  array_push($item, floatval($row["consommation"]));
	array_push($data, $item);
}

echo json_encode($data);

mysql_free_result($result);
mysql_close($link);

 ?>
