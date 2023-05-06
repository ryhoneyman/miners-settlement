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

   public function title($name = null)
   {
      if (is_null($name)) { return $this->var('title'); }

      $this->var('title',$name);
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

   public function fetchGearList()
   {
      $userId   = $this->userId;
      $gearList = $this->db()->query(sprintf("select g.*,i.* from gear g, item i where g.item_id = i.id and profile_id = '%s'",$this->db()->escapeString($userId)),array('keyid' => 'id'));

      if ($gearList === false) { $this->error('Could not query gear list'); return false; }

      $this->var('gearList',$gearList);

      return true;
   }

   public function normalizeItemData($itemData)
   {
      $itemData = array_filter(array_diff_key($itemData,array_flip(array('name','type','description','image'))));

      if (is_null($itemData['level'])) { $itemData['level'] = 0; }

      ksort($itemData);

      return $itemData;
   }

   public function generateItemHash($itemName, $itemData)
   {
      $itemData = $this->normalizeItemData($itemData);

      $itemHash = hash("crc32",json_encode(array('item_name' => $itemName, 'stats' => $itemData)));

      return $itemHash;
   }

   public function getItemLink($itemHash)
   {
      $itemInfo = $this->db()->query(sprintf("select * from item_link where id = '%s'",$this->db()->escapeString($itemHash)),array('keyid' => 'id', 'multi' => false));

      if ($itemInfo === false) { $this->error('Could not query gear list'); return false; }

      if (!$itemInfo) { return null; }

      return array('item_name' => $itemInfo['item_name'], 'stats' => json_decode($itemInfo['stats'],true), 'raw' => $itemInfo);
   }

   public function saveItemLink($itemHash, $itemName, $itemData)
   {
      $itemData = $this->normalizeItemData($itemData);

      $itemStats  = json_encode($itemData,JSON_UNESCAPED_SLASHES);
      $dbResult   = $this->db()->bindExecute("insert into item_link (id,item_name,stats,created) values (?,?,?,now()) ".
                                             "on duplicate key update created = values(created)",
                                             "sss",array($itemHash,$itemName,$itemStats));

      $success = ($dbResult) ? true : false;

      return $success;
   }

   public function getGear($itemHash)
   {
      $userId   = $this->userId;
      $itemInfo = $this->db()->query(sprintf("select g.*, i.* from gear g, item i where g.item_id = i.id and g.profile_id = '%s' and g.item_hash = '%s'",
                                     $this->db()->escapeString($userId),$this->db()->escapeString($itemHash)),array('keyid' => 'id', 'multi' => false));

      if ($itemInfo === false) { $this->error('Could not query gear list'); return false; }

      if (!$itemInfo) { return null; }

      return array('item_name' => $itemInfo['name'], 'stats' => json_decode($itemInfo['stats'],true), 'raw' => $itemInfo);
   }

   public function saveGear($itemId, $itemName, $itemData)
   {
      $userId    = $this->userId;
      $itemHash  = $this->generateItemHash($itemName,$itemData);
      $itemData  = $this->normalizeItemData($itemData);
      $itemStats = json_encode($itemData,JSON_UNESCAPED_SLASHES);
      $dbResult  = $this->db()->bindExecute("insert into gear (profile_id,item_hash,item_id,stats,created,updated) values (?,?,?,?,now(),now()) ".
                                             "on duplicate key update updated = values(updated)",
                                             "ssis",array($userId,$itemHash,$itemId,$itemStats));

      $success = ($dbResult) ? true : false;

      return $success;
   }

   public function deleteGear($itemHash)
   {
      $userId    = $this->userId;
      $dbResult  = $this->db()->bindExecute("delete from gear where item_hash = ? and profile_id = ?","ss",array($itemHash,$userId));

      $success = ($dbResult) ? true : false;

      return $success;
   }
}
?>
