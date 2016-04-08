<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Description of Clear_Logs
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Clear_Logs extends Command {

    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null) {
        $io = self::getIO($event);

        $io->write('Deleting logs files...');

        $cache_path = sprintf('%s/logs', rtrim(APPPATH, '/'));
        $dir_iterator = new \RecursiveDirectoryIterator($cache_path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file) && !in_array($file->getFileName(), array('index.html', '.htaccess'))) {
                unlink($file);
            }

            if (is_dir($file) && !in_array($file->getFileName(), array('.', '..'))) {
                rmdir($file);
            }
        }

        $io->write('<info>Logs successfully deleted.</info>');
    }
}
