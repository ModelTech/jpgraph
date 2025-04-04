<?php

/**
 * JPGraph v4.0.3
 */

namespace ModelTech\JpGraph\Plot;

use ModelTech\JpGraph\Graph;
use ModelTech\JpGraph\Text;
use ModelTech\JpGraph\Util;

/*
 * File:        JPGRAPH_PIE.PHP
 * // Description: Pie plot extension for JpGraph
 * // Created:     2001-02-14
 * // Ver:         $Id: jpgraph_pie.php 1926 2010-01-11 16:33:07Z ljp $
 * //
 * // Copyright (c) Asial Corporation. All rights reserved.
 */
// Defines for PiePlot::SetLabelType()
define('PIE_VALUE_ABS', 1);
define('PIE_VALUE_PER', 0);
define('PIE_VALUE_PERCENTAGE', 0);
define('PIE_VALUE_ADJPERCENTAGE', 2);
define('PIE_VALUE_ADJPER', 2);

/**
 * @class PiePlot
 * // Description: Draws a pie plot
 */
class PiePlot
{
    public $posx                     = 0.5;
    public $posy                     = 0.5;
    public $is_using_plot_theme      = false;
    public $theme                    = 'earth';
    protected $use_plot_theme_colors = false;
    protected $radius                = 0.3;
    protected $explode_radius        = [];
    protected $explode_all           = false;
    protected $explode_r             = 20;
    protected $labels;
    protected $legends;
    protected $csimtargets;
    protected $csimwintargets; // Array of targets for CSIM
    protected $csimareas = ''; // Generated CSIM text
    protected $csimalts; // ALT tags for corresponding target
    protected $data;
    public $title;
    protected $startangle    = 0;
    protected $weight        = 1;
    protected $color         = 'black';
    protected $legend_margin = 6;
    protected $show_labels   = true;
    protected $themearr      = [
        'earth'  => [136, 34, 40, 45, 46, 62, 63, 134, 74, 10, 120, 136, 141, 168, 180, 77, 209, 218, 346, 395, 89, 430],
        'pastel' => [27, 415, 128, 59, 66, 79, 105, 110, 42, 147, 152, 230, 236, 240, 331, 337, 405, 38],
        'water'  => [8, 370, 24, 40, 335, 56, 213, 237, 268, 14, 326, 387, 10, 388],
        'sand'   => [27, 168, 34, 170, 19, 50, 65, 72, 131, 209, 46, 393], ];
    protected $setslicecolors      = [];
    protected $labeltype           = 0; // Default to percentage
    protected $pie_border          = true;
    protected $pie_interior_border = true;
    public $value;
    protected $ishadowcolor         = '';
    protected $ishadowdrop          = 4;
    protected $ilabelposadj         = 1;
    protected $legendcsimtargets    = [];
    protected $legendcsimwintargets = [];
    protected $legendcsimalts       = [];
    protected $adjusted_data        = [];
    public $guideline;
    protected $guidelinemargin         = 10;
    protected $iShowGuideLineForSingle = false;
    protected $iGuideLineCurve         = false;
    protected $iGuideVFactor           = 1.4;
    protected $iGuideLineRFactor       = 0.8;
    protected $la                      = []; // Holds the exact angle for each label

    /**
     * CONSTRUCTOR.
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data  = array_reverse($data);
        $this->title = new Text\Text('');
        $this->title->SetFont(FF_DEFAULT, FS_BOLD);
        $this->value = new DisplayValue();
        $this->value->Show();
        $this->value->SetFormat('%.1f%%');
        $this->guideline = new Graph\LineProperty();
    }

    /**
     * PUBLIC METHODS.
     *
     * @param mixed $x
     * @param mixed $y
     */
    public function SetCenter($x, $y = 0.5)
    {
        $this->posx = $x;
        $this->posy = $y;
    }

    // Enable guideline and set drwaing policy
    public function SetGuideLines($aFlg = true, $aCurved = true, $aAlways = false)
    {
        $this->guideline->Show($aFlg);
        $this->iShowGuideLineForSingle = $aAlways;
        $this->iGuideLineCurve         = $aCurved;
    }

    // Adjuste the distance between labels and labels and pie
    public function SetGuideLinesAdjust($aVFactor, $aRFactor = 0.8)
    {
        $this->iGuideVFactor     = $aVFactor;
        $this->iGuideLineRFactor = $aRFactor;
    }

    public function SetColor($aColor)
    {
        $this->color = $aColor;
    }

    public function SetSliceColors($aColors)
    {
        $this->setslicecolors = $aColors;
    }

    public function SetShadow($aColor = 'darkgray', $aDropWidth = 4)
    {
        $this->ishadowcolor = $aColor;
        $this->ishadowdrop  = $aDropWidth;
    }

    public function SetCSIMTargets($aTargets, $aAlts = '', $aWinTargets = '')
    {
        $this->csimtargets = array_reverse($aTargets);
        if (is_array($aWinTargets)) {
            $this->csimwintargets = array_reverse($aWinTargets);
        }

        if (is_array($aAlts)) {
            $this->csimalts = array_reverse($aAlts);
        }
    }

    public function GetCSIMareas()
    {
        return $this->csimareas;
    }

    public function AddSliceToCSIM($i, $xc, $yc, $radius, $sa, $ea)
    {
        //Slice number, ellipse centre (x,y), height, width, start angle, end angle
        while ($sa > 2 * M_PI) {
            $sa = $sa - 2 * M_PI;
        }

        while ($ea > 2 * M_PI) {
            $ea = $ea - 2 * M_PI;
        }

        $sa = 2 * M_PI - $sa;
        $ea = 2 * M_PI - $ea;

        // Special case when we have only one slice since then both start and end
        // angle will be == 0
        if (abs($sa - $ea) < 0.0001) {
            $sa = 2 * M_PI;
            $ea = 0;
        }

        //add coordinates of the centre to the map
        $xc     = floor($xc);
        $yc     = floor($yc);
        $coords = "{$xc}, {$yc}";

        //add coordinates of the first point on the arc to the map
        $xp = floor(($radius * cos($ea)) + $xc);
        $yp = floor($yc - $radius * sin($ea));
        $coords .= ", {$xp}, {$yp}";

        //add coordinates every 0.2 radians
        $a = $ea + 0.2;

        // If we cross the 360-limit with a slice we need to handle
        // the fact that end angle is smaller than start
        if ($sa < $ea) {
            while ($a <= 2 * M_PI) {
                $xp = floor($radius * cos($a) + $xc);
                $yp = floor($yc - $radius * sin($a));
                $coords .= ", {$xp}, {$yp}";
                $a += 0.2;
            }
            $a -= 2 * M_PI;
        }

        while ($a < $sa) {
            $xp = floor($radius * cos($a) + $xc);
            $yp = floor($yc - $radius * sin($a));
            $coords .= ", {$xp}, {$yp}";
            $a += 0.2;
        }

        //Add the last point on the arc
        $xp = floor($radius * cos($sa) + $xc);
        $yp = floor($yc - $radius * sin($sa));
        $coords .= ", {$xp}, {$yp}";
        if (!empty($this->csimtargets[$i])) {
            $this->csimareas .= "<area shape=\"poly\" coords=\"{$coords}\" href=\"" . $this->csimtargets[$i] . '"';
            $tmp = '';
            if (!empty($this->csimwintargets[$i])) {
                $this->csimareas .= ' target="' . $this->csimwintargets[$i] . '" ';
            }
            if (!empty($this->csimalts[$i])) {
                $tmp = sprintf($this->csimalts[$i], $this->data[$i]);
                $this->csimareas .= " title=\"{$tmp}\" alt=\"{$tmp}\" ";
            }
            $this->csimareas .= " />\n";
        }
    }

    public function SetTheme($aTheme)
    {
        //        Util\JpGraphError::RaiseL(15012,$aTheme);
        //        return;

        if (in_array($aTheme, array_keys($this->themearr), true)) {
            $this->theme               = $aTheme;
            $this->is_using_plot_theme = true;
        } else {
            Util\JpGraphError::RaiseL(15001, $aTheme); //("PiePLot::SetTheme() Unknown theme: $aTheme");
        }
    }

    public function ExplodeSlice($e, $radius = 20)
    {
        if (!is_integer($e)) {
            Util\JpGraphError::RaiseL(15002);
        }
        //('Argument to PiePlot::ExplodeSlice() must be an integer');
        $this->explode_radius[$e] = $radius;
    }

    public function ExplodeAll($radius = 20)
    {
        $this->explode_all = true;
        $this->explode_r   = $radius;
    }

    public function Explode($aExplodeArr)
    {
        if (!is_array($aExplodeArr)) {
            Util\JpGraphError::RaiseL(15003);
            //("Argument to PiePlot::Explode() must be an array with integer distances.");
        }
        $this->explode_radius = $aExplodeArr;
    }

    public function SetStartAngle($aStart)
    {
        if ($aStart < 0 || $aStart > 360) {
            Util\JpGraphError::RaiseL(15004); //('Slice start angle must be between 0 and 360 degrees.');
        }
        if ($aStart == 0) {
            $this->startangle = 0;
        } else {
            $this->startangle = 360 - $aStart;
            $this->startangle *= M_PI / 180;
        }
    }

    // Size in percentage
    public function SetSize($aSize)
    {
        if (($aSize > 0 && $aSize <= 0.5) || ($aSize > 10 && $aSize < 1000)) {
            $this->radius = $aSize;
        } else {
            Util\JpGraphError::RaiseL(15006);
        }

        //("PiePlot::SetSize() Radius for pie must either be specified as a fraction [0, 0.5] of the size of the image or as an absolute size in pixels  in the range [10, 1000]");
    }

    // Set label arrays
    public function SetLegends($aLegend)
    {
        $this->legends = $aLegend;
    }

    // Set text labels for slices
    public function SetLabels($aLabels, $aLblPosAdj = 'auto')
    {
        $this->labels       = array_reverse($aLabels);
        $this->ilabelposadj = $aLblPosAdj;
    }

    public function SetLabelPos($aLblPosAdj)
    {
        $this->ilabelposadj = $aLblPosAdj;
    }

    // Should we display actual value or percentage?
    public function SetLabelType($aType)
    {
        if ($aType < 0 || $aType > 2) {
            Util\JpGraphError::RaiseL(15008, $aType);
        }

        //("PiePlot::SetLabelType() Type for pie plots must be 0 or 1 (not $t).");
        $this->labeltype = $aType;
    }

    // Deprecated.
    public function SetValueType($aType)
    {
        $this->SetLabelType($aType);
    }

    // Should the circle around a pie plot be displayed
    public function ShowBorder($exterior = true, $interior = true)
    {
        $this->pie_border          = $exterior;
        $this->pie_interior_border = $interior;
    }

    // Setup the legends
    public function Legend($graph)
    {
        $colors = array_keys($graph->img->rgb->rgb_table);
        sort($colors);
        $ta = $this->themearr[$this->theme];
        $n  = safe_count($this->data);

        if ($this->setslicecolors == null) {
            $numcolors = safe_count($ta);
            if (($this instanceof PiePlot3D)) {
                $ta = array_reverse(array_slice($ta, 0, $n));
            }
        } else {
            $this->setslicecolors = array_slice($this->setslicecolors, 0, $n);
            $numcolors            = safe_count($this->setslicecolors);
            if ($graph->pieaa && !($this instanceof PiePlot3D)) {
                $this->setslicecolors = array_reverse($this->setslicecolors);
            }
        }

        $sum = 0;
        for ($i = 0; $i < $n; ++$i) {
            $sum += $this->data[$i];
        }

        // Bail out with error if the sum is 0
        if ($sum == 0) {
            Util\JpGraphError::RaiseL(15009);
        }
        //("Illegal pie plot. Sum of all data is zero for Pie!");

        // Make sure we don't plot more values than data points
        // (in case the user added more legends than data points)
        $n = min(safe_count($this->legends), safe_count($this->data));
        if ($this->legends != '') {
            $this->legends = array_reverse(array_slice($this->legends, 0, $n));
        }
        for ($i = $n - 1; $i >= 0; --$i) {
            $l = $this->legends[$i];
            // Replace possible format with actual values
            if (safe_count($this->csimalts) > $i) {
                $fmt = $this->csimalts[$i];
            } else {
                $fmt = '%d'; // Deafult Alt if no other has been specified
            }
            if ($this->labeltype == 0) {
                $l   = sprintf($l, 100 * $this->data[$i] / $sum);
                $alt = sprintf($fmt, $this->data[$i]);
            } elseif ($this->labeltype == 1) {
                $l   = sprintf($l, $this->data[$i]);
                $alt = sprintf($fmt, $this->data[$i]);
            } else {
                $l   = sprintf($l, $this->adjusted_data[$i]);
                $alt = sprintf($fmt, $this->adjusted_data[$i]);
            }

            if (empty($this->csimwintargets[$i])) {
                $wintarg = '';
            } else {
                $wintarg = $this->csimwintargets[$i];
            }

            $imageMapTarget = $this->csimtargets[$i] ?? '';
            if ($this->setslicecolors == null) {
                $graph->legend->Add($l, $colors[$ta[$i % $numcolors]], '', 0, $imageMapTarget, $alt, $wintarg);
            } else {
                $graph->legend->Add($l, $this->setslicecolors[$i % $numcolors], '', 0, $imageMapTarget, $alt, $wintarg);
            }
        }
    }

    // Adjust the rounded percetage value so that the sum of
    // of the pie slices are always 100%
    // Using the Hare/Niemeyer method
    public function AdjPercentage($aData, $aPrec = 0)
    {
        $mul = 100;
        if ($aPrec > 0 && $aPrec < 3) {
            if ($aPrec == 1) {
                $mul = 1000;
            } else {
                $mul = 10000;
            }
        }

        $tmp       = [];
        $result    = [];
        $quote_sum = 0;
        $n         = safe_count($aData);
        for ($i = 0, $sum = 0; $i < $n; ++$i) {
            $sum += $aData[$i];
        }

        foreach ($aData as $index => $value) {
            $tmp_percentage = $value / $sum * $mul;
            $result[$index] = floor($tmp_percentage);
            $tmp[$index]    = $tmp_percentage - $result[$index];
            $quote_sum += $result[$index];
        }
        if ($quote_sum == $mul) {
            if ($mul > 100) {
                $tmp = $mul / 100;
                for ($i = 0; $i < $n; ++$i) {
                    $result[$i] /= $tmp;
                }
            }

            return $result;
        }
        arsort($tmp, SORT_NUMERIC);
        reset($tmp);
        for ($i = 0; $i < $mul - $quote_sum; ++$i) {
            ++$result[key($tmp)];
            next($tmp);
        }
        if ($mul > 100) {
            $tmp = $mul / 100;
            for ($i = 0; $i < $n; ++$i) {
                $result[$i] /= $tmp;
            }
        }

        return $result;
    }

    public function Stroke($img, $aaoption = 0)
    {
        // aaoption is used to handle antialias
        // aaoption == 0 a normal pie
        // aaoption == 1 just the body
        // aaoption == 2 just the values

        // Explode scaling. If anti alias we scale the image
        // twice and we also need to scale the exploding distance
        $expscale = $aaoption === 1 ? 2 : 1;

        if ($this->labeltype == 2) {
            // Adjust the data so that it will add up to 100%
            $this->adjusted_data = $this->AdjPercentage($this->data);
        }

        if ($this->use_plot_theme_colors) {
            $this->setslicecolors = null;
        }

        $colors = array_keys($img->rgb->rgb_table);
        sort($colors);
        $ta = $this->themearr[$this->theme];
        $n  = safe_count($this->data);

        if ($this->setslicecolors == null) {
            $numcolors = safe_count($ta);
        } else {
            // We need to create an array of colors as long as the data
            // since we need to reverse it to get the colors in the right order
            $numcolors = safe_count($this->setslicecolors);
            $i         = 2 * $numcolors;
            while ($n > $i) {
                $this->setslicecolors = array_merge($this->setslicecolors, $this->setslicecolors);
                $i += $n;
            }
            $tt                   = array_slice($this->setslicecolors, 0, $n % $numcolors);
            $this->setslicecolors = array_merge($this->setslicecolors, $tt);
            $this->setslicecolors = array_reverse($this->setslicecolors);
        }

        // Draw the slices
        $sum = 0;
        for ($i = 0; $i < $n; ++$i) {
            $sum += $this->data[$i];
        }

        // Bail out with error if the sum is 0
        if ($sum == 0) {
            Util\JpGraphError::RaiseL(15009); //("Sum of all data is 0 for Pie.");
        }

        // Set up the pie-circle
        if ($this->radius <= 1) {
            $radius = floor($this->radius * min($img->width, $img->height));
        } else {
            $radius = $aaoption === 1 ? $this->radius * 2 : $this->radius;
        }

        if ($this->posx <= 1 && $this->posx > 0) {
            $xc = round($this->posx * $img->width);
        } else {
            $xc = $this->posx;
        }

        if ($this->posy <= 1 && $this->posy > 0) {
            $yc = round($this->posy * $img->height);
        } else {
            $yc = $this->posy;
        }

        $n = safe_count($this->data);

        if ($this->explode_all) {
            for ($i = 0; $i < $n; ++$i) {
                $this->explode_radius[$i] = $this->explode_r;
            }
        }

        // If we have a shadow and not just drawing the labels
        if ($this->ishadowcolor != '' && $aaoption !== 2) {
            $accsum = 0;
            $angle2 = $this->startangle;
            $img->SetColor($this->ishadowcolor);
            for ($i = 0; $sum > 0 && $i < $n; ++$i) {
                $j      = $n - $i - 1;
                $d      = $this->data[$i];
                $angle1 = $angle2;
                $accsum += $d;
                $angle2 = $this->startangle + 2 * M_PI * $accsum / $sum;
                if (empty($this->explode_radius[$j])) {
                    $this->explode_radius[$j] = 0;
                }

                if ($d < 0.00001) {
                    continue;
                }

                $la = 2 * M_PI - (abs($angle2 - $angle1) / 2.0 + $angle1);

                $xcm = $xc + $this->explode_radius[$j] * cos($la) * $expscale;
                $ycm = $yc - $this->explode_radius[$j] * sin($la) * $expscale;

                $xcm += $this->ishadowdrop * $expscale;
                $ycm += $this->ishadowdrop * $expscale;

                $_sa = round($angle1 * 180 / M_PI);
                $_ea = round($angle2 * 180 / M_PI);

                // The CakeSlice method draws a full circle in case of start angle = end angle
                // for pie slices we don't want this behaviour unless we only have one
                // slice in the pie in case it is the wanted behaviour
                if ($_ea - $_sa > 0.1 || $n == 1) {
                    $img->CakeSlice(
                        $xcm,
                        $ycm,
                        $radius - 1,
                        $radius - 1,
                        $angle1 * 180 / M_PI,
                        $angle2 * 180 / M_PI,
                        $this->ishadowcolor
                    );
                }
            }
        }

        /**
         * This is the main loop to draw each cake slice.
         */
        // Set up the accumulated sum, start angle for first slice and border color
        $accsum = 0;
        $angle2 = $this->startangle;
        $img->SetColor($this->color);

        // Loop though all the slices if there is a pie to draw (sum>0)
        // There are n slices in total
        for ($i = 0; $sum > 0 && $i < $n; ++$i) {
            // $j is the actual index used for the slice
            $j = $n - $i - 1;

            // Make sure we havea  valid distance to explode the slice
            if (empty($this->explode_radius[$j])) {
                $this->explode_radius[$j] = 0;
            }

            // The actual numeric value for the slice
            $d = $this->data[$i];

            $angle1 = $angle2;

            // Accumlate the sum
            $accsum += $d;

            // The new angle when we add the "size" of this slice
            // angle1 is then the start and angle2 the end of this slice
            $angle2 = $this->NormAngle($this->startangle + 2 * M_PI * $accsum / $sum);

            // We avoid some trouble by not allowing end angle to be 0, in that case
            // we translate to 360

            // la is used to hold the label angle, which is centered on the slice
            if ($angle2 < 0.0001 && $angle1 > 0.0001) {
                $this->la[$i] = 2 * M_PI - (abs(2 * M_PI - $angle1) / 2.0 + $angle1);
            } elseif ($angle1 > $angle2) {
                // The case where the slice crosses the 3 a'clock line
                // Remember that the slices are counted clockwise and
                // labels are counted counter clockwise so we need to revert with 2 PI
                $this->la[$i] = 2 * M_PI - $this->NormAngle($angle1 + ((2 * M_PI - $angle1) + $angle2) / 2);
            } else {
                $this->la[$i] = 2 * M_PI - (abs($angle2 - $angle1) / 2.0 + $angle1);
            }

            // Too avoid rounding problems we skip the slice if it is too small
            if ($d < 0.00001) {
                continue;
            }

            // If the user has specified an array of colors for each slice then use
            // that a color otherwise use the theme array (ta) of colors
            if ($this->setslicecolors == null) {
                $slicecolor = $colors[$ta[$i % $numcolors]];
            } else {
                $slicecolor = $this->setslicecolors[$i % $numcolors];
            }

            //            $_sa = round($angle1*180/M_PI);
            //            $_ea = round($angle2*180/M_PI);
            //            $_la = round($this->la[$i]*180/M_PI);
            //            echo "Slice#$i: ang1=$_sa , ang2=$_ea, la=$_la, color=$slicecolor<br>";

            // If we have enabled antialias then we don't draw any border so
            // make the bordedr color the same as the slice color
            if ($this->pie_interior_border && $aaoption === 0) {
                $img->SetColor($this->color);
            } else {
                $img->SetColor($slicecolor);
            }
            $arccolor = $this->pie_border && $aaoption === 0 ? $this->color : '';

            // Calculate the x,y coordinates for the base of this slice taking
            // the exploded distance into account. Here we use the mid angle as the
            // ray of extension and we have the mid angle handy as it is also the
            // label angle
            $xcm = $xc + $this->explode_radius[$j] * cos($this->la[$i]) * $expscale;
            $ycm = $yc - $this->explode_radius[$j] * sin($this->la[$i]) * $expscale;

            // If we are not just drawing the labels then draw this cake slice
            if ($aaoption !== 2) {
                $_sa = round($angle1 * 180 / M_PI);
                $_ea = round($angle2 * 180 / M_PI);
                $_la = round($this->la[$i] * 180 / M_PI);
                //echo "[$i] sa=$_sa, ea=$_ea, la[$i]=$_la, (color=$slicecolor)<br>";

                // The CakeSlice method draws a full circle in case of start angle = end angle
                // for pie slices we want this in case the slice have a value larger than 99% of the
                // total sum
                if (abs($_ea - $_sa) >= 1 || $d == $sum) {
                    $img->CakeSlice($xcm, $ycm, $radius - 1, $radius - 1, $_sa, $_ea, $slicecolor, $arccolor);
                }
            }

            // If the CSIM is used then make sure we register a CSIM area for this slice as well
            if ($this->csimtargets && $aaoption !== 1) {
                $this->AddSliceToCSIM($i, $xcm, $ycm, $radius, $angle1, $angle2);
            }
        }

        // Format the titles for each slice
        if ($aaoption !== 2) {
            for ($i = 0; $i < $n; ++$i) {
                if ($this->labeltype == 0) {
                    if ($sum != 0) {
                        $l = 100.0 * $this->data[$i] / $sum;
                    } else {
                        $l = 0.0;
                    }
                } elseif ($this->labeltype == 1) {
                    $l = $this->data[$i] * 1.0;
                } else {
                    $l = $this->adjusted_data[$i];
                }
                if (isset($this->labels[$i]) && is_string($this->labels[$i])) {
                    $this->labels[$i] = sprintf($this->labels[$i], $l);
                } else {
                    $this->labels[$i] = $l;
                }
            }
        }

        if ($this->value->show && $aaoption !== 1) {
            $this->StrokeAllLabels($img, $xc, $yc, $radius);
        }

        // Adjust title position
        if ($aaoption !== 1) {
            $this->title->SetPos(
                $xc,
                $yc - $this->title->GetFontHeight($img) - $radius - $this->title->margin,
                'center',
                'bottom'
            );
            $this->title->Stroke($img);
        }
    }

    /**
     * PRIVATE METHODS.
     *
     * @param mixed $a
     */
    public function NormAngle($a)
    {
        while ($a < 0) {
            $a += 2 * M_PI;
        }

        while ($a > 2 * M_PI) {
            $a -= 2 * M_PI;
        }

        return $a;
    }

    public function Quadrant($a)
    {
        $a = $this->NormAngle($a);
        if ($a > 0 && $a <= M_PI / 2) {
            return 0;
        }

        if ($a > M_PI / 2 && $a <= M_PI) {
            return 1;
        }

        if ($a > M_PI && $a <= 1.5 * M_PI) {
            return 2;
        }

        if ($a > 1.5 * M_PI) {
            return 3;
        }
    }

    public function StrokeGuideLabels($img, $xc, $yc, $radius)
    {
        $n = safe_count($this->labels);

        /**
         * Step 1 of the algorithm is to construct a number of clusters
         * // a cluster is defined as all slices within the same quadrant (almost)
         * // that has an angular distance less than the treshold.
         */
        $tresh_hold = 25 * M_PI / 180; // 25 degrees difference to be in a cluster
        $incluster  = false; // flag if we are currently in a cluster or not
        $clusters   = []; // array of clusters
        $cidx       = -1; // running cluster index

        // Go through all the labels and construct a number of clusters
        for ($i = 0; $i < $n - 1; ++$i) {
            // Calc the angle distance between two consecutive slices
            $a1   = $this->la[$i];
            $a2   = $this->la[$i + 1];
            $q1   = $this->Quadrant($a1);
            $q2   = $this->Quadrant($a2);
            $diff = abs($a1 - $a2);
            if ($diff < $tresh_hold) {
                if ($incluster) {
                    ++$clusters[$cidx][1];
                    // Each cluster can only cover one quadrant
                    // Do we cross a quadrant ( and must break the cluster)
                    if ($q1 != $q2) {
                        // If we cross a quadrant boundary we normally start a
                        // new cluster. However we need to take the 12'a clock
                        // and 6'a clock positions into a special consideration.
                        // Case 1: WE go from q=1 to q=2 if the last slice on
                        // the cluster for q=1 is close to 12'a clock and the
                        // first slice in q=0 is small we extend the previous
                        // cluster
                        if ($q1 == 1 && $q2 == 0 && $a2 > (90 - 15) * M_PI / 180) {
                            if ($i < $n - 2) {
                                $a3 = $this->la[$i + 2];
                                // If there isn't a cluster coming up with the next-next slice
                                // we extend the previous cluster to cover this slice as well
                                if (abs($a3 - $a2) >= $tresh_hold) {
                                    ++$clusters[$cidx][1];
                                    ++$i;
                                }
                            }
                        } elseif ($q1 == 3 && $q2 == 2 && $a2 > (270 - 15) * M_PI / 180) {
                            if ($i < $n - 2) {
                                $a3 = $this->la[$i + 2];
                                // If there isn't a cluster coming up with the next-next slice
                                // we extend the previous cluster to cover this slice as well
                                if (abs($a3 - $a2) >= $tresh_hold) {
                                    ++$clusters[$cidx][1];
                                    ++$i;
                                }
                            }
                        }

                        if ($q1 == 2 && $q2 == 1 && $a2 > (180 - 15) * M_PI / 180) {
                            ++$clusters[$cidx][1];
                            ++$i;
                        }

                        $incluster = false;
                    }
                } elseif ($q1 == $q2) {
                    $incluster = true;
                    // Now we have a special case for quadrant 0. If we previously
                    // have a cluster of one in quadrant 0 we just extend that
                    // cluster. If we don't do this then we risk that the label
                    // for the cluster of one will cross the guide-line
                    if ($q1 == 0 && $cidx > -1 &&
                        $clusters[$cidx][1] == 1 &&
                        $this->Quadrant($this->la[$clusters[$cidx][0]]) == 0) {
                        ++$clusters[$cidx][1];
                    } else {
                        ++$cidx;
                        $clusters[$cidx][0] = $i;
                        $clusters[$cidx][1] = 1;
                    }
                } else {
                    // Create a "cluster" of one since we are just crossing
                    // a quadrant
                    ++$cidx;
                    $clusters[$cidx][0] = $i;
                    $clusters[$cidx][1] = 1;
                }
            } else {
                if ($incluster) {
                    // Add the last slice
                    ++$clusters[$cidx][1];
                    $incluster = false;
                } else {
                    // Create a "cluster" of one
                    ++$cidx;
                    $clusters[$cidx][0] = $i;
                    $clusters[$cidx][1] = 1;
                }
            }
        }
        // Handle the very last slice
        if ($incluster) {
            ++$clusters[$cidx][1];
        } else {
            // Create a "cluster" of one
            ++$cidx;
            $clusters[$cidx][0] = $i;
            $clusters[$cidx][1] = 1;
        }

        /*
        if( true ) {
        // Debug printout in labels
        for( $i=0; $i <= $cidx; ++$i ) {
        for( $j=0; $j < $clusters[$i][1]; ++$j ) {
        $a = $this->la[$clusters[$i][0]+$j];
        $aa = round($a*180/M_PI);
        $q = $this->Quadrant($a);
        $this->labels[$clusters[$i][0]+$j]="[$q:$aa] $i:$j";
        }
        }
        }
         */

        /*
         * Step 2 of the algorithm is use the clusters and draw the labels
         * // and guidelines
         */
        // We use the font height as the base factor for how far we need to
        // spread the labels in the Y-direction.
        $this->value->ApplyFont($img);
        $fh        = $img->GetFontHeight();
        $origvstep = $fh * $this->iGuideVFactor;
        $this->value->SetMargin(0);

        // Number of clusters found
        $nc = safe_count($clusters);

        // Walk through all the clusters
        for ($i = 0; $i < $nc; ++$i) {
            // Start angle and number of slices in this cluster
            $csize = $clusters[$i][1];
            $a     = $this->la[$clusters[$i][0]];
            $q     = $this->Quadrant($a);

            // Now set up the start and end conditions to make sure that
            // in each cluster we walk through the all the slices starting with the slice
            // closest to the equator. Since all slices are numbered clockwise from "3'a clock"
            // we have different conditions depending on in which quadrant the slice lies within.
            if ($q == 0) {
                $start = $csize - 1;
                $idx   = $start;
                $step  = -1;
                $vstep = -$origvstep;
            } elseif ($q == 1) {
                $start = 0;
                $idx   = $start;
                $step  = 1;
                $vstep = -$origvstep;
            } elseif ($q == 2) {
                $start = $csize - 1;
                $idx   = $start;
                $step  = -1;
                $vstep = $origvstep;
            } elseif ($q == 3) {
                $start = 0;
                $idx   = $start;
                $step  = 1;
                $vstep = $origvstep;
            }

            // Walk through all slices within this cluster
            for ($j = 0; $j < $csize; ++$j) {
                // Now adjust the position of the labels in each cluster starting
                // with the slice that is closest to the equator of the pie
                $a = $this->la[$clusters[$i][0] + $idx];

                // Guide line start in the center of the arc of the slice
                $r = $radius + $this->explode_radius[$n - 1 - ($clusters[$i][0] + $idx)];
                $x = round($r * cos($a) + $xc);
                $y = round($yc - $r * sin($a));

                // The distance from the arc depends on chosen font and the "R-Factor"
                $r += $fh * $this->iGuideLineRFactor;

                // Should the labels be placed curved along the pie or in straight columns
                // outside the pie?
                if ($this->iGuideLineCurve) {
                    $xt = round($r * cos($a) + $xc);
                }

                // If this is the first slice in the cluster we need some first time
                // proessing
                if ($idx == $start) {
                    if (!$this->iGuideLineCurve) {
                        $xt = round($r * cos($a) + $xc);
                    }

                    $yt = round($yc - $r * sin($a));

                    // Some special consideration in case this cluster starts
                    // in quadrant 1 or 3 very close to the "equator" (< 20 degrees)
                    // and the previous clusters last slice is within the tolerance.
                    // In that case we add a font height to this labels Y-position
                    // so it doesn't collide with
                    // the slice in the previous cluster
                    $prevcluster = ($i + ($nc - 1)) % $nc;
                    $previdx     = $clusters[$prevcluster][0] + $clusters[$prevcluster][1] - 1;
                    if ($q == 1 && $a > 160 * M_PI / 180) {
                        // Get the angle for the previous clusters last slice
                        $diff = abs($a - $this->la[$previdx]);
                        if ($diff < $tresh_hold) {
                            $yt -= $fh;
                        }
                    } elseif ($q == 3 && $a > 340 * M_PI / 180) {
                        // We need to subtract 360 to compare angle distance between
                        // q=0 and q=3
                        $diff = abs($a - $this->la[$previdx] - 360 * M_PI / 180);
                        if ($diff < $tresh_hold) {
                            $yt += $fh;
                        }
                    }
                } else {
                    // The step is at minimum $vstep but if the slices are relatively large
                    // we make sure that we add at least a step that corresponds to the vertical
                    // distance between the centers at the arc on the slice
                    $prev_a = $this->la[$clusters[$i][0] + ($idx - $step)];
                    $dy     = abs($radius * (sin($a) - sin($prev_a)) * 1.2);
                    if ($vstep > 0) {
                        $yt += max($vstep, $dy);
                    } else {
                        $yt += min($vstep, -$dy);
                    }
                }

                $label = $this->labels[$clusters[$i][0] + $idx];

                if ($csize == 1) {
                    // A "meta" cluster with only one slice
                    $r  = $radius + $this->explode_radius[$n - 1 - ($clusters[$i][0] + $idx)];
                    $rr = $r + $img->GetFontHeight() / 2;
                    $xt = round($rr * cos($a) + $xc);
                    $yt = round($yc - $rr * sin($a));
                    $this->StrokeLabel($label, $img, $xc, $yc, $a, $r);
                    if ($this->iShowGuideLineForSingle) {
                        $this->guideline->Stroke($img, $x, $y, $xt, $yt);
                    }
                } else {
                    $this->guideline->Stroke($img, $x, $y, $xt, $yt);
                    if ($q == 1 || $q == 2) {
                        // Left side of Pie
                        $this->guideline->Stroke($img, $xt, $yt, $xt - $this->guidelinemargin, $yt);
                        $lbladj              = -$this->guidelinemargin - 5;
                        $this->value->halign = 'right';
                        $this->value->valign = 'center';
                    } else {
                        // Right side of pie
                        $this->guideline->Stroke($img, $xt, $yt, $xt + $this->guidelinemargin, $yt);
                        $lbladj              = $this->guidelinemargin + 5;
                        $this->value->halign = 'left';
                        $this->value->valign = 'center';
                    }
                    $this->value->Stroke($img, $label, $xt + $lbladj, $yt);
                }

                // Udate idx to point to next slice in the cluster to process
                $idx += $step;
            }
        }
    }

    public function StrokeAllLabels($img, $xc, $yc, $radius)
    {
        // First normalize all angles for labels
        $n = safe_count($this->la);
        for ($i = 0; $i < $n; ++$i) {
            $this->la[$i] = $this->NormAngle($this->la[$i]);
        }
        if ($this->guideline->iShow) {
            $this->StrokeGuideLabels($img, $xc, $yc, $radius);
        } else {
            $n = safe_count($this->labels);
            for ($i = 0; $i < $n; ++$i) {
                $this->StrokeLabel(
                    $this->labels[$i],
                    $img,
                    $xc,
                    $yc,
                    $this->la[$i],
                    $radius + $this->explode_radius[$n - 1 - $i]
                );
            }
        }
    }

    // Position the labels of each slice
    public function StrokeLabel($label, $img, $xc, $yc, $a, $r)
    {
        // Default value
        if ($this->ilabelposadj === 'auto') {
            $this->ilabelposadj = 0.65;
        }

        // We position the values diferently depending on if they are inside
        // or outside the pie
        if ($this->ilabelposadj < 1.0) {
            $this->value->SetAlign('center', 'center');
            $this->value->margin = 0;

            $xt = round($this->ilabelposadj * $r * cos($a) + $xc);
            $yt = round($yc - $this->ilabelposadj * $r * sin($a));

            $this->value->Stroke($img, $label, $xt, $yt);
        } else {
            $this->value->halign = 'left';
            $this->value->valign = 'top';
            $this->value->margin = 0;

            // Position the axis title.
            // dx, dy is the offset from the top left corner of the bounding box that sorrounds the text
            // that intersects with the extension of the corresponding axis. The code looks a little
            // bit messy but this is really the only way of having a reasonable position of the
            // axis titles.
            $this->value->ApplyFont($img);
            $h = $img->GetTextHeight($label);
            // For numeric values the format of the display value
            // must be taken into account
            if (is_numeric($label)) {
                if ($label > 0) {
                    $w = $img->GetTextWidth(sprintf($this->value->format, $label));
                } else {
                    $w = $img->GetTextWidth(sprintf($this->value->negformat, $label));
                }
            } else {
                $w = $img->GetTextWidth($label);
            }

            if ($this->ilabelposadj > 1.0 && $this->ilabelposadj < 5.0) {
                $r *= $this->ilabelposadj;
            }

            $r += $img->GetFontHeight() / 1.5;

            $xt = round($r * cos($a) + $xc);
            $yt = round($yc - $r * sin($a));

            // Normalize angle
            while ($a < 0) {
                $a += 2 * M_PI;
            }

            while ($a > 2 * M_PI) {
                $a -= 2 * M_PI;
            }

            if ($a >= 7 * M_PI / 4 || $a <= M_PI / 4) {
                $dx = 0;
            }

            if ($a >= M_PI / 4 && $a <= 3 * M_PI / 4) {
                $dx = ($a - M_PI / 4) * 2 / M_PI;
            }

            if ($a >= 3 * M_PI / 4 && $a <= 5 * M_PI / 4) {
                $dx = 1;
            }

            if ($a >= 5 * M_PI / 4 && $a <= 7 * M_PI / 4) {
                $dx = (1 - ($a - M_PI * 5 / 4) * 2 / M_PI);
            }

            if ($a >= 7 * M_PI / 4) {
                $dy = (($a - M_PI) - 3 * M_PI / 4) * 2 / M_PI;
            }

            if ($a <= M_PI / 4) {
                $dy = (1 - $a * 2 / M_PI);
            }

            if ($a >= M_PI / 4 && $a <= 3 * M_PI / 4) {
                $dy = 1;
            }

            if ($a >= 3 * M_PI / 4 && $a <= 5 * M_PI / 4) {
                $dy = (1 - ($a - 3 * M_PI / 4) * 2 / M_PI);
            }

            if ($a >= 5 * M_PI / 4 && $a <= 7 * M_PI / 4) {
                $dy = 0;
            }

            $this->value->Stroke($img, $label, $xt - $dx * $w, $yt - $dy * $h);
        }
    }

    public function UsePlotThemeColors($flag = true)
    {
        $this->use_plot_theme_colors = $flag;
    }
} // @class

/* EOF */
