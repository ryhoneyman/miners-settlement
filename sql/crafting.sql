-- =========== ITEM_SCHEME ==================================

truncate item_crafting;
insert into item_crafting (id,type,item_name,details,created,updated) values
(1001,'scheme','processed-wood','{"cost":{"item":{"wood":75000}}}',now(),now()),
(1002,'scheme','iron-bar','{"cost":{"item":{"iron-ore":5000}}}',now(),now()),
(1003,'scheme','gold-bar','{"cost":{"item":{"gold-ore":5000,"iron-bar":2}}}',now(),now()),
(1004,'scheme','platinum-bar','{"cost":{"item":{"platinum":5000,"gold-bar":2}}}',now(),now()),
(1005,'scheme','tanzanite-essence','{"cost":{"item":{"essence-empty-vial":1,"tanzanite":50000,"slime-drop":12}}}',now(),now()),
(1006,'scheme','emerald-essence','{"cost":{"item":{"essence-empty-vial":1,"emerald":50000,"slime-drop":12}}}',now(),now()),
(1007,'scheme','ruby-essence','{"cost":{"item":{"essence-empty-vial":1,"ruby":50000,"slime-drop":12}}}',now(),now()),
(1008,'scheme','jade-essence','{"cost":{"item":{"essence-empty-vial":1,"jade":50000,"slime-drop":12}}}',now(),now()),
(1009,'scheme','hp-potion','{"cost":{"item":{"meat":1,"slime-drop":2}}}',now(),now()),
(1010,'scheme','large-hp-potion','{"cost":{"item":{"steak":5,"oculothorax":5,"meat":25}}}',now(),now()),
(1011,'scheme','scroll-of-portal','{"cost":{"item":{"material-for-the-enhance-stone-blue":2,"material-for-the-enhance-stone-yellow":3,"material-for-the-enhance-stone-red":5,"material-for-the-enhance-stone-green":5}}}',now(),now()),


(2401,'mitar-forge','amulet-of-protection','{"limit":"UNLIMITED","output":[{"name":"amulet-of-protection","link":true}],"input":[{"name":"stone-scale","count":10},{"name":"mitar-ore","count":50},{"name":"magic-dust","count":5},{"name":"amulet-of-truth"},{"name":"amulet-of-truth"}]}',now(),now()),
(2402,'mitar-forge','amulet-of-elements','{"limit":"UNLIMITED","output":[{"name":"amulet-of-elements","link":true}],"input":[{"name":"stone-scale","count":10},{"name":"mitar-ore","count":50},{"name":"demonic-matter","count":5},{"name":"amulet-of-truth"},{"name":"amulet-of-truth"}]}',now(),now()),
(2403,'mitar-forge','dwarven-amulet','{"limit":"UNLIMITED","output":[{"name":"dwarven-amulet","link":true}],"input":[{"name":"gold-bar","count":25},{"name":"rune-ii","count":3},{"name":"sacred-soul-stone","count":1500},{"name":"amulet-of-protection"},{"name":"amulet-of-protection"}]}',now(),now()),
(2404,'mitar-forge','amulet-of-strength','{"limit":"UNLIMITED","output":[{"name":"amulet-of-strength","link":true}],"input":[{"name":"gold-bar","count":25},{"name":"rune-iii","count":3},{"name":"sacred-soul-stone","count":1500},{"name":"amulet-of-elements"},{"name":"amulet-of-elements"}]}',now(),now()),
(2405,'mitar-forge','jewel-of-mitar','{"limit":"UNLIMITED","output":[{"name":"jewel-of-mitar","link":true}],"input":[{"name":"platinum-bar","count":25},{"name":"rune-v","count":5},{"name":"tanzanite-essence","count":100},{"name":"dwarven-amulet"},{"name":"dwarven-amulet"}]}',now(),now()),
(2406,'mitar-forge','depth-of-mitar','{"limit":"UNLIMITED","output":[{"name":"depth-of-mitar","link":true}],"input":[{"name":"platinum-bar","count":25},{"name":"rune-vi","count":5},{"name":"emerald-essence","count":100},{"name":"dwarven-amulet"},{"name":"dwarven-amulet"}]}',now(),now()),
(2407,'mitar-forge','eyes-of-mitar','{"limit":"UNLIMITED","output":[{"name":"eyes-of-mitar","link":true}],"input":[{"name":"platinum-bar","count":25},{"name":"rune-vii","count":5},{"name":"ruby-essence","count":100},{"name":"amulet-of-strength"},{"name":"amulet-of-strength"}]}',now(),now()),
(2408,'mitar-forge','heart-of-mitar','{"limit":"UNLIMITED","output":[{"name":"heart-of-mitar","link":true}],"input":[{"name":"platinum-bar","count":25},{"name":"rune-viii","count":5},{"name":"jade-essence","count":100},{"name":"amulet-of-strength"},{"name":"amulet-of-strength"}]}',now(),now()),

(2409,'mitar-forge','power-of-mitar','{"limit":"UNLIMITED","output":[{"name":"power-of-mitar","link":true}],"input":[{"name":"sacred-soul-stone","count":10000},{"name":"jewel-of-mitar"},{"name":"depth-of-mitar"},{"name":"eyes-of-mitar"},{"name":"heart-of-mitar"}]}',now(),now()),
(2410,'mitar-forge','eternal-pyre','{"limit":"UNLIMITED","output":[{"name":"eternal-pyre","link":true}],"input":[{"name":"sacred-soul-stone","count":10000},{"name":"jewel-of-mitar"},{"name":"depth-of-mitar"},{"name":"eyes-of-mitar"},{"name":"heart-of-mitar"}]}',now(),now()),
(2411,'mitar-forge','stormforge-amulet','{"limit":"UNLIMITED","output":[{"name":"stormforge-amulet","link":true}],"input":[{"name":"sacred-soul-stone","count":10000},{"name":"jewel-of-mitar"},{"name":"depth-of-mitar"},{"name":"eyes-of-mitar"},{"name":"heart-of-mitar"}]}',now(),now()),
(2412,'mitar-forge','oceans-breath','{"limit":"UNLIMITED","output":[{"name":"oceans-breath","link":true}],"input":[{"name":"sacred-soul-stone","count":10000},{"name":"jewel-of-mitar"},{"name":"depth-of-mitar"},{"name":"eyes-of-mitar"},{"name":"heart-of-mitar"}]}',now(),now()),
(2413,'mitar-forge','airwhisper-charm','{"limit":"UNLIMITED","output":[{"name":"airwhisper-charm","link":true}],"input":[{"name":"sacred-soul-stone","count":10000},{"name":"jewel-of-mitar"},{"name":"depth-of-mitar"},{"name":"eyes-of-mitar"},{"name":"heart-of-mitar"}]}',now(),now()),

(2414,'mitar-forge','high-uldreds-pickaxe','{"limit":"UNLIMITED","output":[{"name":"high-uldreds-pickaxe","link":false}],"input":[{"name":"dwarven-pickaxe"},{"name":"sacred-soul-stone","count":5000},{"name":"rune-ix","count":2},{"name":"mitar-ore","count":100}]}',now(),now()),
(2415,'mitar-forge','axe-of-division','{"limit":"UNLIMITED","output":[{"name":"axe-of-division","link":true}],"input":[{"name":"high-uldreds-pickaxe","count":2},{"name":"mitar-ore","count":100},{"name":"sacred-soul-stone","count":1000},{"name":"sword-of-marriage"},{"name":"sword-of-marriage"}]}',now(),now())

;
