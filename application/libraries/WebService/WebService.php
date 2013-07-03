<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
*	Ensemble des classes nécessaires pour faire fonctionner les Web Services avec l'ERP Sage X3
*/
require_once('entities/cadx_technicalinfos.php');
require_once('entities/cadx_resultxml.php');
require_once('entities/cadx_paramkeyvalue.php');
require_once('entities/cadx_message.php');
require_once('entities/cadx_callcontext.php');
require_once('entities/cadx_param.php');

class WebService{

	/**
	 * Instance de codeigniter
	 * @var object
	 */
	private $CI;

	/**
	 * [$wsdl description]
	 * @var string
	 */
	private $wsdl;

	/**
	 * [$soapClient description]
	 * @var object
	 */
	private $soapClient;

	/**
	 * [$classmap description]
	 * @var array
	 */
	private $classmap;

	/**
	 * [$callContext description]
	 * @var array
	 */
	private $callContext = array();

	public function __construct() {

		$this->CI =& get_instance();
		$this->CI->config->load('webservice');

		$this->wsdl = $this->CI->config->item('ws_wsdl');
		$this->callContext = $this->CI->config->item('ws_context');
		$this->classmap = $this->CI->config->item('ws_classmap');

		$options = array();

		foreach($this->classmap as $key => $value) 
		{
			$options['classmap'][$key] = $value;
		}

		$this->soapClient = new SoapClient($this->wsdl, $options);

	}


	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param string $inputXml
	 * @return CAdxResultXml
	 */
	public function run($publicName, $inputXml) {

		$function_name = 'run';
		$args = array($this->callContext, $publicName, $inputXml);
		$options = array(
			'uri' => '',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
 	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param string $objectXml
	 * @return CAdxResultXml
	 */
	public function save($publicName, $objectXml) {

		$function_name = 'save';
		$args = array($this->callContext, $publicName, $objectXml);
		$options = array(
			'uri' => 'http://www.adonix/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @return CAdxResultXml
	 */
	public function delete( $publicName, $objectKeys) {

		$function_name = 'delete';
		$args = array($this->callContext, $publicName, $objectKeys);
		$options = array(
			'uri' => 'http://www.adonix/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @return CAdxResultXml
	 */
	public function read($publicName, $objectKeys) {

		$function_name = 'read';
		$args = array($this->callContext, $publicName, $objectKeys);
		$options = array(
			'uri' => 'http://www.adonix/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @param int $listSize
	 * @return CAdxResultXml
	 */
	public function query($publicName, $objectKeys, $listSize) {

		$function_name = 'query';
		$args = array($this->callContext, $publicName, $objectKeys, $listSize);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @return CAdxResultXml
	 */
	public function getDescription($publicName) {

		$function_name = 'getDescription';
		$args = array($this->callContext, $publicName);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @param string $objectXml
	 * @return CAdxResultXml
	 */
	public function modify($publicName, $objectKeys, $objectXml) {

		$function_name = 'modify';
		$args = array($this->callContext, $publicName, $objectKeys, $objectXml);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param string $actionCode
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @return CAdxResultXml
	 */
	public function actionObject($callContext, $publicName, $actionCode, $objectKeys) {

		$function_name = 'actionObject';
		$args = array($this->callContext, $publicName, $actionCode, $objectKeys);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @param string $blocKey
	 * @param ArrayOf_xsd_string $lineKeys
	 * @return CAdxResultXml
	 */
	public function deleteLines($callContext, $publicName, $objectKeys, $blocKey, $lineKeys) {

		$function_name = 'deleteLines';
		$args = array($this->callContext, $publicName, $objectKeys, $blocKey, $lineKeys);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @param ArrayOfCAdxParamKeyValue $objectKeys
	 * @param string $blocKey
	 * @param string $lineKey
	 * @param string $lineXml
	 * @return CAdxResultXml
	 */
	public function insertLines($callContext, $publicName, $objectKeys, $blocKey, $lineKey, $lineXml) {

		$function_name = 'insertLines';
		$args = array($this->callContext, $publicName, $objectKeys, $blocKey, $lineKey, $lineXml);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

	/**
	 *
	 *
	 * @param CAdxCallContext $callContext
	 * @param string $publicName
	 * @return CAdxResultXml
	 */
	public function getDataXmlSchema($callContext, $publicName) {

		$function_name = 'getDataXmlSchema';
		$args = array($this->callContext, $publicName);
		$options = array(
			'uri' => 'http://www.adonix.com/WSS',
			'soapaction' => ''
		);

		return $this->soapClient->__soapCall($function_name, $args, $options);
	}

}