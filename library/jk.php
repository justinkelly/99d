<?php

class jk
{

    public static function array_not_empty($values){

        $array = array();
        foreach($values as $key=>$value){
            if( ($value!=='') AND (!is_null($value))) $array[$key] = $value;
        }
        return $array;
    }

    public static function chart($values){
        $percent = ($values->no_of_deals_pocketed/$values->number_of_deals) * 10 ;
        $rounded = floor( $percent ) * 10 ;
        //$rounded = (floor(($deal->no_of_deals_pocketed/$deal->number_of_deals)*10))*10 ;
        if($rounded > '100'){ $rounded ='100';};
        if($percent > '0' and $percent < '1'){ $rounded ='05';};
        if($percent < '10' and $percent > '9.5'){ $rounded ='95';};
        return      $chart='chart_'.$rounded;
    }    

    function get_domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }                   
}
