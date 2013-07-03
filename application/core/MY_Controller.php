<?php

class MY_Controller extends CI_Controller 
{
	
	/*------------------------------------------
	* Variables
	*------------------------------------------*/
	
	/**
	* La vue courant désirée
	* Automtiquement deviné en fonction
	* du nom du controller.
	**/
	protected $view = '';

	/**
	 * L'url de la page de login 
	 **/
	protected $login_url = '';
	
	/**
	* La donnée qui sera envoyé à la vue, au layout
	* ou tout autre template.
	**/
	protected $data = array();
	
	/**
	* Le nom du layout qui incorporera la vue
	**/
	protected $layout;
	
	/**
	* Un nombre arbitraire de class partiel ou colonne
	* latéral à ajouter au sein du layout.
	* $key = nom
	* $value = fichier
	**/
	protected $asides = array('header' => 'template/header', 'footer' => 'template/footer');
	
	/**
	* Un tableau permettant d'ajouter des feuilles de styles spécifique
	* au controller ou a une méthode.
	**/
	protected $default_stylesheets = array('reset','text','grid','styles');
	protected $stylesheets = array();
	
	/**
	* Un tableau permettant d'ajouter des scripts spécifique
	* au controller ou a une méthode
	**/
	protected $default_javascripts = array('lib/underscore', 'lib/jquery', 'lang', 'app', 'lib/modules/ajax', 'lib/modules/form_validation', 'lib/modules/alert_message', 'lib/modules/alert_error');
	protected $javascripts = array();

	/**
	 * Un tableau permettant d'ajouter des jsons
	 */
	protected $jsons = array();
	
	/**
	* La liste des modèles que l'ont désires charger automatiquement
	**/
	protected $models = array();
	
	/**
	* Un string pour la création du nom du modèle
	**/
	protected $model_string = '%_model';
	
	/**
	* Un tableau pour les methods du controleurs accessible seulement en admin.
	**/
	protected $admin_methods = array();
	
	/**
	* Un tableau pour les methods du controleurs accessible seulement en admin et consultant.
	**/
	protected $consultant_methods = array();
	
	/**
	* Un tableau pour les url autorisées sans authentification
	**/
	protected $authorize_url = array();
	
	/**
	* Un tableau des pages dont les liens doivent etre testé active ou non
	**/
	protected $links = array();

	/**
	 * Un string correspondant à la langue
	 */
	protected $language = '';
	
	/*------------------------------------------
	* Constructeur
	*------------------------------------------*/
	
	/**
	* Initialise le controller et essaye d'autoloader le model
	**/
	public function __construct() {
		parent::__construct();
		$this->load->library('auth');

		//Vérification de la connexion
		$this->_is_logged();

		//Set assets
		$this->_set_assets();

		//Gestion des langues
		$this->_set_lang();

		//Auto-load du model
		$this->_load_models();

		//Set les pages actives
		$this->_is_active_page();
	}
	
	/*------------------------------------------
	* Function d'autoload de la vue
	*------------------------------------------*/
	
	/**
	* Override du mechanisme d'execution de codeigniter.
	* Route la requete a travers la bonne action.
	* Cette methode supporte les custom 404 methodes et
	* charge automatiquement la vue au sein du layout. 
	**/
	public function _remap($method) {
		//Si method exist dans la controller
		if(method_exists($this, $method)) 
		{
			// Appel la function de retour avec le nom de la methode 
			// et l'uri complete sans la method
			call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));
		}
		else 
		{
			//Si une methode spécifique 404 existe pour le controller
			if(method_exists($this, '_404'))
			{
				call_user_func_array(array($this, '_404'), array($method));
			}
			else 
			{
				//Appel la page standard 404 pour l'url demandé
				show_404(strtolower(get_class($this)).'/'.$method);
			}
		}
		
		//Charge les stylesheets, les javascripts et les jsons
		$this->_load_stylesheets();
		$this->_load_javascripts();
		$this->_load_jsons();
		$this->_set_id_body();
		
		//Appel la fonction _load_view de la class
		$this->_load_view();
	}
	
	/**
	* Function permettant de charger automatiquement la vue
	* Permet aussi d'utiliser les conventions standard de codeigniter
	**/
	protected function _load_view(){
		//Si $this->views = FALSE on ne veut rien charger (utiliser les conventions habituel)
		if($this->view !== FALSE) 
		{

			$this->load->helper('url');

			// If $this->view n'est pas vide, il l'a charge. Sinon il essaye de deviner la 
			// vue en fonction du nom de la vue et de la method appellée
			if(!empty($this->view)) {
				$view = $this->view;
			} else {
				$view = $this->router->directory . $this->router->class . '/' . $this->router->method;
			}
			
			//Charger la vue yield du layout
			$data['yield'] = $this->load->view($view, $this->data, TRUE);
			
			//Test s'il y a des asides de déclaré
			if(!empty($this->asides))
			{
				foreach ($this->asides as $name => $file) 
				{
					$data['yield_'.$name] = $this->load->view($file, $this->data, TRUE);
				}
			}
			
			//Charge dans notre data actuel la vue et les asides
			$data = array_merge($this->data, $data);
			$layout = FALSE;
			
			// Si nous n'avons pas spécifié de layout particulier 
			// il essaye de le deviner
			if(!isset($this->layout))
			{
				if(file_exists(APPPATH . 'views/layouts/' . $this->router->class . '.php'))
				{
					$layout = 'layouts/' . $this->router->class;
				}
				else 
				{
					$layout = 'layouts/application';
				}
			}
			
			// Si on a spécifier un nom
			else if ($this->layout !== FALSE)
			{
				$layout = $this->layout;
			}
			
			// Si on est pas intéressé de charger un layout
			if($layout == FALSE)
			{
				$this->output->set_output($data['yield']);
			} 
			// On envoie la sauce !!!
			else 
			{
				$this->load->view($layout, $data);
			}
		} 
	}
	
	/*------------------------------------------
	* Function d'autoload de la vue
	*------------------------------------------*/
	
	/**
	 * Permet d'autoloader le model principal du controller
	 * @return boolean
	 */
	private function _load_models(){

		foreach ($this->models as $key => $model) 
		{
			$this->load->model($this->_model_name($model));
		}
	}
	
	/**
	 * Permet retourner le model à charger en fonction de $model_string
	 * @param  string $model nom du model
	 * @return string        
	 */
	protected function _model_name($model)
	{
		return str_replace('%', $model, $this->model_string);
	}
	
	/**
	 * Permet de vérifier que l'utilisateur actif est bien connecté
	 * @return boolean 
	 */
	private function _is_logged()
	{
		$url = uri_string();
		$login_page = 'login';

		if($url != $this->login_url && $url != $login_page) 
		{
			if(!$this->auth->is_login()) 
			{
				//Teste si c'est un requete ajax
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
				{
					$data = array(
						'error' => 'ajax_disconnected',
						'message' => "Access Denied - You have been disconnected"
						);

					$this->to_json($data);
					return;
				} 
				else(!in_array($url, $this->authorize_url))
				{
					redirect($this->login_url . '?url=' . $url);
				}
			}
		}
	}
	
	/**
	 * Permet d'autoloader les fichiers css
	 * @return array
	 */
	private function _load_stylesheets()
	{
		$stylesheets = $this->_get_asset_version($this->default_stylesheets, 'css', 'css');

		if($this->stylesheets != null) 
		{
			$this->stylesheets = $this->_get_asset_version($this->stylesheets, 'css', 'css');
			
			if(is_array($this->stylesheets))
			{
				foreach($this->stylesheets as $css)
				{
					array_push($stylesheets, $css);
				}
			}
			else
			{
				array_push($stylesheets, $this->stylesheets);
			}
			
			$this->data['stylesheets'] = $stylesheets;

		} else {
			$this->data['stylesheets'] = $this->default_stylesheets;	
		}
	}
	
	/**
	 * Permet d'autoloader automatiquement les fichiers javascripts.
	 * @return array 
	 */
	private function _load_javascripts()
	{
		$javascripts = $this->_get_asset_version($this->default_javascripts, 'js', 'js');

		if($this->javascripts != null) 
		{
			$this->javascripts = $this->_get_asset_version($this->javascripts, 'js', 'js');

			if(is_array($this->javascripts))
			{
				foreach($this->javascripts as $js)
				{
					array_push($javascripts, $js);
				}
			}
			else 
			{
				array_push($javascripts, $this->javascripts);
			}
			$this->data['javascripts'] = $javascripts;
		} 
		else 
		{
			$this->data['javascripts'] = $javascripts;
		}
	}

	/**
	 * Function permettant de déterminer la version 
	 * de l'asset en fonction de sa date de modification
	 */
	private function _get_asset_version($asset, $folder, $type) 
	{

		$url = 'assets/' . $folder . '/';
		$ext = '.' . $type;
 	
 		if($asset != null)
 		{
			if(is_array($asset))
			{
				foreach($asset as $key => $value)
				{
					$filename = $url . $value . $ext;
					$asset[$key] = $this->_asset_get_version($filename);
				}
			}
			else
			{
				$filename = $url . $asset . $ext;
				$asset = $this->_asset_get_version($filename);
			}
		}

		return $asset;
	}

	/**
	 * Permet de récupérer la version
	 * en fonction de la date de modification
	 * du fichier.
	 * @param  [type] $filename [description]
	 * @return [type]           [description]
	 */
	private function _asset_get_version($filename) 
	{
		$time = filemtime($filename);
		
		return $filename . '?v=' . $time;
	}

	/**
	 * Permet d'autoloader automatiquement les fichiers jsons.
	 * @return array
	 */
	private function _load_jsons()
	{
		if($this->jsons != null)
		{
			$this->data['jsons'] = $this->jsons;
		} else {
			$this->data['jsons'] = null;
		}
	}
	
	/**
	 * Permet de déterminer si le lien d'un menu est equivalent a la page courante.
	 * @return string : active ou non.
	 */
	private function _is_active_page () 
	{
		if(is_array($this->links))
		{
			foreach ($this->links as $key => $value) 
			{
				if($value == uri_string()) 
				{
					$this->data['active_links'][$key] = "active";
				} else 
				{
					$this->data['active_links'][$key] = "";
				}
			}
		}
	}

	/**
	 * Permet de setter de nouveau lien afin de tester leur activité.
	 */
	public function set_active_links($data) 
	{
		if(is_array($data)) {
			foreach($data as $link) 
			{
				array_push($this->links, $link);
			}
		} 
		else 
		{
			array_push($this->links, $data);
		}	
	}

	/**
	 * Permet de retourner en json si respond_to=json en GET
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function respond_to($type = false){
		if($this->input->get('respond_to') == $type)
		{
			return true;
		}
		elseif($type == 'html' && $this->input->get('respond_to') == false)
		{
			return true;
		}

		return false;
	}

	/**
	 * Permet de convertir un tableau en json 
	 * et de l'envoyer à la vue
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function to_json($data) {
		$this->view = false;
		header('Content-Type: application/json');
		echo utf8_encode(json_encode($data));
	}

	/**
	 * Permet de setter un json.
	 */
	public function set_json($name, $data, $obj = false)
	{
		$suffix = '';
		if($obj == false)
		{
			$suffix = "var " . $name;
		}
		else
		{
			$suffix = $obj . "." . $name;
		}
		$this->jsons[$name] = $suffix . " = " . json_encode($data);
	}

	/** 
	 * Permet d'utiliser une partial
	 */
	public function render_partial($name, $path = false)
	{
		if($path == false)
		{
			$path = $this->router->directory . $this->router->class . '/' . '_' . $name . ".php"; 
		}
		
		$this->data['partial'][$name] = $this->load->view($path, $this->data, true);
	}

	/**
	 * Permet d'initialiser la langue
	 * ainsi que de la stocker en session
	 */
	private function _set_lang() {
		$this->language = $this->config->item('language');
		$this->session->set_userdata('language', $this->language);
		$this->lang->load('template', $this->session->userdata('language'));
	}

	/**
	 * Permet d'ajouter un id au body
	 * en fonction du controller et de la
	 * method appellé.
	 */
	private function _set_id_body() {

		$id_body = '';

		if($this->uri->segment(2))
		{
			if($this->uri->segment(3))
			{
				$id_body = 'page-' . $this->uri->segment(2) . '-' . $this->uri->segment(3);
			}
			else
			{
				$id_body = 'page-' . $this->uri->segment(2) . '-index';
			}
		}
		else
		{
			$id_body = "page-index";
		}

		$this->data['id_body'] = $id_body;

		return true;
	}

}