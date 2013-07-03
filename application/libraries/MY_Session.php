<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by Aubin LORIEUX
 */

/**
 * ------------------------------------------------------------------------
 * CI Session Class Extension for AJAX calls.
 * ------------------------------------------------------------------------
 *
 * ====- Save as application/libraries/MY_Session.php -====
 */

class MY_Session extends CI_Session {

    /**
     * sess_update()
     *
     * Permet de ne pas mettre à jours la sessin en cas de call ajax.
     *
     * @access    public
     * @return    void
     */
    public function sess_update()
    {
        $CI = get_instance();

        if ( ! $CI->input->is_ajax_request())
       {
           parent::sess_update();
       }
    }

    /**
     * sess_destroy()
     *
     * Nettoie le tableau user_data à la destruction de la session
     *
     * @access    public
     * @return    void
     */
    public function sess_destroy()
    {
        $this->userdata = array();

        parent::sess_destroy();
    }

}

/* End of file MY_Session.php */
/* Location: ./application/libraries/MY_Session.php */ 