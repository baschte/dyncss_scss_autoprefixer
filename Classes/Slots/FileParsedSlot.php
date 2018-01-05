<?php
namespace Baschte\DyncssScssAutoprefixer\Slots;
use \Autoprefixer;

/**
 * Class FileParsedSlot
 */
class FileParsedSlot {

    /**
     * @param string $fileContent
     * @return array
     * @throws \AutoprefixerException
     */
    public function afterFileParsed(string $fileContent) {

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

        return [$fileContent];
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
