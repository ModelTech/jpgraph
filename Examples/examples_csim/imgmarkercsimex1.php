<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$datay1 = [4, 26, 15, 44];

// Setup the graph
$__width  = 300;
$__height = 200;
$graph    = new Graph\Graph($__width, $__height);
$graph->SetMarginColor('white');
$graph->SetScale('textlin');
$graph->SetFrame(false);
$graph->SetMargin(30, 5, 25, 20);

// Setup the tab
$graph->tabtitle->Set(' Year 2003 ');
$graph->tabtitle->SetFont(FF_ARIAL, FS_BOLD, 13);
$graph->tabtitle->SetColor('darkred', '#E1E1FF');

// Enable X-grid as well
$graph->xgrid->Show();

// Use months as X-labels
$graph->xaxis->SetTickLabels($graph->gDateLocale->GetShortMonth());

// Create the plot
$p1 = new Plot\LinePlot($datay1);
$p1->SetColor('navy');

$p1->SetCSIMTargets(['#1', '#2', '#3', '#4', '#5']);

// Use an image of favourite car as
$p1->mark->SetType(MARK_IMG, __DIR__ . '/../assets/saab_95.jpg', 0.5);
//$p1->mark->SetType(MARK_SQUARE);

// Displayes value on top of marker image
$p1->value->SetFormat('%d mil');
$p1->value->Show();
$p1->value->SetColor('darkred');
$p1->value->SetFont(FF_ARIAL, FS_BOLD, 10);
// Increase the margin so that the value is printed avove tje
// img marker
$p1->value->SetMargin(14);

// Incent the X-scale so the first and last point doesn't
// fall on the edges
$p1->SetCenter();

$graph->Add($p1);

$graph->StrokeCSIM();
