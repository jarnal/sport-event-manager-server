<?php

namespace TeamManager\CommonBundle\Utils;

class CommonUtils
{

    /**
     *
     */
    static public function getCurrentSeason()
    {
        $date = new \DateTime();
        if($date->format('m') >= '09'){
            $season = (int)$date->format('Y');
            return $season."-".($season+1);
        } else {
            $season = (int)$date->format('Y');
            return ($season-1)."-".$season;
        }
    }

    /**
     * @param $pStartYear
     * @param $pEndYear
     */
    static public function getSeasonList($pStartYear, $pEndYear)
    {
        $seasons = array();
        for($i=$pStartYear; $i<=$pEndYear; $i++){
            $seasons[] = $i.'-'.($i+1);
        }
        return $seasons;
    }

}