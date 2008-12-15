CREATE TABLE 
	`tasks` (                                               
          `id` tinyint(3) NOT NULL auto_increment,                           
          `priority` tinyint(3) NOT NULL,                                    
          `status` enum('NEW','BLOCKED','COMPLETE') NOT NULL default 'NEW',  
          `description` varchar(255) default NULL,                           
          `updated` timestamp NOT NULL default CURRENT_TIMESTAMP,            
          PRIMARY KEY  (`id`)                                                
	) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1    