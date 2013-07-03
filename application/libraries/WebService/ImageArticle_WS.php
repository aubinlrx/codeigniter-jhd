<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('WebService.php');

/**
* 	Affichage d'un article avec sa photo 
**/
class ImageArticle_WS extends WebService {

	/**
	 * Constructeur de la classe
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Permet de récupérer l'image  
	 * d'un article à l'aide du 
	 * Webservice.
	 * @param  [type] $itm [description]
	 * @return [type]      [description]
	 */
	public function showImage($itm) {
		$WS = "*" ;
		$timeStamp = time().mt_rand(100, 999);
		$arr = array();

		$cle = new CAdxParamKeyValue() ;
		$cle->key = "ITMREF"; 
		$cle->value = $itm ; 
		
		$this->CAdxResultXml = $this->read("ARTICLE", $cle);
		$result = $this->CAdxResultXml->resultXml;

		$dom = new DomDocument();
		$dom->loadXML($result);
		$RES = $dom->getElementsByTagName('GRP');
		
		foreach ($RES as $R) {
			$commande = $R->getElementsByTagName('FLD');
			
			foreach($commande as $c) {
				$val = $c->getAttribute('NAME') ;
				
				if ($val == "IMG") 	 {
					$mimetype = explode('/', $c->getAttribute('MIMETYPE'));
					$arr['filetype'] = $mimetype[0];
					$arr['extension'] = $mimetype[1];
					$arr['file'] = $timeStamp;
					$tempDir = 'assets/tmp/';
					$tempName = "tempImage_{$timeStamp}.tmp";					
					$data = $c->nodeValue ;
					file_put_contents($tempDir.$tempName, $data) ;
				}
			}
		}

		return $arr;
	}
}