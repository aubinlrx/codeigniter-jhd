<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CAdxParam {
	
	/**
	 * Instance de CodeIgniter
	 * @var object
	 */
	private $CI;

	/**
	 * Context
	 * @var [type]
	 */
	private $params;

	public function __construct() {
		$this->CI =& get_instance();
	}
	
	function setContext($p) {
			
		$this->params["PARAM"] = array(
			"AX_PAR" => array(
				array(
					"AXPARCOD" => '$REC_MAX',
					"AXPARVAL" => $p['pagination']['perPage']
				),
				array(
					"AXPARCOD" => '$REC_STARTAT',
					"AXPARVAL" => $p['pagination']['startAt']
				),
				array(
					"AXPARCOD" => '$TOTAL_COUNT',
					"AXPARVAL" => '0'
				),
				array(
					"AXPARCOD" => '$RET_COUNT',
					"AXPARVAL" => '0'
				),
				array(
					"AXPARCOD" => '$HAS_NEXT',
					"AXPARVAL" => '0'
				),
				array(
					"AXPARCOD" => 'SALFCY',
					"AXPARVAL" => $this->CI->session->userdata('salfcy')
				),
				array(
					"AXPARCOD" => 'BPRNUM',
					"AXPARVAL" => $this->CI->session->userdata('bprnum')		
				),
				array(
					"AXPARCOD" => 'STOFCY',
					"AXPARVAL" => $this->CI->session->userdata('stofcy')
				),
				array(
					"AXPARCOD" => 'DEFSTOFCY',
					"AXPARVAL" => 'P21'
				),
				array(
					"AXPARCOD" => 'CUR',
					"AXPARVAL" => $this->CI->session->userdata('cur')
				)
			)
		);
	}
	
	
	function addAXPAR ($arrayAXPAR) {
		$this->params["PARAM"]["AX_PAR"][] = $arrayAXPAR;
	}
	
	function addAXWHR ($arrayAXWHR) {
		$this->params["PARAM"]["AX_WHR"][] = $arrayAXWHR;
	}
	
	function addAXORD ($arrayAXORD) {
		$this->params["PARAM"]["AX_ORD"][] = $arrayAXORD;
	}
	
	
	function addRES ($RES) {
		$this->params["PARAM"]["RES"] = $RES;
	}
	
	function addG1 ($G1) {
		$this->params["PARAM"]["G1"] = $G1;
	}
	
	function addAXLOGPAR ($LOGINF) {
		$this->params["PARAM"]["AXLOG_PAR"] = $LOGINF;
	}
	
	function addPARAMelem($name,$TAB) {
		$this->params["PARAM"][$name] = $TAB;
	}
	
	function getArray () {
		return $this->params;
	}
	
	function getJson () {
		return json_encode($this->params);
	}
	
}