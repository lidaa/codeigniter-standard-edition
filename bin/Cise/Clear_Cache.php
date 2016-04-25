<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Clear_Cache
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Clear_Cache extends Command
{

    private static $cache_path;
    
    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null)
    {
        $io = self::getIO($event);

        self::init($io);

        $io->write('Deleting cache files...');
        
        $dir_iterator = new \RecursiveDirectoryIterator(self::$cache_path);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file) && !in_array($file->getFileName(), array('index.html', '.htaccess'))) {
                unlink($file);
            }

            if (is_dir($file) && !in_array($file->getFileName(), array('.', '..'))) {
                rmdir($file);
            }
        }

        $io->write('<info>Cache successfully deleted.</info>');
    }

    /**
     * init
     * 
     * @param type $io
     */
    private static function init($io)
    {
        if (!file_exists($file_path = rtrim(APPPATH, '/') . '/config/config.php')) {
            $io->write('<error>The configuration file config.php does not exist.</error>');
        }

        include($file_path);

        if ("" == $config['cache_path']) {
            self::$cache_path = sprintf('%s/cache', rtrim(APPPATH, '/'));
        } else {
            self::$cache_path = $config['cache_path'];
        }
    }
}
