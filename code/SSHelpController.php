<?php
class SSHelpController extends Controller {

	public function index( SS_HTTPRequest $request ) {
		$args = $request->getVars();
		if( !$type = @$args['type'] ) $type = 'subsite';
		if( !$assetsFolder = @$args['assets-folder'] ) $assetsFolder = 'help';
		
		if( $type == 'subsite' ) {
			if( !$siteName = @$args['site-name'] ) $siteName = 'User Help and Documentation';
			if( !$subsiteID = @$args['subsite-id'] ) {
				$this->showUsage("--subsite-id is required");
				exit;
			}
			Subsite::changeSubsite($subsiteID);
			$subsite = Subsite::currentSubsite();
			$subsite->Theme = 'ss-help';
			$subsite->write();
			$siteConfig = SiteConfig::current_site_config();
			$siteConfig->Title = $siteName;
			$siteConfig->write();
		}
		else if( $type == 'directory' ) {
			echo "The directory mode has not been implemented\n";
			exit;
		}
		else {
			$this->showUsage();
			exit;
		}
		$moduleDir = dirname(dirname(__FILE__));
		
		$files = $moduleDir.'/data/File.csv';
		$siteTree = $moduleDir.'/data/SiteTree.csv';
		if (!is_readable($files)) {
			return "Cannot open $files";
		}
		if (!is_readable($siteTree)) {
			return "Cannot open $siteTree";
		}
		$filesCsv = new EasyCSV($files, true, 'array');
		foreach( $filesCsv as $fileRow ) {
			if( $fileRow['ClassName'] == 'Folder' ) {
				continue;
			}
			$file = new File();
			$fileName = $this->replacePath($fileRow['Filename'], $assetsFolder);
			if( DataObject::get_one('File', "Filename = '$fileName'") ) {
				continue;
			}
			$sourceFile = "$moduleDir/$fileRow[Filename]";
			$filePath = Director::baseFolder()."/$fileName";
			$folder = Folder::findOrMake(str_replace('assets/', '', dirname($fileName)));
			if( !is_file($filePath) ) {
				copy($sourceFile, $filePath);
				chmod($filePath, 0664);
			}
			foreach( $fileRow as $name => $value ) {
				if( $name != 'ID' ) {
					$file->$name = $this->handleValue($name, $value);
				}
			}
			$file->Filename = $fileName;
			$file->ParentID = $folder->ID;
			$file->write();
		}
		
		$siteTreeCsv = new EasyCSV($siteTree, true, 'array');
		$idMapping = array();
		foreach( $siteTreeCsv as $siteTreeRow ) {
			$class = $siteTreeRow['ClassName'];
			$siteTree = new $class();
			foreach( $siteTreeRow as $name => $value ) {
				if( $name != 'ID' ) {
					$siteTree->$name = $this->handleValue($name, $this->replacePath($value, $assetsFolder));
				}
			}
			if( $siteTree->ParentID ) {
				$siteTree->ParentID = $idMapping[$siteTree->ParentID];
			}
			$siteTree->write();
			$idMapping[$siteTreeRow['ID']] = $siteTree->ID;
		}
		foreach( DataObject::get('SiteTree', "SiteTree.ID IN (".implode(', ', $idMapping).")") as $siteTree ) { /* @var $siteTree SiteTree */
			$siteTree->Content = $this->replaceSiteTreeIDs($siteTree->Content, $idMapping);
			$siteTree->Status = 'Published';
			$siteTree->write();
			$siteTree->publish('Stage', 'Live');
		}
		echo "You will now need to ensure that the $moduleDir/theme folder is symlinked (or copied) into themes/ss-help".NL;
	}

	function replacePath( $path, $assetsFolder ) {
		if( $assetsFolder ) {
			return str_replace('assets/', "assets/$assetsFolder/", $path);
		}
		return $path;
	}

	function replaceSiteTreeIDs( $content, $idMapping ) {
		return preg_replace('/(\[sitetree_link[^\]]+?id=)(\d+)\]/ie', '"$1".$idMapping[$2]."]"', $content);
	}

	function handleValue( $name, $value ) {
		if( $value == '\N' ) {
			return null;
		}
		return preg_replace('/\\\\([\r\n"\'])/', '$1', $value); 
	}

	function showUsage( $message = null ) {
		if( $message ) {
			echo "Invalid usage: $message\n";
		}
		$argv = $GLOBALS['argv'];
		echo "Usage: $argv[0] --subsite-id=X [--assets-folder={help}] [--site-name={User Help and Documentation}]\n";
		exit;
	}

}
