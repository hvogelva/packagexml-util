<?php
require 'PackageXMLGenConfig.php';
require 'PackageXMLUtil.php';

class PackageXMLGenerator {

	private $rootPath = 'force-app/main/default/';
	private $metadataArr;
	private $ignoredLines;

	public function __construct() {
		$this->metadataArr = array();
		$this->ignoredLines = array();
	}

	public function generatePackage($diffStr, $options) {
		$this->process($diffStr, $options['rootPath']);

		if (!empty($this->metadataArr)) {
			if (isset($this->metadataArr['Unsupported'])) {
				// Removes 'Unsupported' from the array, but stores it in a var
				$unsupportedArr = $this->metadataArr['Unsupported'];
				unset($this->metadataArr['Unsupported']);

				// If showSupported, adds it again (but at the end)
				if ($options['showSupported']) {
					$this->metadataArr['Unsupported'] = $unsupportedArr;
				}
			}
		}
	}

	public function getMetadataArray() {
		return $this->metadataArr;
	}

	public function getIgnoredLines() {
		return $this->ignoredLines;
	}

	private function process($diffStr, $rootPath) {
		$config = PackageXMLGenConfig::getConfig();

		if ($rootPath == null) {
			$rootPath = $this->rootPath;
		}

		// Line break split
		$diffArr = preg_split('/\n/', $diffStr);

		if (!empty($diffArr)) {
			// diffLine E.g. force-app/main/default/classes/ExampleClass.cls
			foreach ($diffArr as $diffLine) {
				// Ignore empty line
				if (empty(trim($diffLine))) { 
					continue; 
				}

				$rawLine = $diffLine;
				/* String cleaning section */

				// Trims and removes some git characters. E.g.
				// 'modified: force-app/...' -> 'force-app/...')
				// 'A force-app/...' -> 'force-app/...')
				$diffLine = trim(preg_replace('/(.+?:)|(^.? )/', '', trim($diffLine), 1));

				// Some resulting lines may be enclosed in double quotes
				// E.g. "force-app/main/default/layouts/..."
				if (str_starts_with($diffLine, '"') && str_ends_with($diffLine, '"')) {
					$diffLine = preg_replace('/"/', '', $diffLine, 1); // Removes first
					$diffLine = trim(preg_replace('/("(?!.*"))/', '', $diffLine)); // Removes last
				}

				// Decodes octal escaped characters (bash). E.g. '\303\251' 
				$diffLine = preg_replace_callback('/\\\\([0-7]{1,3})/', function ($m) {
					return chr(octdec($m[1]));
				}, $diffLine);

				// Removes 'rootPath' from diffLine (force-app/main/default)
				$diffLine = str_replace($rootPath, '', $diffLine); // -> classes/ExampleClass.cls

				/* Ends string cleaning section */

				// Contains '/'
				if (str_contains($diffLine, '/')) { // /* && str_contains($diffLine, '.') */
					$explodeArr = explode('/', $diffLine, 2);
					$folderStr = $explodeArr[0];
					$metadataStr = $explodeArr[1];

					// Metadata type supported in the config file
					if (isset($config[$folderStr])) {
						$memberValue = explode('.', $metadataStr, 2)[0];
						
						if (!empty($memberValue)) {
							$typeName;

							// Generic type
							if (!isset($config[$folderStr]['CustomParser'])) {
								$typeName = $config[$folderStr]['TypeName'];
							}
							// 'Custom' type
							else {
								$parser = $config[$folderStr]['CustomParser'];
								$typeName = $parser->getTypeName($metadataStr);
								$memberValue = $parser->getMemberValue($typeName, $metadataStr);
							}
							
							if (!isset($this->metadataArr[$typeName])) {
								$this->metadataArr[$typeName] = array();
							}

							if (!in_array($memberValue, $this->metadataArr[$typeName])) {
								array_push($this->metadataArr[$typeName], $memberValue);
							}

							natcasesort($this->metadataArr[$typeName]);
						}
					}
					// Metadata type not supported yet in the config file
					else {
						if (!isset($this->metadataArr['Unsupported'])) {
							$this->metadataArr['Unsupported'] = array();
						}

						array_push($this->metadataArr['Unsupported'], $diffLine);
						natcasesort($this->metadataArr['Unsupported']);
					}
				}
				else {
					array_push($this->ignoredLines, $rawLine);
				}
			}
			
			ksort($this->metadataArr);
		}
	}
}
?>