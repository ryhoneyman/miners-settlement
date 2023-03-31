<?php

//    Copyright 2023 - Ryan Honeyman

include_once 'common/main.class.php';
include_once 'common/random.class.php';

class MinersMain extends Main
{
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

      $this->userName = $userId;
 
      $userDir = APP_CONFIGDIR."/profile/$userId";

      if (!is_dir($userDir)) { mkdir($userDir,0755); }
   }

   private function generateUserId()
   {
      $random = new Random($this->debug);

      return $random->uniqueId();
   }
}
?>
