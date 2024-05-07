-- ======== RUNEPOST ========================================

truncate runepost;
insert into runepost (id,name,label,description,attributes,location_id) values
(1400,'stone-cave-emerald-mine','Emerald Mine Runepost','',null,1440),
(1530,'necromancers-altar-floor5-skeleton-mage','Skeleton Mage Runepost','',null,1525),
(1700,'dungeon-floor1-rat','Rat Runepost','',null,1705),
(1730,'dungeon-floor6-bandit','Bandit Runepost','',null,1730),
(1760,'dungeon-floor13-dungeon-king','Dungeon King Runepost','',null,1765),
(2400,'mitar-forge','Forge Runepost','',null,2410),
(2420,'mitar-mines-room4-uhr-rahz','Uhr-Rahz Runepost','',null,2435),
(2440,'mitar-mines-room11-sharra','Sharra Runepost','',null,2470),
(2460,'mitar-mines-room13-stone-eater','Stone Eater Runepost','',null,2480),
(2590,'einlor-sumbeor','Sumbeor Runepost','',null,2500),
(3000,'big-surprise-scroll','Big Surprise Runepost','',null,3000),
(3010,'pumpkin-reaver-scroll','Pumpkin Reaver Runepost','',null,3005),
(3020,'uber-medusa-scroll','Uber Medusa Runepost','',null,3010),
(3030,'uber-nemesis-scroll','Uber Nemesis Runepost','',null,3015),
(3040,'uber-minotaur-scroll','Uber Minotaur Runepost','',null,3020),
(3050,'uber-spartan-scroll','Uber Spartan Runepost','',null,3025),
(3400,'golden-dragon-den','Golden Dragon Den Runepost','','{"exclusive":true}',3400),
(3500,'sapphire-dragon-den','Sapphire Dragon Den Runepost','','{"exclusive":true}',3500),
(3600,'graveyard','Graveyard Runepost','','{"exclusive":true}',3600),
(3700,'nightwing','Nightwing Runepost','',null,99999)
;


-- ======== RUNEWORD ========================================


truncate runeword;
insert into runeword (id,name,label,description,requires,runepost_id,cost,attributes) values
(1401,'defenders-faith','DEFENDERS FAITH','',null,1400,'{"item":{"rune-i":2}}','{"myself":{"extra-defense":{"percent.chance":10,"percent.adjust":100}}}'),
(1402,'patience-is-gold','PATIENCE IS GOLD','',null,1400,'{"item":{"rune-ii":1}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-10}}}'),
(1403,'leeches-touch','LEECHES TOUCH','',null,1400,'{"item":{"rune-i":2,"rune-ii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":5}}}'),
(1404,'elemental-ward','ELEMENTAL WARD','','["bone-crusher"]',1400,'{"item":{"rune-i":1,"rune-iii":1,"rune-iv":1,"rune-v":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":25},"extra-defense":{"percent.chance":20,"percent.adjust":200}},"enemy":{"stun":{"percent.chance":15,"flat.adjust":3},"speed":{"percent.chance":100,"percent.adjust":-20}}}'),
(1405,'deep-snow','DEEP SNOW','','["ice-floe"]',1400,'{"item":{"rune-i":2,"rune-ii":1}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-20}}}'),
(1406,'arson','ARSON','','["dragon-crust"]',1400,'{"item":{"rune-iv":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":25}}}'),
(1407,'swift-wings','SWIFT WINGS','','["ancient-scale"]',1400,'{"item":{"rune-vi":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":25}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-25}}}'),
(1408,'burning-sky','BURNING SKY','','["dragons-shield"]',1400,'{"item":{"rune-i":1,"rune-vii":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":30}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-30}}}'),

(1531,'dragon-heart','DRAGON HEART','','["blade-of-the-damned"]',1530,'{"item":{"rune-i":1,"rune-ii":1,"rune-vi":2}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":400},"extra-defense":{"percent.chance":15,"percent.adjust":150},"speed":{"percent.chance":100,"percent.adjust":25},"lifesteal":{"percent.chance":100,"percent.adjust":25}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-25},"speed":{"percent.chance":100,"percent.adjust":-15}}}'),
(1532,'void-wall','VOID WALL','','["kings-judgement"]',1530,'{"item":{"rune-i":1,"rune-v":1,"rune-vi":2}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":300},"speed":{"percent.chance":100,"percent.adjust":15},"lifesteal":{"percent.chance":100,"percent.adjust":35}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":3},"defense":{"percent.chance":100,"percent.adjust":-20},"speed":{"percent.chance":100,"percent.adjust":-25}}}'),
(1533,'blackhole-shock','BLACKHOLE SHOCK','','["ward-of-souls"]',1530,'{"item":{"rune-v":2,"rune-vi":2}}','{"myself":{"extra-defense":{"percent.chance":15,"percent.adjust":250},"speed":{"percent.chance":100,"percent.adjust":25},"lifesteal":{"percent.chance":100,"percent.adjust":15}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":6},"defense":{"percent.chance":100,"percent.adjust":-15},"speed":{"percent.chance":100,"percent.adjust":-15}}}'),
(1534,'anihilation-strike','ANIHILATION STRIKE','','["royal-blessing"]',1530,'{"item":{"rune-i":1,"rune-vi":3}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":800},"extra-defense":{"percent.chance":20,"percent.adjust":200},"speed":{"percent.chance":100,"percent.adjust":15},"lifesteal":{"percent.chance":100,"percent.adjust":25}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-25},"speed":{"percent.chance":100,"percent.adjust":-10}}}'),
(1535,'snow-barrier','SNOW BARRIER','','["underworlds-guardian"]',1530,'{"item":{"rune-v":1,"rune-viii":1}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":400}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-35}}}'),
(1536,'inferno','INFERNO','','["glacier-wall"]',1530,'{"item":{"rune-vi":1,"rune-viii":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":35}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-35}}}'),
(1537,'white-avalanche','WHITE AVALANCHE','','["primal-ward"]',1530,'{"item":{"rune-iii":2,"rune-viii":1}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":1000},"speed":{"percent.chance":100,"percent.adjust":35}}}'),
(1538,'wall-of-fire','WALL OF FIRE','','["blizzard-of-doom"]',1530,'{"item":{"rune-vii":1,"rune-viii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":45}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":7}}}'),
(1539,'blink-of-an-eye','BLINK OF AN EYE','','["final-stand"]',1530,'{"item":{"rune-ii":1,"rune-vi":1,"rune-ix":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":40}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(1540,'shall-not-pass','SHALL NOT PASS','','["willbreaker"]',1530,'{"item":{"rune-ii":1,"rune-v":1,"rune-ix":1}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":500}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-40}}}'),
(1541,'one-shot-one-kill','ONE SHOT ONE KILL','','["thousand-soul"]',1530,'{"item":{"rune-ii":1,"rune-iv":1,"rune-ix":1}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":1200},"speed":{"percent.chance":100,"percent.adjust":40}}}'),
(1542,'succubus','SUCCUBUS','','["silent-string"]',1530,'{"item":{"rune-ii":1,"rune-vii":1,"rune-ix":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":50}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":8}}}'),
(1543,'the-end','THE END','','["barrier-of-none"]',1530,'{"item":{"rune-ii":1,"rune-iii":1,"rune-ix":1}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":1200}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(1544,'infatuation','INFATUATION','','["titanslayer"]',1530,'{"item":{"rune-i":1,"rune-v":1,"rune-ix":1}}','{"enemy":{"stun":{"percent.chance":20,"flat.adjust":8},"defense":{"percent.chance":100,"percent.adjust":-40}}}'),
(1545,'deadly-infection','DEADLY INFECTION','','["souleater"]',1530,'{"item":{"rune-i":1,"rune-vii":1,"rune-ix":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":50}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-40}}}'),
(1546,'defenders-luck','DEFENDER\'S LUCK','','["nightguard"]',1530,'{"item":{"rune-i":1,"rune-vi":1,"rune-ix":1}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":500},"lifesteal":{"percent.chance":100,"percent.adjust":50}}}'),
(1547,'nightstrike','NIGHTSTRIKE','','["nightguard"]',1530,'{"item":{"rune-i":1,"rune-ix":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":40}}}'),
(1548,'my-will','MY WILL','','["willbreaker"]',1530,'{"item":{"rune-ii":1,"rune-ix":1}}','{"enemy":{"stun":{"percent.chance":15,"flat.adjust":15}}}'),
(1549,'assassins-move','ASSASSIN\'S MOVE','','["silent-string"]',1530,'{"item":{"rune-iii":1,"rune-ix":1}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-35}}}'),
(1550,'perfect-hit','PERFECT HIT','','["titanslayer"]',1530,'{"item":{"rune-iv":1,"rune-ix":1}}','{"myself":{"critical-hit":{"percent.chance":10,"percent.adjust":3000}}}'),
(1551,'bloodthirsty','BLOODTHIRSTY','','["souleater"]',1530,'{"item":{"rune-v":1,"rune-ix":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":75}}}'),

(1701,'stone-skin','STONE SKIN','',null,1700,'{"item":{"rune-i":1}}','{"enemy":{"defense":{"percent.chance":100,"percent.adjust":-5}}}'),
(1702,'faster-than-light','FASTER THAN LIGHT','','["bone-shield"]',1700,'{"item":{"rune-ii":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":10}}}'),
(1703,'inner-strength','INNER STRENGTH','','["elven-protector"]',1700,'{"item":{"rune-i":1,"rune-iii":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":15}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-10}}}'),
(1704,'warriors-way','WARRIOR\'S WAY','','["nightmare-shield"]',1700,'{"item":{"rune-i":1,"rune-ii":1,"rune-iv":1,"rune-v":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":20},"critical-hit":{"percent.chance":10,"percent.adjust":300}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-20},"speed":{"percent.chance":100,"percent.adjust":-15}}}'),
(1705,'dragon-skin','DRAGON SKIN','','["dragons-claw"]',1700,'{"item":{"rune-iii":1}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":180}}}'),
(1706,'white-mirror','WHITE MIRROR','','["ice-storm-blade"]',1700,'{"item":{"rune-iii":1,"rune-vii":1}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":350}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-30}}}'),

(1731,'pure-luck','PURE LUCK','',null,1730,'{"item":{"rune-i":3}}','{"enemy":{"stun":{"percent.chance":5,"flat.adjust":2}}}'),
(1732,'heroic-knock-out','HEROIC KNOCK OUT','','["undead-wall"]',1730,'{"item":{"rune-iii":3}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":15}},"enemy":{"stun":{"percent.chance":15,"flat.adjust":4}}}'),
(1733,'demonic-blackout','DEMONIC BLACKOUT','','["elven-queens-shield"]',1730,'{"item":{"rune-i":1,"rune-iv":2,"rune-v":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":20}},"enemy":{"stun":{"percent.chance":15,"flat.adjust":6},"defense":{"percent.chance":100,"percent.adjust":-15},"speed":{"percent.chance":100,"percent.adjust":-15}}}'),
(1734,'time-freeze','TIME FREEZE','','["ice-shard"]',1730,'{"item":{"rune-iii":1,"rune-iv":1}}','{"enemy":{"stun":{"percent.chance":20,"flat.adjust":5}}}'),
(1735,'burning-ground','BURNING GROUND','','["fiery-fang"]',1730,'{"item":{"rune-iii":1,"rune-vi":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":30}},"enemy":{"stun":{"percent.chance":15,"flat.adjust":6}}}'),
(1736,'binding-flames','BINDING FLAMES','','["blade-of-fire"]',1730,'{"item":{"rune-iii":1,"rune-vii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":40}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":6}}}'),

(1761,'berserker-wrath','BERSERKER WRATH','',null,1760,'{"item":{"rune-i":1,"rune-ii":1}}','{"myself":{"critical-hit":{"percent.chance":10,"percent.adjust":200}}}'),
(1762,'empowering-barrier','EMPOWERING BARRIER','','["elven-legend"]',1760,'{"item":{"rune-ii":1,"rune-iii":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-20}}}'),
(1763,'demolishing-blast','DEMOLISHING BLAST','','["sword-of-marriage"]',1760,'{"item":{"rune-iii":2,"rune-iv":1,"rune-v":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15},"speed":{"percent.chance":100,"percent.adjust":15},"critical-hit":{"percent.chance":15,"percent.adjust":600}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":20}}}'),
(1764,'ice-shock','ICE SHOCK','','["hoarfrost-shield"]',1760,'{"item":{"rune-iv":1,"rune-vi":1}}','{"myself":{"critical-hit":{"percent.chance":20,"percent.adjust":700},"speed":{"percent.chance":100,"percent.adjust":25}}}'),
(1765,'white-storm','WHITE STORM','','["white-shell"]',1760,'{"item":{"rune-ii":1,"rune-vii":1}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":900},"speed":{"percent.chance":100,"percent.adjust":30}}}'),

(2401,'path-of-enlightenment','PATH OF ENLIGHTENMENT','','["amulet-of-truth"]',2400,'{"item":{"rune-i":1,"rune-ix":2}}','{"myself":{"earth-resist":{"percent.chance":100,"flat.adjust":50}}}'),
(2402,'mitar-walls','MITAR WALLS','','["amulet-of-protection"]',2400,'{"item":{"rune-ii":1,"rune-ix":2}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":65}}}'),
(2403,'elements-of-power','ELEMENTS OF POWER','','["amulet-of-elements"]',2400,'{"item":{"rune-iii":1,"rune-ix":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":30}}}'),
(2404,'unabashed-mind','UNABASHED MIND','','["dwarven-amulet"]',2400,'{"item":{"rune-iv":1,"rune-ix":2}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":20}}}'),
(2405,'dwarven-might','DWARVEN MIGHT','','["amulet-of-strength"]',2400,'{"item":{"rune-v":1,"rune-ix":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":40}}}'),

(2421,'my-precious','MY PRECIOUS','','["jewel-of-mitar"]',2420,'{"item":{"rune-vi":1,"rune-ix":2}}','{"myself":{"earth-resist":{"percent.chance":100,"flat.adjust":110}}}'),
(2422,'shadows-of-mitar','SHADOWS OF MITAR','','["jewel-of-mitar"]',2420,'{"item":{"rune-i":1,"rune-ix":3}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-20}}}'),
(2423,'fist-of-stone','FIST OF STONE','','["depth-of-mitar"]',2420,'{"item":{"rune-ii":1,"rune-ix":3}}','{"myself":{"earth-damage":{"percent.chance":100,"flat.adjust":60}}}'),
(2424,'mitar-soul','MITAR SOUL','','["depth-of-mitar"]',2420,'{"item":{"rune-iii":1,"rune-ix":3}}','{"myself":{"extra-defense":{"percent.chance":100,"percent.adjust":10}}}'),
(2425,'swift-strike','SWIFT STRIKE','','["eyes-of-mitar"]',2420,'{"item":{"rune-iv":1,"rune-ix":3}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":10}}}'),
(2426,'magma-eruption','MAGMA ERUPTION','','["eyes-of-mitar"]',2420,'{"item":{"rune-v":1,"rune-ix":3}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":60}}}'),
(2427,'dungeon-parasite','DUNGEON PARASITE','','["heart-of-mitar"]',2420,'{"item":{"rune-vi":1,"rune-ix":3}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":30}}}'),
(2428,'shield-of-mitar','SHIELD OF MITAR','','["heart-of-mitar"]',2420,'{"item":{"rune-vii":1,"rune-ix":3}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":110}}}'),
(2429,'insomnia','INSOMNIA','','["axe-of-division"]',2420,'{"item":{"rune-iv":1,"rune-viii":1,"rune-ix":1}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":20}}}'),
(2430,'collapse-of-mitar','COLLAPSE OF MITAR','','["axe-of-division"]',2420,'{"item":{"rune-v":1,"rune-vii":1,"rune-ix":1}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":50}}}'),

(2441,'earths-power','EARTH\'S POWER','','["power-of-mitar"]',2440,'{"item":{"rune-ii":1,"rune-ix":4}}','{"myself":{"earth-damage":{"percent.chance":100,"flat.adjust":50}}}'),
(2442,'dwarven-force','DWARVEN FORCE','','["power-of-mitar"]',2440,'{"item":{"rune-i":1,"rune-ix":4}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(2443,'energy-grab','ENERGY GRAB','','["power-of-mitar"]',2440,'{"item":{"rune-iii":1,"rune-ix":4}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":30}}}'),
(2444,'mitar-armor','MITAR ARMOR','','["uldreds-warfist"]',2440,'{"item":{"rune-i":2,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":70}}}'),
(2445,'uldreds-strike','ULDRED\'S STRIKE','','["uldreds-warfist"]',2440,'{"item":{"rune-ii":2,"rune-ix":2}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-35}}}'),
(2446,'earthquake','EARTHQUAKE','','["uldreds-warfist"]',2440,'{"item":{"rune-iii":2,"rune-ix":2}}','{"myself":{"earth-damage":{"percent.chance":100,"flat.adjust":120}}}'),
(2447,'battle-heat','BATTLE HEAT','','["dungeon-warden"]',2440,'{"item":{"rune-iv":2,"rune-ix":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":40}}}'),
(2448,'call-of-the-mountain','CALL OF THE MOUNTAIN','','["dungeon-warden"]',2440,'{"item":{"rune-v":2,"rune-ix":2}}','{"myself":{"earth-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(2449,'thief-of-souls','THIEF OF SOULS','','["dungeon-warden"]',2440,'{"item":{"rune-vi":2,"rune-ix":2}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":25}}}'),
(2450,'resistant-barrier','RESISTANT BARRIER','','["hammer-of-doom"]',2440,'{"item":{"rune-vii":2,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":75}}}'),
(2451,'elemental-shield','ELEMENTAL SHIELD','','["hammer-of-doom"]',2440,'{"item":{"rune-viii":2,"rune-ix":2}}','{"enemy":{"defense":{"percent.chance":100,"percent.adjust":-45}}}'),
(2452,'fire-skin','FIRE SKIN','','["hammer-of-doom"]',2440,'{"item":{"rune-i":1,"rune-viii":1,"rune-ix":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":120}}}'),

(2461,'fire-of-mitar','FIRE OF MITAR','','["underworlds-guardian"]',2460,'{"item":{"rune-ii":1,"rune-vii":1,"rune-ix":2}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(2462,'mitar-element','MITAR ELEMENT','','["underworlds-guardian"]',2460,'{"item":{"rune-iii":1,"rune-vi":1,"rune-ix":2}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(2463,'shield-of-fire','SHIELD OF FIRE','','["underworlds-guardian"]',2460,'{"item":{"rune-iv":1,"rune-v":1,"rune-ix":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":80}}}'),
(2464,'earth-summon','EARTH SUMMON','','["face-of-mitar"]',2460,'{"item":{"rune-iv":1,"rune-v":1,"rune-ix":2}}','{"myself":{"earth-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(2465,'burning-flesh','BURNING FLESH','','["face-of-mitar"]',2460,'{"item":{"rune-iii":1,"rune-vi":1,"rune-ix":2}}','{"myself":{"critical-hit":{"percent.chance":15,"percent.adjust":2000}}}'),
(2466,'unbreakable-mind','UNBREAKABLE MIND','','["face-of-mitar"]',2460,'{"item":{"rune-ii":1,"rune-vii":1,"rune-ix":2}}','{"myself":{"extra-defense":{"percent.chance":100,"percent.adjust":10}}}'),
(2467,'stone-avalanche','STONE AVALANCHE','','["uldreds-peace"]',2460,'{"item":{"rune-i":1,"rune-viii":1,"rune-ix":2}}','{"myself":{"earth-damage":{"percent.chance":100,"flat.adjust":80}}}'),
(2468,'will-of-mitar','WILL OF MITAR','','["uldreds-peace"]',2460,'{"item":{"rune-v":1,"rune-vii":1,"rune-ix":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":40}}}'),
(2469,'unbroken','UNBROKEN','','["uldreds-peace"]',2460,'{"item":{"rune-iv":1,"rune-viii":1,"rune-ix":2}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":25}}}'),

(3031,'necromancers-barrier','NECROMANCER\'S BARRIER','','["necromancer-teeth"]',3030,'{"item":{"rune-i":1,"rune-ii":1,"rune-ix":1,"rune-x":1}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":50},"extra-defense":{"percent.chance":100,"percent.adjust":10}}}'),
(3032,'death-steam','DEATH STEAM','','["necromancer-teeth"]',3030,'{"item":{"rune-vii":1,"rune-viii":2,"rune-xi":1}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":25}}}'),
(3033,'life-after-death','LIFE AFTER DEATH','','["necromancer-teeth"]',3030,'{"item":{"rune-vii":1,"rune-ix":1,"rune-x":1,"rune-xii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":20},"speed":{"percent.chance":100,"percent.adjust":10}}}'),
(3034,'immune-to-death','IMMUNE TO DEATH','','["necromancer-teeth"]',3030,'{"item":{"rune-ix":1,"rune-x":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":100},"earth-resist":{"percent.chance":100,"flat.adjust":100}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-25}}}'),

(3041,'ban-hammer','BAN HAMMER','','["minotaurs-pride"]',3040,'{"item":{"rune-x":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"critical-hit":{"percent.chance":20,"percent.adjust":2000},"wind-damage":{"percent.chance":100,"flat.adjust":40}}}'),
(3042,'greek-power','GREEK POWER','','["minotaurs-pride"]',3040,'{"item":{"rune-ix":2,"rune-x":2}}','{"enemy":{"stun":{"percent.chance":20,"flat.adjust":2}}}'),
(3043,'forbidden-love','FORBIDDEN LOVE','','["minotaurs-pride"]',3040,'{"item":{"rune-vii":1,"rune-ix":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":20},"speed":{"percent.chance":100,"percent.adjust":15}}}'),
(3044,'bulls-natural-immunity','BULL\'S NATURAL IMMUNITY','','["minotaurs-pride"]',3040,'{"item":{"rune-ix":1,"rune-x":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"water-resist":{"percent.chance":100,"flat.adjust":100},"lightning-resist":{"percent.chance":100,"flat.adjust":100}}}'),

(3051,'true-potential','TRUE POTENTIAL','','["spear-of-the-gods"]',3050,'{"item":{"rune-x":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15},"stun-resist":{"percent.chance":100,"percent.adjust":70}}}'),
(3052,'rising-power','RISING POWER','','["spear-of-the-gods"]',3050,'{"item":{"rune-vii":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":10}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-25}}}'),
(3053,'deathwish','DEATHWISH','','["spear-of-the-gods"]',3050,'{"item":{"rune-viii":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"critical-hit":{"percent.chance":10,"percent.adjust":2000},"wind-damage":{"percent.chance":100,"flat.adjust":100}}}'),
(3054,'crushing-steel','CRUSHING STEEL','','["glory-shield"]',3050,'{"item":{"rune-viii":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"critical-hit":{"percent.chance":10,"percent.adjust":1400},"speed":{"percent.chance":100,"percent.adjust":40}}}'),
(3055,'shouts-of-defeated','SHOUTS OF DEFEATED','','["glory-shield"]',3050,'{"item":{"rune-vi":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":50}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-50}}}'),
(3056,'gods-blessing','GODS BLESSING','','["glory-shield"]',3050,'{"item":{"rune-ix":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":25},"wind-resist":{"percent.chance":100,"flat.adjust":100},"lightning-resist":{"percent.chance":100,"flat.adjust":100}}}'),

(3061,'fireburn','FIREBURN','','["fire-tongue"]',3000,'{"item":{"rune-i":1,"rune-x":1,"rune-xii":2}}','{"enemy":{"defense":{"percent.chance":100,"percent.adjust":-40}}}'),
(3062,'melting-touch','MELTING TOUCH','','["fire-tongue"]',3000,'{"item":{"rune-v":1,"rune-ix":1,"rune-xii":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":100}}}'),
(3063,'flame-burst','FLAME BURST','','["fire-tongue"]',3000,'{"item":{"rune-v":1,"rune-ix":1,"rune-xii":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15},"speed":{"percent.chance":100,"percent.adjust":20}}}'),
(3064,'fireshell','FIRESHELL','','["fire-tongue"]',3000,'{"item":{"rune-iv":1,"rune-x":1,"rune-xii":2}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":100},"lightning-resist":{"percent.chance":100,"flat.adjust":100}}}'),

(3071,'bloody-kiss','BLOODY KISS','','["spiders-claw"]',3010,'{"item":{"rune-x":2,"rune-xii":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":20}},"enemy":{"stun":{"percent.chance":20,"flat.adjust":3}}}'),
(3072,'striking-bite','STRIKING BITE','','["spiders-claw"]',3010,'{"item":{"rune-ii":1,"rune-ix":1,"rune-xi":1}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":100}}}'),
(3073,'poison-web','POISON WEB','','["spiders-claw"]',3010,'{"item":{"rune-x":3,"rune-xii":1}}','{"myself":{"wind-resist":{"percent.chance":100,"flat.adjust":100},"earth-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(3074,'venom-swarm','VENOM SWARM','','["spiders-claw"]',3010,'{"item":{"rune-i":1,"rune-xi":1,"rune-xii":2}}','{"myself":{"extra-defense":{"percent.chance":20,"percent.adjust":500},"speed":{"percent.chance":100,"percent.adjust":20}}}'),

(3081,'fire-vision','FIRE VISION','','["medusas-vision"]',3020,'{"item":{"rune-vii":1,"rune-viii":1,"rune-x":1,"rune-xii":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":125}}}'),
(3082,'water-vision','WATER VISION','','["medusas-vision"]',3020,'{"item":{"rune-vii":1,"rune-viii":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"water-resist":{"percent.chance":100,"flat.adjust":125}}}'),
(3083,'wind-vision','WIND VISION','','["medusas-vision"]',3020,'{"item":{"rune-vii":1,"rune-viii":1,"rune-x":1,"rune-xii":1}}','{"myself":{"wind-resist":{"percent.chance":100,"flat.adjust":125}}}'),
(3084,'earth-vision','EARTH VISION','','["medusas-vision"]',3020,'{"item":{"rune-vii":1,"rune-viii":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"earth-resist":{"percent.chance":100,"flat.adjust":125}}}'),
(3085,'lightning-vision','LIGHTNING VISION','','["medusas-vision"]',3020,'{"item":{"rune-vii":1,"rune-viii":1,"rune-x":1,"rune-xii":1}}','{"myself":{"lightning-resist":{"percent.chance":100,"flat.adjust":125}}}'),
(3086,'natural-beauty','NATURAL BEAUTY','','["medusas-vision"]',3020,'{"item":{"rune-x":2,"rune-xi":1,"rune-xii":1}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":100},"extra-defense":{"percent.chance":25,"percent.adjust":500}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":25}}}'),

(3101,'aquatic-symbiosis','AQUATIC SYMBIOSIS','','["aquatic-edge","tempest-guard"]',2590,'{"item":{"rune-x":1,"rune-xii":3}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":100},"extra-defense":{"percent.chance":100,"percent.adjust":10}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(3102,'aquatic-synergy','AQUATIC SYNERGY','','["aquatic-edge","tempest-guard"]',2590,'{"item":{"rune-xii":4}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":10},"speed":{"percent.chance":100,"percent.adjust":20},"critical-hit":{"percent.chance":15,"percent.adjust":2000}}}'),
(3103,'lightning-link','LIGHTNING LINK','','["stormbreaker-blade","thunderclap-barrier"]',2590,'{"item":{"rune-xii":2,"rune-xi":1,"rune-x":1}}','{"myself":{"stun-resist":{"percent.chance":100,"percent.adjust":100},"extra-defense":{"percent.chance":100,"percent.adjust":10}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-40}}}'),
(3104,'thunder-bind','THUNDER BIND','','["stormbreaker-blade","thunderclap-barrier"]',2590,'{"item":{"rune-xii":2,"rune-xi":1,"rune-x":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":10},"speed":{"percent.chance":100,"percent.adjust":20},"critical-hit":{"percent.chance":15,"percent.adjust":2000}}}'),
(3105,'aero-boost','AERO BOOST','','["airheart-blade","skyblaze-ward"]',2590,'{"item":{"rune-xii":2,"rune-xi":1,"rune-x":1}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":15}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-35},"stun":{"percent.chance":10,"flat.adjust":3}}}'),
(3106,'zephyr-link','ZEPHYR LINK','','["airheart-blade","skyblaze-ward"]',2590,'{"item":{"rune-xii":2,"rune-xi":1,"rune-x":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":20},"critical-hit":{"percent.chance":20,"percent.adjust":2000}},"enemy":{"defense":{"percent.chance":100,"percent.adjust":-40}}}'),


(3401,'storms-embrace','STORM\'S EMBRACE','','["aquatic-edge","tempest-guard"]',3400,'{"item":{"rune-xi":3,"rune-ix":2}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":500},"lightning-resist":{"percent.chance":100,"flat.adjust":1000},"health":{"percent.chance":100,"flat.adjust":1000}}}'),
(3402,'oceans-fury','OCEAN\'S FURY','','["aquatic-edge"]',3400,'{"item":{"rune-xi":2,"rune-x":1,"rune-viii":1,"rune-vii":1}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":500},"lightning-resist":{"percent.chance":100,"flat.adjust":250}}}'),
(3403,'tideguard','TIDEGUARD','','["tempest-guard"]',3400,'{"item":{"rune-xii":1,"rune-x":2,"rune-ix":1,"rune-vi":1}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":500},"lightning-resist":{"percent.chance":100,"flat.adjust":500}}}'),
(3404,'mermaids-vow','MERMAID\'S VOW','','["kings-judgement"]',3400,'{"item":{"rune-x":1,"rune-ix":2,"rune-ix":2,"rune-viii":1,"rune-vii":1}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":350},"lightning-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(3405,'majestic-radiance','MAJESTIC RADIANCE','','["royal-blessing"]',3400,'{"item":{"rune-xi":1,"rune-x":2,"rune-ix":2}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":450},"lightning-resist":{"percent.chance":100,"flat.adjust":100}}}'),

(3501,'thunderlords-havoc','THUNDERLORD\'S HAVOC','','["stormbreaker-blade","thunderclap-barrier"]',3500,'{"item":{"rune-xi":3,"rune-ix":2}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":500},"water-resist":{"percent.chance":100,"flat.adjust":2000},"health":{"percent.chance":100,"flat.adjust":1000}}}'),
(3502,'celestial-thunderstorm','CELESTIAL THUNDERSTORM','','["stormbreaker-blade"]',3500,'{"item":{"rune-xi":2,"rune-x":1,"rune-viii":1,"rune-vii":1}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":500},"water-resist":{"percent.chance":100,"flat.adjust":250}}}'),
(3503,'electrifying-assault','ELECTRIFYING ASSAULT','','["thunderclap-barrier"]',3500,'{"item":{"rune-xii":1,"rune-x":2,"rune-ix":1,"rune-vi":1}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":500},"water-resist":{"percent.chance":100,"flat.adjust":500}}}'),
(3504,'arcane-thunder','ARCANE THUNDER','','["blade-of-the-damned"]',3500,'{"item":{"rune-x":1,"rune-ix":2,"rune-ix":2,"rune-viii":1,"rune-vii":1}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":350},"water-resist":{"percent.chance":100,"flat.adjust":100}}}'),
(3505,'voltage-infusion','VOLTAGE INFUSION','','["ward-of-souls"]',3500,'{"item":{"rune-xi":1,"rune-x":2,"rune-ix":2}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":450},"water-resist":{"percent.chance":100,"flat.adjust":100}}}'),

(3601,'fire-resist-ug-graveyard','FIRE RESIST','','["underworlds-guardian"]',3600,'{"item":{"rune-vi":1,"rune-vii":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":1000}}}'),
(3602,'fire-resist+-ug-graveyard','FIRE RESIST +','','["underworlds-guardian"]',3600,'{"item":{"rune-viii":1,"rune-ix":1,"rune-x":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":2000}}}'),
(3603,'fire-resist++-sw-graveyard','FIRE RESIST ++','','["skyblaze-ward"]',3600,'{"item":{"rune-ix":1,"rune-x":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":6000}}}'),
(3604,'gust-of-wind','GUST OF WIND','','["axe-of-division"]',3600,'{"item":{"rune-vi":1,"rune-vii":1,"rune-viii":1,"rune-ix":1}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":1000}}}'),
(3605,'gusts-grasp','GUST\'S GRASP','','["axe-of-division"]',3600,'{"item":{"rune-vii":1,"rune-viii":1,"rune-ix":1,"rune-x":1}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":1500}}}'),
(3606,'windstorms-fury','WINDSTORM\'S FURY','','["axe-of-division"]',3600,'{"item":{"rune-ix":1,"rune-x":2,"rune-xi":1}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":2500}}}'),
(3607,'tornados-tempest','TORNADO\'S TEMPEST','','["axe-of-division"]',3600,'{"item":{"rune-x":1,"rune-xi":2,"rune-xii":1}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":3000}}}'),
(3608,'zephyrs-torrent','ZEPHYR\'S TORRENT','','["airheart-blade"]',3600,'{"item":{"rune-x":1,"rune-xi":2,"rune-xii":1}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":12000}}}'),
(3609,'attackspeed-graveyard','ATTACKSPEED','','["airheart-blade"]',3600,'{"item":{"rune-xi":1,"rune-xii":3}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":50}}}'),
(3610,'lifesteal-graveyard','LIFESTEAL','','["skyblaze-ward"]',3600,'{"item":{"rune-vii":1,"rune-x":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":30}}}'),
(3611,'defense-graveyard','DEFENSE','','["skyblaze-ward"]',3600,'{"item":{"rune-ix":1,"rune-x":2,"rune-xi":1}}','{"myself":{"defense":{"percent.chance":100,"flat.adjust":500}}}'),
(3612,'fire-resist++-tb-graveyard','FIRE RESIST ++','','["thunderclap-barrier"]',3600,'{"item":{"rune-ix":1,"rune-x":1,"rune-xi":1,"rune-xii":1}}','{"myself":{"fire-resist":{"percent.chance":100,"flat.adjust":6000}}}'),

(3701,'vinewrap','VINEWRAP','','["naturesong-edge"]',3700,'{"item":{"rune-xiii":2,"rune-xii":1,"rune-x":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15},"stun-resist":{"percent.chance":100,"percent.adjust":80},"earth-damage":{"percent.chance":100,"flat.adjust":250}}}'),
(3702,'razorleaf','RAZORLEAF','','["naturesong-edge"]',3700,'{"item":{"rune-xiii":2,"rune-xii":1,"rune-xi":1}}','{"myself":{"critical-hit":{"percent.chance":10,"percent.adjust":2000},"attack":{"percent.chance":100,"flat.adjust":200},"earth-damage":{"percent.chance":100,"flat.adjust":250}}}'),
(3703,'quickthorn','QUICKTHORN','','["naturesong-edge"]',3700,'{"item":{"rune-xiii":2,"rune-xii":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":30},"earth-damage":{"percent.chance":100,"flat.adjust":200}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-25}}}'),
(3704,'rootguard','ROOTGUARD','','["terraflora-shield"]',3700,'{"item":{"rune-xiii":2,"rune-xii":1,"rune-ix":1}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":15},"speed":{"percent.chance":100,"percent.adjust":25},"extra-defense":{"percent.chance":25,"percent.adjust":250},"health":{"percent.chance":100,"flat.adjust":1000}},"enemy":{"stun":{"percent.chance":25,"flat.adjust":6},"speed":{"percent.chance":100,"percent.adjust":-30},"defense":{"percent.chance":100,"percent.adjust":-25},"attack":{"percent.chance":100,"percent.adjust":-25}}}'),
(3705,'natures-rip','NATURE\'S RIP','','["naturesong-edge","terraflora-shield"]',3700,'{"item":{"rune-xiii":3,"rune-ix":1}}','{"myself":{"earth-damage":{"percent.chance":100,"flat.adjust":500},"attack":{"percent.chance":100,"flat.adjust":300}},"enemy":{"speed":{"percent.chance":100,"percent.adjust":-30}}}'),
(3706,'leafbarrier','LEAF BARRIER','','["naturesong-edge","terraflora-shield"]',3700,'{"item":{"rune-xiii":3,"rune-viii":1}}','{"myself":{"water-resist":{"percent.chance":100,"flat.adjust":200},"earth-resist":{"percent.chance":100,"flat.adjust":200},"health":{"percent.chance":100,"flat.adjust":1000},"defense":{"percent.chance":100,"flat.adjust":200}}}'),


-- "effects":{}
-- "effects":{"myself":{}}
-- "effects":{"enemy":{}}
-- "effects":{"myself":{},"enemy":{}}

-- "myself":{}
-- "lifesteal":{"percent.chance":100,"percent.adjust":50},
-- "critical-hit":{"percent.chance":10,"percent.adjust":500},
-- "stun-resist":{"percent.chance":100,"percent.adjust":100},
-- "extra-defense":{"percent.chance":15,"percent.adjust":400},
-- "speed":{"percent.chance":100,"percent.adjust":30},

-- "enemy":{}
-- "stun":{"percent.chance":75,"flat.adjust":3},
-- "speed":{"percent.chance":100,"percent.adjust":-40},
-- "defense":{"percent.chance":100,"percent.adjust":-50},

(12453,'slowing-down-stormforge-amulet','SLOWING DOWN','','["stormforge-amulet"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-ii":1,"rune-ix":2}}','{"enemy":{"speed":{"percent.chance":100,"percent.adjust":-35}}}'),
(12454,'lightning-stormforge-amulet','LIGHTNING','','["stormforge-amulet"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-x":1,"rune-ix":2}}','{"myself":{"lightning-damage":{"percent.chance":100,"flat.adjust":50}}}'),
(12455,'lifesteal-stormforge-amulet','LIFESTEAL','','["stormforge-amulet"]',2440,'{"item":{"rune-xii":2,"rune-xi":1,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":40}}}'),
(12456,'attackspeed-eternal-pyre','ATTACKSPEED','','["eternal-pyre"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-x":1,"rune-ix":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":10}}}'),
(12457,'fire-eternal-pyre','FIRE','','["eternal-pyre"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-x":1,"rune-ix":2}}','{"myself":{"fire-damage":{"percent.chance":100,"flat.adjust":50}}}'),
(12458,'lifesteal-eternal-pyre','LIFESTEAL','','["eternal-pyre"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-iii":1,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":30}}}'),
(12459,'lifesteal-airwhisper-charm','LIFESTEAL','','["airwhisper-charm"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-viii":1,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":30}}}'),
(12460,'wind-airwhisper-charm','FIRE','','["airwhisper-charm"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-x":1,"rune-ix":2}}','{"myself":{"wind-damage":{"percent.chance":100,"flat.adjust":50}}}'),
(12461,'extra-defense-airwhisper-charm','EXTRA DEFENSE','','["airwhisper-charm"]',2440,'{"item":{"rune-xii":2,"rune-xi":1,"rune-ix":2}}','{"myself":{"extra-defense":{"percent.chance":100,"percent.adjust":10}}}'),
(12462,'lifesteal-oceans-breath','LIFESTEAL','','["oceans-breath"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-viii":1,"rune-ix":2}}','{"myself":{"lifesteal":{"percent.chance":100,"percent.adjust":35}}}'),
(12463,'water-oceans-breath','WATER','','["oceans-breath"]',2440,'{"item":{"rune-xii":1,"rune-xi":1,"rune-x":1,"rune-ix":2}}','{"myself":{"water-damage":{"percent.chance":100,"flat.adjust":50}}}'),
(12464,'attackspeed-oceans-breath','ATTACKSPEED','','["oceans-breath"]',2440,'{"item":{"rune-xii":2,"rune-xi":1,"rune-ix":2}}','{"myself":{"speed":{"percent.chance":100,"percent.adjust":10}}}')
;
