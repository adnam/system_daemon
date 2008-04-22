#!/usr/bin/php -q
<?php
/**
 * System_Daemon turns PHP-CLI scripts into daemons.
 * 
 * PHP version 5
 *
 * @category  System
 * @package   System_Daemon
 * @author    Kevin <kevin@vanzonneveld.net>
 * @copyright 2008 Kevin van Zonneveld
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id$
 * @link      http://trac.plutonia.nl/projects/system_daemon
 */

/**
 * System_Daemon Example Code
 * 
 * If you run this code successfully, a daemon will be spawned
 * but unless have already generated the init.d script, you have
 * no real way of killing it yet.
 * 
 * In this case wait 3 runs, which is the maximum in this example. 
 * 
 * If you're inpatient you can also type:
 * killall -9 example.php
 * OR:
 * killall -9 php
 * 
 */

error_reporting(E_ALL);

// Arguments 
$runmode                = array();
$runmode["no-daemon"]   = false;
$runmode["help"]        = false;
$runmode["write-initd"] = false;
foreach ($argv as $k=>$arg) {
    if (substr($arg, 0, 2) == "--" && isset($runmode[substr($arg, 2)])) {
        $runmode[substr($arg, 2)] = true;
    }
}

// Help
if ($runmode["help"] == true) {
    echo "Usage: ".$argv[0]." [runmode]\n";
    echo "Available runmodes:\n"; 
    foreach ($runmode as $runmod=>$val) {
        echo " --".$runmod."\n";
    }
    die();
}
    
// Spawn Daemon 
if ($runmode["no-daemon"] == false) {
    // conditional so use include
    $path_to_daemon = "System/Daemon.php";
    
    if (!include $path_to_daemon) {
        die("Unable to locate System_Daemon class\n");
    }
    
    $daemon                 = new System_Daemon("mydaemon", true);
    $daemon->appDir         = dirname(__FILE__);
    $daemon->appDescription = "My 1st Daemon";
    $daemon->authorName     = "Kevin van Zonneveld";
    $daemon->authorEmail    = "kevin@vanzonneveld.net";
    $daemon->start();
    
    if (!$runmode["write-initd"]) {
        echo "Not writing an init.d script this time\n";
    } else {
        echo "Writing an init.d script: ";
        if (!$daemon->initdWrite()) {
            echo "failed!\n";
        } else {
            echo "OK\n";
        }
    }
}

// Run your code
$runningOkay = true;
$runCount = 1;
while (!$daemon->isDying && $runningOkay && $runCount <=3) {
    // do deamon stuff
    echo $daemon->appName." daemon is running in the background... (run ".$runCount." of 3)\n";
    print_r(get_declared_classes());
    $runningOkay = true;
    
    // relax the system by sleeping for a little bit
    sleep(2);
    $runCount++;
}

if ($runmode["no-daemon"] == false) {
    echo "Stopping ".$daemon->appName."\n";
    $daemon->stop();
}
?>