<?php

/**
 * JPGraph v4.0.3
 */

namespace ModelTech\JpGraph\Plot;

use ModelTech\JpGraph\Util;

/**
 * File:        JPGRAPH_STOCK.PHP
 * // Description: Stock plot extension for JpGraph
 * // Created:     2003-01-27
 * // Ver:         $Id: jpgraph_stock.php 1364 2009-06-24 07:07:44Z ljp $
 * //
 * // Copyright (c) Asial Corporation. All rights reserved.
 */

/**
 * @class StockPlot
 */
class StockPlot extends Plot
{
    protected $iTupleSize = 4;
    private $iWidth       = 9;
    private $iEndLines    = 1;
    private $iStockColor1 = 'white';
    private $iStockColor2 = 'darkred';
    private $iStockColor3 = 'darkred';

    /**
     * CONSTRUCTOR.
     *
     * @param mixed $datay
     * @param mixed $datax
     */
    public function __construct($datay, $datax = false)
    {
        if (safe_count($datay) % $this->iTupleSize) {
            Util\JpGraphError::RaiseL(21001, $this->iTupleSize);
            //('Data values for Stock charts must contain an even multiple of '.$this->iTupleSize.' data points.');
        }
        parent::__construct($datay, $datax);
        $this->numpoints /= $this->iTupleSize;
    }

    /**
     * PUBLIC METHODS.
     *
     * @param mixed $aColor
     * @param mixed $aColor1
     * @param mixed $aColor2
     * @param mixed $aColor3
     */
    public function SetColor($aColor, $aColor1 = 'white', $aColor2 = 'darkred', $aColor3 = 'darkred')
    {
        $this->color        = $aColor;
        $this->iStockColor1 = $aColor1;
        $this->iStockColor2 = $aColor2;
        $this->iStockColor3 = $aColor3;
    }

    public function SetWidth($aWidth)
    {
        // Make sure it's odd
        $this->iWidth = 2 * floor($aWidth / 2) + 1;
    }

    public function HideEndLines($aHide = true)
    {
        $this->iEndLines = !$aHide;
    }

    // Gets called before any axis are stroked
    public function PreStrokeAdjust($graph)
    {
        if ($this->center) {
            $a = 0.5;
            $b = 0.5;
            ++$this->numpoints;
        } else {
            $a = 0;
            $b = 0;
        }
        $graph->xaxis->scale->ticks->SetXLabelOffset($a);
        $graph->SetTextScaleOff($b);
    }

    // Method description
    public function Stroke($img, $xscale, $yscale)
    {
        $n = $this->numpoints;
        if ($this->center) {
            --$n;
        }

        if (isset($this->coords[1])) {
            if (safe_count($this->coords[1]) != $n) {
                Util\JpGraphError::RaiseL(2003, safe_count($this->coords[1]), $n);
            // ("Number of X and Y points are not equal. Number of X-points:". safe_count($this->coords[1])." Number of Y-points:$numpoints");
            } else {
                $exist_x = true;
            }
        } else {
            $exist_x = false;
        }

        if ($exist_x) {
            $xs = $this->coords[1][0];
        } else {
            $xs = 0;
        }

        $ts              = $this->iTupleSize;
        $this->csimareas = '';
        for ($i = 0; $i < $n; ++$i) {
            //If value is NULL, then don't draw a bar at all
            if ($this->coords[0][$i * $ts] === null) {
                continue;
            }

            if ($exist_x) {
                $x = $this->coords[1][$i];
                if ($x === null) {
                    continue;
                }
            } else {
                $x = $i;
            }
            $xt = $xscale->Translate($x);

            $neg    = $this->coords[0][$i * $ts] > $this->coords[0][$i * $ts + 1];
            $yopen  = $yscale->Translate($this->coords[0][$i * $ts]);
            $yclose = $yscale->Translate($this->coords[0][$i * $ts + 1]);
            $ymin   = $yscale->Translate($this->coords[0][$i * $ts + 2]);
            $ymax   = $yscale->Translate($this->coords[0][$i * $ts + 3]);

            $dx = floor($this->iWidth / 2);
            $xl = $xt - $dx;
            $xr = $xt + $dx;

            if ($neg) {
                $img->SetColor($this->iStockColor3);
            } else {
                $img->SetColor($this->iStockColor1);
            }
            $img->FilledRectangle($xl, $yopen, $xr, $yclose);
            $img->SetLineWeight($this->weight);
            if ($neg) {
                $img->SetColor($this->iStockColor2);
            } else {
                $img->SetColor($this->color);
            }

            $img->Rectangle($xl, $yopen, $xr, $yclose);

            if ($yopen < $yclose) {
                $ytop    = $yopen;
                $ybottom = $yclose;
            } else {
                $ytop    = $yclose;
                $ybottom = $yopen;
            }
            $img->SetColor($this->color);
            $img->Line($xt, $ytop, $xt, $ymax);
            $img->Line($xt, $ybottom, $xt, $ymin);

            if ($this->iEndLines) {
                $img->Line($xl, $ymax, $xr, $ymax);
                $img->Line($xl, $ymin, $xr, $ymin);
            }

            // A chance for subclasses to add things to the bar
            // for data point i
            $this->ModBox($img, $xscale, $yscale, $i, $xl, $xr, $neg);

            // Setup image maps
            if (!empty($this->csimtargets[$i])) {
                $this->csimareas .= '<area shape="rect" coords="' .
                round($xl) . ',' . round($ytop) . ',' .
                round($xr) . ',' . round($ybottom) . '" ';
                $this->csimareas .= ' href="' . $this->csimtargets[$i] . '"';
                if (!empty($this->csimalts[$i])) {
                    $sval = $this->csimalts[$i];
                    $this->csimareas .= " title=\"{$sval}\" alt=\"{$sval}\" ";
                }
                $this->csimareas .= "  />\n";
            }
        }

        return true;
    }

    // A hook for subclasses to modify the plot
    public function ModBox($img, $xscale, $yscale, $i, $xl, $xr, $neg)
    {
    }
} // @class
