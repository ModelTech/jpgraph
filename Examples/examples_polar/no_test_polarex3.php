<?php

/**
 * JPGraph v4.0.3
 */

// A simple Polar graph, example 2

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;

require_once 'jpgraph/jpgraph_polar.php';

$data = [0, 1, 10, 2, 30, 25, 40, 60,
    50, 110, 60, 160, 70, 210, 75, 230, 80, 260,
    85, 270, 90, 280,
    95, 270, 100, 260, 105, 230,
    110, 210, 120, 160, 130, 110, 140, 60,
    150, 25, 170, 2, 180, 1, ];

$__width  = 300;
$__height = 300;
$graph    = new PolarGraph($__width, $__height);
$graph->SetScale('log', 100);
$graph->SetType(POLAR_180);

$graph->title->Set('Polar plot #3');
$graph->title->SetFont(FF_FONT2, FS_BOLD);
$graph->title->SetColor('navy');

$graph->axis->ShowGrid(true, false);

$p = new PolarPlot($data);
$p->SetFillColor('lightred@0.5');

$graph->Add($p);

$graph->Stroke();
