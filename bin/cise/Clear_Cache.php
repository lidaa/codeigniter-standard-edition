<?php

use Composer\Script\Event;

/**
 * Description of Clear_Cache
 *
 * @author Adil Oukha <a.oukha@dm73.net>
 */
class Clear_Cache
{
    public static function run(Event $event = null) {
        if($event) {
            $io = $event->getIO();
        } else {
            $io = new Input_Output();
        }
        
        $io->writeln('Deleting cache files and log files...');
        
        $cachePath = sprintf('%s/app/cache', dirname(dirname(dirname(__FILE__))));
        $dir_iterator = new \RecursiveDirectoryIterator($cachePath);
        $iterator = new \RecursiveIteratorIterator($dir_iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $file) {
            if (is_file($file) && !in_array($file->getFileName(), array('index.html', '.htaccess'))) {
                unlink($file);
            }

            if (is_dir($file) && !in_array($file->getFileName(), array('.', '..'))) {
                rmdir($file);
            }
        }
        
        $io->writeln('<error>Cache successfully deleted.</comment>');
    }
}
