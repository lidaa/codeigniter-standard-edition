<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Description of Buil_Params
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Build_Params extends Command {
    
    private static $templates_path;
    private static $params;

    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null) {
        $io = self::getIO($event);

        self::init();        
        
        $user_params = array();
        foreach (self::$params as $key => $value) {
            $template_path = sprintf('%s/%s.php', self::$templates_path, $key);
            $destination_file = sprintf('%s/config/%s.php', rtrim(APPPATH, '/'), $key);
            
            $io->write(sprintf('<info>Generate "%s.php" from the template %s :</info>', $key, $template_path));
            foreach ($value as $sub_key => $sub_value) {
                $response = $io->ask('<question>Type your ' . $sub_key . ' </question> (default: "' . $sub_value . '"): ', $sub_value);                
                $user_params[$key][$sub_key] = ($response === '""') ? "" : $response;
            }

            self::render($user_params[$key], $template_path, $destination_file, $io);
        }

        self::save_default_params($user_params, $io);
        
        $io->write('<comment>You can add/edit templates files and change their params in params.ini</comment>');
    }
    
    /**
     * init
     */
    private static function init() {
        self::$templates_path = sprintf('%s/templates', dirname(dirname(__FILE__)));
        
        $file = sprintf('%s/default.params.ini', self::$templates_path);
        
        if(!file_exists($file)) {
            $file = sprintf('%s/params.ini', self::$templates_path);            
        }
        
        self::$params = parse_ini_file($file, true);
    }

    /**
     * render
     * 
     * @param type $params
     * @param type $template_path
     * @param type $destination_file
     * @param type $io
     */
    private static function render($params, $template_path, $destination_file, $io) {
        $content = file_get_contents($template_path);
        foreach ($params as $key => $value) {
            $content = str_replace("%$key%", $value, $content);
        }

        file_put_contents($destination_file, $content);
        
        self::git_ignore($destination_file, $io);
    }
    
    /**
     * save_default_params
     * 
     * @param type $user_params
     * @param type $io
     */
    private static function save_default_params($user_params, $io) {
        $file = sprintf('%s/default.params.ini', self::$templates_path);
        $res = array();
        foreach ($user_params as $key => $val) {
            if (is_array($val)) {
                $res[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
                }
            } else {
                $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
            }
        }

        self::safe_file_rewrite($file, implode("\r\n", $res));
        
        self::git_ignore($file, $io);
    }

    /**
     * safe_file_rewrite
     * 
     * @param type $file_name
     * @param type $data_to_save
     */
    private static function safe_file_rewrite($file_name, $data_to_save) {
        if ($fp = fopen($file_name, 'w')) {
            $start_Time = microtime(TRUE);
            do {
                $can_write = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                if (!$can_write) {
                    usleep(round(rand(0, 100) * 1000));
                }
            } while ((!$can_write)and ( (microtime(TRUE) - $start_Time) < 5));

            //file was locked so now we can store information
            if ($can_write) {
                fwrite($fp, $data_to_save);
                flock($fp, LOCK_UN);
            }

            fclose($fp);
        }
    }
    
    /**
     * git_ignore
     * 
     * @param type $file_path
     * @param type $io
     */
    private static function git_ignore($file_path, $io) {
        $gitignore_file = sprintf('%s/.gitignore', dirname(dirname(dirname(__FILE__))));
        $prefix = dirname(dirname(dirname(__FILE__)));
        $file_relative_path = $file_path;
        
        if (substr($file_relative_path, 0, strlen($prefix)) == $prefix) {
            $file_relative_path = substr($file_relative_path, strlen($prefix));
        }

        if((strpos(file_get_contents($gitignore_file), $file_relative_path) === false) && (strpos(file_get_contents($gitignore_file), trim($file_relative_path, '/')) === false)) {
            file_put_contents($gitignore_file, PHP_EOL . $file_relative_path, FILE_APPEND);
            
            $io->write(sprintf('The file "%s" has been added to the gitignore.', $file_relative_path));
        }
    }
}
