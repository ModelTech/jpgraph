<?php

/**
 * JPGraph v4.0.3
 */

namespace ModelTech\JpGraph\Graph;

use ModelTech\JpGraph\Util;

/**
 * File:        JPGRAPH_DATE.PHP
 * // Description: Classes to handle Date scaling
 * // Created:     2005-05-02
 * // Ver:         $Id: jpgraph_date.php 1106 2009-02-22 20:16:35Z ljp $
 * //
 * // Copyright (c) Asial Corporation. All rights reserved.
 */
class DateScale extends LinearScale
{
    private $date_format     = '';
    private $iStartAlign     = false;
    private $iEndAlign       = false;
    private $iStartTimeAlign = false;
    private $iEndTimeAlign   = false;

    /**
     * CONSTRUCTOR.
     *
     * @param mixed $aMin
     * @param mixed $aMax
     * @param mixed $aType
     */
    public function __construct($aMin = 0, $aMax = 0, $aType = 'x')
    {
        assert($aType == 'x');
        assert($aMin <= $aMax);

        $this->type       = $aType;
        $this->scale      = [$aMin, $aMax];
        $this->world_size = $aMax - $aMin;
        $this->ticks      = new LinearTicks();
        $this->intscale   = true;
    }

    /**
     * Utility Function AdjDate()
     * // Description: Will round a given time stamp to an even year, month or day
     * // argument.
     *
     * @param mixed $aTime
     * @param mixed $aRound
     * @param mixed $aYearType
     * @param mixed $aMonthType
     * @param mixed $aDayType
     */
    public function AdjDate($aTime, $aRound = 0, $aYearType = false, $aMonthType = false, $aDayType = false)
    {
        $y = (int) date('Y', $aTime);
        $m = (int) date('m', $aTime);
        $d = (int) date('d', $aTime);
        $h = 0;
        $i = 0;
        $s = 0;
        if ($aYearType !== false) {
            $yearAdj = [0 => 1, 1 => 2, 2 => 5];
            if ($aRound == 0) {
                $y = floor($y / $yearAdj[$aYearType]) * $yearAdj[$aYearType];
            } else {
                ++$y;
                $y = ceil($y / $yearAdj[$aYearType]) * $yearAdj[$aYearType];
            }
            $m = 1;
            $d = 1;
        } elseif ($aMonthType !== false) {
            $monthAdj = [0 => 1, 1 => 6];
            if ($aRound == 0) {
                $m = floor($m / $monthAdj[$aMonthType]) * $monthAdj[$aMonthType];
                $d = 1;
            } else {
                ++$m;
                $m = ceil($m / $monthAdj[$aMonthType]) * $monthAdj[$aMonthType];
                $d = 1;
            }
        } elseif ($aDayType !== false) {
            if ($aDayType == 0) {
                if ($aRound == 1) {
                    //++$d;
                    $h = 23;
                    $i = 59;
                    $s = 59;
                }
            } else {
                // Adjust to an even week boundary.
                $w = (int) date('w', $aTime); // Day of week 0=Sun, 6=Sat

                // Adjust to start on Mon
                if ($w == 0) {
                    $w = 6;
                } else {
                    --$w;
                }

                if ($aRound == 0) {
                    $d -= $w;
                } else {
                    $d += (7 - $w);
                    $h = 23;
                    $i = 59;
                    $s = 59;
                }
            }
        }

        return mktime($h, $i, $s, $m, $d, $y);
    }

    /**
     * Wrapper for AdjDate that will round a timestamp to an even date rounding
     * // it downwards.
     *
     * @param mixed $aTime
     * @param mixed $aYearType
     * @param mixed $aMonthType
     * @param mixed $aDayType
     */
    public function AdjStartDate($aTime, $aYearType = false, $aMonthType = false, $aDayType = false)
    {
        return $this->AdjDate($aTime, 0, $aYearType, $aMonthType, $aDayType);
    }

    /**
     * Wrapper for AdjDate that will round a timestamp to an even date rounding
     * // it upwards.
     *
     * @param mixed $aTime
     * @param mixed $aYearType
     * @param mixed $aMonthType
     * @param mixed $aDayType
     */
    public function AdjEndDate($aTime, $aYearType = false, $aMonthType = false, $aDayType = false)
    {
        return $this->AdjDate($aTime, 1, $aYearType, $aMonthType, $aDayType);
    }

    /**
     * Utility Function AdjTime()
     * // Description: Will round a given time stamp to an even time according to
     * // argument.
     *
     * @param mixed $aTime
     * @param mixed $aRound
     * @param mixed $aHourType
     * @param mixed $aMinType
     * @param mixed $aSecType
     */
    public function AdjTime($aTime, $aRound = 0, $aHourType = false, $aMinType = false, $aSecType = false)
    {
        $y = (int) date('Y', $aTime);
        $m = (int) date('m', $aTime);
        $d = (int) date('d', $aTime);
        $h = (int) date('H', $aTime);
        $i = (int) date('i', $aTime);
        $s = (int) date('s', $aTime);
        if ($aHourType !== false) {
            $aHourType %= 6;
            $hourAdj = [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 6, 5 => 12];
            if ($aRound == 0) {
                $h = floor($h / $hourAdj[$aHourType]) * $hourAdj[$aHourType];
            } else {
                if (($h % $hourAdj[$aHourType] == 0) && ($i > 0 || $s > 0)) {
                    ++$h;
                }
                $h = ceil($h / $hourAdj[$aHourType]) * $hourAdj[$aHourType];
                if ($h >= 24) {
                    $aTime += 86400;
                    $y = (int) date('Y', $aTime);
                    $m = (int) date('m', $aTime);
                    $d = (int) date('d', $aTime);
                    $h -= 24;
                }
            }
            $i = 0;
            $s = 0;
        } elseif ($aMinType !== false) {
            $aMinType %= 5;
            $minAdj = [0 => 1, 1 => 5, 2 => 10, 3 => 15, 4 => 30];
            if ($aRound == 0) {
                $i = floor($i / $minAdj[$aMinType]) * $minAdj[$aMinType];
            } else {
                if (($i % $minAdj[$aMinType] == 0) && $s > 0) {
                    ++$i;
                }
                $i = ceil($i / $minAdj[$aMinType]) * $minAdj[$aMinType];
                if ($i >= 60) {
                    $aTime += 3600;
                    $y = (int) date('Y', $aTime);
                    $m = (int) date('m', $aTime);
                    $d = (int) date('d', $aTime);
                    $h = (int) date('H', $aTime);
                    $i = 0;
                }
            }
            $s = 0;
        } elseif ($aSecType !== false) {
            $aSecType %= 5;
            $secAdj = [0 => 1, 1 => 5, 2 => 10, 3 => 15, 4 => 30];
            if ($aRound == 0) {
                $s = floor($s / $secAdj[$aSecType]) * $secAdj[$aSecType];
            } else {
                $s = ceil($s / $secAdj[$aSecType] * 1.0) * $secAdj[$aSecType];
                if ($s >= 60) {
                    $s = 0;
                    $aTime += 60;
                    $y = (int) date('Y', $aTime);
                    $m = (int) date('m', $aTime);
                    $d = (int) date('d', $aTime);
                    $h = (int) date('H', $aTime);
                    $i = (int) date('i', $aTime);
                }
            }
        }

        return mktime($h, $i, $s, $m, $d, $y);
    }

    /**
     * Wrapper for AdjTime that will round a timestamp to an even time rounding
     * // it downwards.
     * // Example: AdjStartTime(mktime(18,27,13,2,22,2005),false,2) => 18:20.
     *
     * @param mixed $aTime
     * @param mixed $aHourType
     * @param mixed $aMinType
     * @param mixed $aSecType
     */
    public function AdjStartTime($aTime, $aHourType = false, $aMinType = false, $aSecType = false)
    {
        return $this->AdjTime($aTime, 0, $aHourType, $aMinType, $aSecType);
    }

    /**
     * Wrapper for AdjTime that will round a timestamp to an even time rounding
     * // it upwards
     * // Example: AdjEndTime(mktime(18,27,13,2,22,2005),false,2) => 18:30.
     *
     * @param mixed $aTime
     * @param mixed $aHourType
     * @param mixed $aMinType
     * @param mixed $aSecType
     */
    public function AdjEndTime($aTime, $aHourType = false, $aMinType = false, $aSecType = false)
    {
        return $this->AdjTime($aTime, 1, $aHourType, $aMinType, $aSecType);
    }

    /**
     * DateAutoScale
     * // Autoscale a date axis given start and end time
     * // Returns an array ($start,$end,$major,$minor,$format).
     *
     * @param mixed $aStartTime
     * @param mixed $aEndTime
     * @param mixed $aDensity
     * @param mixed $aAdjust
     */
    public function DoDateAutoScale($aStartTime, $aEndTime, $aDensity = 0, $aAdjust = true)
    {
        // Format of array
        // array ( Decision point,  array( array( Major-scale-step-array ),
        //       array( Minor-scale-step-array ),
        //       array( 0=date-adjust, 1=time-adjust, adjustment-alignment) )
        //
        $scalePoints =
            [
            /* Intervall larger than 10 years */
            SECPERYEAR * 10, [[SECPERYEAR * 5, SECPERYEAR * 2],
                [SECPERYEAR],
                [0, YEARADJ_1, 0, YEARADJ_1], ],

            /* Intervall larger than 2 years */
            SECPERYEAR * 2, [[SECPERYEAR], [SECPERYEAR],
                [0, YEARADJ_1], ],

            /* Intervall larger than 90 days (approx 3 month) */
            SECPERDAY * 90, [[SECPERDAY * 30, SECPERDAY * 14, SECPERDAY * 7, SECPERDAY],
                [SECPERDAY * 5, SECPERDAY * 7, SECPERDAY, SECPERDAY],
                [0, MONTHADJ_1, 0, DAYADJ_WEEK, 0, DAYADJ_1, 0, DAYADJ_1], ],

            /* Intervall larger than 30 days (approx 1 month) */
            SECPERDAY * 30, [[SECPERDAY * 14, SECPERDAY * 7, SECPERDAY * 2, SECPERDAY],
                [SECPERDAY, SECPERDAY, SECPERDAY, SECPERDAY],
                [0, DAYADJ_WEEK, 0, DAYADJ_1, 0, DAYADJ_1, 0, DAYADJ_1], ],

            /* Intervall larger than 7 days */
            SECPERDAY * 7, [[SECPERDAY, SECPERHOUR * 12, SECPERHOUR * 6, SECPERHOUR * 2],
                [SECPERHOUR * 6, SECPERHOUR * 3, SECPERHOUR, SECPERHOUR],
                [0, DAYADJ_1, 1, HOURADJ_12, 1, HOURADJ_6, 1, HOURADJ_1], ],

            /* Intervall larger than 1 day */
            SECPERDAY, [[SECPERDAY, SECPERHOUR * 12, SECPERHOUR * 6, SECPERHOUR * 2, SECPERHOUR],
                [SECPERHOUR * 6, SECPERHOUR * 2, SECPERHOUR, SECPERHOUR, SECPERHOUR],
                [1, HOURADJ_12, 1, HOURADJ_6, 1, HOURADJ_1, 1, HOURADJ_1], ],

            /* Intervall larger than 12 hours */
            SECPERHOUR * 12, [[SECPERHOUR * 2, SECPERHOUR, SECPERMIN * 30, 900, 600],
                [1800, 1800, 900, 300, 300],
                [1, HOURADJ_1, 1, MINADJ_30, 1, MINADJ_15, 1, MINADJ_10, 1, MINADJ_5], ],

            /* Intervall larger than 2 hours */
            SECPERHOUR * 2, [[SECPERHOUR, SECPERMIN * 30, 900, 600, 300],
                [1800, 900, 300, 120, 60],
                [1, HOURADJ_1, 1, MINADJ_30, 1, MINADJ_15, 1, MINADJ_10, 1, MINADJ_5], ],

            /* Intervall larger than 1 hours */
            SECPERHOUR, [[SECPERMIN * 30, 900, 600, 300], [900, 300, 120, 60],
                [1, MINADJ_30, 1, MINADJ_15, 1, MINADJ_10, 1, MINADJ_5], ],

            /* Intervall larger than 30 min */
            SECPERMIN * 30, [[SECPERMIN * 15, SECPERMIN * 10, SECPERMIN * 5, SECPERMIN],
                [300, 300, 60, 10],
                [1, MINADJ_15, 1, MINADJ_10, 1, MINADJ_5, 1, MINADJ_1], ],

            /* Intervall larger than 1 min */
            SECPERMIN, [[SECPERMIN, 15, 10, 5],
                [15, 5, 2, 1],
                [1, MINADJ_1, 1, SECADJ_15, 1, SECADJ_10, 1, SECADJ_5], ],

            /* Intervall larger than 10 sec */
            10, [[5, 2],
                [1, 1],
                [1, SECADJ_5, 1, SECADJ_1], ],

            /* Intervall larger than 1 sec */
            1, [[1],
                [1],
                [1, SECADJ_1], ],
        ];

        $ns = safe_count($scalePoints);
        // Establish major and minor scale units for the date scale
        $diff = $aEndTime - $aStartTime;
        if ($diff < 1) {
            return false;
        }

        $done = false;
        $i    = 0;
        while (!$done) {
            if ($diff > $scalePoints[2 * $i]) {
                // Get major and minor scale for this intervall
                $scaleSteps = $scalePoints[2 * $i + 1];
                $major      = $scaleSteps[0][min($aDensity, safe_count($scaleSteps[0]) - 1)];
                // Try to find out which minor step looks best
                $minor = $scaleSteps[1][min($aDensity, safe_count($scaleSteps[1]) - 1)];
                if ($aAdjust) {
                    // Find out how we should align the start and end timestamps
                    $idx = 2 * min($aDensity, floor(safe_count($scaleSteps[2]) / 2) - 1);
                    if ($scaleSteps[2][$idx] === 0) {
                        // Use date adjustment
                        $adj = $scaleSteps[2][$idx + 1];
                        if ($adj >= 30) {
                            $start = $this->AdjStartDate($aStartTime, $adj - 30);
                            $end   = $this->AdjEndDate($aEndTime, $adj - 30);
                        } elseif ($adj >= 20) {
                            $start = $this->AdjStartDate($aStartTime, false, $adj - 20);
                            $end   = $this->AdjEndDate($aEndTime, false, $adj - 20);
                        } else {
                            $start = $this->AdjStartDate($aStartTime, false, false, $adj);
                            $end   = $this->AdjEndDate($aEndTime, false, false, $adj);
                            // We add 1 second for date adjustment to make sure we end on 00:00 the following day
                            // This makes the final major tick be srawn when we step day-by-day instead of ending
                            // on xx:59:59 which would not draw the final major tick
                            ++$end;
                        }
                    } else {
                        // Use time adjustment
                        $adj = $scaleSteps[2][$idx + 1];
                        if ($adj >= 30) {
                            $start = $this->AdjStartTime($aStartTime, $adj - 30);
                            $end   = $this->AdjEndTime($aEndTime, $adj - 30);
                        } elseif ($adj >= 20) {
                            $start = $this->AdjStartTime($aStartTime, false, $adj - 20);
                            $end   = $this->AdjEndTime($aEndTime, false, $adj - 20);
                        } else {
                            $start = $this->AdjStartTime($aStartTime, false, false, $adj);
                            $end   = $this->AdjEndTime($aEndTime, false, false, $adj);
                        }
                    }
                }
                // If the overall date span is larger than 1 day ten we show date
                $format = '';
                if (($end - $start) > SECPERDAY) {
                    $format = 'Y-m-d ';
                }
                // If the major step is less than 1 day we need to whow hours + min
                if ($major < SECPERDAY) {
                    $format .= 'H:i';
                }
                // If the major step is less than 1 min we need to show sec
                if ($major < 60) {
                    $format .= ':s';
                }
                $done = true;
            }
            ++$i;
        }

        return [$start, $end, $major, $minor, $format];
    }

    // Overrides the automatic determined date format. Must be a valid date() format string
    public function SetDateFormat($aFormat)
    {
        $this->date_format = $aFormat;
        $this->ticks->SetLabelDateFormat($this->date_format);
    }

    public function AdjustForDST($aFlg = true)
    {
        $this->ticks->AdjustForDST($aFlg);
    }

    public function SetDateAlign($aStartAlign, $aEndAlign = false)
    {
        if ($aEndAlign === false) {
            $aEndAlign = $aStartAlign;
        }
        $this->iStartAlign = $aStartAlign;
        $this->iEndAlign   = $aEndAlign;
    }

    public function SetTimeAlign($aStartAlign, $aEndAlign = false)
    {
        if ($aEndAlign === false) {
            $aEndAlign = $aStartAlign;
        }
        $this->iStartTimeAlign = $aStartAlign;
        $this->iEndTimeAlign   = $aEndAlign;
    }

    public function AutoScale($img, $aStartTime, $aEndTime, $aNumSteps, $_adummy = false)
    {
        // We need to have one dummy argument to make the signature of AutoScale()
        // identical to LinearScale::AutoScale
        if ($aStartTime == $aEndTime) {
            // Special case when we only have one data point.
            // Create a small artifical intervall to do the autoscaling
            $aStartTime -= 10;
            $aEndTime += 10;
        }
        $done = false;
        $i    = 0;
        while (!$done && $i < 5) {
            list($adjstart, $adjend, $maj, $min, $format) = $this->DoDateAutoScale($aStartTime, $aEndTime, $i);
            $n                                            = floor(($adjend - $adjstart) / $maj);
            if ($n * 1.7 > $aNumSteps) {
                $done = true;
            }
            ++$i;
        }

        /*
        if( 0 ) { // DEBUG
        echo "    Start =".date("Y-m-d H:i:s",$aStartTime)."<br>";
        echo "    End   =".date("Y-m-d H:i:s",$aEndTime)."<br>";
        echo "Adj Start =".date("Y-m-d H:i:s",$adjstart)."<br>";
        echo "Adj End   =".date("Y-m-d H:i:s",$adjend)."<p>";
        echo "Major = $maj s, ".floor($maj/60)."min, ".floor($maj/3600)."h, ".floor($maj/86400)."day<br>";
        echo "Min = $min s, ".floor($min/60)."min, ".floor($min/3600)."h, ".floor($min/86400)."day<br>";
        echo "Format=$format<p>";
        }
         */

        if ($this->iStartTimeAlign !== false && $this->iStartAlign !== false) {
            Util\JpGraphError::RaiseL(3001);
            //('It is only possible to use either SetDateAlign() or SetTimeAlign() but not both');
        }

        if ($this->iStartTimeAlign !== false) {
            if ($this->iStartTimeAlign >= 30) {
                $adjstart = $this->AdjStartTime($aStartTime, $this->iStartTimeAlign - 30);
            } elseif ($this->iStartTimeAlign >= 20) {
                $adjstart = $this->AdjStartTime($aStartTime, false, $this->iStartTimeAlign - 20);
            } else {
                $adjstart = $this->AdjStartTime($aStartTime, false, false, $this->iStartTimeAlign);
            }
        }
        if ($this->iEndTimeAlign !== false) {
            if ($this->iEndTimeAlign >= 30) {
                $adjend = $this->AdjEndTime($aEndTime, $this->iEndTimeAlign - 30);
            } elseif ($this->iEndTimeAlign >= 20) {
                $adjend = $this->AdjEndTime($aEndTime, false, $this->iEndTimeAlign - 20);
            } else {
                $adjend = $this->AdjEndTime($aEndTime, false, false, $this->iEndTimeAlign);
            }
        }

        if ($this->iStartAlign !== false) {
            if ($this->iStartAlign >= 30) {
                $adjstart = $this->AdjStartDate($aStartTime, $this->iStartAlign - 30);
            } elseif ($this->iStartAlign >= 20) {
                $adjstart = $this->AdjStartDate($aStartTime, false, $this->iStartAlign - 20);
            } else {
                $adjstart = $this->AdjStartDate($aStartTime, false, false, $this->iStartAlign);
            }
        }
        if ($this->iEndAlign !== false) {
            if ($this->iEndAlign >= 30) {
                $adjend = $this->AdjEndDate($aEndTime, $this->iEndAlign - 30);
            } elseif ($this->iEndAlign >= 20) {
                $adjend = $this->AdjEndDate($aEndTime, false, $this->iEndAlign - 20);
            } else {
                $adjend = $this->AdjEndDate($aEndTime, false, false, $this->iEndAlign);
            }
        }
        $this->Update($img, $adjstart, $adjend);
        if (!$this->ticks->IsSpecified()) {
            $this->ticks->Set($maj, $min);
        }

        if ($this->date_format == '') {
            $this->ticks->SetLabelDateFormat($format);
        } else {
            $this->ticks->SetLabelDateFormat($this->date_format);
        }
    }
}
