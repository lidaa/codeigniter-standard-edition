<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Command
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Command
{
    /**
     * getIO
     * 
     * @param Event $event
     * @return \Cise\Input_Output
     */
    protected static function getIO($event)
    {
        self::initConstants();
        
        if ($event instanceof Event) {
            $io = $event->getIO();
        } else {
            $io = new Input_Output();
        }
        
        return $io;
    }
    
    /**
     * getArgv
     * 
     * @global array $argv
     * @param bool $as_string
     * @return string
     */
    protected static function getArgv($as_string = false)
    {
        global $argv;
        
        $tab_argv = array_slice($argv, 2);
        
        if (!$as_string) {
            return $tab_argv;
        }
        
        return implode(' ', $tab_argv);
    }
    
    /**
     * initConstants
     */
    private static function initConstants()
    {
        defined('CISE_CMD') || define('CISE_CMD', true);
        
        include_once sprintf('%s/index.php', dirname(dirname(dirname(__FILE__))));
    }
}
