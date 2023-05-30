<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => true,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
));

$main->title('Player Management');

$input = $main->obj('input');

$main->fetchPlayerList($main);

$addPlayer    = $input->get('addplayer','alphanumeric,dot,dash,underscore,space');
$deletePlayer = $input->get('delete','alphanumeric,dot,dash,underscore,space');
$addPressed   = ($input->get('add')) ? true : false;

if ($addPressed && $addPlayer) { 
   if ($main->addPlayer($addPlayer)) { $main->fetchPlayerList($main); }
}
else if ($deletePlayer) {
   if ($main->deletePlayer($deletePlayer)) { header("Location: ".$main->pageUri); exit; }
} 


$errors = $main->error();

include 'ui/header.php';


if ($errors) { 
   print displayRow(errorCard($errors));
}

playerDisplay($main);

insertModal();

include 'ui/footer.php';

?>
<?php

function playerDisplay($main)
{
   $db         = $main->db();
   $html       = $main->obj('html');
   $userId     = $main->userId;
   $playerList = $main->var('playerList');

   $addContent = "<div class='input-group'>".
                 $html->inputText('addplayer',null,array('size' => 25)).
                 "<span class='input-group-append'>".
                 $html->submit('add','Add',array('class' => 'btn btn-primary btn-sm')).
                 "</span>".
                 "</div>";

   $listContent = "<table border=0 class='table' style='width:auto;'>".
                  "<thead><tr><th></th><th>Name</h><th>Gear</th><th>Runes</th><th>Created</th><th>Updated</th></tr></thead>".
                  "<tbody>";

   if ($playerList) {
      foreach ($playerList as $playerId => $playerInfo) {
         $deleteButton = sprintf("<button class='btn btn-tool fa fa-trash-alt text-danger open-modal' data-name='%s' ".
                                 "data-toggle='modal' data-target='#modal-window' title='Delete Player' style='border:none;'></button>",
                                 $playerInfo['name']); 
         $listContent .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                                 $deleteButton,$playerInfo['name'],'','',$playerInfo['created'],$playerInfo['updated']);
      }
   }
   else { $listContent .= "<tr><td colspan=6>No players found on this profile.</td></tr>"; }

   $listContent .= "</tbody>".
                   "</table>";

   print $html->startForm(array('method' => 'post')).
         displayRow(displayCard(array('header' => 'Add Player'),$addContent)).
         $html->endForm().
         displayRow(displayCard(array('header' => 'Player List', 'container' => 'col-12', 'card' => 'card-success'),$listContent));
}

function displayRow($content)
{
   return "<div class='row'>".
          $content.
          "</div>";
}

function errorCard($message, $options = null)
{
   $containerClass = $options['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';;
   $cardClass      = $options['card'] ?: 'card-danger';
   $cardTitle      = $options['title'] ?: 'Error';

   return "<div class='$containerClass'>".
          "    <div class='card $cardClass'>".
          "       <div class='card-header'><b>$cardTitle</b>".
          "          <div class='card-tools'>".
          "             <button type='button' class='btn bg-danger btn-sm' data-card-widget='remove'><i class='fas fa-times'></i></button>".
          "          </div>".
          "       </div>".
          "       <div class='card-body'>".
          "       ".$message.
          "       </div>".
          "   </div>".
          "</div>";
}

function displayCard($cardProperties, $content)
{
   $containerClass = $cardProperties['container'] ?: 'col-12 col-xl-3 col-lg-6 col-md-6 col-sm-12';;
   $cardClass      = $cardProperties['card'] ?: 'card-primary';
   $cardHeader     = $cardProperties['header'] ?: 'Card';

   return "<div class='$containerClass'>".
          "    <div class='card $cardClass'>".
          "       <div class='card-header'><b>$cardHeader</b></div>".
          "       <div class='card-body'>".
          "       ".$content.
          "       </div>".
          "   </div>".
          "</div>";

}

function insertModal()
{
   print "<div class='modal fade' id='modal-window' aria-hidden='true' style='display:none;'>\n".
         "<div class='modal-dialog modal-sm'>\n".
         "<div class='modal-content'><form>\n".
         "<div class='modal-header'><h4 class='modal-title'>Confirm Delete?</h4>\n".
         "  <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>\n".
         "</div>\n".
         "<div class='modal-body' id='modal-body'>\n".
         "</div>\n".
         "<div class='modal-footer justify-content-between'>\n".
         "  <button type='button' class='btn btn-default' data-dismiss='modal'>No</button>\n".
         "  <button type='submit' class='btn btn-primary'>Yes</button>\n".
         "</div>\n".
         "</form></div><!-- /.modal-content -->\n".
         "</div><!-- /.modal-dialog -->\n".
         "</div> <!-- /.modal -->\n";

   print "<script>\n".
         "  $(document).on('click', '.open-modal', function() {\n".
         "     var deleteName  = $(this).data('name');\n".
         "     var modalHTML   = \"<p>Are you sure you want to delete \"+deleteName+\"?<input type='hidden' name='delete' id='delete' value='\"+deleteName+\"'/>\";\n".
         "     $('#modal-body').html(modalHTML);\n".
         "     $('#modal-window').modal('show');\n".
         "  });\n".
         "</script>\n";
}


?>
