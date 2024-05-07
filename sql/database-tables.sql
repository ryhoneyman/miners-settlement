CREATE TABLE `profile` (
  `id`         char(13) NOT NULL,
  `email`      varchar(255) DEFAULT NULL,
  `password`   char(64) DEFAULT NULL,	
  `created`    datetime DEFAULT NULL,
  `updated`    datetime DEFAULT NULL,
  `verifycode` char(64) DEFAULT NULL,
  `linked`     tinyint unsigned DEFAULT 0,
  `verified`   tinyint unsigned DEFAULT 0,
  `active`     tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `profile_idx1` (`linked`,`verified`,`active`)
);


CREATE TABLE `profile_data` (
  `id`         int unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` char(13) NOT NULL,
  `name`       varchar(100) NOT NULL,
  `data`       text NOT NULL,
  `created`    datetime DEFAULT NULL,
  `updated`    datetime DEFAULT NULL,
  `active`     tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE (`profile_id`,`name`),
  KEY `profile_data_idx1` (`profile_id`,`name`,`active`)
);


CREATE TABLE `entitlement` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `name`         varchar(100) NOT NULL,
  `label`        varchar(100) NOT NULL,  
  `description`  text DEFAULT NULL,  
  `data`         text NOT NULL,
  `created`      datetime DEFAULT NULL,
  `updated`      datetime DEFAULT NULL,
  `active`       tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE (`name`),
  KEY `entitlement_idx1` (`name`,`label`,`active`)
);


CREATE TABLE `activation_code` (
  `id`         int unsigned NOT NULL AUTO_INCREMENT,
  `code`       char(32) NOT NULL,
  `profile_id` char(13) DEFAULT NULL,
  `data`       text NOT NULL,
  `applied`    datetime DEFAULT NULL,
  `created`    datetime DEFAULT NULL,
  `updated`    datetime DEFAULT NULL,
  `active`     tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE (`code`),
  KEY `code_idx1` (`code`,`active`)
);


CREATE TABLE `player` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `profile_id`   char(13) NOT NULL,
  `name`         varchar(100) NOT NULL,
  `description`  text DEFAULT NULL,
  `created`      datetime DEFAULT NULL,
  `updated`      datetime DEFAULT NULL,
  `active`       tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`profile_id`,`name`),
  KEY `player_idx1` (`profile_id`,`name`,`active`)
);

CREATE TABLE `gear` (
  `id`         int unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` char(13) NOT NULL,
  `item_hash`  char(10) NOT NULL,
  `item_id`    int unsigned NOT NULL,
  `stats`      text NOT NULL,
  `created`    datetime DEFAULT NULL,
  `updated`    datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`item_hash`),
  KEY `gear_idx1` (`profile_id`,`item_id`)
);


CREATE TABLE `item` (
  `id`             int unsigned NOT NULL AUTO_INCREMENT,
  `name`           varchar(100) NOT NULL,
  `label`          varchar(100) NOT NULL,
  `type`           varchar(50) NOT NULL,
  `description`    text DEFAULT NULL,
  `image`          varchar(255) NOT NULL,
  `attributes`     text NOT NULL,
  `required_level` smallint unsigned DEFAULT NULL,
  `tier`           tinyint unsigned DEFAULT 0,
  `ranking`        smallint unsigned DEFAULT NULL,
  `created`        datetime DEFAULT NULL,
  `updated`        datetime DEFAULT NULL,
  `active`         tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  KEY `item_idx1` (`name`,`label`,`type`,`active`)
);


CREATE TABLE `item_scheme` (
  `id`             int unsigned NOT NULL AUTO_INCREMENT,
  `item_id`        int unsigned DEFAULT NULL,
  `cost`           text NOT NULL,
  `created`        datetime DEFAULT NULL,
  `updated`        datetime DEFAULT NULL,
  `active`         tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `item_idx1` (`item_id`)
);


CREATE TABLE `item_link` (
  `id`         char(10) NOT NULL,
  `item_name`  varchar(100) NOT NULL,
  `stats`      text NOT NULL,
  `created`    datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_link_idx1` (`id`,`created`)
);



CREATE TABLE `runeword` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `name`         varchar(100) NOT NULL,
  `label`        varchar(100) NOT NULL,
  `description`  text DEFAULT NULL,
  `requires`     text DEFAULT NULL,
  `cost`         text NOT NULL,
  `attributes`   text NOT NULL,
  `runepost_id`  int unsigned DEFAULT NULL,   
  `created`      datetime DEFAULT NULL,
  `updated`      datetime DEFAULT NULL,
  `active`       tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  KEY `runeword_idx1` (`name`,`label`,`runepost_id`,`active`)
);


CREATE TABLE `runepost` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `name`         varchar(100) NOT NULL,
  `label`        varchar(100) NOT NULL,
  `description`  text DEFAULT NULL,
  `attributes`   text DEFAULT NULL,  
  `location_id`  int unsigned DEFAULT NULL,   
  `created`      datetime DEFAULT NULL,
  `updated`      datetime DEFAULT NULL,
  `active`       tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  KEY `runepost_idx1` (`name`,`label`,`location_id`,`active`)
);


CREATE TABLE `player_build` (
  `id`         int unsigned NOT NULL AUTO_INCREMENT,
  `name`       varchar(50) NOT NULL,
  `player_id`  int unsigned DEFAULT NULL,
  `build`      text NOT NULL,
  `created`    datetime DEFAULT NULL,
  `updated`    datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`player_id`,`name`),
  KEY `player_build_idx1` (`player_id`,`name`)
);



CREATE TABLE `location` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `name`         varchar(100) NOT NULL,
  `label`        varchar(100) NOT NULL,
  `description`  text DEFAULT NULL,
  `region`       varchar(100) NOT NULL,
  `area`         varchar(100) NOT NULL,
  `section`      varchar(100) DEFAULT NULL,
  `floor`        varchar(100) DEFAULT NULL,
  `room`         varchar(100) DEFAULT NULL,
  `created`      datetime DEFAULT NULL,
  `updated`      datetime DEFAULT NULL,
  `active`       tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  KEY `location_idx1` (`name`,`region`,`area`,`section`,`floor`,`room`,`active`)
);


CREATE TABLE `monster` (
  `id`             int unsigned NOT NULL AUTO_INCREMENT,
  `name`           varchar(100) NOT NULL,
  `label`          varchar(100) NOT NULL,
  `type`           varchar(50) NOT NULL,
  `description`    text DEFAULT NULL,
  `image`          varchar(255) DEFAULT NULL,
  `attributes`     text NOT NULL,
  `level`          smallint unsigned DEFAULT NULL,
  `xp`             smallint unsigned DEFAULT NULL,
  `respawn_timer`  smallint unsigned DEFAULT NULL,
  `spawn_count`    tinyint unsigned DEFAULT NULL,
  `revivable`      tinyint unsigned DEFAULT 1,
  `battle_timer`   smallint unsigned DEFAULT 300,
  `location_id`    int unsigned DEFAULT NULL,   
  `created`        datetime DEFAULT NULL,
  `updated`        datetime DEFAULT NULL,
  `active`         tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  KEY `monster_idx1` (`name`,`label`,`type`,`level`,`location_id`,`active`)
);


CREATE TABLE `monster_drop` (
  `id`            int unsigned NOT NULL AUTO_INCREMENT,
  `monster_id`    int unsigned NOT NULL,
  `item_id`       int unsigned NOT NULL,
  `min_count`     smallint unsigned DEFAULT 1,
  `max_count`     smallint unsigned DEFAULT 1,
  `frequency`     tinyint unsigned DEFAULT NULL,
  `created`       datetime DEFAULT NULL,
  `updated`       datetime DEFAULT NULL,
  `active`        tinyint unsigned DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE (`monster_id`,`item_id`),
  KEY `monster_drop_idx1` (`monster_id`,`item_id`,`active`)
);



CREATE TABLE `log` (
  `id`           int unsigned NOT NULL AUTO_INCREMENT,
  `profile_id`   char(13) NOT NULL,
  `remote_addr`  varchar(15) DEFAULT NULL,
  `name`         varchar(100) NOT NULL,
  `data`         text DEFAULT NULL,
  `created`      datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_idx1` (`profile_id`,`name`)
);






(11011,'foo','FOO','crafted','','{}',now(),now()),
(11012,'foo','FOO','crafted','','{}',now(),now()),
(11013,'foo','FOO','crafted','','{}',now(),now()),
(11014,'foo','FOO','crafted','','{}',now(),now()),
(11015,'foo','FOO','crafted','','{}',now(),now()),
(11016,'foo','FOO','crafted','','{}',now(),now()),
(11017,'foo','FOO','crafted','','{}',now(),now()),
(11018,'foo','FOO','crafted','','{}',now(),now()),


portals/gates:
('city-portal-town',
('nature-valley-portal-west',
('virimel-portal-center',
('virimel-portal-east',
('bridge-portal'
('dungeon-floor4-portal','Dungeon 4th Floor - Portal - Dungeon Fortress',
('dungeon-floor8-portal','Dungeon 8th Floor - Portal - Dungeon Fortress',
('dungeon-floor12-portal','Dungeon 12th Floor - Portal - Dungeon Fortress',
('stone-cave-floor4-portal','Stone Cave 4th Floor - Portal - Gold Mine',
('stone-cave-floor8-portal','Stone Cave 8th Floor - Portal - Emerald Mine',
('stone-cave-floor12-portal','Stone Cave 12th Floor - Portal - Insatiable Stone',



15 yellow
40 orange
50 T5 + firetongue + glory shield + sotg
80 uber items  sc +nt + mino + medusa
90 1st ring
100 2nd ring


1100 dungeon
1200 necromancer-altar
1300 archangels/lod
1400 mitar
1500 einlor
5000 tower
6000 archstone
7000 ubers
8000 events
