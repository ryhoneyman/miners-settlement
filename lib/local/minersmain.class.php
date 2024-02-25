<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/mainbase.class.php';
include_once 'common/random.class.php';

class MinersMain extends MainBase
{
   public $userId         = null;
   public $hashTypes      = null;
   public $currentVersion = '1.6.4';

   public function __construct($options = null)
   {
      parent::__construct($options);

      if ($options['format']) {
         $this->buildClass('format','Format',null,'local/format.class.php');
      }

      $this->hashTypes = array(
         ''            => '',    // simple data hash, no type
         'profile'     => 'pr',  // profile id from profile table
         'player'      => 'pl',  // player id from player table
         'item'        => 'it',  // item id from item table
         'playergear'  => 'pg',  // player gear id from gear table
         'playerbuild' => 'pb',  // player build id from player_build table
         'itemlink'    => 'il',  // item id from item_link table
         'itemgear'    => 'ig',  // gear-only item id from item table
         'monster'     => 'mo',  // monster id from monster table
      );
   }

   public function title($name = null)
   {
      if (is_null($name)) { return $this->var('title'); }

      $this->var('title',$name);
   }

   public function webhookInit()
   {
      $currentId     = $_COOKIE['userid'] ?: null;
      $userId        = $currentId ?: $this->generateUserId();
      $cookieExpires = time()+(60*60*24*400); // 400 days is enforced in browsers;

      if (!$currentId) {
         $this->sendCookies(array('userid' => array('value' => $userId, 'expires' => $cookieExpires, 'path' => '/', 'domain' => $_SERVER['SERVER_NAME'])));
         $this->logger('newUser',array('userId' => $userId, 'expires' => $cookieExpires));
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

   public function fetchProfileData($useCache = true)
   {
      $userId = $this->userId;

      $sessionProfileData = $this->sessionValue('profileData');

      if (is_null($sessionProfileData) || !$useCache) { 
         $profileData = $this->db()->query(sprintf("select * from profile_data where profile_id = '%s'",$this->db()->escapeString($userId)),array('keyid' => 'name'));

         if ($profileData === false) { $this->error('Could not query profile data'); return false; }
 
         $this->sessionValue('profileData',$profileData);
      }
      else { $profileData = $sessionProfileData; }

      $this->var('profileData',$profileData);

      return true;
   }

   public function fetchProfileEntitlement($useCache = true)
   {
      $profileData = $this->var('profileData');

      if (!$profileData || !$useCache) { 
         $fetchResult = $this->fetchProfileData($useCache); 

         if ($fetchResult === false) { return false; }

         $profileData = $this->var('profileData');

         if (!$profileData) { return true; }
      }

      $entitlement = (array_key_exists('entitlement',$profileData)) ? json_decode($profileData['entitlement']['data'],true) : array();

      $this->var('entitlement',$entitlement);

      return true; 
   }

   public function getProfileEntitlement($name, $useCache = true) 
   {
      $entitlement = $this->var('entitlement');

      if (is_null($entitlement) || !$useCache) {
         $fetchResult = $this->fetchProfileEntitlement($useCache); 
 
         if ($fetchResult === false) { return false; }

         $entitlement = $this->var('entitlement');
      }

      if (!$entitlement || !array_key_exists($name,$entitlement)) { return false; }

      return $entitlement[$name];
   }

   public function deletePlayer($playerHash)
   {
      $userId         = $this->userId;
      $playerList     = $this->getPlayerList(array('skipCache' => true));
      $playerHashList = $this->getPlayerHashList();
      $playerId       = $playerHashList[$playerHash] ?: null;
   
      if (!$userId)   { $this->error('Cannot delete player, no user ID detected.'); return false; }
      if (!$playerId) { $this->error("Cannot delete player, player does not exist."); return false; }
   
      $dbResult = $this->db()->bindExecute("delete from player where profile_id = ? and id = ?",
                                           "ss",array($userId,$playerId));
   
      $success = ($dbResult) ? true : false;
   
      if (!$success) { $main->this('Cannot delete player, database error.'); }
      else { $this->fetchPlayerList(array('skipCache' => true)); }
   
      return $success;
   }
   
   public function addPlayer($playerName)
   {
      $userId     = $this->userId;
      $playerList = $this->getPlayerList(array('skipCache' => true, 'keyId' => 'name'));
   
      if (!$userId) { $this->error('Cannot add player, no user ID detected.'); return false; }
   
      if ($playerList[$playerName]) { $this->error('Cannot add player, player name already exists.'); return false; }
   
      $dbResult = $this->db()->bindExecute("insert into player (profile_id,name,created,updated) values (?,?,now(),now())",
                                           "ss",array($userId,$playerName));
   
      $success = ($dbResult) ? true : false;
   
      if (!$success) { $this->error('Cannot add player, database error.'); }
      else { $this->fetchPlayerList(array('skipCache' => true)); }
   
      return $success;
   }

   public function getPlayerBuildList($options = null)      { return $this->getList('playerbuild',$options); }
   public function getPlayerList($options = null)           { return $this->getList('player',$options); }
   public function getPlayerGearList($options = null)       { return $this->getList('playergear',$options); }
   public function getRunewordList($options = null)         { return $this->getList('runeword',$options); }
   public function getItemGearList($options = null)         { return $this->getList('itemgear',$options); }
   public function getMonsterList($options = null)          { return $this->getList('monster',$options); }
   public function getItemGearListByType($options = null)   { return $this->getItemGearList(array_replace($options ?: array(),array('subType' => 'bytype'))); }
   public function getPlayerGearListByType($options = null) { return $this->getPlayerGearList(array_replace($options ?: array(),array('subType' => 'bytype'))); }

   public function getList($type, $options = null)
   {
      $type        = strtolower($type);
      $subType     = strtolower($options['subType']) ?: 'default';
      $keyId       = strtolower($options['keyId']) ?: 'id';
      $varName     = sprintf("%s-%s-%s-%s",$type,$subType,$keyId,"list");
      $fetchResult = $this->fetchList($type,$options);

      if ($fetchResult === false) { return false; }

      return $this->var($varName);
   }

   public function fetchPlayerBuildList($options = null)      { return $this->fetchList('playerbuild',$options); }
   public function fetchPlayerList($options = null)           { return $this->fetchList('player',$options); }
   public function fetchPlayerGearList($options = null)       { return $this->fetchList('playergear',$options); }
   public function fetchRunewordList($options = null)         { return $this->fetchList('runeword',$options); }
   public function fetchItemGearList($options = null)         { return $this->fetchList('itemgear',$options); }
   public function fetchMonsterList($options = null)          { return $this->fetchList('monster',$options); }
   public function fetchItemGearListByType($options = null)   { return $this->fetchItemGearList(array_replace($options ?: array(),array('subType' => 'bytype'))); }
   public function fetchPlayerGearListByType($options = null) { return $this->fetchPlayerGearList(array_replace($options ?: array(),array('subType' => 'bytype'))); }

   public function fetchList($type, $options = null)
   {
      $listData  = array();
      $skipCache = ($option['skipCache']) ? true : false;
      $type      = strtolower($type);
      $subType   = strtolower($options['subType']) ?: 'default';
      $keyId     = strtolower($options['keyId']) ?: 'id';
      $varName   = sprintf("%s-%s-%s-%s",$type,$subType,$keyId,"list");

      if (!$type) { $this->error('No type specified for list fetch'); return false; }

      if (!$skipCache && $this->var($varName)) { return true; }

      if ($type == 'itemgear') {
         $gearTypes = $this->obj('constants')->gearTypes();
         $typeList  = implode(',',array_map(function($value) { return "'".preg_replace('/[^\w\-]/','',$value)."'"; },
                                            array_unique(array_filter(array_keys($gearTypes)))));
         $result    = $this->db()->query("select * from item where type in ($typeList) and active = 1 order by tier asc, ranking asc, name asc",
                                         array('keyid' => $keyId));

         if ($result === false) { $this->error('Could not query item list'); return false; }

         if ($subType == 'bytype') {
            foreach ($result as $resultId => $resultInfo) { $listData[$resultInfo['type']][$resultId] = $resultInfo; }
         }
         else { $listData = $result; }
      }
      else if ($type == 'runeword') {
         $listData = $this->db()->query("select * from runeword where active = 1 order by name asc",array('keyid' => $keyId));

         if ($listData === false) { $this->error('Could not query runeword list'); return false; }
      }
      else if ($type == 'playergear') {
         $userId = $this->userId;
         $result = $this->db()->query(sprintf("select g.*,i.*, g.id as id from gear g, item i where g.item_id = i.id and profile_id = '%s'",
                                              $this->db()->escapeString($userId)),array('keyid' => $keyId));

         if ($result === false) { $this->error('Could not query gear list'); return false; }

         if ($subType == 'bytype') {
            foreach ($result as $resultId => $resultInfo) { $listData[$resultInfo['type']][$resultId] = $resultInfo; }
         }
         else { $listData = $result; }
      }
      else if ($type == 'player') {
         $userId   = $this->userId;
         $listData = $this->db()->query(sprintf("select * from player where profile_id = '%s'",$this->db()->escapeString($userId)),array('keyid' => $keyId));

         if ($listData === false) { $this->error('Could not query player list'); return false; }
      }
      else if ($type == 'playerbuild') {
         $userId   = $this->userId;
         $listData = $this->db()->query(sprintf("select pb.*, p.profile_id, p.name as player_name from player_build pb, player p where pb.player_id = p.id and p.profile_id = '%s'",
                                                $this->db()->escapeString($userId)),array('keyid' => $keyId));

         if ($listData === false) { $this->error('Could not query player build list'); return false; }
      }
      else if ($type == 'monster') {
         $area  = $options['area'] ?: null;
         $query = (is_null($area)) ? "select l.*,m.* from monster m left join location l on m.location_id = l.id"
                                   : sprintf("select l.*,m.* from monster m, location l where m.location_id = l.id and l.area = '%s'",$this->db()->escapeString($area));

         $listData = $this->db()->query($query,array('keyid' => $keyId));

         if ($listData === false) { $this->error('Could not query monster list'); return false; }
      }

      $this->var($varName,$listData);

      return true;
   }

   public function getItemById($itemId)
   {
      $result = $this->getItem('id',$itemId,false);

      if ($result === false) { $this->error('Could not query item list'); return false; }

      return $result;
   }

   public function getItemByName($itemName)
   {
      $result = $this->getItem('name',$itemName,false);

      if ($result === false) { $this->error('Could not query item list'); return false; }

      return $result;
   }

   public function getItem($byColumn = 'name', $columnList = null, $multiSelect = true, $keyId = 'id')
   {
      if (!is_null($columnList) && !is_array($columnList)) { $columnList = array($columnList); }

      $columnSelect = implode(',',array_map(function($value) { return "'".preg_replace('/[^\w\-]/','',$value)."'"; },
                                            array_unique(array_filter($columnList))));

      $result = $this->db()->query(sprintf("select * from item where $byColumn in (%s) and active = 1",$columnSelect),array('keyid' => $keyId, 'multi' => $multiSelect));

      if ($result === false) { $this->error('Could not query item list'); return false; }

      if ($multiSelect) {
         foreach ($result as $resultId => $resultData) { $result[$resultId]['attributes'] = json_decode($resultData['attributes'],true); }
      } 
      else { $result['attributes'] = json_decode($result['attributes'],true); }

      return $result;
   }

   public function getItemCrafting($byColumn = 'type', $columnList = null, $multiSelect = true, $keyId = 'id')
   {
      if (!is_null($columnList) && !is_array($columnList)) { $columnList = array($columnList); }

      $columnSelect = implode(',',array_map(function($value) { return "'".preg_replace('/[^\w\-]/','',$value)."'"; },
                                            array_unique(array_filter($columnList))));

      $result = $this->db()->query(sprintf("select * from item_crafting where $byColumn in (%s) and active = 1",$columnSelect),array('keyid' => $keyId, 'multi' => $multiSelect));

      if ($result === false) { $this->error('Could not query item crafting list'); return false; }

      if ($multiSelect) {
         foreach ($result as $resultId => $resultData) { $result[$resultId]['details'] = json_decode($resultData['details'],true); }
      } 
      else { $result['details'] = json_decode($result['details'],true); }

      return $result;
   }

   // hash encode/decode is used to obscure database ids/values to prevent information leak and/or lowers the known data to attack
   public function getPlayerBuildHashList() { return $this->getHashList('playerbuild'); }
   public function getPlayerGearHashList()  { return $this->getHashList('playergear'); }
   public function getItemGearHashList()    { return $this->getHashList('itemgear'); }
   public function getPlayerHashList()      { return $this->getHashList('player'); }
   public function getMonsterHashList()     { return $this->getHashList('monster'); }

   public function getHashList($type) 
   {
      $return = array();
      $type   = strtolower($type);
      $prefix = $this->hashTypes[$type];
      
      if (!$prefix) { return false; }  // even if a type is defined, we don't want the non-prefixed types

      $listData = $this->getList($type);

      foreach ($listData as $entryId => $entryInfo) {
         $return[$this->generateLookupHash($type,$entryId)] = $entryId;
      }

      return $return;
   }

   public function hashData($data)                   { return $this->generateLookupHash('',$data); }
   public function hashItemId($itemId)               { return $this->generateLookupHash('item',$itemId); }
   public function hashItemLink($itemName,$itemData) { return $this->generateLookupHash('itemlink',$this->uniqueItemData($itemName,$itemData)); }
   public function hashSaveGear($itemName,$itemData) { return $this->generateLookupHash('playergear',$this->uniqueItemData($itemName,$itemData)); }
   public function hashPlayerGearId($itemId)         { return $this->generateLookupHash('playergear',$itemId); }
   public function hashPlayerBuildId($buildId)       { return $this->generateLookupHash('playerbuild',$buildId); }
   public function hashItemGearId($itemId)           { return $this->generateLookupHash('itemgear',$itemId); }
   public function hashMonsterId($monsterId)         { return $this->generateLookupHash('monster',$monsterId); }
   public function hashPlayerId($playerId)           { return $this->generateLookupHash('player',$playerId); }

   // lookup hashes are used to obscure item ids and names in the database when passing between client and server
   public function generateLookupHash($type, $data)
   {
      $type = strtolower($type);

      if (!array_key_exists($type,$this->hashTypes)) { return false; }

      return sprintf("%s%s",$this->hashTypes[$type],hash("crc32",$data));
   }

   public function uniqueItemData($itemName, $itemData)
   {
      return json_encode(array('item_name' => $itemName, 'data' => $this->normalizeItemData($itemData)));
   }

   public function normalizeItemData($itemData)
   {
      $itemData = array_filter(array_diff_key($itemData,array_flip(array('name','type','description','image'))));

      if (is_null($itemData['level'])) { $itemData['level'] = 0; }

      ksort($itemData);

      return $itemData;
   }

   public function getItemLink($itemHash)
   {
      $itemInfo = $this->db()->query(sprintf("select * from item_link where id = '%s'",$this->db()->escapeString($itemHash)),array('keyid' => 'id', 'multi' => false));

      if ($itemInfo === false) { $this->error('Could not query gear list'); return false; }

      if (!$itemInfo) { return null; }

      return array('item_name' => $itemInfo['item_name'], 'stats' => json_decode($itemInfo['stats'],true), 'raw' => $itemInfo);
   }

   public function saveItemLink($itemName, $itemData)
   {
      $itemData = $this->normalizeItemData($itemData);
      $itemHash = $this->hashItemLink($itemName, $itemData);

      $itemStats  = json_encode($itemData,JSON_UNESCAPED_SLASHES);
      $dbResult   = $this->db()->bindExecute("insert into item_link (id,item_name,stats,created) values (?,?,?,now()) ".
                                             "on duplicate key update created = values(created)",
                                             "sss",array($itemHash,$itemName,$itemStats));

      $success = ($dbResult) ? true : false;

      $this->logger('saveItemLink',array('itemHash' => $itemHash, 'itemName' => $itemName, 'success' => $success));

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
      $saveData  = array_merge($itemData,array('userId' => $userId, 'ts' => time()));  // add entropy to the data for uniqueness
      $itemHash  = $this->hashSaveGear($itemName,$saveData);
      $itemData  = $this->normalizeItemData($itemData);
      $itemStats = json_encode($itemData,JSON_UNESCAPED_SLASHES);
      $dbResult  = $this->db()->bindExecute("insert into gear (profile_id,item_hash,item_id,stats,created,updated) values (?,?,?,?,now(),now()) ".
                                            "on duplicate key update updated = values(updated)",
                                            "ssis",array($userId,$itemHash,$itemId,$itemStats));

      $success = ($dbResult) ? true : false;

      $this->logger('saveGear',array('itemId' => $itemId, 'itemName' => $itemName, 'success' => $success));

      return $success;
   }

   public function updateGear($itemHash, $itemData)
   {
      $userId    = $this->userId;
      $itemData  = $this->normalizeItemData($itemData);
      $itemStats = json_encode($itemData,JSON_UNESCAPED_SLASHES);
      $dbResult  = $this->db()->bindExecute("update gear set stats = ?, updated = now() where item_hash = ? and profile_id = ?","sss",array($itemStats,$itemHash,$userId));
 
      $success = ($dbResult) ? true : false;

      $this->logger('updateGear',array('itemHash' => $itemHash, 'success' => $success));

      return $success;
   }

   public function deleteGear($itemHash)
   {
      $userId   = $this->userId;
      $dbResult = $this->db()->bindExecute("delete from gear where item_hash = ? and profile_id = ?","ss",array($itemHash,$userId));

      $success = ($dbResult) ? true : false;

      return $success;
   }

   public function fvRound($value, $options = null)
   {
      return round($value,0,PHP_ROUND_HALF_ODD);
   }

   public function logger($name, $data, $options = null)
   {
      $userId   = $this->userId ?: 'none';
      $dbResult = $this->db()->bindExecute("insert into log (profile_id,remote_addr,name,data,created) value (?,?,?,?,now())",
                                           "ssss",array($userId,$_SERVER['REMOTE_ADDR'],$name,json_encode($data,JSON_UNESCAPED_SLASHES)));

      $success = ($dbResult) ? true : false;

      return $success;
   }
}
?>
