<?php
include_once 'miners-settlement-init.php';
include_once 'local/minersmain.class.php';

$main = new MinersMain(array(
   'debugLevel'     => 0,
   'errorReporting' => false,
   'sessionStart'   => true,
   'memoryLimit'    => null,
   'sendHeaders'    => true,
   'database'       => true,
   'input'          => true,
   'html'           => true,
   'adminlte'       => true,
));

$main->title('Player Management');

$input = $main->obj('input');
$alte  = $main->obj('adminlte');

$addPlayer  = $input->get('addplayer','alphanumeric,dot,dash,underscore,space');
$deleteHash = $input->get('delete','alphanumeric,dot,dash,underscore,space');
$addPressed = ($input->get('add')) ? true : false;

if ($addPressed && $addPlayer) { 
   $main->addPlayer($addPlayer);
}
else if ($deleteHash) {
   if ($main->deletePlayer($deleteHash)) { header("Location: ".$main->pageUri); exit; }
} 

$errors = $main->error();

include 'ui/header.php';

if ($errors) { print $alte->displayRow($alte->errorCard($errors)); }

print playerDisplay($main).
      insertModal();

include 'ui/footer.php';

?>
<?php

function playerDisplay($main)
{
   $html            = $main->obj('html');
   $alte            = $main->obj('adminlte');
   $userId          = $main->userId;
   $playerList      = $main->getPlayerList();
   $playerBuildList = $main->getPlayerBuildList();

   $buildCounts = array();
   foreach ($playerBuildList as $buildId => $buildInfo) { $buildCounts[$buildInfo['player_id']]++; }

   $addContent = "<div class='input-group'>".
                 $html->inputText('addplayer',null,array('size' => 25)).
                 "<span class='input-group-append'>".
                 $html->submit('add','Add',array('class' => 'btn btn-primary btn-sm')).
                 "</span>".
                 "</div>";

   $listContent = "<table border=0 class='table' style='width:auto;'>".
                  "<thead><tr><th></th><th>Name</h><th>Builds</th><th>Created</th><th>Updated</th></tr></thead>".
                  "<tbody>";

   if ($playerList) {
      foreach ($playerList as $playerId => $playerInfo) {
         $deleteButton = sprintf("<button class='btn btn-tool fa fa-trash-alt text-danger open-modal' data-name='%s' data-value='%s' ".
                                 "data-toggle='modal' data-target='#modal-window' title='Delete Player' style='border:none;'></button>",
                                 $playerInfo['name'],$main->hashPlayerId($playerId)); 
         $listContent .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                                 $deleteButton,$playerInfo['name'],($buildCounts[$playerId] ?: 0),$playerInfo['created'],$playerInfo['updated']);
      }
   }
   else { $listContent .= "<tr><td colspan=6>No players found on this profile.</td></tr>"; }

   $listContent .= "</tbody>".
                   "</table>";

   return $html->startForm(array('method' => 'post')).
          $alte->displayRow($alte->displayCard($addContent,array('title' => 'Add Player'))).
          $html->endForm().
          $alte->displayRow($alte->displayCard($listContent,array('title' => 'Player List', 'container' => 'col-12', 'card' => 'card-success')));
}

function insertModal()
{
   return "<div class='modal fade' id='modal-window' aria-hidden='true' style='display:none;'>\n".
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
          "</div> <!-- /.modal -->\n\n".
          "<script>\n".
          "  $(document).on('click', '.open-modal', function() {\n".
          "     var deleteName  = $(this).data('name');\n".
          "     var deleteValue = $(this).data('value');\n".
          "     var modalHTML   = \"<p>Are you sure you want to delete \"+deleteName+\"?<input type='hidden' name='delete' id='delete' value='\"+deleteValue+\"'/>\";\n".
          "     $('#modal-body').html(modalHTML);\n".
          "     $('#modal-window').modal('show');\n".
          "  });\n".
          "</script>\n";
}


?>
