<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;
use ModelTech\JpGraph\Util;

$f                   = new Util\FuncGenerator('cos($x)*$x');
list($xdata, $ydata) = $f->E(-1.2 * M_PI, 1.2 * M_PI);

$f                     = new Util\FuncGenerator('$x*$x');
list($x2data, $y2data) = $f->E(-2, 2);

// Setup the basic graph
$__width  = 450;
$__height = 350;
$graph    = new Graph\Graph($__width, $__height);
$graph->SetScale('linlin');
$graph->SetShadow();
$graph->img->SetMargin(50, 50, 60, 40);
$graph->SetBox(true, 'black', 2);
$graph->SetMarginColor('white');
$graph->SetColor('lightyellow');

// ... and titles
$graph->title->Set('Example of Function plot');
$graph->title->SetFont(FF_FONT1, FS_BOLD);
$graph->subtitle->Set("(With some more advanced axis formatting\nHiding first and last label)");
$graph->subtitle->SetFont(FF_FONT1, FS_NORMAL);
$graph->xgrid->Show();

$graph->yaxis->SetPos(0);
$graph->yaxis->SetWeight(2);
$graph->yaxis->HideZeroLabel();
$graph->yaxis->SetFont(FF_FONT1, FS_BOLD);
$graph->yaxis->SetColor('black', 'darkblue');
$graph->yaxis->HideTicks(true, false);
$graph->yaxis->HideFirstLastLabel();

$graph->xaxis->SetWeight(2);
$graph->xaxis->HideZeroLabel();
$graph->xaxis->HideFirstLastLabel();
$graph->xaxis->SetFont(FF_FONT1, FS_BOLD);
$graph->xaxis->SetColor('black', 'darkblue');

$lp1 = new Plot\LinePlot($ydata, $xdata);
$lp1->SetColor('blue');
$lp1->SetWeight(2);

$lp2           = new Plot\LinePlot($y2data, $x2data);
list($xm, $ym) = $lp2->Max();
$lp2->SetColor('red');
$lp2->SetWeight(2);

$graph->Add($lp1);
$graph->Add($lp2);
$graph->Stroke();
