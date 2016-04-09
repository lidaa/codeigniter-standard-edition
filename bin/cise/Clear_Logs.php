<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Description of Clear_Logs
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Clear_Logs extends Command {

    private static $log_path;

    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null) {
        $io = self::getIO($event);

        self::init($io);
        
        $io->write('Deleting logs files...');

        $dir_iterator = new \RecursiveDirectoryIterator(self::$log_path);
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

    /**
     * init
     */
    private static function init($io) {
        if (!file_exists($file_path = rtrim(APPPATH, '/') . '/config/config.php')) {
            $io->write('<error>The configuration file config.php does not exist.</error>');
        }

        include($file_path);

        if("" == $config['log_path']) {
            self::$log_path = sprintf('%s/logs', rtrim(APPPATH, '/'));
        } else {
            self::$log_path = $config['log_path'];
        }
    }
}
