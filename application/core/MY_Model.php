<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class MY_Model héritant de CI_Model
 * Cette classe base model permet de donnée accés au fonction principal de CRUD
 * équivalente au sein de tous les models de l'application.
 *
 * Method GET : GET, GET_BY, GET_ALL, GET_MANY, GET_MANY_BY
 * Method INSERT : INSERT, INSERT_MANY
 * Method UPDATE : UPDATE, UPDATE_BY, UPDATE_MANY, UPDATE_ALL
 * Method DELETE : DELETE, DELETE_BY, DELETE_MANY
 *
 * Des observers sont mis en place afin de d'accéder a des méthodes BEFORE, AFTER.
 */
class MY_Model extends CI_Model {

	/*------------------------------------------
	* Variables
	*------------------------------------------*/

	/**
	 * Le dossier utilisé
	 * @var string
	 */
	protected $_dossier;

	/**
	 * La table par défault du model
	 * @var string
	 */
	protected $_table;

	/**
	 * La base de donnée par défault du model
	 * @var string
	 */
	protected $_db;

	/**
	 * La clé primaire du model
	 * @var string
	 */
	protected $primary_key = 'id';

	/**
	 * Format de la date en base de donnée
	 * @var string
	 */
	protected $date_format = 'd-m-Y';

	/**
	 * Format de l'heure/minute et base de donnée
	 * @var string
	 */
	protected $time_format = 'Hi';

	/**
	 * Format de la date null
	 * @var string
	 */
	protected $null_date = '1753-01-01 00:00:00';

	/**
	 * Format de l'heure minute null
	 * @var string
	 */
	protected $null_time = '00:00';

	/**
	 * Parametre pour la function traduction()
	 * Reference de la table dans la table de traduction
	 * Reference des champs à traduire dans la table de traduction
	 */
	protected $table_trad = FALSE;
	protected $col_trad = array();

	/**
	 * Gestion de la suppression souple et des clés de suppression du model.
	 * @var boolean & string
	 */
	protected $soft_delete = FALSE;
	protected $soft_delete_key = 'deleted';
	protected $_temporary_with_deleted = FALSE;


	/**
	 * Les différents callbacks disponible pour le model. 
	 * Chacuns d'eux sont des simples listes de methodes.
	 * Ces methods méthodes fonctionneront sur $this.
	 * @var array
	 */
	protected $before_create = array();
    protected $after_create = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callbacks_parameters = array();

    /**
     * Un tableau pour les règles de validation
     * @var array
     */
    protected $validate = array();

    /**
     * Passer les validations mementanément.
     * Utiliser à l'aide de skip_validation() afin de passer la validation 
     * les prochaines executions.
     * @var boolean
     */
    protected $skip_validation = FALSE;

    /**
     * Par défaut Codeigniter convertis le resultat en objet.
     * Ces variables permettent de modifier ce comportement.
     * @var string
     */
    protected $return_type = 'object';
    protected $_temporary_return_type = NULL;

    /*------------------------------------------
	* Methodes Generiques
	*------------------------------------------*/

	public function __construct() {
		parent::__construct();
		$this->load->helper('inflector');
		$this->_set_database();
		$this->_set_table();
		$this->_fetch_table();
		$this->load_callback('after_get', 'to_utf8');
		$this->load_callback('before_create', 'to_iso');
		$this->load_callback('before_update', 'to_iso');
		$this->_temporary_return_type = $this->return_type;
	}

	/*------------------------------------------
	* Methodes CRUD
	*------------------------------------------*/

	/**
	 * Return un seul champs en fonction de la clé primaire
	 * @return object
	 */
	public function get($primary_value) 
	{
		$this->trigger('before_get');

		if($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->db->where($this->soft_delete_key, FALSE);
		}

		$this->_by_primary_key($primary_value);
		$row = $this->db->get($this->_table)
						->{$this->_return_type()}();

		/*$row = $this->db->where($this->primary_key, $primary_value)
						->get($this->_table)
						->{$this->_return_type()}();*/

		$this->_temporary_return_type = $this->return_type;

		$row = $this->trigger('after_get', $row);

		return $row;
	}

	/**
	 * Permet de retourner la clé primaire.
	 * @param  string $primary_value 
	 * @return array                
	 */
	public function get_primary_value($primary_value) {

		return $this->db->where($this->primary_key, $primary_value)
						->get($this->_table)
						->row(1);
	}

	/**
	 * Retourne un champs en fonction d'une clause WHERE. 
	 * Peut comprendre toutes les valeurs possible à :
	 * $this->db->where
	 */
	public function get_by()
	{
		$where = func_get_args();
		$this->_set_where($where);

		if($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->db->where($this->soft_delete_key, FALSE);
		}

		$this->trigger('before_get');

		$row = $this->db->get($this->_table)
						->{$this->_return_type()}();

		$this->_temporary_return_type = $this->return_type;

		$this->trigger('after_get', $row);

		return $row;
	}

	/**
	 * Retourne un tableau de donnée en fonction d'un array valeur de clé primaire
	 */
	public function get_many($values)
	{
		if($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->db->where($this->soft_delete_key, FALSE);
		}

		$this->_by_primary_key($values, 'where_in');
		
		//$this->db->where_in($this->primary_key, $values);

		return $this->get_all();
	}

	/**
	 * Retourne un tableau de donnée en fonction d'une clause WHERE.
	 */
	public function get_many_by()
	{
		$where = func_get_args();
		$this->_set_where($where);

		if($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->db->where($this->soft_delete_key, FALSE);
		}

		return $this->get_all();
	}

	/**
	 * Retourne toutes les données. Peut aussi servir comme $this->db->get()
	 */
	public function get_all()
	{
		$this->trigger('before_get');

		$result = $this->db->get($this->_table)
						   ->{$this->_return_type(1)}();

		$this->_temporary_return_type = $this->return_type;

		foreach($result as &$row) {
			$row = $this->trigger('after_get', $row);
		}

		//$result = $this->trigger('after_get', $result);

		return $result;
	}

	/**
	 * Permet d'insérer de nouvelle donnée dans la table. 
	 * Le parametre doit être un tableau associatif.
	 * Retourne l'id de l'élément créée
	 */
	public function insert($data, $skip_validation = FALSE)
	{
		$valid = TRUE;

		if($skip_validation === FALSE) {
			$valid = $this->_run_validation($data);
		}

		if($valid) 
		{
			$data = $this->trigger('before_create', $data);
			$this->db->insert($this->_table, $data);
			
			$insert_id = $this->insert_id();

			$insert_id = $this->trigger('after_create', array($data, $insert_id));

			return $insert_id;
		}
		else 
		{
			return FALSE;
		}
	}

	/**
	 * Permet d'inserer plusieurs lignes. Retourne un tableau de ces lignes.
	 */
	public function insert_many($data, $skip_validation = FALSE)
	{
		$ids = array();

		foreach($data as $row)
		{
			$ids[] = $this->insert($row, $skip_validation);
		}

		return $ids;
	}

	/**
	 * Permet de mettre à jour un ligne de la table en fonction de la clé primaire.
	 */
	public function update($primary_value, $data, $skip_validation = FALSE) {
		$valid = TRUE;

		$data = $this->trigger('before_update', $data);

		if($skip_validation === FALSE) 
		{
			$valid = $this->_run_validation($data);
		}

		if($valid)
		{

			$this->_by_primary_key($primary_value);
			$result = $this->db->set($data)
							   ->update($this->_table);

			/*$result = $this->db->where($this->primary_key, $primary_value)
							   ->set($data)
							   ->update($this->_table);*/

			$this->trigger('after_update', array($data, $result));

			return $result;
		}
		else 
		{
			return FALSE;
		}
	}

	/**
	 * Permet de mettre à jours des lignes de la table en fonction d'un tableau de valeurs de clé primaire.
	 */
	public function update_many($primary_values, $data, $skip_validation = FALSE) 
	{
		$valid = TRUE;

		$data = $this->trigger('before_update', $data);

		if($skip_validation === FALSE) 
		{
			$valid = $this->_run_validation($data);
		}

		if($valid)
		{
			$this->_by_primary_key($primary_values);
			$result = $this->db->set($data)
							   ->update($this->_table);

			/*$result = $this->db->where_in($this->primary_key, $primary_values)
							   ->set($data)
							   ->update($this->_table);*/

			$this->trigger('after_update', array($data, $result));

			return $result;
		}
		else 
		{
			return FALSE;
		}
	}


	/**
	 * Permet de mettre à jours des lignes de la table en fonction d'une clause WHERE
	 */
	public function update_by()
	{
		$args = func_get_args();
		$data = array_pop($args);
		$this->_set_where($args);

		$data = $this->trigger('before_update', $data);

		if($this->_run_validation($data))
		{
			$result = $this->db->set($data)
							   ->update($this->_table);

			$this->trigger('after_update', array($data, $result));

			return $result;
		}
		else 
		{
			return FALSE;
		}
	}

	/**
	 * Permet de mettre à jour tous les champs.
	 */
	public function update_all($data)
	{
		$data = $this->trigger('before_update', $data);
		$result = $this->db->set($data)
						   ->update($this->_table);

		$this->trigger('after_update', array($data, $result));

		return $result;
	}

	/**
	 * Permet de supprimer en fonction d'un id
	 */
	public function delete($id)
	{
		$this->trigger('before_delete', $id);

		$this->_by_primary_key($id);
		//$this->db->where($this->primary_key, $id);

		if($this->soft_delete)
		{
			$result = $this->db->update($this->_table, array($this->soft_delete_key => TRUE));
		}
		else 
		{
			$result = $this->db->delete($this->_table);
		}

		$this->trigger('after_delete', $result);

		return $result;
	}

	/**
	 * Permet de supprimer en fonction d'une clause WHERE
	 */
	public function delete_by()
	{
		$where = func_get_args();
		$this->_set_where($where);

		$where = $this->trigger('before_delete', $where);

		if($this->soft_delete)
		{
			$result = $this->db->update($this->_table, array($this->soft_delete_key => TRUE));
		}
		else
		{
			$result = $this->delete($this->_table);
		}

		$this->trigger('after_delete', $result);

		return $result;
	}

	/**
	 * Permet de supprimer en fonction de plusieurs valeurs de la clé primaire
	 */
	public function delete_many($primary_values)
	{
		$primary_values = $this->trigger('before_update', $primary_values);

		$this->_by_primary_key($primary_values);
		//$this->db->where($this->primary_key, $primary_values);

		if($this->soft_delete)
		{
			$result = $this->db->update($this->_table, array($this->soft_delete_key => TRUE));
		} 
		else 
		{
			$result = $this->delete($this->_table);
		}

		$this->trigger('after_update', $result);

		return $result;
	}
	

	/*------------------------------------------
	* Methode utile
	*------------------------------------------*/

	/**
	 * Retourne et genere un tableau form_dropdown
	 */
	function drop_down()
	{
		$args = func_get_args();

		if(count($args) == 2)
		{
			list($key, $value) = $args;
		}
		else {
			$key = $this->primary_key;
			$value = $args[0];
		}

		$this->trigger('before_dropdown', array($key, $value));

		$result = $this->db->select(array($key, $value))
						   ->get($this->_table)
						   ->result();

		$options = array();

		foreach($result as $row) 
		{
			$options[$row->{$key}] = $row->{$value};
		}


		$options = $this->trigger('after_dropdown', $options);

		return $options;
	}

	/**
	 * Retourne un count en fonction d'une valeur WHERE.
	 */
	public function count_by()
	{
		$where = func_get_args();
		$this->_set_where($where);

		return $this->db->count_all_results($this->_table);
	}

	/**
	 * Retourne le nombre total de resultat sans tenir compte des précédentes conditions
	 */
	public function count_all()
	{
		return $this->db->count_all($this->_table);
	}

	/**
	 * Permet de ne pas tenir compte des validations.
	 */
	public function skip_validation() 
	{
		$this->skip_validation = TRUE;
		return $this;
	}

	/**
	 * Getter pour skip_validation
	 */
	public function get_skip_validation() 
	{
		return $this->skip_validation;
	}

	/**
	 * Retourne l'id auto-increment suivant de la table. (Seulement MySQL)
	 */
	public function get_next_id() 
	{
		return (int) $this->db->select('AUTO_INCREMENT')
							  ->from('information_schema.TABLES')
							  ->where('TABLE_NAME', $this->_table)
							  ->where('TABLE_SCHEMA', $this->db->database)
							  ->get()
							  ->row()
							  ->AUTO_INCREMENT;

	}

	/**
	 * Getter pour le nom de la table
	 */
	public function get_table()
	{
		return $this->_table;
	}

	/*------------------------------------------
	* Createur de requete - Accés direct
	*------------------------------------------*/

	/**
	 * Un wrapper à $this->db->order_by()
	 */
	public function order_by($criteria, $order = 'ACS')
	{
		if( is_array($criteria) )
		{
			foreach($crireria as $key => $val)
			{
				$this->db->order_by($key, $val);
			}
		}
		else 
		{
			$this->db->order_by($criteria, $order);
		}

		return $this;
	}

	/**
	 * Un wrapper à $this->db->limit()
	 */
	public function limit($limit, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this;
	}


	/*------------------------------------------
	* Global Scopes
	*------------------------------------------*/

	/**
	 * Retourne l'appel suivant en tant qu'un tableau plutôt qu'un objet
	 */
	public function as_array()
	{
		$this->_temporary_return_type = 'array';
		return $this;
	}

	/**
	 * Retourne l'appel suivant en tant qu'un objet plutôt qu'un tableau
	 */
	public function as_objet()
	{
		$this->_temporary_return_type = 'object';
		return $this;
	}

	/**
	 * Ne tient pas compte de la suppression souple lors du prochain appel.
	 */
	public function with_deleted() 
	{
		$this->_temporary_with_deleted = TRUE;
		return $this;
	}

	/*------------------------------------------
	* Observeurs
	*------------------------------------------*/

	/**
	 * Mysql DATETIME created_at & updated_at
	 */
	public function created_at($row, $col = FALSE)
	{
		if($col == FALSE)
		{
			$row['created_at'] = date('Y-m-d H:i:s');
		}
		else 
		{
			$row[$col] = date('Y-m-d H:i:s');
		}
		
		return $row;
	}

	public function updated_at($row, $col = FALSE)
	{
		if($col == FALSE)
		{
			$row['updated_at'] = date('Y-m-d H:i:s');
		}
		else 
		{
			$row[$col] = date('Y-m-d H:i:s');
		}
		return $row;
	}

	/**
	 * Serialization automatiques des données, donnant la possibilité
	 * de passer a travers les objets et laissant la serialization se faire en arrière plan.
	 */
	public function serialize($row) 
	{
		foreach($this->callbacks_parameters as $column)
		{
			$row[$column] = serialize($row[$column]);
		}

		return $row;
	}

	public function unserialize($row) 
	{
		foreach ($this->callbacks_parameters as $column) {
			if(is_array($row)) 
			{
				$row[$column] = serialize($row[$column]);
			}
			else
			{
				$row->$column = unserialize($row->$column);
			}
		}

		return $row;
	}
	

	/*------------------------------------------
	* Methodes Internes
	*------------------------------------------*/

	/**
	 * Déclenche un évenement et appelle sont observateurs.
	 * @param   $event 
	 * @param	$data  
	 * @return  $data    
	 */
	public function trigger($event, $data = FALSE) 
	{
		if(isset($this->$event) && is_array($this->$event))
		{
			foreach($this->$event as $method)
			{
				if(strpos($method, '('))
				{
					preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

					$method = $matches[1];
					$this->callbacks_parameters = explode(',', $matches[3]);
				}

				$data = call_user_func(array($this, $method), $data);
			}
		}

		return $data;
	}

	public function insert_id() {
		$insert_id = $this->db->insert_id();

		if($insert_id === true)
		{
			$result = $this->db->query('select @@IDENTITY as id')->result();
			$insert_id = $result[0]->id;
		}

		return $insert_id;
	}

	/**
	 * Validation des données en fonction des données en paramètre
	 * @param  $data
	 * @return boolean
	 */
	private function _run_validation($data)
	{
		if($this->skip_validation) {
			return TRUE;
		}

		if(!empty($this->validate)) 
		{
			foreach($data as $key => $val)
			{
				$_POST['key'] = $val;
			}

			$this->load->library('form_validation');

			if(is_array($this->validate)) {
				$this->form_validation->set_rules($this->validate);

				return $this->form_validation->run();
			}
			else
			{
				return $this->form_validation->run($this->validate);
			}
		}
		else 
		{
			return TRUE;
		}
	}

	/**
	 * Utilise le pluriel du model comme table si elle n'est pas renseigné.
	 */
	private function _fetch_table()
	{
		if($this->_table == NULL)
		{
			$this->_table = plural(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
		}
	}

	/*------------------------------------------
	* Chargement de plusieurs base de données
	*------------------------------------------*/

	private function _set_database()
	{
		if(!$this->_db)
		{
			$this->load->database();
		}
		else 
		{
			$this->db = $this->load->database($this->_db, TRUE);
		}
	}

	/**
	 * Permet d'initialiser la table avec le nom du dossier
	 */
	private function _set_table()
	{
		$dossier = $this->config->item('dossier');
		if($dossier === false)
		{
			log_message('info', '"Dossier" params is not set');
			return;
		}
		else
		{
			$this->_dossier = $dossier;
			$this->_table = $this->_dossier . '.' . $this->_table;
		}
	}

	/**
	 * Utilise where de façon plus maligne
	 */
	private function _set_where($params) 
	{
		if(count($params) == 1)
		{
			$this->db->where($params[0]);
		}
		else
		{
			if(is_array($params[0]) && is_array($params[1]))
			{
				foreach($params[0] as $key => $col)
				{
					$this->db->where($col, $params[1][$key]);
				}
			}
			elseif(count($params[1]) == 1)
			{
				if(is_array($params[1]))
				{
					$this->db->where($params[0], $params[1][0]);
				}
				else
				{
					$this->db->where($params[0], $params[1]);
				}
			}
			else
			{
				$this->db->where_in($params[0], $params[1]);
			}
			
		}
	}

	/**
	 * Retourne le nom de la methode pour le type de retour
	 */
	private function _return_type($multi = FALSE)
	{
		$method = ($multi) ? 'result' : 'row';
		return $this->_temporary_return_type == 'array' ? $method . '_array' : $method; 
	}

	/**
	 * Retourne le where en cas de multiple primary key
	 */
	private function _by_primary_key($primary_value, $mode = TRUE)
	{
		
		$where = ($mode) ? 'where' : $mode;

		if(is_array($this->primary_key) && is_array($primary_value))
		{
			foreach ($this->primary_key as $key => $primary_key) 
			{	
				$this->db->{$where}($primary_key, $primary_value[$key]);
			}
		}
		else
		{
			$this->db->{$where}($this->primary_key, $primary_value);
		}
	}

	/**
	 * Function permettant d'encoder l'intégralité du resultat
	 * en UTF-8
	 */
	public function to_utf8($data) 
	{
		array_walk_recursive($data, 'MY_Model::encode_to_utf8');
		
		return $data;
	}

	/**
	 * Function permettant d'encoder en UTF-8
	 */
	public function encode_to_utf8(&$item, $key)
	{
		if(mb_detect_encoding($item) !== 'ASCII')
		{
			$item = utf8_encode($item);
		}		
	}

	/**
	 * Function permettant d'encoder l'intégralité du resultat
	 * en ISO-8859-1
	 */
	public function to_iso($data)
	{
		array_walk_recursive($data, 'MY_Model::encode_to_iso');

		return $data;
	}

	/**
	 * Function permettant d'encoder en ISO-8859-1
	 */
	public function encode_to_iso(&$item, $key)
	{
		$item = mb_convert_encoding($item, "ISO-8859-1");
	}



	

	/*---------------------------------------------
	* Possibilité de charger un Model dans un autre
	* --------------------------------------------*/

	/**
    *
    * Allow models to use other models
    *
    * This is a substitute for the inability to load models
    * inside of other models in CodeIgniter.  Call it like
    * this:
    *
    * $salaries = model_load_model('salary');
    * ...
    * $salary = $salaries->get_salary($employee_id);
    *
    * @param string $model_name The name of the model that is to be loaded
    *
    * @return object The requested model object
    *
    */
   public function load_model($model)
   {
   		$CI =& get_instance();
   		
   		if(!is_array($model))
   		{
   			$model_name = $model."_model";
      		$CI->load->model($model_name);
      		$this->{$model_name} = $CI->$model_name;
   		}
   		else
   		{
   			foreach ($model as $key => $name) {
   				$model_name = $name."_model";
   				$CI->load->model($model_name);
   				$this->{$model_name} = $CI->$model_name;
   			}
   		}
   		
   }

   /**
    * Permet de charger les callbacks
    * Se réalise dans le constructeur de chaque model.
    * @param  [type] $type  [description]
    * @param  [type] $value [description]
    * @return [type]        [description]
    */
   public function load_callback($type, $value) {

   		if(is_array($value))
   		{
   			foreach($value as $el)
   			{
   				if(method_exists($this, $el))
   				{
   					array_push($this->{$type}, $el);
   				}
   				else
   				{
   					show_error("can't find the method : " . $el . " for " . $type);
   				}
   			}
   		}
   		else
   		{
   			if(method_exists($this, $value))
   			{
   				array_push($this->{$type}, $value);
   			}
   			else{
   				show_error("can't find the method : " . $value . " for " . $type);
   			}
   		}
   }

   /**
    * Permet de retourner le dossier
    * @return string
    */
	public function get_dossier() 
	{
		return $this->_dossier;
	}
	
}