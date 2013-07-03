<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Permet de rechercher si l'element passé
 * en paramètre est présent en id du tableau
 * @param  string $elem  
 * @param  array $array 
 * @return boolean       
 */
if ( ! function_exists('in_multiarray'))
{
    function in_multiarray($elem, $array)
    {

        $test = false;

        foreach($array as $key => $value)
        {
            if(is_array($elem))
            {
                $test_tmp = array();

                foreach ($elem as $k => $v) {
                    if($value[$k] == $v)
                    {
                        $test_tmp[] = true;
                    }
                }

                if(count($test_tmp) == 2)
                {
                    $test = true;
                }
            }
            else
            {
                if($value['id'] == $elem)
                {   
                    $test = true;
                }
            }
        }

        return $test;
    }
}