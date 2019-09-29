<?php
    /*
    * @version 0.1 (wizard)
    */
    global $session;
    if ($this->owner->name == 'panel')
    {
        $out['CONTROLPANEL'] = 1;
    }
    $qry = "1";
    // search filters
    // QUERY READY
    global $save_qry;
    if ($save_qry) {$qry = $session->data['terneo_thermostats_qry'];}
    else {$session->data['terneo_thermostats_qry'] = $qry;}

    if (!$qry) {$qry = "1";}

    //---сортировка
    global $sortby;

    if ($sortby) {$sortby_terneo_thermostats = $sortby;}
    else {$sortby_terneo_thermostats = "ID DESC";}

    $out['SORTBY'] = $sortby_terneo_thermostats;
    // SEARCH RESULTS
    $res = SQLSelect("SELECT * FROM terneo_thermostats WHERE $qry ORDER BY " . $sortby_terneo_thermostats);
    if ($res[0]['ID'])
    {
        //paging($res, 100, $out); // search result paging
        $total = count($res);
        for ($i = 0; $i < $total; $i++)
        {
            // some action for every record if required
            //$tmp = explode(' ', $res[$i]['UPDATED']);
            //$res[$i]['UPDATED'] = fromDBDate($tmp[0]) . " " . $tmp[1];
            $res[$i]['API_MODE'] = $this->config['API_MODE'];
        }
        $out['RESULT'] = $res;
    }
