<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Permet de récupérer le temps entre deux dates
 */
if ( ! function_exists('time_between_two_date'))
{
    function time_between_two_date($start_date, $end_date)
    {
        if(is_string($start_date))
        {
            $start_date = new DateTime($start_date);
        }
        if(is_string($end_date))
        {
            $end_date = new DateTime($end_date);
        }
        
        return $start_date->diff($end_date);   
    }
}

/**
 * Permet de récupérer le jour entre deux dates
 */
if( ! function_exists('day_between_two_date')) 
{
    function day_between_two_date($start_date, $end_date)
    {
        return time_between_two_date($start_date, $end_date)->format('%a');
    }
}

/**
 * Permet de convertir un float en un couple hh:mm
 */
if( ! function_exists('float_to_hours_minutes'))
{
    function float_to_hours_minutes($float)
    {
        $hours = (int) $float;
        $minutes = round(($float - $hours) * 60);

        if($minutes < 10) 
        {
            $minutes = "0".$minutes;
        }

        $string = "".$hours.":".$minutes."";

        return $string;
    }
}

/**
 * Permet de convertir un string en int heures
 */
if( ! function_exists('string_to_hours'))
{
    function string_to_hours($string)
    {
        $arr = explode(":", $string);

        return (int) $arr[1] / 60 
                + (int) $arr[0]; 
    }
}

/**
 * Permet de convertir une interval en minutes
 */
if( ! function_exists('interval_to_minutes'))
{
    function interval_to_minutes($interval)
    {
        return   ($interval->s / 60)
               + ($interval->i)
               + ($interval->h * 60)
               + ($interval->d * 24);
    }
}

/**
 * Permet de convertir une interval en heures
 */
if( ! function_exists('interval_to_hours'))
{
    function interval_to_hours($interval)
    {
        return   ($interval->s / 60 / 60)
               + ($interval->i / 60)
               + ($interval->h)
               + ($interval->d * 24);
    }
}

/**
 * Permet d'additionner deux intervales.
 */
if( ! function_exists('add_two_interval'))
{
    function add_two_interval($interval1, $interval2)
    {
        $tmp_interval = new DateTime('00:00');
        $interval = clone $tmp_interval;
        
        $tmp_interval->add($interval1);
        $tmp_interval->add($interval2);
        
        return $interval->diff($tmp_interval);
    }
}

/**
 * Permet soustraire deux intervales.
 */
if( ! function_exists('sub_two_interval'))
{
    function sub_two_interval($interval1, $interval2)
    {
        $tmp_interval = new DateTime('00:00');
        $interval = clone $tmp_interval;
    }
}

/**
 * Permet de soustraire une interval à une date
 */
if( ! function_exists('sub_interval_to_date'))
{
    function sub_interval_to_date($end_date, $interval)
    {
        if(is_string($end_date))
        {
            $end_date = new DateTime($end_date);
        }
        return $end_date->sub($interval);
    }
}

/**
 * Permet de retourner en fonction d'une 
 * date la semaine complète.
 */
if( ! function_exists('range_week'))
{
    function range_week($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        
        //$dt = strtotime($datestr);
        $dt = new DateTime($datestr);
        $start = clone $dt;
        $end = clone $dt;

        $res['start'] = $start->modify('Monday this week');
        $res['end'] = $end->modify('Sunday this week');

        return $res;
    }
}

/**
 * Permet en fonction d'une date de 
 * retourner le mois complet.
 */
if( ! function_exists('range_month'))
{
    function range_month($datestr) {
        date_default_timezone_set(date_default_timezone_get());

        $dt = new DateTime($datestr);
        $start = clone $dt;
        $end = clone $dt;

        $res['start'] = $start->modify('first day of this month');
        $res['end'] = $end->modify('last day of this month');

        return $res;
    }
}

/**
 * Permet de retourner en fonction d'une date
 * les jours de la semaine avec un format de date 
 */
if( ! function_exists('week_from_monday'))
{
    function week_from_monday($date, $format = false) {

        //Initialisation du format
        $format = ($format == false) ? 'Y-m-d' : $format;

        // Assuming $date is in format DD-MM-YYYY
        list($day, $month, $year) = explode("-", $date);

        // Get the weekday of the given date
        $wkday = date('l',mktime('0','0','0', $month, $day, $year));

        switch($wkday) {
            case 'Monday': $numDaysToMon = 0; break;
            case 'Tuesday': $numDaysToMon = 1; break;
            case 'Wednesday': $numDaysToMon = 2; break;
            case 'Thursday': $numDaysToMon = 3; break;
            case 'Friday': $numDaysToMon = 4; break;
            case 'Saturday': $numDaysToMon = 5; break;
            case 'Sunday': $numDaysToMon = 6; break;   
        }

        // Timestamp of the monday for that week
        $monday = mktime('0','0','0', $month, $day-$numDaysToMon, $year);

        $seconds_in_a_day = 86400;

        // Get date for 7 days from Monday (inclusive)
        for($i=0; $i<7; $i++)
        {
            $dates[$i] = date($format, $monday+($seconds_in_a_day*$i));
        }

        return $dates;
    }
}

/**
 * Permet de retourner en fonction d'une date
 * les jours du mois.
 * Il est possible spécifier un format.
 */
if( ! function_exists('days_of_month'))
{
    function days_of_month($date_debut, $format = false) {

        //Initialisation du format
        $format = ($format == false) ? 'Y-m-d' : $format;

        //Assuming $date is in format Y-m-d
        list($year, $month, $day) = explode("-", $date_debut);
        $end = new DateTime($date_debut);
        $end->modify('last day of this month');

        $last_day = $end->format('d');

        for($i=0; $i<=$last_day - 1; $i++)
        {
            $dates[$i] = date($format, mktime('0','0','0',$month, $day, $year));
            $day++;
        }

        return $dates;
    }
}

/**
 * Permet de convertir un tableau de date
 * un certain format.
 */
if ( ! function_exists('array_date_to_format')) 
{
    function array_date_to_format($arr, $format = 'Y-m-d') {

        $res = array();

        foreach ($arr as $key => $value) {
            if(is_string($value))
            {
                $date = new DateTime($value);
            }
            else
            {
                $date = $value;
            }
            $res[$key] = $date->format($format);    
        }

        return $res;
    }
}

/**
 * Permet d'ajouter une heure à une date
 */
if( ! function_exists('add_hours_to_datetime'))
{
    function add_hours_to_datetime($date, $hours)
    {
        if(is_string($date))
        {
            $date = new DateTime($date);
        }

        $hours_arr = str_split($hours, 2);
        $string_interval = 'PT'. (float) $hours_arr[0] . 'H' . (float) $hours_arr[1] . 'M';

        $final_date  = $date->add(new DateInterval($string_interval));

        return $final_date;
    }
}
