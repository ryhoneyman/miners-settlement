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
// Overview: Base class from which all other classes are derived
//======================================================================================================
/* Example:

   class Example extends Base
   {
      ...
   }
*/
//======================================================================================================


class Base
{
   protected $version = 1.0;
   protected $self    = array();
   protected $data    = array();
   protected $debug   = null;

   //===================================================================================================
   // Description: Returns version of this class.
   // Input: null()
   // Output: int(version), Version number
   //===================================================================================================
   public function version() { return $this->version; }

   //===================================================================================================
   // Description: Creates the class object
   // Input: object(debug), Debug object created from debug.class
   // Input: array(options), List of options to set in the class
   // Output: null()
   //===================================================================================================
   public function __construct($debug = null, $options = null)
   {
      $this->debug           = $debug;
      $this->self['debug']   = $debug;
      $this->self['options'] = $options;

      if (isset($options)) {
         foreach ($options as $option => $optionval) { $this->set_option($option,$optionval); }
      }
   }

   //===================================================================================================
   // Description: Sets option to class
   // Input: string(opt), Name of option to set
   // Input: string(value), Name of value to set
   // Output: null()
   //===================================================================================================
   public function set_option($opt, $value = null) { $this->self["option.$opt"] = $value; }

   //===================================================================================================
   // Description: Gets option from class
   // Input: string(opt), Name of option to retrieve
   // Output: string(value), Value of option
   //===================================================================================================
   public function get_option($opt) { return $this->self["option.$opt"]; }

   //===================================================================================================
   // Description: Debug relay function; available if class was constructed with Debug object.
   // Input: int(level), Level of debug assertion (0-7)
   // Input: string(mesg), Message to write to debug
   // Output: null()
   //===================================================================================================
   public function debug($level,$mesg)
   {
      if (!isset($this->self['debug'])) { return; }

      $tracelevel = ($this->self["option.tracelevel"]) ? $this->self["option.tracelevel"] : 2;

      $this->self['debug']->trace($level,$mesg,$tracelevel);
   }

   public function set($var, $val = null) { $this->data[$var] = $val; }
   public function get($var) { return $this->data[$var]; }

   //===================================================================================================
   // Description: Checks whether an array is associative
   // Input: array(array), Name of array to check
   // Output: boolean(value), True if array was associative
   //===================================================================================================
   public function is_assoc($array) {
      return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
   }

   public function is_json($string) {
      return (!empty($string) && is_string($string) && is_array(json_decode($string,true)) && json_last_error() == 0);
   }
}

?>
