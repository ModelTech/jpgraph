<?php

/**
 * JPGraph v4.0.3
 */

require_once __DIR__ . '/../../src/config.inc.php';
define('DEBUGMODE', true);
ini_set('display_errors', (int) DEBUGMODE);
ini_set('display_startup_errors', (int) DEBUGMODE);
if (DEBUGMODE) {
    error_reporting(E_ALL);
}

use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Plot;

// Some data
$data = [50, 28, 25, 27, 31, 20];

// A new pie graph
$__width  = 400;
$__height = 320;
$piegraph = new Graph\PieGraph($__width, $__height);

$n = count($data); // Number of slices

// No border around graph
$piegraph->SetFrame(false);

// Setup title
$piegraph->title->Set('CSIM Center Pie plot');
$piegraph->title->SetFont(FF_ARIAL, FS_BOLD, 18);
$piegraph->title->SetMargin(8); // Add a little bit more margin from the top

// Create the pie plot
$p1 = new Plot\PiePlotC($data);

// Set the radius of pie (as fraction of image size)
$p1->SetSize(0.32);

// Label font and color setup
$p1->value->SetFont(FF_ARIAL, FS_BOLD, 11);
$p1->value->SetColor('white');

// Setup the title on the center circle
$p1->midtitle->Set("Distribution\n2008 H1");
$p1->midtitle->SetFont(FF_ARIAL, FS_NORMAL, 12);

// Set color for mid circle
$p1->SetMidColor('yellow');

// Use percentage values in the legends values (This is also the default)
$p1->SetLabelType(PIE_VALUE_PER);

// The label array values may have printf() formatting in them. The argument to the
// form,at string will be the value of the slice (either the percetage or absolute
// depending on what was specified in the SetLabelType() above.
$lbl = ["Jan\n%.1f%%", "Feb\n%.1f%%", "March\n%.1f%%",
    "Apr\n%.1f%%", "May\n%.1f%%", "Jun\n%.1f%%", ];
$p1->SetLabels($lbl);

// Add drop shadow to slices
$p1->SetShadow();

// Explode all slices 15 pixels
$p1->ExplodeAll(15);

// Setup the CSIM targets
global $_wrapperfilename;
$targ  = [];
$alt   = [];
$wtarg = [];
for ($i = 0; $i <= $n; ++$i) {
    $urlarg  = 'pie_clickedon=' . ($i + 1);
    $targ[]  = $_wrapperfilename . '?' . $urlarg;
    $alt[]   = 'val=%d';
    $wtarg[] = '';
}
$p1->SetCSIMTargets($targ, $alt, $wtarg);
$p1->SetMidCSIM($targ[0], $alt[0], $wtarg[0]);

// Add plot to pie graph
$piegraph->Add($p1);

// Send back the image when we are called from within the <img> tag
$piegraph->StrokeCSIMImage();
