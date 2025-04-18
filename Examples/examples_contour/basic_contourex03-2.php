<?php

/**
 * JPGraph v4.0.3
 */

// Basic contour plot example
require_once __DIR__ . '/../../src/config.inc.php';

use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$data = [
    [12, 7, 3, 15],
    [18, 5, 1, 9],
    [13, 9, 5, 12],
    [5, 3, 8, 9],
    [1, 8, 5, 7], ];

// Basic contour graph
$__width  = 350;
$__height = 250;
$graph    = new Graph\Graph($__width, $__height);
$graph->SetScale('intint');

// Show axis on all sides
$graph->SetAxisStyle(AXSTYLE_BOXOUT);

// Adjust the margins to fit the margin
$graph->SetMargin(30, 100, 40, 30);

// Setup
$graph->title->Set('Basic contour plot with multiple axis');
$graph->title->SetFont(FF_ARIAL, FS_BOLD, 12);

// A simple contour plot with default arguments (e.g. 10 isobar lines)
$cp = new Plot\ContourPlot($data, 10, 2);

// Display the legend
$cp->ShowLegend();

$graph->Add($cp);

// ... and send the graph back to the browser
$graph->Stroke();
