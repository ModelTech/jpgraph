<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

// Data can be specified using both ordinal index of the axis
// as well as the direction label.
$data = [
    0  => [3, 2, 1, 2, 2],
    4  => [1, 1, 1.5, 2],
    6  => [1, 1, 1.5, 2],
    12 => [2, 3, 5, 1],
];

$xpos1 = 0.26;
$xpos2 = 0.74;
$ypos1 = 0.5;
$ypos2 = 0.9;

// First create a new windrose graph with a title
$__width  = 650;
$__height = 350;
$graph    = new Graph\WindroseGraph($__width, $__height);
$graph->title->Set('Interpretation of ordinal keys');
$graph->title->SetFont(FF_VERDANA, FS_BOLD, 14);
$graph->title->SetColor('navy');

// Create the first plot
$wp1 = new Plot\WindrosePlot($data);
$wp1->SetType(WINDROSE_TYPE16);

// This is the default encoding
$wp1->SetDataKeyEncoding(KEYENCODING_ANTICLOCKWISE);
$wp1->legend->Hide();
$wp1->SetPos($xpos1, $ypos1);
$wp1->SetSize(0.5);

// Create the second plot
$wp2 = new Plot\WindrosePlot($data);
$wp2->SetType(WINDROSE_TYPE16);
$wp2->SetDataKeyEncoding(KEYENCODING_CLOCKWISE);
$wp2->legend->Hide();
$wp2->SetPos($xpos2, $ypos1);
$wp2->SetSize(0.5);

$txt1 = new Text('KEYENCODING_ANTICLOCKWISE');
$txt1->SetFont(FF_COURIER, FS_BOLD, 12);
$txt1->SetPos($xpos1, $ypos2);
$txt1->SetAlign('center', 'top');

$txt2 = new Text('KEYENCODING_CLOCKWISE');
$txt2->SetFont(FF_COURIER, FS_BOLD, 12);
$txt2->SetPos($xpos2, $ypos2);
$txt2->SetAlign('center', 'top');

// Finally add it to the graph and send back to the client
$graph->Add($wp1);
$graph->Add($txt1);

$graph->Add($wp2);
$graph->Add($txt2);

$graph->Stroke();
