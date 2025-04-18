<?php

/**
 * JPGraph v4.0.3
 */

// $Id: horizbarex4.php,v 1.4 2002/11/17 23:59:27 aditus Exp $
require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$datay = [1992, 1993, 1995, 1996, 1997, 1998, 2001];

// Size of graph
$__width  = 400;
$__height = 500;

// Set the basic parameters of the graph
$graph = new Graph\Graph($__width, $__height);
$graph->SetScale('textlin');

$top    = 60;
$bottom = 30;
$left   = 80;
$right  = 30;
$graph->Set90AndMargin($left, $right, $top, $bottom);

// Nice shadow
$graph->SetShadow();

// Setup labels
$lbl = ["Andrew\nTait", "Thomas\nAnderssen", "Kevin\nSpacey", "Nick\nDavidsson",
    "David\nLindquist", "Jason\nTait", "Lorin\nPersson", ];
$graph->xaxis->SetTickLabels($lbl);

// Label align for X-axis
$graph->xaxis->SetLabelAlign('right', 'center', 'right');

// Label align for Y-axis
$graph->yaxis->SetLabelAlign('center', 'bottom');

// Titles
$graph->title->Set('Number of incidents');

// Create a bar pot
$bplot = new Plot\BarPlot($datay);
$bplot->SetFillColor('orange');
$bplot->SetWidth(0.5);
$bplot->SetYMin(1990);

$graph->Add($bplot);

$graph->Stroke();
