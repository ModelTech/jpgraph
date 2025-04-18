<?php

/**
 * JPGraph v4.0.3
 */

namespace ModelTech\JpGraph\Image;

use ModelTech\JpGraph\Plot;

/**
 * File:        JPGRAPH_SCATTER.PHP
 * // Description: Scatter (and impuls) plot extension for JpGraph
 * // Created:     2001-02-11
 * // Ver:         $Id: jpgraph_scatter.php 1397 2009-06-27 21:34:14Z ljp $
 * //
 * // Copyright (c) Asial Corporation. All rights reserved.
 */

/**
 * @class FieldArrow
 * // Description: Draw an arrow at (x,y) with angle a
 */
class FieldArrow
{
    public $iColor     = 'black';
    public $iSize      = 10; // Length in pixels for  arrow
    public $iArrowSize = 2;
    private $isizespec = [
        [2, 1], [3, 2], [4, 3], [6, 4], [7, 4], [8, 5], [10, 6], [12, 7], [16, 8], [20, 10],
    ];

    public function __construct()
    {
        // Empty
    }

    public function SetSize($aSize, $aArrowSize = 2)
    {
        $this->iSize      = $aSize;
        $this->iArrowSize = $aArrowSize;
    }

    public function SetColor($aColor)
    {
        $this->iColor = $aColor;
    }

    public function Stroke($aImg, $x, $y, $a)
    {
        // First rotate the center coordinates
        list($x, $y) = $aImg->Rotate($x, $y);

        $old_origin = $aImg->SetCenter($x, $y);
        $old_a      = $aImg->a;
        $aImg->SetAngle(-$a + $old_a);

        $dx = round($this->iSize / 2);
        $c  = [$x - $dx, $y, $x + $dx, $y];
        $x += $dx;

        list($dx, $dy) = $this->isizespec[$this->iArrowSize];
        $ca            = [$x, $y, $x - $dx, $y - $dy, $x - $dx, $y + $dy, $x, $y];

        $aImg->SetColor($this->iColor);
        $aImg->Polygon($c);
        $aImg->FilledPolygon($ca);

        $aImg->SetCenter($old_origin[0], $old_origin[1]);
        $aImg->SetAngle($old_a);
    }
}
