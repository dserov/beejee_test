<?php
require_once 'vendor/autoload.php';

spl_autoload_register("gbStandardAutoload");

function gbStandardAutoload($className)
{
    $dirs = [
            'controller',
            'lib',
            'lib/smarty',
        'model'
    ];
    $found = false;
    foreach ($dirs as $dir) {
        $fileName = __DIR__ . '/' . $dir . '/' . $className . '.class.php';
        if (is_file($fileName)) {

            require_once($fileName);
            $found = true;
        }
    }
	//$obj = new A;
		
    if (!$found) {
        throw new Exception('Unable to load ' . $className);
}
    return true;
}


//$object = new Test;

