-- ============ ENTITLEMENT ===========================================

truncate entitlement;
insert into entitlement (id,name,label,description,data,created,updated) values
(1001,'creator','Site Creator','Where it all started','{"icon":"crown","class":"text-yellow"}',now(),now()),
(1003,'developer','Site Developer','Bugs with game features','{"icon":"code","class":"text-purple"}',now(),now()),
(1004,'tester','Site Tester','You break it, you buy it','{"icon":"vial","class":"text-green"}',now(),now()),
(1006,'founder','Founding Member','With us from the beginning','{"icon":"trophy","class":"text-orange"}',now(),now()),


(1101,'simulation-usage','Simulation Usage','Allowed to use simulation tools','{"icon":"tachometer-alt","class":"text-white"}',now(),now()),

(2001,'guild-herbs','Herbs Guild','"We slay dragons round here" -Sunshine','{"icon":"cannabis","class":"text-green"}',now(),now()),
(2002,'guild-wizzards','Wizzards Guild','The most magical guilds in the game','{"icon":"hat-wizard","class":"text-purple"}',now(),now())

;

