<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation 
{

	/**
	 * Contructeur de la Class. 
	 * Elle appel aussi le contructeur de la class mère
	 * @param array $config 
	 */
    function __construct($config = array()){
    	parent::__construct($config);
    	$this->CI =& get_instance();
    }

    /**
     * Permet de retourner un tableau
     * d'erreur de validation.
     * @return array 
     */
    function error_array(){
	    if(count($this->_error_array) === 0){
	        return FALSE;
	    }else{
	        return $this->_error_array;
	    }
	}

	/**
	 * Permet de valider que le champs 
	 * à l'aide de la table et d'une
	 * colonne passées en paramètre
	 * @param  string $str  
	 * @param  string $args 
	 * @return boolean       
	 */
	public function exist($str, $args) {

		$args = explode('.', $args);

		$table = $args[0].'_model';

		if(count($args) > 2)
		{
			array_shift($args);
			$cols = array();
			$values = array();

			foreach($args as $key => $value)
			{
				if($key == 0)
				{
					array_push($cols, $value);
					array_push($values, $str);
				}
				else
				{
					$elem = explode(':', $value);
					array_push($cols, $elem[0]);
					array_push($values, $elem[1]);
				}
			}

			$arr = $this->CI->{$table}->get_by($cols, $values);
		}
		else
		{
			$col = $args[1];
			$arr = $this->CI->{$table}->get_by($col,$str);
		}

		return (count($arr) != 1) ? false : true;
	}

	/**
	 * Permet de validation que le champs est
	 * est une decimal.
	 * @param  string  $str  
	 * @param  string  $args 
	 * @return boolean       
	 */
	public function has_decimal($str, $args) {

	    if (! is_numeric($str))
	    {
	        // throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
	        return false;
	    }

	    if (strpos($str, ".") == false) {
	    	return true;
	    }

	    $nb_decimal = strlen($str) - strrpos($str, '.') - 1;

	    return ($nb_decimal == $args) ? true : false;
	}

	/**
	 * Permet de valider que le champs est 
	 * bien du format hh:mm
	 * @param 	string  $str  
	 * @param   string  $args 
	 * @return boolean       
	 */
	public function is_hours_minutes($str) {

		//format hh:mm : "/(?:[01][0-9]|2[0-4]):[0-5][0-9]/"
		return ( ! preg_match("/[0-9]+:[0-5][0-9]/", $str)) ? FALSE : TRUE;
	}

	/**
	 * Permet de valider que le champs est
	 * bien compris dans la liste.
	 * @param  string $str  
	 * @param  string $args 
	 * @return boolean
	 */		
	public function in_list($str, $args) {

		$args = explode(';', $args);
		
		if(count($args) == 4)
		{
			$table = $args[0].'_model';
			$col = $args[1];
			$type = $args[2];
			$value = $args[3];

			$arr = $this->CI->{$table}->get_many_by($col, $value);

			$list = array();
			foreach ($arr as $value) {
				$list[] = $value->{$type};
			}

			if(in_array($str, $list))
			{
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Permet de valider que le champs est
	 * bien une valeur comprise entre les 
	 * deux bornes alpha numeric passées 
	 * en arguments
	 * @param  string $str  
	 * @param  string $args 
	 * @return bool       
	 */
	public function between_alpha_numeric($str, $args) {
		
		$args = explode(';', $args);
		if(count($args) == 2)
		{
			$min = $args[0];
			$max = $args[1];

			$test_min = strcmp($string, $min);
			$test_max = strcmp($string, $max);

			if($test_min >= 0 && $test_max <= 0)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Permet de valider que le champs est
	 * bien une valeur comprise entre les
	 * deux bornes passées en arguments
	 * @param  string $str  
	 * @param  string $args 
	 * @return bool       
	 */
	public function between_numeric($str, $args) {
		
		$args = explode(';', $args);
		$min = $args[0];
		$max = $args[1];

		if(count($args == 2))
		{
			$min = floatval($min);
			$max = floatval($max);
			$numeric = floatval($str);

			if($numeric >= $min && $numeric <= $max)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Permet de valider que le champs est
	 * bien une valeur booleenne 0 ou 1
	 * @param  string $str 
	 * @return boolean
	 */
	public function boolean($str) {
		if($str == 0 || $str == 1)
		{
			return true;
		}

		return false;
	}
	
}