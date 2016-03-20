<?php

namespace CISE\Composer;

use Composer\Script\Event;

class ComposerScript {

    private static $app_template_path;
    private static $app_config_path;
    private static $db_template_path;
    private static $db_config_path;

    public static function run(Event $event) {
        self::init();

        $io = $event->getIO();

        $file = sprintf('%s/templates/params.ini', dirname(dirname(__FILE__)));
        $params = parse_ini_file($file, true);
        $userParams = array();

        $callback = function ($typeInput) {
            if (!$typeInput) {
                throw new \Exception('"subclass_prefix" should not be null');
            }

            return $typeInput;
        };

        $io->write('<info>Change App Config:</info>');
        foreach ($params['APP'] as $key => $value) {
            if ('subclass_prefix' == $key && !($value)) {
                $subclass_prefix = $io->askAndValidate('Type your ' . $key . ' (default: ' . $value . '): ', $callback, null, $value);
                self::render(array('subclass_prefix' => $subclass_prefix), self::$app_template_path, self::$app_template_path);
            } else {
                $userParams['APP'][$key] = $io->ask('Type your ' . $key . ' (default: ' . $value . '): ', $value);
            }
        }

        $io->write('<info>Change DataBase Config:</info>');
        foreach ($params['DB'] as $key => $value) {
            $userParams['DB'][$key] = $io->ask('Type your ' . $key . ' (default: ' . $value . '): ', $value);
        }

        self::renderTemplates($userParams);
        self::generateIniParams($userParams, $file);

        $io->write('<info>Your params were successfully saved.</info>');

        $io->write('<info>Deleting cache files and log files...</info>');

        self::clearCache();
        self::clearLogs();
        
        $io->write('<info>Cache and Logs successfully deleted.</info>');
    }

    private static function init() {
        $app_config_folder = sprintf('%s/app/config', dirname(dirname(dirname(__FILE__))));
        $templates_folder = sprintf('%s/templates/', dirname(dirname(__FILE__)));

        self::$app_template_path = sprintf('%s/config.php', $templates_folder);
        self::$app_config_path = sprintf('%s/config.php', $app_config_folder);

        self::$db_template_path = sprintf('%s/database.php', $templates_folder);
        self::$db_config_path = sprintf('%s/database.php', $app_config_folder);
    }

    private static function renderTemplates($params) {
        self::render($params['APP'], self::$app_template_path, self::$app_config_path);
        self::render($params['DB'], self::$db_template_path, self::$db_config_path);
    }

    private static function render($params, $template_path, $destination_file) {
        $content = file_get_contents($template_path);
        foreach ($params as $key => $value) {
            $content = str_replace("%$key%", $value, $content);
        }

        file_put_contents($destination_file, $content);
    }

    private static function generateIniParams($array, $file) {
        $res = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval)
                    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
            } else
                $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
        }

        self::safeFileRewrite($file, implode("\r\n", $res));
    }

    private static function safeFileRewrite($fileName, $dataToSave) {
        if ($fp = fopen($fileName, 'w')) {
            $startTime = microtime(TRUE);
            do {
                $canWrite = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$canWrite)
                    usleep(round(rand(0, 100) * 1000));
            } while ((!$canWrite)and ( (microtime(TRUE) - $startTime) < 5));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $dataToSave);
                flock($fp, LOCK_UN);
            }

            fclose($fp);
        }
    }

    private static function clearCache() {
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
    }

    private static function clearLogs() {
        $cachePath = sprintf('%s/app/logs', dirname(dirname(dirname(__FILE__))));
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
    }

}
