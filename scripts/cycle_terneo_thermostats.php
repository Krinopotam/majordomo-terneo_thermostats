<?php
    chdir(dirname(__FILE__) . '/../');
    include_once("./config.php");
    include_once("./lib/loader.php");
    include_once("./lib/threads.php");
    set_time_limit(0);

    // connecting to database
    $db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
    include_once("./load_settings.php");
    //include_once(DIR_MODULES . "control_modules/control_modules.class.php");
    //$ctl = new control_modules();
    include_once(DIR_MODULES . 'terneo_thermostats/terneo_thermostats.class.php');
    $terneo_thermostats_module = new terneo_thermostats();
    $terneo_thermostats_module->getConfig();
    $sleepTime = (int)$terneo_thermostats_module->config['UPDATE_PERIOD'];

    if ($sleepTime == 0)
    {
        setGlobal('cycle_terneo_thermostats', 'stop');
        setGlobal('cycle_terneo_thermostats', '0');
        exit;
    }

    setGlobal('cycle_terneo_thermostats', '1');

    while (TRUE)
    {
        setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);

        $terneo_thermostats_module->processCycle();

        if (file_exists('./reboot') || IsSet($_GET['onetime']))
        {
            $db->Disconnect();
            exit;
        }
        sleep($sleepTime);
    }
    DebMes("Unexpected close of cycle: " . basename(__FILE__));
