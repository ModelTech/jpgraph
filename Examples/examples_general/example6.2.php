<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$ydata  = [11, 3, 8, 12, 5, 1, 9, 13, 5, 7];
$y2data = [354, 200, 265, 99, 111, 91, 198, 225, 293, 251];

// Create the graph and specify the scale for both Y-axis
$__width  = 300;
$__height = 240;
$graph    = new Graph\Graph($__width, $__height);
$graph->SetScale('textlin');
$graph->SetShadow();

// Adjust the margin
$graph->img->SetMargin(40, 40, 20, 70);

// Create the two linear plot
$lineplot = new Plot\LinePlot($ydata);
$lineplot->SetStepStyle();

// Adjust the axis color
$graph->yaxis->SetColor('blue');

$graph->title->Set('Example 6.2');
$graph->xaxis->title->Set('X-title');
$graph->yaxis->title->Set('Y-title');

$graph->title->SetFont(FF_FONT1, FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

// Set the colors for the plots
$lineplot->SetColor('blue');
$lineplot->SetWeight(2);

// Set the legends for the plots
$lineplot->SetLegend('Plot 1');

// Add the plot to the graph
$graph->Add($lineplot);

// Adjust the legend position
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.4, 0.95, 'center', 'bottom');

// Display the graph
$graph->Stroke();
