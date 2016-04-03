<?php

namespace Cise;

use Composer\Script\Event;

/**
 * Description of Run_Sql
 *
 * @author Lidaa <aa_dil@hotmail.fr>
 */
class Run_Sql extends Command {

    /**
     * run
     * 
     * @param Event $event
     */
    public static function run(Event $event = null) {
        $io = self::getIO($event);

        // Path to the system folder
        define('BASEPATH', str_replace('\\', '/', './vendor/codeigniter/framework/system'));

        $base_path = dirname(dirname(dirname(__FILE__)));

        define('ENVIRONMENT', 'development');
        define('APPPATH', sprintf('%s/app/', $base_path));

        // Is the config file in the environment folder?
        if (!file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php') && !file_exists($file_path = APPPATH . 'config/database.php')) {
            $io->write('<error>The configuration file database.php does not exist.</error>');
        }

        include($file_path);

        if (!isset($active_group)) {
            $io->write('<error>You have not specified a database connection group via $active_group in your config/database.php file.</error>');
        }
        
        $hostname = $db[$active_group]['hostname'];
        $username = $db[$active_group]['username'];
        $password = $db[$active_group]['password'];
        $database = $db[$active_group]['database'];

        try {
            mysqli_report(MYSQLI_REPORT_STRICT);
            $mysqli = new \mysqli($hostname, $username, $password);
        
            $io->write(sprintf('<comment>Connected to %s:%s.</comment> ', $hostname, $database));
        
            $sql_script_path = $io->ask('<question>Enter your SQl script path :</question> ', null);
            
            self::execute_script($mysqli,  $db[$active_group], $sql_script_path);
                        
            $io->write('<info>The script was executed successfully.</info>');            
        } catch (\Exception $e) {
            $io->write('<error>' . $e->getMessage() . '</error>');
        }
    }
    
    /**
     * execute_script
     * 
     * @param type $mysqli
     * @param type $param
     * @param type $sql_script_path
     * @throws \Exception
     */
    private static function execute_script($mysqli, $param, $sql_script_path) {
        self::create_db($mysqli, $param);
                    
        if(!file_exists($sql_script_path)) {
            throw new \Exception(sprintf('"%s" does not exist.', $sql_script_path));
        }

        if('sql' !== strtolower(pathinfo($sql_script_path, PATHINFO_EXTENSION))) {
            throw new \Exception('Only sql file are allowed.');
        }
        
        $queries = file_get_contents($sql_script_path);
        
        $mysqli->query('USE ' . $param['database']);
        
        $mysqli->multi_query($queries);
        
        $mysqli->close();
    }
    
    /**
     * create_db
     * 
     * @param type $mysqli
     * @param type $db_params
     * @throws \Exception
     */
    private static function create_db($mysqli, $db_params) {
        if (isset($db_params['database'])) {
            $sql = sprintf('CREATE DATABASE IF NOT EXISTS %s', $db_params['database']);

            $sql .= (isset($db_params['char_set']) ? sprintf(' CHARACTER SET %s', $db_params['char_set']) : '');
            $sql .= (isset($db_params['dbcollat']) ? sprintf(' COLLATE %s', $db_params['dbcollat']) : '');
            
            $mysqli->query($sql);
        } else {
            throw new \Exception('You have not specified a database name in your config/database.php file.');
        }
    }


}
