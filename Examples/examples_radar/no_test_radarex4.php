<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;

require_once 'jpgraph/jpgraph_radar.php';

// Some data to plot
$data = [55, 80, 26, 31, 95];

// Create the graph and the plot
$__width  = 250;
$__height = 200;
$graph    = new RadarGraph($__width, $__height);

// Add a drop shadow to the graph
$graph->SetShadow();

// Create the titles for the axis
$titles = $graph->gDateLocale->GetShortMonth();
$graph->SetTitles($titles);

// Add grid lines
$graph->grid->Show();
$graph->grid->SetLineStyle('dashed');

$plot = new RadarPlot($data);
$plot->SetFillColor('lightblue');

// Add the plot and display the graph
$graph->Add($plot);
$graph->Stroke();
