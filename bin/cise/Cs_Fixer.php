<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Description of Clear_Logs
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Cs_Fixer extends Command {
    
    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null) {
        $io = self::getIO($event);
     
        $vendor_path = dirname(dirname(dirname(BASEPATH)));
        $php_cs_fixer =  sprintf('%s/bin/php-cs-fixer', $vendor_path);

        exec($php_cs_fixer . ' ' . self::getArgv(true));
    }
}
