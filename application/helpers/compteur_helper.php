<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('generate_cpt'))
{
    function generate_cpt($suffix, $last_cpt = FALSE, $date_ref)
    {
        $date = $date_ref->format('ymd');
        $bool = false;
        $prefix = "0001";
    	
    	if($last_cpt && count($last_cpt) > 0) 
    	{
            
	    	$last_date = substr($last_cpt, -10, 6);

	    	if($date == $last_date)
	    	{
	    		 $cpt = preg_replace_callback( "|(\d+)|", "inc", $last_cpt);
                 $bool = TRUE;
	    	}
    	}

        if($bool == FALSE)
        {
            $cpt = $suffix.$date.$prefix;
        }

        return $cpt;
    }

    function inc($matches) {
    	 return ++$matches[1];
    }   
}