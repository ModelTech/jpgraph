<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

setlocale(LC_ALL, 'et_EE.ISO-8859-1');

$data1y = [12, 8, 19, 3, 10, 5];
$data2y = [8, 2, 11, 7, 14, 4];

// Create the graph. These two calls are always required
$__width  = 310;
$__height = 200;
$graph    = new Graph\Graph($__width, $__height);
$graph->SetScale('textlin');

$graph->SetShadow();
$graph->img->SetMargin(40, 30, 20, 40);

// Create the bar plots
$b1plot = new Plot\BarPlot($data1y);
$b1plot->SetFillColor('orange');
$b2plot = new Plot\BarPlot($data2y);
$b2plot->SetFillColor('blue');

// Create the grouped bar plot
$gbplot = new Plot\AccBarPlot([$b1plot, $b2plot]);

// ...and add it to the graPH
$graph->Add($gbplot);

$graph->title->Set('Accumulated bar plots');
$graph->xaxis->title->Set('X-title');
$graph->yaxis->title->Set('Y-title');

$graph->title->SetFont(FF_FONT1, FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

// Display the graph
$graph->Stroke();
