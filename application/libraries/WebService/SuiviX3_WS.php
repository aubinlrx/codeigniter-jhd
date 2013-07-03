<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('WebService.php');

/**
 * Gestion des mouvements de stock
 */
class SuiviX3_WS extends WebService {

	/**
	 * Constructeur de la classe
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Permet d'appeller le webService
	 * de suivie.
	 * @param [type] $cpt [description]
	 */
	function call_suivi($cpt) {
		
		$grp = "GRP1";
		
		$this->CAdxResultXml = $this->run("YTDFSUIVI", "<PARAM><GRP ID=\"$grp\"><FLD NAME=\"YCPT\">$cpt</FLD><FLD NAME=\"YRETOUR\"></FLD></GRP></PARAM>");
		$result = $this->CAdxResultXml->resultXml;
		
		if ($this->CAdxResultXml->status != 0) 
		{
			// Tout s'est bien passé. On peut mettre un message à l'utilisateur (dans la variable YRETOUR du flux XML)
			// return "Ok";

			return true; 
		} 
		else 
		{
			$result = $this->CAdxResultXml->messages ;
			
			return $result[0]->message ;
		}
	}
}
?>