<?php
require 'parsers/ParserInterface.php';

foreach (glob(__DIR__ . '/parsers/*.parser.php') as $classFile) {
    include $classFile;
}

class PackageXMLGenConfig {

	public static function getConfig() {
		// Generics
		$configArr = array(
			'applications' => array('TypeName' => 'CustomApplication'),
			'classes' => array('TypeName' => 'ApexClass'),
			'customPermissions' => array('TypeName' => 'CustomPermission'),
			'email' => array('TypeName' => 'EmailTemplate'),
			'flexipages' => array('TypeName' => 'FlexiPage'),
			'flows' => array('TypeName' => 'Flow'),
			'globalValueSets' => array('TypeName' => 'GlobalValueSet'),
			'layouts' => array('TypeName' => 'Layout'),
			'permissionsets' => array('TypeName' => 'PermissionSet')
		);
		
		// Dinamically loads custom parsers
		foreach (get_declared_classes() as $className) {
			if (in_array('ParserInterface', class_implements($className))) {
				$parser = new $className();
				$configArr[$parser->getFolderName()] = array('CustomParser' => $parser);
        	}
        }

        return $configArr;
	}
}
?>