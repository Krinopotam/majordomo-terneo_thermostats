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
    setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);

    while (TRUE)
    {
        $keenetic_lan_devices_module->getConfig();
        $sleepTime = (int)$keenetic_lan_devices_module->config['UPDATE_PERIOD'];

        if ($sleepTime == 0) //'период обновления не указан, значит не обновляем, пока не будет указан
        {
            sleep(30);
            setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
            continue;
        }

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
