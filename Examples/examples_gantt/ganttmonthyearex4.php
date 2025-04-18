<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$graph = new Graph\GanttGraph();

$graph->title->Set('Adding a spaning title');

// Setup some "very" nonstandard colors
$graph->SetMarginColor('lightgreen@0.8');
$graph->SetBox(true, 'yellow:0.6', 2);
$graph->SetFrame(true, 'darkgreen', 4);
$graph->scale->divider->SetColor('yellow:0.6');
$graph->scale->dividerh->SetColor('yellow:0.6');

// Explicitely set the date range
// (Autoscaling will of course also work)
$graph->SetDateRange('2001-11-06', '2002-1-10');

// Display month and year scale with the gridlines
$graph->ShowHeaders(GANTT_HMONTH | GANTT_HYEAR | GANTT_HWEEK);
$graph->scale->month->grid->SetColor('gray');
$graph->scale->month->grid->Show(true);
$graph->scale->year->grid->SetColor('gray');
$graph->scale->year->grid->Show(true);

//Setup spanning title
$graph->scale->tableTitle->Set('Phase 1');
$graph->scale->tableTitle->SetFont(FF_ARIAL, FS_NORMAL, 16);
$graph->scale->SetTableTitleBackground('darkgreen@0.6');
$graph->scale->tableTitle->Show(true);

// Setup activity info

// For the titles we also add a minimum width of 100 pixels for the Task name column
$graph->scale->actinfo->SetColTitles(['Name', 'Duration', 'Start', 'Finish'], [100]);
$graph->scale->actinfo->SetBackgroundColor('green:0.5@0.5');
$graph->scale->actinfo->SetFont(FF_ARIAL, FS_NORMAL, 10);
$graph->scale->actinfo->vgrid->SetStyle('solid');
$graph->scale->actinfo->vgrid->SetColor('gray');

// Data for our example activities
$data = [
    [0, ['Pre-study', '102 days', "23 Nov '01", "1 Mar '02"], '2001-11-23', '2002-03-1', FF_ARIAL, FS_NORMAL, 8],
    [1, ['Prototype', '21 days', "26 Oct '01", "16 Nov '01"],
        '2001-10-26', '2001-11-16', FF_ARIAL, FS_NORMAL, 8, ],
    [2, ['Report', '12 days', "1 Mar '02", "13 Mar '02"],
        '2002-03-01', '2002-03-13', FF_ARIAL, FS_NORMAL, 8, ],
];

// Create the bars and add them to the gantt chart
for ($i = 0; $i < count($data); ++$i) {
    $bar = new Plot\GanttBar($data[$i][0], $data[$i][1], $data[$i][2], $data[$i][3], '[50%]', 10);
    if (count($data[$i]) > 4) {
        $bar->title->SetFont($data[$i][4], $data[$i][5], $data[$i][6]);
    }
    $bar->SetPattern(BAND_RDIAG, 'yellow');
    $bar->SetFillColor('gray');
    $bar->progress->Set(0.5);
    $bar->progress->SetPattern(GANTT_SOLID, 'darkgreen');
    $graph->Add($bar);
}

// Output the chart
$graph->Stroke();
