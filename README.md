# codeigniter-standard-edition

```
php composer.phar create-project lidaa/codeigniter-standard-edition PROJECT_NAME dev-master
```

## Commands:

php bin/cise/console "CMD_NAME" ( php bin/Cise/console Clear_Cache )

```
[.] Build_Params => Generate config files ( config.php, database.php, ...).
[.] Clear_Cache => Delete cache files.
[.] Clear_Logs => Delete logs files.
[.] Run_Sql => Execute a sql file.
[.] Cs_Fixer => Use php-cs-fixer tool with the configuration in a ".php_cs".
```

## Note

```
- In order for the log file to actually be written, the app/logs/ directory must be writable.
- Before the cache files can be written you must set the file permissions on your app/cache/ directory such that it is writable.
```
