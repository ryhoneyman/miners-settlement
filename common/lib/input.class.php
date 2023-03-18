<?php

//    Copyright 2009,2010 - Ryan Honeyman
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>
//


//======================================================================================================
// Overview:
//======================================================================================================
/* Example:


*/
//======================================================================================================

include_once 'base.class.php';

class Input extends Base
{
   protected $version = 1.0;

   //===================================================================================================
   // Description: Creates the class object
   // Input: object(debug), Debug object created from debug.class
   // Input: array(options), List of options to set in the class
   // Output: null()
   //===================================================================================================
   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);

      $this->self['get.types'] = array(
         'alpha'   => 'a-zA-Z',
         'numeric' => '\d',
         'alphanumeric' => '\w',
         'space' => '\ ',
         'dot' => '\.',
         'period' => '\.',
         'comma' => '\,',
         'dash' => '\-',
         'pipe' => '\|',
         'underscore' => '\_',
         'colon' => '\:',
         'semicolon' => '\;',
         'parenthesis' => '\(\)',
         'forwardslash' => '\/',
         'backslash' => addslashes('\\'),
         'ampersand' => '\&',
         'percent' => '\%',
         'star' => '\*',
         'equals' => '\=',
      );
   }

   public function strip($value, $regex)
   {
      $return = preg_replace("/$regex/",'',$value);

      return $return;
   }

   public function match($value, $regex)
   {
      $return = preg_match("/$regex/",$value);

      return $return;
   }

   public function get($name, $allowed = 'alphanumeric')
   {
      $value = $this->variable($name);

      // This assumes the calling script will perform additional checks against the input
      // since we are returning the raw value here.  Allowing all can be dangerous if not
      // tempered on the caller side.
      //=================================================================================
      if ($allowed == 'all') {
         return $value;
      }
      else if ($allowed == 'email') {
         // This does not support an array of e-mail, which isn't ever used in my experience.
         //==================================================================================
         $valid = $this->match($value,'^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$');
         return ($valid) ? $value : NULL;
      }

      $patterns = $this->self['get.types'];

      $pattern = '[^';
      foreach (explode(',',$allowed) as $type) {
         $pattern .= ($patterns[$type]) ? $patterns[$type] : '';
      }
      $pattern .= ']+';

      if (is_array($value)) {
         $return = array();
         foreach ($value as $thisvalue) {
            $return[] = $this->strip($thisvalue,$pattern);
         }
      }
      else {
         $return = $this->strip($value,$pattern);
      }

      return $return;
   }

   public function variable($name)
   {
      return (isset($_GET[$name])) ? $_GET[$name] : ((isset($_POST[$name])) ? $_POST[$name] : '');
   }
}

?>
