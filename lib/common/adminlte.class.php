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

class AdminLTE extends Base
{
   protected $version = 1.0;
   public    $options = null;

   //===================================================================================================
   // Description: Creates the class object
   // Input: object(debug), Debug object created from debug.class
   // Input: array(options), List of options to set in the class
   // Output: null()
   //===================================================================================================
   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);
   }

   function displayRow($content)
   {
      return "<div class='row'>\n".
             $content.
             "</div>\n";
   }
   
   function errorCard($content, $cardProperties = null)
   {
      $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';
      $cardClass      = $cardProperties['card'] ?: 'card-danger';
      $cardTitle      = $cardProperties['title'] ?: 'Error';
      $cardId         = $cardProperties['id'] ?: 'errorcard';
   
      return "<div class='$containerClass'>".
             "    <div id='$cardId' class='card $cardClass'>".
             "       <div class='card-header'><b>$cardTitle</b>".
             "          <div class='card-tools'>".
             "             <button type='button' class='btn bg-danger btn-sm' data-card-widget='remove'><i class='fas fa-times'></i></button>".
             "          </div>".
             "       </div>".
             "       <div class='card-body'>".
             "       ".$content.
             "       </div>".
             "   </div>".
             "</div>";
   }
   
   function displayCard($content, $cardProperties = null)
   {
      $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';
      $cardClass      = $cardProperties['card'] ?: 'card-primary';
      $cardTitle      = $cardProperties['title'] ?: 'Card';
      $cardId         = $cardProperties['id'] ?: 'card';
   
      return "<div class='$containerClass'>".
             "    <div id='$cardId' class='card $cardClass'>".
             "       <div class='card-header'><h3 class='card-title text-bold'>$cardTitle</h3></div>".
             "       <div class='card-body'>".
             "       ".$content.
             "       </div>".
             "   </div>".
             "</div>";
   
   }
   
   function displayTabbedCard($content, $cardProperties = null)
   {
      $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';
      $cardClass      = $cardProperties['card'] ?: 'card-primary';
      $cardTitle      = $cardProperties['title'] ?: 'Card';
      $cardId         = $cardProperties['id'] ?: 'tabcard';
   
      $headerContent = array();
      $bodyContent   = array();
   
      foreach ($content as $position => $tabInfo) {
         $tabName   = $tabInfo['name'];
         $tabData   = $tabInfo['data'];
         $tabActive = ($position === 0) ? true : false;
   
         $headerContent[] = "             <li class='nav-item'><a class='nav-link".(($tabActive) ? ' active' : '')."' id='$cardId-$position-tab' ".
                                             "data-toggle='pill' href='#$cardId-$position' role='tab' aria-controls='$cardId-$position' ".
                                             "aria-selected='".(($tabActive) ? 'true' : 'false')."'>$tabName</a></li>\n";
         $bodyContent[]   = "             <div class='tab-pane fade".(($tabActive) ? ' show active' : '')."' id='$cardId-$position' role='tabpanel' ".
                                             "aria-labelledby='$cardId-$position-tab'>$tabData</div>\n";
      }
   
      return "<div class='$containerClass'>\n".
             "    <div class='card $cardClass card-tabs'>\n".
             "       <div class='card-header p-0 pt-1'>\n".
             "          <ul class='nav nav-tabs' id='$cardId' role='tablist'>\n".
             "             <li class='pt-2 px-3'><h3 class='card-title text-bold'>$cardTitle</h3></li>\n".
             implode('',$headerContent).
             "          </ul>\n".
             "       </div>\n".
             "       <div class='card-body'>\n".
             "          <div class='tab-content' id='{$cardId}Content'>\n".
             implode('',$bodyContent).
             "          </div>\n".
             "       </div>\n".
             "   </div>\n".
             "</div>\n";
   }
}

?>
