<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

$__width  = 0;
$__height = 0;
$graph    = new Graph\GanttGraph($__width, $__height);
$graph->SetShadow();

// Add title and subtitle
$graph->title->Set('A nice main title');
$graph->title->SetFont(FF_ARIAL, FS_BOLD, 12);
$graph->subtitle->Set('(Draft version)');

// Show day, week and month scale
$graph->ShowHeaders(GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH);

// Instead of week number show the date for the first day in the week
// on the week scale
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

// Make the week scale font smaller than the default
$graph->scale->week->SetFont(FF_FONT0);

// Use the short name of the month together with a 2 digit year
// on the month scale
$graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAMEYEAR4);
$graph->scale->month->SetFontColor('white');
$graph->scale->month->SetBackgroundColor('blue');

// Format the bar for the first activity
// ($row,$title,$startdate,$enddate)
$activity = new Plot\GanttBar(7, 'Project', '2001-12-21', '2002-02-20');

// Yellow diagonal line pattern on a red background
$activity->SetPattern(BAND_RDIAG, 'yellow');
$activity->SetFillColor('red');

// Finally add the bar to the graph
$graph->Add($activity);

// ... and display it
$graph->Stroke();
