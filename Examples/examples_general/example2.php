<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

// Some (random) data
$ydata = [11, 3, 8, 12, 5, 1, 9, 13, 5, 7];

// Size of the overall graph
$__width  = 350;
$__height = 250;

// Create the graph and set a scale.
// These two calls are always required
$graph = new Graph\Graph($__width, $__height);
$graph->SetScale('intlin');

// Setup margin and titles
$graph->SetMargin(40, 20, 20, 40);
$graph->title->Set('Calls per operator');
$graph->subtitle->Set('(March 12, 2008)');
$graph->xaxis->title->Set('Operator');
$graph->yaxis->title->Set('# of calls');

// Create the linear plot
$lineplot = new Plot\LinePlot($ydata);

// Add the plot to the graph
$graph->Add($lineplot);

// Display the graph
$graph->Stroke();
