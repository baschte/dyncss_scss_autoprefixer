<?php

namespace Baschte\DyncssScssAutoprefixer\Parser;

use KayStrobach\Dyncss\Utilities\ApplicationContext;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;
use \Autoprefixer;

/**
 * @todo fix type hinting in @param comments
 */
abstract class AbstractAutoprefixParser extends \KayStrobach\Dyncss\Parser\AbstractParser{

	/**
	 * @param $inputFilename
	 * @param null $outputFilename
	 * @return string
	 *
	 * @todo add typehinting
	 */
	public function compileFile($inputFilename, $outputFilename = null) {

		if(!$this->prepareEnvironment($inputFilename)) {
			return $inputFilename;
		}
		if($outputFilename === null) {
			$outputFilename = PATH_site . $this->cachePath . basename($inputFilename);
		}
		$outputFilenamePathInfo = pathinfo($outputFilename);
		$noExtensionFilename = $outputFilename . '-' . hash('crc32b', $inputFilename) . '-' . hash('crc32b', serialize($this->overrides)) . '-' . hash('crc32b', filemtime($inputFilename));
		$preparedFilename = $noExtensionFilename . '.' . $outputFilenamePathInfo['extension'];
		$cacheFilename = $noExtensionFilename . '.cache';
		$outputFilename = $noExtensionFilename . '.css';

		$this->inputFilename = $inputFilename;
		$this->outputFilename = $outputFilename;
		$this->cacheFilename = $cacheFilename;

		// exit if a precompiled version already exists
		if ((file_exists($outputFilename)) && (!ApplicationContext::isDevelopmentModeActive() && (!$this->config['enableDebugMode']))) {
			return $outputFilename;
		}

		//write intermediate file, if the source has been changed, the rest is done by the cache management
		if(@filemtime($preparedFilename) < @filemtime($inputFilename) || $this->_checkIfCompileNeeded($inputFilename)) {
			file_put_contents($preparedFilename, $this->_prepareCompile(file_get_contents($inputFilename)));

			$fileContent = $this->_postCompile($this->_compileFile($inputFilename, $preparedFilename, $outputFilename, $cacheFilename));

			if(!class_exists("\Autoprefixer")){
				include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dyncss_scss_autoprefixer') . 'Resources/Private/vladkens/autoprefixer/lib/Autoprefixer.php');
			}

			if($this->_command_exists("node")){
				//Get Extensionsettings for Autoprefixer
				$autoprefixerConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dyncss_scss_autoprefixer']);
				$autoprefixerConfiguration = $autoprefixerConfiguration['autoprefixerSettings'];

				if(isset($autoprefixerConfiguration)){
					$autoprefixer = new Autoprefixer($autoprefixerConfiguration);
					$fileContent = $autoprefixer->compile($fileContent);
				}
			}

			if($fileContent !== false) {
				file_put_contents($outputFilename, $fileContent);
				// important for some cache clearing scenarios
				if(file_exists($preparedFilename)) {
					unlink($preparedFilename);
				}
			}
		}

		return $outputFilename;
	}

	/**
	 * Determines if a command exists on the current environment
	 *
	 * @param string $command The command to check
	 * @return bool True if the command has been found ; otherwise, false.
	 */
	private function _command_exists($command)
	{
		$whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';

		$process = proc_open(
			"$whereIsCommand $command",
			array(
				0 => array("pipe", "r"), //STDIN
				1 => array("pipe", "w"), //STDOUT
				2 => array("pipe", "w"), //STDERR
			),
			$pipes
		);

		if($process !== false) {
			$stdout = stream_get_contents($pipes[1]);
			$stderr = stream_get_contents($pipes[2]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			proc_close($process);

			return $stdout != '';
		}

		return false;
	}
}
