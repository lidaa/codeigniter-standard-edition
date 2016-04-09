# codeigniter-standard-edition

```
php composer.phar create-project lidaa/codeigniter-standard-edition PROJECT_NAME dev-master
```

## Commands:

php bin/cise/console "CMD_NAME" ( php bin/cise/console Clear_Cache )

```
[.] Build_Params => generate config files ( config.php, database.php, ...)
[.] Clear_Cache => delete cache files
[.] Clear_Logs => delete logs files
[.] Run_Sql => execute a sql file
```

## Note

```
- In order for the log file to actually be written, the app/logs/ directory must be writable.
- Before the cache files can be written you must set the file permissions on your app/cache/ directory such that it is writable.
```
