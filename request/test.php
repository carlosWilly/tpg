<?php
// Path of the Package folder
require_once 'package/planets.phar';

$data = array(
    "day" => 12, // date of birth
    "month" => 4, // month of birth
    "year" => 1992, // Year of birth
    "hour" => 9, // Hour of birth in 24 hour format. eg. 4PM should be given as 16
    "minute" => 21, // Minute of birth
    "latitude" => 25.3176, // latitude in decimal format. North is taken as + and South as -.
    "longitude" => 82.9739, //  longitiude in decimal format. East is taken + and West as -.
    "timezone" => 5.5 // Timezone in decimal format eg. GMT+0530 will become 5.5
);

$horoscope = new PlanetsCalc($data);

// Get the planetary positions in Array
$planetPositionsInArray = $horoscope->getAllPlanetDegreeSpeed();

echo "Planets Degree";
echo "<br/>";
// Convert data to JSON and Print
echo json_encode($planetPositionsInArray);

// Get House Cusp Positions in Array
$houseCuspsInArray = $horoscope->getAllHouseDegree();

echo "<br/>";
echo "House Cusps";
echo "<br/>";
// Convert data to JSON and Print
echo(json_encode($houseCuspsInArray));
