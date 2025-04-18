<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

// Some data
$datay = [7, 19, 11, 4, 20];

// Create the graph and setup the basic parameters
$__width  = 300;
$__height = 200;
$graph    = new Graph\Graph($__width, $__height, 'auto');
$graph->img->SetMargin(40, 30, 40, 50);
$graph->SetScale('textint');
$graph->SetFrame(true, 'blue', 1);
$graph->SetColor('lightblue');
$graph->SetMarginColor('lightblue');

// Setup X-axis labels
$a = $graph->gDateLocale->GetShortMonth();
$graph->xaxis->SetTickLabels($a);
$graph->xaxis->SetFont(FF_FONT1);
$graph->xaxis->SetColor('darkblue', 'black');

// Setup "hidden" y-axis by given it the same color
// as the background (this could also be done by setting the weight
// to zero)
$graph->yaxis->SetColor('lightblue', 'darkblue');
$graph->ygrid->SetColor('white');

// Setup graph title ands fonts
$graph->title->Set('Using grace = 50%');
$graph->title->SetFont(FF_FONT2, FS_BOLD);
$graph->xaxis->SetTitle('Year 2002', 'center');
$graph->xaxis->SetTitleMargin(10);
$graph->xaxis->title->SetFont(FF_FONT2, FS_BOLD);

// Add some grace to the top so that the scale doesn't
// end exactly at the max value.
$graph->yaxis->scale->SetGrace(50);

// Create a bar pot
$bplot = new Plot\BarPlot($datay);
$bplot->SetFillColor('darkblue');
$bplot->SetColor('darkblue');
$bplot->SetWidth(0.5);
$bplot->SetShadow('darkgray');

// Setup the values that are displayed on top of each bar
// Must use TTF fonts if we want text at an arbitrary angle
$bplot->value->Show();
$bplot->value->SetFont(FF_ARIAL, FS_NORMAL, 8);
$bplot->value->SetFormat('$%d');
$bplot->value->SetColor('darkred');
$bplot->value->SetAngle(45);
$graph->Add($bplot);

// Finally stroke the graph
$graph->Stroke();
