<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;

require_once 'jpgraph/jpgraph_radar.php';

// Some data to plot
$data = [55, 80, 46, 71, 95];

// Create the graph and the plot
$__width  = 250;
$__height = 200;
$graph    = new RadarGraph($__width, $__height);

// Create the titles for the axis
$titles = $graph->gDateLocale->GetShortMonth();
$graph->SetTitles($titles);

$plot = new RadarPlot($data);
$plot->SetFillColor('lightblue');

// Add the plot and display the graph
$graph->Add($plot);
$graph->Stroke();
