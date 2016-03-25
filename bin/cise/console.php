<?php

require 'Input_Output.php';

$command = $argv[1]; 

require $command . '.php';

$command::run();