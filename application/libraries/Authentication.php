<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Authentication {
		protected $CI;
		protected $admin = "admin";
		protected $tma = "tma";
		protected $jhd = "jhd";
		protected $client = "client";
		
		protected $table_user = "users";
		protected $table_societe = "societes";
		
		/**
		* Authentication()
		* Methode de constructeur
		* permet d'obtenir une instance de Codeigniter afin d'instancier librarie, helper et model
		* avec l'objet CI.
		* @models : none
		* @view : none
		**/
		function Authentication () {
			$this->CI =& get_instance();
			$this->CI->load->database();
			$this->CI->load->helper('url');
			$this->CI->load->library('PasswordHash');
		}
		
		/**
		* login()
		* Methode qui va gérer la vérification d'authentification
		* @models : none
		* @params : username et password passé par le formulaire de connexion
		* @view : none
		**/
		function login($username, $password, $url = false) {
			
			//Requete de l'utilisateur dont l'username et egal à l'username passé en param.
			
			$row = '';
			//If adresse mail
			$Syntaxe='/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix'; 
			if(preg_match($Syntaxe,$username)) {
				$row = 'mail';	
			} else {
				$row = 'username';
			}
			
			$requete = 	"SELECT u.id, u.nom, u.prenom, u.id_societe, u.id_user_type, u.password_hash, s.is_client_helpdesk ".
						"FROM ".$this->table_user." as u ".
						"JOIN ".$this->table_societe." as s ON u.id_societe = s.id ".
						"WHERE u.".$row." = '".$username."'";
			
			$query = $this->CI->db->query($requete);
			
			//Si il existe
			if($query->num_rows == 1) {
				foreach ($query->result() as $row) {
					if($row->id_user_type == $this->CI->config->item('id_client') && $row->is_client_helpdesk == 0) {
						$this->CI->session->set_flashdata('error', 'Vous n\'êtes pas en droit de vous connecter.');
						redirect('/login');
						} else {
							//On vérifie que son password et le même que celui rentrer 
							//avec la lib passwordhash
							//var_dump($this->CI->passwordhash->HashPassword($password));
							if($this->CI->passwordhash->CheckPassword($password, $row->password_hash)) {
								$session_id = $this->_generate_uid(); 
								$data_session = array(
									'user_id' => $row->id,
									'user_nom_prenom' => $row->prenom." ".$row->nom,
									'user_type_id' => $row->id_user_type,
									'societe_id' => $row->id_societe,
									'uid' => $session_id
								);
								//On stock en session, iduser, idusertype, idsociete et uid(session_id)
								$this->CI->session->set_userdata($data_session);
								//On stock aussi l'uid en base pour la sécurité
								if($this->CI->db->query("UPDATE users SET session_id = '".$session_id."' WHERE id = '".$row->id."'")){
									if($url) {
										redirect($url);
									} else {
										redirect('/dashboard');	
									}
								}
							} else {
								//Sinon on retourne sur la page login avec un message d'erreur
								$this->CI->session->set_flashdata('error', 'le nom d\'utilisateur ou le mot de passe est incorrect');
								redirect('/login');
							}
						}	
				}
			} else {
				//Sinon on retourne sur la page login avec un message d'erreur
				$this->CI->session->set_flashdata('error', 'le nom d\'utilisateur ou le mot de passe est incorrect');
				redirect('/login');
			}
		}
		
		/**
		* logout()
		* Methode permettant de se déconnecter
		* @models : none
		* @params : none
		* @view : none
		**/
		function logout() {
			$data_session = array(
				'user_id' => '',
				'user_type_id' => '',
				'societe_id' => '',
				'uid' => ''
			);
			//On remet les valeurs de la session à zero.
			$this->CI->session->unset_userdata($data_session);
		}
		
		/**
		* is_login()
		* Methode qui va vérifier que le bon utilisateur est authentifié a chaque ouverture de page
		* @models : Users
		* @params : none
		* @view : none
		**/
		function is_login() {
			$is_log = FALSE;
			//Si l'user_id différent de null
			if($this->CI->session->userdata('user') != null){
				
				//On récupère les informations en session
				$user = unserialize($this->CI->session->userdata('user'));
				$session_id = $this->CI->session->userdata('session_id');

				//On vérifie que l'user_id et session_id en session correspond a un user
				$this->CI->load->model('Matricule_model');
				$query = $this->CI->Matricule_model->get($user->EMPNUM_0);

				if(count($query) == 1) {
					$is_log = TRUE;
				}
			}
			
			//On retourne un boolean
			return $is_log;
		}
		
		/**
		* is_admin()
		* Permet de tester si l'utilisateur est un admin
		**/
		function is_admin() {
			return $this->in_group($this->CI->config->item('admin_group'));
		}
		
		/**
		* is_consultant()
		* Permet de tester si l'utilisateur est un consultant
		**/
		function is_consultant() {
			return $this->in_group($this->CI->config->item('consultant_group'));
		}
		
		/**
		* is_tma()
		* Permet de tester si l'utilisateur fait partie du group tma
		**/
		function is_tma() {
			return $this->is_user($this->CI->config->item('id_tma_account'));
		}
		
		/**
		* is_jhd()
		* Permet de tester si l'utilisateur fait partie de jhd
		**/
		function is_jhd() {
			return $this->in_group($this->jhd);
		}
		
		/**
		* is_tma()
		* Permet de tester si l'utilisateur est un client
		**/
		function is_client() {
			return $this->in_group($this->CI->config->item('client_group'));
		}
		
		/**
		* in_group()
		* Methode qui va vérifier l'user connecté est dans le group passé en param
		* @models : Users
		* @params : id de l'user_type
		* @view : none
		**/
		function in_group($group) {
			//on recupére l'id de l'user en session
			$user_id = $this->CI->session->userdata('user_id');
			$query = $this->CI->db->query("SELECT id_user_type FROM users JOIN user_types ON user_types.id = users.id_user_type WHERE users.id = '".$user_id."' AND user_types.label = '".$group."'");
			//Si un resultat on return true
			if($query->num_rows == 1) {
				return true;
			}
			
			//Sinon on retourne false
			return false;
		}
		
		/**
		* is_user()
		* Methode qui va vérifier l'user connecté est l'user passé en param
		* @models : Users
		* @params : id de l'user
		* @view : non
		**/
		function is_user() {
			//on récupère l'id de l'user en session
			$user_id = $this->CI->session->userdata('user_id');
			$query = $this->CI->db->query("SELECT id FROM users WHERE id = ".$user_id);
			//Si un resultat on return true
			if($query->num_rows == 1) {
				return true;
			}
			
			return false;
		}
		
		/**
		* generate_password()
		* Permet générer un nouveau mot de passe aléatoire
		* @return (string) password
		**/
		function generate_password($length) {
			$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$i = 0;
			$password = "";
			while ($i <= $length) {
				$password .= $chars{mt_rand(0,strlen($chars))};
				$i++;
			}
			return $password;
		}
		
		/**
		* _generate_uid()
		* Methode permet de generer un session_id aleatoire.
		* @models : Users
		* @params : id de l'user_type
		* @view : none
		**/
		private function _generate_uid() {
			return md5(uniqid(rand(), true));
		}
	}
?>