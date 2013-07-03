<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Permet d'ajouter un paramêtre
 * en session en générant un UUID
 */
if ( ! function_exists('set_param_session'))
{
	function set_param_session($value)
	{
		$CI =& get_instance();
		$CI->load->helper('string');
		$CI->load->library('session');
		
		//Generation d'une chaine aléatoire
		$uuid = random_string('alnum', 10);
		
		//Stockage en session de la data avec
		//en clé l'id aleatoire.
		$data[$uuid] = $value;
		$CI->session->set_userdata($data);

		return $uuid; 
	}
}

/**
 * Permet de récupérer un paramêtre en 
 * session en fonction d'un UUID
 */
if ( ! function_exists('get_param_session'))
{
	function get_param_session($uuid)
	{
		$CI =& get_instance();
		$CI->load->library('session');

		//On récupère la valeur stocké en session.
		return $CI->session->userdata($uuid);
	}
}

/**
 * Permet de supprimer un paramêtre
 * en lui passant un UUID
 */
if( ! function_exists('dest_param_session'))
{
	function dest_param_session($uuid)
	{
		$CI =& get_instance();
		$CI->load->library('session');

		//Delete the value in session
		return $CI->session->unset_userdata($uuid);
	}
}