<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Cs_Fixer Command
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Cs_Fixer extends Command
{

    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null)
    {
        $io = self::getIO($event);

        $vendor_path = dirname(dirname(dirname(BASEPATH)));
        $php_cs_fixer = sprintf('%s/bin/php-cs-fixer', $vendor_path);

        $cmd = sprintf('%s fix --config-file %s/.php_cs', $php_cs_fixer, dirname(APPPATH));

        $io->write(sprintf('<info>Runing %s</info>', $cmd));

        $rep = system($cmd);

        if ($rep) {
            $io->write('<info>Successfully fixed.</info>');
        }
    }
}
