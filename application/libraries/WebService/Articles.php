<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('WebService.php');

/**
* 	Affichage d'une liste d'article sous forme de tableau mis en page 
**/
class Articles extends WebService {

	/**
	 * Constructeur de la classe
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Permet de récupérer la liste des
	 * articles.
	 * @return [type] [description]
	 */
	public function showListe() {
		
		$WS = "*" ; 
		$this->CAdxResultXml = $this->query("ARTICLE",$WS ,100);
		$result = $this->CAdxResultXml->resultXml;
		
		$dom = new DomDocument();
		$dom->loadXML($result);
		$RES = $dom->getElementsByTagName('LIN');
		
		foreach ($RES as $R) {
			$commande = $R->getElementsByTagName('FLD');
			echo "<div id='itmref'>";
		
			foreach($commande as $c) {
				$val = $c->getAttribute('NAME') ;
				if ($val == "ITMREF") 	 
				{
					echo "<A href='monArticle.php?itmref=$c->nodeValue'>" ;
					echo $c->nodeValue ;
					echo "</A><br/>";
				}
				if ($val == "C2") 
				{
					echo $c->nodeValue ; 
					echo "</BR>";
				}
			}
			echo "</div><br>";
		}
		
		return "";
	}
}
?>