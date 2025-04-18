<?php

/**
 * JPGraph v4.0.3
 */

// $Id: groupbarex1.php,v 1.2 2002/07/11 23:27:28 aditus Exp $
require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$datay1 = [35, 160, 0, 0, 0, 0];
$datay2 = [35, 190, 190, 190, 190, 190];
$datay3 = [20, 70, 70, 140, 230, 260];

$__width  = 450;
$__height = 200;
$graph    = new Graph\Graph($__width, $__height, 'auto');
$graph->SetScale('textlin');
$graph->SetShadow();
$graph->img->SetMargin(40, 30, 40, 40);
$graph->xaxis->SetTickLabels($graph->gDateLocale->GetShortMonth());

$graph->xaxis->title->Set('Year 2002');
$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

$graph->title->Set('Group bar plot');
$graph->title->SetFont(FF_FONT1, FS_BOLD);

$bplot1 = new Plot\BarPlot($datay1);
$bplot2 = new Plot\BarPlot($datay2);
$bplot3 = new Plot\BarPlot($datay3);

$bplot1->SetFillColor('orange');
$bplot2->SetFillColor('brown');
$bplot3->SetFillColor('darkgreen');

$bplot1->SetShadow();
$bplot2->SetShadow();
$bplot3->SetShadow();

$bplot1->SetShadow();
$bplot2->SetShadow();
$bplot3->SetShadow();

$gbarplot = new Plot\GroupBarPlot([$bplot1, $bplot2, $bplot3]);
$gbarplot->SetWidth(0.6);
$graph->Add($gbarplot);

$graph->Stroke();
