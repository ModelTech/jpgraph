<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;

require_once 'jpgraph/jpgraph_matrix.php';

$data = [
    [0, null, 2, 3, 4, 5, 6, 7, 8, 9, 10, 8, 6, 4, 2],
    [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0, 8, 5, 9, 2],
    [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 2, 4, 5, 7],
    [10, 9, 8, 17, 6, 5, 4, 3, 2, 1, 0, 8, 6, 4, 2],
    [0, 1, 2, 3, 4, 4, 9, 7, 8, 9, 10, 3, 2, 7, 2],
    [8, 1, 2, 3, 4, 8, 3, 7, 8, 9, 10, 5, 3, 9, 1],
    [10, 3, 5, 7, 6, 5, 4, 3, 12, 1, 0, 6, 5, 10, 2],
    [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, null, 8, 6, 4, 2],
];

for ($i = 0; $i < count($data[0]); ++$i) {
    $xlabels[$i] = sprintf('xlabel: %02d', $i);
}
for ($i = 0; $i < count($data); ++$i) {
    $ylabels[$i] = sprintf('ylabel: %02d', $i);
}

// Setup a nasic matrix graph
$__width  = 400;
$__height = 250;
$graph    = new MatrixGraph($__width, $__height);
$graph->SetMarginColor('white');
$graph->title->Set('Adding labels on the edges');
$graph->title->SetFont(FF_ARIAL, FS_BOLD, 14);

// Create one matrix plot
$mp = new MatrixPlot($data, 1);
$mp->SetModuleSize(13, 15);
$mp->SetCenterPos(0.35, 0.45);
$mp->colormap->SetNullColor('gray');

// Setup column lablels
$mp->collabel->Set($xlabels);
$mp->collabel->SetSide('bottom');
$mp->collabel->SetFont(FF_ARIAL, FS_NORMAL, 8);
$mp->collabel->SetFontColor('darkgray');

// Setup row lablels
$mp->rowlabel->Set($ylabels);
$mp->rowlabel->SetSide('right');
$mp->rowlabel->SetFont(FF_ARIAL, FS_NORMAL, 8);
$mp->rowlabel->SetFontColor('darkgray');

// Move the legend more to the right
$mp->legend->SetMargin(90);

$graph->Add($mp);
$graph->Stroke();
