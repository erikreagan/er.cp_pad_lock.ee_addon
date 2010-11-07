<?php if ( ! defined('BASEPATH') && ! defined('EXT') ) exit('Invalid file request');
// ini_set('error_reporting',E_ALL);
/**
 * CP Pad Lock
 *
 * @package CP Pad Lock
 * @version 1.5.0
 * @author Erik Reagan http://erikreagan.com
 * @copyright Copyright (c) 2010 Erik Reagan
 * @see http://github.com/erikreagan/er.cp_pad_lock.ee_addon
 */


class Cp_pad_lock_ext
{
   
   private  $settings       = array();

   public   $name           = 'CP Pad Lock';
   public   $version        = '1.1.0';
   public   $description    = 'Lock the Control Panel down to only Super Admin access';
   public   $settings_exist = 'y';
   public   $docs_url       = '';


   
   /**
    * Constructor
    *
    * @access  public
    * @param   array|string  Extension settings associative array or an empty string
    */
   public function Cp_pad_lock_ext($settings='')
   {
      
      if (version_compare(APP_VER, '2', '<'))
      {
         // EE 1.x is in play
         global $DB, $OUT;
         $this->DB   =& $DB;
         $this->OUT  =& $OUT;
      } else {
         // EE 2.x is in play
         $this->EE	=& get_instance();
         $this->DB   =& $this->EE->db;
         $this->OUT  =& $this->EE->output;
      }
      
      $this->settings = $settings;
      
   }
   // End constructor Cp_pad_lock_ext()

   
   /**
    * Activates the extension
    *
    * @access     public
    * @return     void
    */
   public function activate_extension()
   {
      
      $data = array(
         'class'        => __CLASS__,
         'method'       => 'sessions_end',
         'hook'         => 'sessions_end',
         'settings'     => serialize($this->settings()),
         'priority'     => 1,
         'version'      => $this->version,
         'enabled'      => "y"
      );

      $this->DB->query($this->DB->insert_string('exp_extensions',$data));
      
   }
   // End function activate_extension()
   
   
   
   
   /**
    * Update the extension
    *
    * @access     public
    * @param      string
    * @return     bool
    */
   public function update_extension($current='')
   {
      
      if ($current == '' OR $current == $this->version)
      {
         return FALSE;
      }

      $this->DB->query("UPDATE exp_extensions SET version = '".$DB->escape_str($this->version)."' WHERE class = '".__CLASS__."'");
      
   }
   // End function update_extension()
   
   
   
   
   /**
    * Disables the extension the extension and deletes settings from DB
    * 
    * @access     public
    */
   public function disable_extension()
   {
      $this->DB->query("DELETE FROM exp_extensions WHERE class = '".__CLASS__."'");
   }
   // End function disable_extension()
   
   
   
   
   /**
    * Settings for our extension
    *
    * @access     public
    * @return     array
    */
   public function settings()
   {
      
      $settings = array();
      
      $settings['message_page_title']   = "CP Pad Lock";
      $settings['message_page_heading'] = "The Control Panel is currently unavailable";
      $settings['message_content']      = "Due to system maintenance the Control Panel is currently unavailable.";
      
      return $settings;
      
   }
   // End function settings()
   
   
   
   
   /**
    * Change the output of the CP
    *
    * @access     public
    * @return     string
    */
   public function sessions_end( &$SESS )
   {
      
      // Only utilize this hook if we have a control panel request and the user's member group is NOT super admins
      // We also don't use this hook if the user isn't logged in yet. That would disable the login screen all together
      if (REQ == 'CP' && $SESS->userdata['member_id'] != '0' && $SESS->userdata['group_id'] != '1')
      {
         // Setup our contet
         $data = array(
            'title'   => $this->settings['message_page_title'],
            'heading' => $this->settings['message_page_heading'],
            'content' => $this->settings['message_content']
         );
         // Spit out the message
         return $this->OUT->show_message($data);
         
      } else {
         
         // Looks like we either aren't in a CP request OR the user is a super admin, so just act like nothing happened
         return $SESS;
         
      }
      
   }
   // End function sessions_end()
   
   
}
// End class Cp_pad_lock_ext()

/* End of file ext.cp_pad_lock.php */