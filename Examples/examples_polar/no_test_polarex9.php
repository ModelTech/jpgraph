<?php

/**
 * JPGraph v4.0.3
 */

// A simple Polar graph,

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;

require_once 'jpgraph/jpgraph_polar.php';

$data = [0, 1, 10, 2, 30, 25, 40, 60,
    50, 110, 60, 160, 70, 210, 75, 230, 80, 260, 85, 370,
    90, 480,
    95, 370, 100, 260, 105, 230,
    110, 210, 120, 160, 130, 110, 140, 60,
    150, 25, 170, 2, 180, 1, ];

$__width  = 350;
$__height = 320;
$graph    = new PolarGraph($__width, $__height);
$graph->SetScale('log', 100);
$graph->SetType(POLAR_180);

// Hide frame around graph (by setting width=0)
$graph->SetFrame(true, 'white', 1);

// Show both major and minor grid lines
$graph->axis->ShowGrid(true, true);

// Set color for gradient lines
$graph->axis->SetGridColor('lightblue:0.9', 'lightblue:0.9', 'lightblue:0.9');

// Set label and axis colors
$graph->axis->SetColor('black', 'navy', 'darkred');

// Draw the ticks on the bottom side of the radius axis
$graph->axis->SetTickSide(SIDE_DOWN);

// Increase the margin for the labels since we changed the
// side of the ticks.
$graph->axis->SetLabelMargin(6);

// Change fonts
$graph->axis->SetFont(FF_ARIAL, FS_NORMAL, 8);
$graph->axis->SetAngleFont(FF_ARIAL, FS_NORMAL, 8);

// Setup axis title
$graph->axis->SetTitle('Coverage (in meter)', 'middle');
$graph->axis->title->SetFont(FF_FONT1, FS_BOLD);

// Setup graph title
$graph->title->Set('Polar plot #9');
$graph->title->SetFont(FF_ARIAL, FS_BOLD, 16);
$graph->title->SetColor('navy');

// Setup tab title
$graph->tabtitle->Set('Microphone #1');
$graph->tabtitle->SetColor('brown:0.5', 'lightyellow');

$p = new PolarPlot($data);
$p->SetFillColor('lightblue@0.5');
$p->mark->SetType(MARK_SQUARE);

$graph->Add($p);

$graph->Stroke();
