CREATE TABLE IF NOT EXISTS `#__users` ( 
	`id` INT NOT NULL AUTO_INCREMENT , 
	`name` VARCHAR(255) NOT NULL , 
	`username` VARCHAR(255) NOT NULL , 
	`email` VARCHAR(255) NOT NULL ,
	`password` VARCHAR(60) NOT NULL , 
	`status` INT NULL DEFAULT NULL , 
	`activation` INT NULL DEFAULT '1' ,
	 PRIMARY KEY (`id`) 
) ENGINE = InnoDB; 

CREATE TABLE `#__recover` ( 
	`user_id` INT NOT NULL , 
	`token` VARCHAR(255) NOT NULL , 
	`password` VARCHAR(255) NOT NULL ,
	 UNIQUE (`token`) 
) ENGINE = InnoDB;

CREATE TABLE `#__activation` ( 
	`user_id` INT NOT NULL , 
	`token` VARCHAR(255) NOT NULL ,
	 UNIQUE (`token`) 
) ENGINE = InnoDB;