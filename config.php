<?php
/**
 * This file contains configuration for the ClickBooth coding test application
 * (tracer version).
 *
 * @todo normally, I would be done using Zend_Config_Xml or Zend_Config_Ini,
 * 	but in essence of simplicity, a simple array will be used
 *
 * @author 		A.J. Brown <aj@ajbrown.org>
 */

$aConfig = array();

$aConfig[ 'DbPlatform' ] = 'mysql';	// 'mysql', 'sqlite'
//$aConfig[ 'DbPlatform' ] = 'sqlite';
$aConfig[ 'DbHostname' ] = 'localhost';
$aConfig[ 'DbUsername' ] = 'clickbooth';
$aConfig[ 'DbPassword' ] = 'l337passw0rd';
$aConfig[ 'DbSchema']	 = 'clickbooth';
//$aConfig[ 'DbSchema']	 = 'clickbooth.db';
