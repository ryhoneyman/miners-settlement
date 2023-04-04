<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/main.class.php';
include_once 'common/random.class.php';

class MinersMain extends Main
{
   public $userId = null;

   public function __construct($debug = null, $options = null)
   {
      parent::__construct($debug,$options);
   }

   public function webhookInit()
   {
      $currentId = $_COOKIE['userid'] ?: null;
      $userId    = $currentId ?: $this->generateUserId();

      $cookieExpires = time()+(60*60*24*400); // 400 days is enforced in browsers;

      if (!$currentId) {
         $this->sendCookies(array('userid' => array('value' => $userId, 'expires' => $cookieExpires, 'path' => null, 'domain' => $_SERVER['SERVER_NAME'])));
      }

      $this->userId = $userId;
   }

   public function webhookFinal() 
   {
      $this->updateProfile($this->userId);
   }

   private function generateUserId()
   {
      $random = new Random($this->debug);

      return $random->uniqueId();
   }

   private function updateProfile($userId)
   {
      if (!$this->db()) { return null; }

      $statement = "insert into profile (id,created,updated) values (?,now(),now()) ".
                   "on duplicate key update updated = values(updated)";
      $types     = 's';
      $data      = array($userId);

      $result = $this->db()->bindExecute($statement,$types,$data);
  
      return $result;
   }

   public function deletePlayer($playerName)
   {
      $userId     = $this->userId;
      $playerList = $this->var('playerList');
   
      if (!$userId) { $this->error('Cannot add player, no user ID detected.'); return false; }
   
      if (!$playerList[$playerName]) { $this->error("Cannot delete player $playerName, player does not exist."); return false; }
   
      $dbResult = $this->db()->bindExecute("delete from player where profile_id = ? and name = ?",
                                           "ss",array($userId,$playerName));
   
      $success = ($dbResult) ? true : false;
   
      if (!$success) { $main->this('Cannot delete player, database error.'); }
   
      return $success;
   }
   
   public function addPlayer($playerName)
   {
      $userId     = $this->userId;
      $playerList = $this->var('playerList');
   
      if (!$userId) { $this->error('Cannot add player, no user ID detected.'); return false; }
   
      if ($playerList[$playerName]) { $this->error('Cannot add player, player name already exists.'); return false; }
   
      $dbResult = $this->db()->bindExecute("insert into player (profile_id,name,created,updated) values (?,?,now(),now())",
                                           "ss",array($userId,$playerName));
   
      $success = ($dbResult) ? true : false;
   
      if (!$success) { $this->error('Cannot add player, database error.'); }
   
      return $success;
   }
   
   public function fetchPlayerList()
   {
      $userId     = $this->userId;
      $playerList = $this->db()->query(sprintf("select * from player where profile_id = '%s'",$this->db()->escapeString($userId)),array('keyid' => 'name'));
   
      if ($playerList === false) { $this->error('Could not query player list'); return false; }
   
      $this->var('playerList',$playerList);
   
      return true;
   }

   public function title($name = null)
   {
      if (is_null($name)) { return $this->var('title'); }

      $this->var('title',$name);
   }
}
?>
