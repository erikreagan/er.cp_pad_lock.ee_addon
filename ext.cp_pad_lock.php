<?php
// ini_set('error_reporting',E_ALL);
/**
 * CP Pad Lock
 * 
 * This file must be placed in the
 * /system/extensions/ folder in your ExpressionEngine installation.
 *
 * @package CPPadLock
 * @version 1.0.0
 * @author Erik Reagan http://erikreagan.com
 * @copyright Copyright (c) 2010 Erik Reagan
 * @see http://github.com/erikreagan/er.cp_pad_lock.ee_addon
 */


if ( ! defined('EXT')) exit('Invalid file request');

define('CPPL_name', 'CP Pad Lock');
define('CPPL_version', '1.0.0');
define('CPPL_underscores', 'Cp_pad_lock');

class Cp_pad_lock
{
   
   private  $settings       = array();

   public   $name           = CPPL_name;
   public   $version        = CPPL_version;
   public   $description    = 'Lock the Control Panel down to only Super Admin access';
   public   $settings_exist = 'n';
   public   $docs_url       = '';



   /**
    * PHP4 Constructor
    *
    * @access   public
    * @see      __construct()
    */
   public function Cp_pad_lock($settings='')
   {
      $this->__construct($settings);
   }

   
   /**
    * PHP 5 Constructor
    *
    * @access  public
    * @param   array|string  Extension settings associative array or an empty string
    */
   public function __construct($settings='')
   {
      $this->settings = $settings;
   }


   
   /**
    * Activates the extension
    *
    * @access     public
    * @return     bool
    */
   public function activate_extension()
   {
      global $DB;

      $hooks = array(
         'show_full_control_panel_end' => 'show_full_control_panel_end'
      );

      foreach ($hooks as $hook => $method)
      {
         $sql[] = $DB->insert_string('exp_extensions',
            array(
               'extension_id' => '',
               'class'        => CPPL_underscores,
               'method'       => $method,
               'hook'         => $hook,
               'settings'     => '',
               'priority'     => 10,
               'version'      => CPPL_version,
               'enabled'      => "y"
            )
         );
      }

      // run all sql queries
      foreach ($sql as $query)
      {
         $DB->query($query);
      }
      
      return TRUE;
   }
   
   
   
   /**
    * Update the extension
    *
    * @access     public
    * @param      string
    * @return     bool
    */
   public function update_extension($current='')
   {
       global $DB;

       if ($current == '' OR $current == CPPL_version)
       {
           return FALSE;
       }

       $DB->query("UPDATE exp_extensions 
                   SET version = '".$DB->escape_str(CPPL_version)."' 
                   WHERE class = '".CPPL_underscores."'");
   }
   
   
   
   /**
    * Disables the extension the extension and deletes settings from DB
    * 
    * @access     public
    */
   public function disable_extension()
   {
       global $DB;
       $DB->query("DELETE FROM exp_extensions WHERE class = '".CPPL_underscores."'");
   }
   
   
   
   /**
    * Change the output of the CP
    *
    * @access     public
    * @return     string
    */
   public function show_full_control_panel_end( $out )
   {
      global $EXT, $SESS;
      
      if($EXT->last_call !== FALSE)
      {
         $out = $EXT->last_call;
      }
      
      $message = str_replace('This site','This site\'s Control Panel',file_get_contents(PATH.'utilities/offline.html'));
      
      return ($SESS->userdata['group_id'] != '1') ? $message : $out ;
      
   }
   
   
}
// END class

/* End of file ext.cp_pad_lock.php */
/* Location: ./system/extensions/ext.cp_pad_lock.php */