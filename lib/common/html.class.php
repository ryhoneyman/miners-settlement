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

class HTML extends Base
{
   protected $version = 1.2;

   public function table($data, $index = array(), $options = array())
   {
      if (!$options['datatable'] && (!is_array($data) || empty($data))) {
         //return "No valid data given to render table.";
      }

      if (empty($index)) {
         $index = array_values(array_slice($data,0,1));
         $index = array_keys($index[0]);
      }

      if (!is_array($index) || empty($index)) { return "Could not detect header columns for table."; }

      $header = array();
      foreach ($index as $name => $value) {
         list($hid,$hval) = (is_int($name)) ? array($value,$value) : array($value,$name);
         $header[$hid] = $hval;
      }

      $tid     = ($options['table.id'])    ? $options['table.id']    : 'epictable';
      $tclass  = ($options['table.class']) ? $options['table.class'] : 'epictable';
      $tstyle  = ($options['table.style']) ? $options['table.style'] : '';
      $thclass = ($options['datatable'])   ? " class='cursorpointer'" : '';

      $htrclass = ($options['compatibility.zebra']) ? " class='header'" : '';

      $return = "<table id='$tid' class='$tclass' style='$tstyle'>\n".
                "<thead><tr{$htrclass}>\n";

      if ($options['datatable.drilldown']) { array_unshift($header,''); }

      foreach ($header as $hid => $column) {
         $return .= "<th{$thclass}>$column</th>\n";
      }

      $return .= "</tr></thead>\n<tbody>";

      if (!is_array($data)) { $data = array(); }

      $rowcount = 0;

      foreach ($data as $id => $info) {
         $rowcount++;
         $trid = ($options['tr']['id'][$id]) ? $options['tr']['id'][$id] : '';
         $trclass = ($options['tr']['class'][$id]) ?
                     $options['tr']['class'][$id] :
                     (($options['table.tr.class']) ? $options['table.tr.class'] : '');

         if ($options['compatibility.zebra']) { $trclass = ($rowcount % 2 == 0) ? 'even' : 'odd'; }

         if ($trid) { $trid = " id='$trid'"; }
         if ($trclass) { $trclass = " class='$trclass'"; }

         $return .= "<tr{$trid}{$trclass}>\n";

         foreach ($header as $hid => $column) {
            $tdclass = ($options['td']['class']['cell'][$column][$id]) ?
                        $options['td']['class']['cell'][$column][$id] :
                        (($options['td']['class']['column'][$column]) ?
                         $options['td']['class']['column'][$column] :
                         (($options['table.td.class']) ?
                           $options['table.td.class'] : ''));

            if ($tdclass) { $tdclass = " class='$tdclass'"; }

            $tdval   = $info[$hid];
            $return .= "<td{$tdclass}>$tdval</td>\n";
         }

         $return .= "</tr>\n";
      }

      $return .= "</tbody>\n".
                 "</table>\n";

      if ($options['datatable']) {
         // Datatables options available at: http://www.datatables.net/ref
         //===============================================================
         $dtoptions = array(
            'aaSorting'      => '[]',
            'iDisplayLength' => 10,
            'aLengthMenu'    => '[[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]',
         );

         if ($options['datatable.ajax']) {
            $dtoptions['sAjaxSource'] = "'".$options['datatable.ajax']."'";
            $dtoptions['aoColumns'] = "[\n";
            foreach ($header as $hid => $column) {
               $dtoptions['aoColumns'] .= "            { 'mData': '$hid' },\n";
            }
            $dtoptions['aoColumns'] .= "      ]";
         }

         if ($options['datatable.drilldown']) {
            $dtoptions['bProcessing'] = 'true';
            $dtoptions['aoColumns'] =
            "[\n".
            "            {\n".
            "               'mData': null,\n".
            "               'sClass': 'control',\n".
            "               'sDefaultContent': '<img src=\"'+sImageUrl+'details_open.png'+'\">'\n".
            "            },\n";

            foreach ($header as $hid => $column) {
               if (!$hid) { continue; }
               $dtoptions['aoColumns'] .= "            { 'mData': '$hid' },\n";
            }

            $dtoptions['aoColumns'] .= "      ]";
         }

         if (is_array($options['datatable.options'])) {
            foreach ($options['datatable.options'] as $name => $value) {
               $dtoptions[$name] = $value;
            }
         }

         $optstring = "";
         foreach ($dtoptions as $name => $value) {
            $optstring .= "      '$name': $value,\n";
         }

         $extradt = "";
         if ($options['datatable.drilldown']) {
            $drillitems = $options['datatable.drilldown'];

            $extradt =  "$('#$tid').on('click', 'td.control', function () {\n".
                        "   var nTr = this.parentNode;\n".
                        "   var i = $.inArray( nTr, anOpen );\n\n".
                        "   if ( i === -1 ) {\n".
                        "      $('img', this).attr( 'src', sImageUrl+'details_close.png' );\n".
                        "      var nDetailsRow = oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), ".
                                                              "'details' );\n".
                        "      $('div.innerDetails', nDetailsRow).slideDown();\n".
                        "      anOpen.push( nTr );\n".
                        "   }\n".
                        "   else {\n".
                        "         $('img', this).attr( 'src', sImageUrl+'details_open.png' );\n".
                        "         $('div.innerDetails', $(nTr).next()[0]).slideUp( function () {\n".
                        "            oTable.fnClose( nTr );\n".
                        "            anOpen.splice( i, 1 );\n".
                        "         });\n".
                        "   }\n".
                        "});\n\n".
                        "function fnFormatDetails( oTable, nTr )\n".
                        "{\n".
                        "  var oData = oTable.fnGetData( nTr );\n".
                        "  var sOut =\n".
                        "    '<div class=\"innerDetails\">'+\n".
                        "      '<table cellpadding=5 cellspacing=0 border=0 ".
                                      "style=\"margin-left:5%;\">'+\n";

                        foreach ($drillitems as $column => $did) {
                           $extradt .= "        '<tr><td>$column</td><td>'+oData.$did+'</td></tr>'+\n";
                        }

            $extradt .= "      '</table>'+\n".
                        "    '</div>';\n".
                        "  return sOut;\n".
                        "}\n";


         }

         $filter = "";
         if ($options['datatable.filter']) {
            $filteropts = array(
               'sPlaceHolder' => '"head:after"',
            );
            if (is_array($options['datatable.filter.options'])) {
               foreach ($options['datatable.filter.options'] as $name => $value) {
                  $filteropts[$name] = $value;
               }
            }

            $filter = ".columnFilter({";
            foreach ($filteropts as $name => $value) {
               $filter .= "'$name': $value,";
            }
            $filter .= "})";
         }

         $return .= "<script>\n".
                    "var anOpen = [];\n".
                    "var sImageUrl = '/images/jquery/datatables/';\n".
                    "var oTable;\n\n".
                    "$(document).ready(function() {\n".
                    "   oTable = $('#$tid').dataTable({\n".
                    "$optstring".
                    "   })$filter;\n".
                    "});\n".
                    "$extradt\n".
                    "</script>\n";

         if ($options['datatable.drilldown']) {
            $return .= "<style>div.innerDetails { display: none }</style>\n";
         }
      }

      return $return;
   }

   public function divbox($name, $content = null, $options = null)
   {
      $background   = ($options['background']) ? $options['background'] : 'ffffff';
      $width        = ($options['width']) ? $options['width']."px" : 'auto';
      $display      = ($options['display']) ? $options['display'] : 'inline-block';
      $bordercolor  = ($options['border.color']) ? $options['border.color'] : '000000';
      $bordersize   = ($options['border.size']) ? $options['border.size'] : '1';
      $padding      = ($options['padding']) ? $options['padding'] : '10';
      $borderradius = ($options['border.radius']) ? $options['border.radius'] : '5';

      return "<div id='$name' style='background-color: #$background; width: $width; ".
                  "display: $display; ".
                  "border: ${bordersize}px #${bordercolor} solid; padding: ${padding}px; ".
                  "-moz-border-radius: ${borderradius}px; -webkit-border-radius: ${borderradius}px; ".
                  "border-radius: ${borderradius}px;'>$content</div>\n";
   }

   public function startform($properties = null)
   {
      $form   = "<form%s>\n";
      $fields = "";

      if (is_array($properties) && !empty($properties)) {
         $fieldlist = array();
         foreach ($properties as $name => $value) {
            $fieldlist[] = "$name='$value'";
         }
         $fields = ' '.implode(' ',$fieldlist);
      }
      $html = sprintf($form,$fields);

      return $html;
   }

   public function endform()
   {
      return "</form>\n";
   }

   public function submit($name = 'submit', $value = 'Go', $options = null)
   {
      $this->debug(5,"name=$name, value=$value");

      $class    = ($options['class']) ? $options['class'] : 'btn-wide btn btn-primary';
      $disabled = ($options['disabled']) ? $options['disabled'] : '';
      $type     = ($options['type']) ? $options['type'] : 'submit';

      $html = "<input type=$type class='$class $disabled' id='$name' name='$name' value='$value'>\n";

      return $html;
   }

   public function textarea($name, $text='', $cols=30, $rows=5)
   {
      $this->debug(5,"textarea called, name=$name, text=$text, cols=$cols, rows=$rows");

      $html = "<textarea id='$name' name='$name' cols='$cols' rows='$rows'>$text</textarea>\n";

      return $html;
   }

   public function input_text($name, $text='', $maxlength=2048, $size=20, $options = null)
   {
      $this->debug(5,"input_text called, name=$name, text=$text, maxlength=$maxlength, size=$size");

      $params = array("type=text","id='$name'","name='$name'","value='$text'","maxlength='$maxlength'","size='$size'");

      if (is_array($options['params'])) { 
         foreach ($options['params'] as $param => $paramValue) { $params[] = sprintf("%s='%s'",$param,$paramValue); }
      }

      $html = sprintf("<input %s>\n",implode(' ',$params));

      return $html;
   }

   public function hidden_text($name, $value)
   {
      $html = "<input type=hidden id='$name' name='$name' value='$value'>\n";

      return $html;
   }

   function select($name, $values, $select = array(), $attrib = array())
   {
      $size   = ($attrib['size'])   ? $attrib['size']   : 1;
      $multi  = ($attrib['multi'])  ? $attrib['multi']  : 0;
      $script = ($attrib['script']) ? $attrib['script'] : '';
      $assoc  = ($attrib['assoc'])  ? $attrib['assoc']  : 0;

      if (isset($select)) {
         if (is_array($select)) {
            foreach ($select as $item) { $selected[$item] = 1; }
         }
         else { $selected[$select] = 1; }
      }

      if (!$this->is_assoc($values) && !$assoc) {
         foreach ($values as $item) { $options[$item] = $item; }
      }
      else { $options = $values; }

      if (!is_array($options)) { $options = array(); }

      $html = sprintf("<select id='%s' name='%s%s' size='%s'%s%s>\n",
                      $name,$name,($multi) ? "[]" : "",$size,($multi) ? " multiple" : "",
                      ($script) ? " $script" : "");

      foreach ($options as $key => $value) {
         if (is_array($value)) {
            $html .= "<optgroup label='$key'>\n";
            foreach ($value as $gkey => $gvalue) {
               if (!$gvalue) { $gvalue = $gkey; }
               $html .= sprintf("<option value='%s'%s>%s</option>\n",
                                $gkey,(isset($selected[$gkey])) ? " selected" : "",$gvalue);
            }
            $html .= "</optgroup>\n";
         }
         else {
            $html .= sprintf("<option value='%s'%s>%s</option>\n",
                             $key,(isset($selected[$key])) ? " selected" : "",$value);
         }
      }

      $html .= "</select>\n";

      return $html;
   }

   public function checkbox($name, $value = 1, $checked = '')
   {
      $ischecked = 0;

      if (is_array($checked)) {
         foreach ($checked as $thischeck) {
            if ($thischeck == $value) {
               $ischecked = 1;
               break;
            }
         }
      }
      else if ($checked == $value) { $ischecked = 1; }

      return "<input type=checkbox name='$name' value='$value'".
             (($ischecked) ? ' checked' : '').">\n";
   }

   public function set($key,$value) {
      if (isset($key)) { $this->self["data.$key"] = $value; }
   }

   public function get($key) {
      if (isset($key)) { return $this->self["data.$key"]; }
   }

   public function clear($key) {
      if (isset($key)) { unset($this->self["data.$key"]); }
   }

   public function dynamictags($data)
   {
      $regex = '/\<dynamic\s+name\s*=\s*[\'"](\S+?)[\'"]\>/i';

      if (is_array($data)) {
         return $this->get($data[1]);
      }

      while (preg_match($regex,$data)) {
         $data = preg_replace_callback($regex,array(&$this,'dynamictags'),$data);
      }

      return $data;
   }

   public function parse($html) {
      if (!is_array($html)) {
         if (file_exists($html)) { $html = file($html); }
         else { return; }
      }

      $return = "";

      foreach ($html as $line) {
         $return .= $this->dynamictags($line);
      }

      return $return;
   }

   public function update_text($id, $text)
   {
      return "<script type='text/javascript'>\n".
             "document.getElementById('$id').innerHTML = '$text';\n</script>\n";
   }

   public function pulsate_text($id, $count = 5, $speed = 500)
   {
      return "<script type='text/javascript'>\n".
             "$(document).ready(function() {\n".
             "   var i = 0;\n".
             "   function pulsate() {\n".
             "      if(i >= $count) return;\n".
             "      $('.$id').\n".
             "         animate({opacity: 0.2}, $speed, 'linear').\n".
             "         animate({opacity: 1}, $speed, 'linear', pulsate);\n".
             "      i++;\n".
             "   }\n".
             "   pulsate();\n".
             "});\n".
             "</script>\n";
   }

   public function tipbox($type, $text)
   {
      $items = array(
         'tip' =>  array('title' => 'HELPFUL TIP', 'icon' => 'tip-lightbulb.png'),
         'note' => array('title' => 'INTERESTING NOTE', 'icon' => 'tip-pencil.png')
      );

      $return = "<table border=0 width=400><tr>\n".
                "<td valign=top><img src='/images/tip-icons/".$items[$type]['icon']."'></td>\n".
                "<td>\n".
                "<table border=1 width=400><tr id='tableevenrow'>\n".
                "<td valign=top><b>".$items[$type]['title'].":</b><br>\n".
                "$text</td>\n".
                "</tr></table>\n".
                "</td></tr></table>\n";

      return $return;
   }
}

?>
