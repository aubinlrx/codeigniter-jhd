<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Asset Helper
 *
 * @package     JHD
 * @author      Aubin LORIEUX
 * @copyright   Copyright (c) 2013, Sekati LLC.
 * @license     http://www.opensource.org/licenses/mit-license.php
 * @link        http://aubinlorieux.com
 * @version     v1.0b
 *
 * @usage       $autoload['config'] = array('assets');
 *              $autoload['helper'] = array('assets');
 *              
 * @example     <?= css_asset('form', 'screen') ?>
 *              <link rel="stylesheet" href="form.css" media="screen" />
 *              
 * @example     <?= js_asset('lib/jquery', array('data-main => 'main.js'')) ?> 
 *              <script type="text/javascript" data-main="main.js" src="http://domain.com/assets/js/lib/jquery.js"></script>
 *
 * @example     <?= render_partial('form', $data, ['views/example/_form.php']) ?>
 *              <div class="partial">Contenu de la partial</div>
 *
 * @install     Add in CI application/config directory assets.php
 *              Add in CI application/helpers directory assets_helper.php
 *              Initialize both file in appliation/config/autload.php
 *
 *              $autoload['config'] = array('assets');
 *              $autoload['helper'] = array('assets');
 *
 *              Mandatory for the use of base_url()
 *              $autoload['helper'] = array('url');
 *
 * @notes       Organized assets in the top level of your CodeIgniter 2.x app:
 *                  - assets/
 *                      -- css/
 *                          -- styles.css
 *                      -- js/
 *                          --lib/
 *                              --modules/
 *                          -- controllers/
 *                          -- models/
 *                          -- script.js
 *                      -- img/
 *                          -- bg.png
 *                  - application/
 *                      -- config/asset.php
 *                      -- helpers/asset_helper.php
 */


/**
 * Get css URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('css_url'))
{
    function css_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('css_path');
    }
}

/**
 * Get js URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('js_url'))
{
    function js_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('js_path');
    }
}

/**
 * Get js controller URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('controller_url'))
{
    function controller_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('js_controllers_path');
    }
}

/**
 * Get js model URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('model_url'))
{
    function model_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('js_models_path');
    }
}

/**
 * Get js lib URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('lib_url'))
{
    function lib_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('js_lib_path');
    }
}

/**
 * Get module lib URL
 *
 * @access  public
 * @return  string
 */
if( ! function_exists('module_url'))
{
    function module_url()
    {
        $CI =& get_instance();

        return base_url() . $CI->config->item('js_modules_path');
    }
}

/**
 * Return css balise with the right target
 *
 * @access  public
 * @param   string  Filename
 * @return  string
 */
if ( ! function_exists('css_asset'))
{
    function css_asset($filename, $media = "all")
    {
        $extension = '.css';
        return '<link type="text/css" rel="stylesheet" media="' . $media . '" href="' . css_url(). $filename . $extension . '" />'."\n";
    }
}

/**
 * Return js balise with the right target by type
 *
 * @access  public
 * @param   string  Filename
 * @param   string  Type(default: false)
 * @return  string
 */
if ( ! function_exists('js_asset'))
{
    function js_asset($filename, $attrs = array())
    {
        $extension = '.js';
        $out = '<script type="text/javascript" src="' . js_url() . $filename . $extension . '"';

        foreach ($attrs as $key => $value) {
            $out .= ' ' . $key . '="' . $value . '"';
        }

        $out .= '></script>'."\n";

        return $out;
    }
}

/**
 * Permet d'utiliser une partial
 */
if( ! function_exists('render_partial'))
{
    public function render_partial($name, $data, $path = false)
    {

        $CI =& get_instance();

        if($path == false)
        {
            $path = $CI->router->directory . $CI->router->class . '/' . '_' . $name . ".php"; 
        }
        
        return $CI->load->view($path, $data, true);
    }
}

/**
 * Permet d'ajouter l'url en fonction
 * pour l'affichage d'une image BLOB
 * [!!!! => Nécessite le controllers image.php]
 */
if( ! function_exists('get_image_blob'))
{
    function get_image_blob($arr)
    {
        if(count($arr) == 3)
        {
            return base_url() . 'image/display?filetype=' .$arr['filetype']. '&extension=' .$arr['extension']. '&file=' .$arr['file'];
        }
        else
        {
            return "filenotfound.png";
        }
    }
}