<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Auth {

		protected $CI;

		/**
		 * Construteur de la class Auth
		 */
		public function __construct() {
			$this->CI =& get_instance();
			$this->CI->load->helper('url');
			$this->CI->load->model('Matricule_model');
		}

		public function login($id, $url = false) 
		{
			$user = false;

			if( $id )
			{
				$user = $this->CI->Matricule_model->get_by('EMPNUM_0', $id);
			} 

			if( $user !== false && count($user) == 1 )
			{
				$user = (object) array(
						'EMPNUM_0'	=> $user->EMPNUM_0,
						'EMPDES_0'	=> $user->EMPDES_0,
						'EMPSHO_0'	=> $user->EMPSHO_0,
						'YTDFGRP_0' => $user->YTDFGRP_0
					);

				$data['user'] = serialize($user);

				$this->CI->session->set_userdata($data);

				return true;
			} 
			else 
			{
				return false;
			}

		}

		public function logout() 
		{
			$data['user'] = false;
			$data['authorized_url'] = false;
			$this->CI->session->set_userdata($data);
		}

		public function is_login() {
			if($this->CI->session->userdata('user') !== false)
			{
				return true;
			}
			else 
			{
				return false;
			}
		}

		public function get_logged_user() {
			if($this->is_login())
			{
				return unserialize($this->CI->session->userdata('user'));
			}
			
			return false;
		}

		public function get_num_logged_user() {
			$user = $this->get_logged_user();
			
			if($user !== false)
			{
				return $user->EMPNUM_0;
			}

			return false;
			
		}

		public function get_des_logged_user() {
			$user = $this->get_logged_user();

			if($user !== false)
			{
				return $user->EMPDES_0;
			}

			return false;
			
		}

		public function get_grp_logged_user() {
			$user = $this->get_logged_user();

			if($user !== false)
			{
				return $user->YTDFGRP_0;
			}
			
			return false;
			
		}
	}