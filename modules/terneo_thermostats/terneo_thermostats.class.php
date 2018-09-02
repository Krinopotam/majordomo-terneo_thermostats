<?php
    /**
     * Термостаты Terneo
     * @package project
     * @author Wizard <sergejey@gmail.com>
     * @copyright http://majordomo.smartliving.ru/ (c)
     * @version 0.1 (wizard, 16:09:19 [Sep 01, 2018])
     */
    //
    //
    class terneo_thermostats extends module
    {
        /**
         * terneo_thermostats
         *
         * Module class constructor
         *
         * @access private
         */
        function __construct()
        {
            $this->name = "terneo_thermostats";
            $this->title = "Термостаты Terneo";
            $this->module_category = "<#LANG_SECTION_DEVICES#>";
            $this->checkInstalled();
        }

        /**
         * saveParams
         *
         * Saving module parameters
         *
         * @access public
         */
        function saveParams($data = 1)
        {
            $p = array();
            if (IsSet($this->id))
            {
                $p["id"] = $this->id;
            }
            if (IsSet($this->view_mode))
            {
                $p["view_mode"] = $this->view_mode;
            }
            if (IsSet($this->edit_mode))
            {
                $p["edit_mode"] = $this->edit_mode;
            }
            if (IsSet($this->data_source))
            {
                $p["data_source"] = $this->data_source;
            }
            if (IsSet($this->tab))
            {
                $p["tab"] = $this->tab;
            }

            return parent::saveParams($p);
        }

        /**
         * getParams
         *
         * Getting module parameters from query string
         *
         * @access public
         */
        function getParams()
        {
            global $id;
            global $mode;
            global $view_mode;
            global $edit_mode;
            global $data_source;
            global $tab;
            if (isset($id))
            {
                $this->id = $id;
            }
            if (isset($mode))
            {
                $this->mode = $mode;
            }
            if (isset($view_mode))
            {
                $this->view_mode = $view_mode;
            }
            if (isset($edit_mode))
            {
                $this->edit_mode = $edit_mode;
            }
            if (isset($data_source))
            {
                $this->data_source = $data_source;
            }
            if (isset($tab))
            {
                $this->tab = $tab;
            }
        }

        /**
         * Run
         *
         * Description
         *
         * @access public
         */
        function run()
        {
            global $session;
            $out = array();
            if ($this->action == 'admin')
            {
                $this->admin($out);
            }
            else
            {
                $this->usual($out);
            }
            if (IsSet($this->owner->action))
            {
                $out['PARENT_ACTION'] = $this->owner->action;
            }
            if (IsSet($this->owner->name))
            {
                $out['PARENT_NAME'] = $this->owner->name;
            }
            $out['VIEW_MODE'] = $this->view_mode;
            $out['EDIT_MODE'] = $this->edit_mode;
            $out['MODE'] = $this->mode;
            $out['ACTION'] = $this->action;
            $out['DATA_SOURCE'] = $this->data_source;
            $out['TAB'] = $this->tab;
            $this->data = $out;
            $p = new parser(DIR_TEMPLATES . $this->name . "/" . $this->name . ".html", $this->data, $this);
            $this->result = $p->result;
        }

        /**
         * BackEnd
         *
         * Module backend
         *
         * @access public
         */
        function admin(&$out)
        {
            $this->getConfig();
            $out['API_URL'] = $this->config['API_URL'];
            if (!$out['API_URL'])
            {
                $out['API_URL'] = 'http://';
            }
            $out['API_KEY'] = $this->config['API_KEY'];
            $out['API_USERNAME'] = $this->config['API_USERNAME'];
            $out['API_PASSWORD'] = $this->config['API_PASSWORD'];
            if ($this->view_mode == 'update_settings')
            {
                global $api_url;
                $this->config['API_URL'] = $api_url;
                global $api_key;
                $this->config['API_KEY'] = $api_key;
                global $api_username;
                $this->config['API_USERNAME'] = $api_username;
                global $api_password;
                $this->config['API_PASSWORD'] = $api_password;
                $this->saveConfig();
                $this->redirect("?");
            }
            if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source'])
            {
                $out['SET_DATASOURCE'] = 1;
            }
            if ($this->data_source == 'terneo_thermostats' || $this->data_source == '')
            {
                if ($this->view_mode == '' || $this->view_mode == 'search_terneo_thermostats')
                {
                    $this->search_terneo_thermostats($out);
                }
                if ($this->view_mode == 'update_terneo_thermostats')
                {
                    $this->update_terneo_thermostats($out);
                }
                if ($this->view_mode == 'edit_terneo_thermostats')
                {
                    $this->edit_terneo_thermostats($out, $this->id);
                }
                if ($this->view_mode == 'delete_terneo_thermostats')
                {
                    $this->delete_terneo_thermostats($this->id);
                    $this->redirect("?data_source=terneo_thermostats");
                }
            }
            if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source'])
            {
                $out['SET_DATASOURCE'] = 1;
            }
            if ($this->data_source == 'terneo_thermostat_values')
            {
                if ($this->view_mode == '' || $this->view_mode == 'search_terneo_thermostat_values')
                {
                    $this->search_terneo_thermostat_values($out);
                }
                if ($this->view_mode == 'edit_terneo_thermostat_values')
                {
                    $this->edit_terneo_thermostat_values($out, $this->id);
                }
            }
        }

        /**
         * FrontEnd
         *
         * Module frontend
         *
         * @access public
         */
        function usual(&$out)
        {
            $this->admin($out);
        }

        /**
         * terneo_thermostats search
         *
         * @access public
         */
        function search_terneo_thermostats(&$out)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostats_search.inc.php');
        }


        /**
         * terneo_thermostats update
         *
         * @access public
         */
        function update_terneo_thermostats(&$out)
        {
            $this->loadDevicesStatus();
            $this->redirect("?data_source=terneo_thermostats");
        }

        /**
         * terneo_thermostats edit/add
         *
         * @access public
         */
        function edit_terneo_thermostats(&$out, $id)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostats_edit.inc.php');
        }

        /**
         * terneo_thermostats delete record
         *
         * @access public
         */
        function delete_terneo_thermostats($id)
        {
            $rec = SQLSelectOne("SELECT * FROM terneo_thermostats WHERE ID='$id'");
            // some action for related tables
            SQLExec("DELETE FROM terneo_thermostats WHERE ID='" . $rec['ID'] . "'");
        }

        /**
         * terneo_thermostat_values search
         *
         * @access public
         */
        function search_terneo_thermostat_values(&$out)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostat_values_search.inc.php');
        }

        /**
         * terneo_thermostat_values edit/add
         *
         * @access public
         */
        function edit_terneo_thermostat_values(&$out, $id)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostat_values_edit.inc.php');
        }

        function propertySetHandle($object, $property, $value)
        {
            $this->getConfig();
            $table = 'terneo_thermostat_values';
            $properties = SQLSelect("SELECT * FROM $table WHERE LINKED_OBJECT LIKE '" . DBSafe($object) . "' AND LINKED_PROPERTY LIKE '" . DBSafe($property) . "'");
            $total = count($properties);
            if ($total)
            {
                for ($i = 0; $i < $total; $i++)
                {
                    $isUpdated=false;
                    $confirmedValue = 0;

                    if ($properties[$i]['TITLE'] == 'powerOff')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPut("/device/" . $properties[$i]['DEVICE_SID'] . "/parameters/", '[{"key": "powerOff", "value": ' . ($value==1 ? "true" : "false") . '}]');

                        if (is_array($result) && isset($result[0]) && $result[0]['key'] == "powerOff")
                        {
                            $confirmedValue = ($result[0]['value']=="False" ? 0 : 1);
                            $isUpdated = TRUE;
                        }
                    }
                    elseif ($properties[$i]['TITLE'] == 'mode')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPut("/device/" . $properties[$i]['DEVICE_SID'] . "/parameters/", '[{"key": "mode", "value": ' . $value . '}]');

                        if (is_array($result) && isset($result[0]) && $result[0]['key'] == "mode")
                        {
                            $confirmedValue = $result[0]['value'];
                            $isUpdated = TRUE;
                        }
                    }
                    elseif ($properties[$i]['TITLE'] == 'temp_setpoint')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPut("/device/" . $properties[$i]['DEVICE_SID'] . "/setpoint/", '{"value": ' . $value . '}');

                        if (is_array($result) && isset($result['value']))
                        {
                            $confirmedValue = $result['value'];
                            $isUpdated = TRUE;
                        }
                    }

                    if ($isUpdated)
                    {
                        SQLExec("UPDATE  $table SET VALUE='" . DBSafe($confirmedValue) . "', UPDATED='" . date('Y-m-d H:i:s') . "' WHERE ID=" . (int)$properties[$i]['ID']);
                    }
                }
            }
        }

        function processCycle()
        {
            $this->getConfig();

            $this->loadDevicesStatus();

            DebMes("Цикл terneo_termostats отработал.");
            //$this->debug ($equipments);
            //DebMes($this->config);
        }

        /**
         * Install
         *
         * Module installation routine
         *
         * @access private
         */
        function install($data = '')
        {
            parent::install();
        }

        //*************************** My Functions **************************
        /**
         * Получает токен для подключения к API Terneo
         * @return string имя токена
         */
        private function  getToken()
        {
            if (!isset($this->config['API_USERNAME']) or $this->config['API_USERNAME'] == "" or !isset($this->config['API_PASSWORD']) or $this->config['API_PASSWORD'] == "" or !isset($this->config['API_URL']) or $this->config['API_URL'] == "")
            {
                return "";
            }

            $data = array('email' => $this->config['API_USERNAME'], 'password' => $this->config['API_PASSWORD']);

            // use key 'http' even if you send the request to https://...
            $options = array
            (
                'http' => array
                (
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($this->config['API_URL'] . "/login/", FALSE, $context);
            if (!$result)
            {
                $this->config['API_KEY'] = "Error";
                $this->saveConfig();

                return "";
            }

            $jsonResult = $this->json_array($result);

            if (array_key_exists('access_token', $jsonResult))
            {
                $this->config['API_KEY'] = $jsonResult['access_token'];
                $this->saveConfig();

                return $jsonResult['access_token'];
            }

            return "";
        }

        /**
         * Возвращает информацию о всех термостатах
         * @return mixed|null
         */
        private function getAllDevicesInfo()
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGet("/device/");

            return $result;
        }

        /**
         * Возвращает более о конкретном термостате
         * @param int $id идентификатор термостата (id)
         * @return mixed|null
         */
        private function getDeviceInfo($id)
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGet("/device/" . $id . "/");

            return $result;
        }

        /**
         * Возвращает дополнительные параметры конкретного термостата (зачем то они сделаны отдельно)
         * @param int $id
         * @return mixed|null
         */
        private function getDeviceParameters($id)
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGet("/device/" . $id . "/parameters/");

            return $result;
        }

        /**
         * Загружает и обновляет значения термостатов
         */
        function loadDevicesStatus()
        {
            $equipments =  $this->getAllDevicesInfo();

            if (!is_array($equipments) or !isset($equipments['results']))
            {
                return;
            }

            foreach ($equipments['results'] as $key => $value)
            {
                $req_qry = " SN = '" . DBSafe($value['sn']) . "'";

                $rec = SQLSelectOne("SELECT * FROM terneo_thermostats WHERE $req_qry");

                $rec['DEVICE_NAME'] = $value['name'];
                $rec['FIRMWARE'] = $value['firmware'];

                if (!$rec['ID'])
                {
                    $rec = array();
                    $rec['TITLE'] = $value['name'];
                    $rec['SID'] = $value['id'];
                    $rec['SN'] = $value['sn'];
                    $rec['ID'] = SQLInsert('terneo_thermostats', $rec);
                }
                else
                {
                    $rec['UPDATED'] = date('Y-m-d H:i:s');
                    SQLUpdate('terneo_thermostats', $rec);
                }

                if (!$rec['ID'])
                {
                    return;
                }

                $this->loadValues($rec['ID'], $rec['SID']);
            }
        }

        /**
         * Обновление значений устройства
         * @param int $deviceId устройства в БД
         * @param int $sid системный ID устройства на сайте Terneo
         */
        function loadValues($deviceId, $sid)
        {
            $rec_vals=array();
            $rec_val=array();

            $deviceParameters =  $this->getDeviceParameters($sid);

            if (is_array($deviceParameters))
            {
                foreach ($deviceParameters as $parameterKey => $parameterValue)
                {
                    if ($parameterValue['key'] == "mode")
                    {
                        $rec_val['DEVICE_ID'] = $deviceId;
                        $rec_val['DEVICE_SID'] = $sid;
                        $rec_val['TITLE'] = "mode";
                        $rec_val['DESCRIPTION'] = "(R/W) Режим (0 - по расписанию, 1 - ручной)";
                        $rec_val['VALUE'] = $parameterValue['value'];
                        $rec_vals[] = $rec_val;
                    }

                    elseif ($parameterValue['key'] == "powerOff")
                    {
                        $rec_val['DEVICE_ID'] = $deviceId;
                        $rec_val['DEVICE_SID'] = $sid;
                        $rec_val['TITLE'] = "powerOff";
                        $rec_val['DESCRIPTION'] = "(R/W) Выключение (0 - вкл, 1 - выкл.)";
                        $rec_val['VALUE'] = ($parameterValue['value'] ? 1 : 0);
                        $rec_vals[] = $rec_val;
                    }
                }
            }

            $device =  $this->getDeviceInfo($sid);

            if (is_array($device) && isset($device['data']))
            {
                if (is_array($device['data']) and isset($device['data']['temp_setpoint']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_setpoint";
                    $rec_val['DESCRIPTION'] = " (R/W) Установленная температура";
                    $rec_val['VALUE'] = $device['data']['temp_setpoint'];
                    $rec_vals[] = $rec_val;
                }
                
                if (is_array($device['data']) and isset($device['data']['temp_current']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_current";
                    $rec_val['DESCRIPTION'] = " (R/O) Текущая температура";
                    $rec_val['VALUE'] = $device['data']['temp_current'];
                    $rec_vals[] = $rec_val;
                }

                 if (is_array($device['data']) and isset($device['data']['setpoint_state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "setpoint_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Установленная температура достигнута";
                    $rec_val['VALUE'] = ($device['data']['setpoint_state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['temperature_limits']) and isset($device['data']['temperature_limits']['max_value']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temperature_max_limit";
                    $rec_val['DESCRIPTION'] = "(R/O) MAX лимит температуры";
                    $rec_val['VALUE'] = $device['data']['temperature_limits']['max_value'];
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['temperature_limits']) and isset($device['data']['temperature_limits']['min_value']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temperature_min_limit";
                    $rec_val['DESCRIPTION'] = "(R/O) MIN лимит температуры";
                    $rec_val['VALUE'] = $device['data']['temperature_limits']['min_value'];
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['temp_mode_type']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_mode_type";
                    $rec_val['DESCRIPTION'] = "(R/O) Режим температуры";
                    $rec_val['VALUE'] = $device['data']['temp_mode_type'];
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['overheat_state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "overheat_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Статус перегрева";
                    $rec_val['VALUE'] = ($device['data']['overheat_state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['is_online']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "is_online";
                    $rec_val['DESCRIPTION'] = "(R/O) Онлайн";
                    $rec_val['VALUE'] = ($device['data']['is_online'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['connection_state']) and isset($device['data']['connection_state']['state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "connection_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Статус подключения";
                    $rec_val['VALUE'] = ($device['data']['connection_state']['state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['connection_state']) and isset($device['data']['connection_state']['offline_minutes']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "offline_minutes";
                    $rec_val['DESCRIPTION'] = "(R/O) Минут offline";
                    $rec_val['VALUE'] = $device['data']['connection_state']['offline_minutes'];
                    $rec_vals[] = $rec_val;
                }

                if (is_array($device['data']) and isset($device['data']['connection_state']) and isset($device['data']['connection_state']['last_connection_time']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "last_connection_time";
                    $rec_val['DESCRIPTION'] = "(R/O) Время последнего подключения";
                    $rec_val['VALUE'] = $device['data']['connection_state']['last_connection_time'];
                    $rec_vals[] = $rec_val;
                }
            }

            foreach ($rec_vals as $rec_val)
            {
                $this->processValues($deviceId, $rec_val);
            }
        }

        /**
         * Сохраняет новые значения и вызывает привязанные события или свойства
         * @param $device_id
         * @param $rec_val
         * @param int $params
         */
        function processValues($device_id, $rec_val, $params = 0) {

            $old_rec = SQLSelectOne("SELECT * FROM terneo_thermostat_values WHERE DEVICE_ID=".(int)$device_id." AND TITLE LIKE '".DBSafe($rec_val['TITLE'])."'");

            $old_value = "";

            if (!$old_rec['ID'])
            {
                $rec_val['ID'] = SQLInsert('terneo_thermostat_values', $rec_val);
            }
            else
            {
                $old_value = $old_rec['VALUE'];

                $rec_val['ID'] = $old_rec['ID'];
                $rec_val['DEVICE_ID'] = $old_rec['DEVICE_ID'];
                $rec_val['LINKED_OBJECT'] = $old_rec['LINKED_OBJECT'];
                $rec_val['LINKED_PROPERTY'] = $old_rec['LINKED_PROPERTY'];
                $rec_val['LINKED_METHOD'] = $old_rec['LINKED_METHOD'];
                $rec_val['UPDATED'] = date('Y-m-d H:i:s');
                SQLUpdate('terneo_thermostat_values', $rec_val);
            }

            //если вдруг связанное свойство пустое (только привязали), то сразу его обновляем текущим значением
            if ($rec_val['LINKED_OBJECT'] && $rec_val['LINKED_PROPERTY'])
            {
                $linkedValue = getGlobal($rec_val['LINKED_OBJECT'] . '.' . $rec_val['LINKED_PROPERTY']);

                $this->debug ("$linkedValue".$linkedValue);
                if ($linkedValue!=$rec_val['VALUE'])
                {
                    setGlobal($rec_val['LINKED_OBJECT'] . '.' . $rec_val['LINKED_PROPERTY'], $rec_val['VALUE'], array($this->name => '0'));
                }
            }

            // Если значение метрики не изменилось, то выходим.
            if ($old_value == $rec_val['VALUE']) return;

            // Иначе обновляем привязанное свойство.
            if ($rec_val['LINKED_OBJECT'] && $rec_val['LINKED_PROPERTY']) {
                setGlobal($rec_val['LINKED_OBJECT'] . '.' . $rec_val['LINKED_PROPERTY'], $rec_val['VALUE'], array($this->name => '0'));
            }

            // И вызываем привязанный метод.
            if ($rec_val['LINKED_OBJECT'] && $rec_val['LINKED_METHOD']) {
                if (!is_array($params)) {
                    $params = array();
                }
                $params['VALUE'] = $rec_val['VALUE'];
                $params['OLD_VALUE'] = $old_value;
                $params['NEW_VALUE'] = $rec_val['VALUE'];

                callMethodSafe($rec_val['LINKED_OBJECT'] . '.' . $rec_val['LINKED_METHOD'], $params);
            }
        }

        /**
         * @param string $apiPath путь в Api
         * @param array $data [optional] дополнительные данные для отправки
         * @return mixed|null
         */
        private function ApiGet($apiPath, array $data = NULL)
        {
            if ($data==NULL)
            {
                $data = array();
            }

            // use key 'http' even if you send the request to https://...
            $options = array
            (
                'http' => array
                (
                    'header'  => "Authorization: " . "Token " . $this->config['API_KEY'],
                    'method'  => 'GET',
                    'content' => http_build_query($data)
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($this->config['API_URL'] . $apiPath, FALSE, $context);

            if (!$result)
            {
                return NULL;
            }

            return $this->json_array($result);
        }

        /**
         * @param string $apiPath путь в Api
         * @param array $data [optional] дополнительные данные для отправки
         * @return mixed|null
         */
        private function ApiPut($apiPath, $data)
        {
            // use key 'http' even if you send the request to https://...
            $options = array
            (
                'http' => array
                (
                    'header'  => "Authorization: " . "Token " . $this->config['API_KEY'] . "\r\nContent-Type: application/json",
                    'method'  => 'PUT',
                    'content' => $data
                )
            );

            $context = stream_context_create($options);
            $result = file_get_contents($this->config['API_URL'] . $apiPath, FALSE, $context);

            if (!$result)
            {
                return NULL;
            }

            return $this->json_array($result);
        }

        /**
         * @param string $str  JSON строка в текстовом виде
         * @return mixed массив, преобразованный из JSON строки
         */
        function json_array($str)
        {
            $result = json_decode($str, TRUE);

            return $result;
        }

        /**
         * Выводит в log файл объект (print_r)
         * @param mixed $content
         */
        function debug($content)
        {
            $this->log(print_r($content, TRUE));
        }

        /**
         * Выводит в log файл строку
         * @param string $message
         */
        function log($message)
        {
            //echo $message . "\n";
            // DEBUG MESSAGE LOG
            if (!is_dir(ROOT . 'cms/debmes'))
            {
                mkdir(ROOT . 'cms/debmes', 0777);
            }

            $today_file = ROOT . 'cms/debmes/log_' . date('Y-m-d') . '-terneo_thermostats.php.txt';
            $data = date("H:i:s") . " " . $message . "\n";
            file_put_contents($today_file, $data, FILE_APPEND | LOCK_EX);
        }
        //*******************************************************************

        /**
         * Uninstall
         *
         * Module uninstall routine
         *
         * @access public
         */
        function uninstall()
        {
            SQLExec('DROP TABLE IF EXISTS terneo_thermostats');
            SQLExec('DROP TABLE IF EXISTS terneo_thermostat_values');
            parent::uninstall();
        }

        /**
         * dbInstall
         *
         * Database installation routine
         *
         * @access private
         */
        function dbInstall($data)
        {
            /*
            terneo_thermostats -
            terneo_thermostat_values -
            */
            $data = <<<EOD
 terneo_thermostats: ID int(10) unsigned NOT NULL auto_increment
 terneo_thermostats: TITLE varchar(100) NOT NULL DEFAULT ''
 terneo_thermostats: SID varchar(255) NOT NULL DEFAULT ''
 terneo_thermostats: SN varchar(255) NOT NULL DEFAULT ''
 terneo_thermostats: DEVICE_NAME varchar(255) NOT NULL DEFAULT ''
 terneo_thermostats: FIRMWARE varchar(255) NOT NULL DEFAULT ''
 terneo_thermostats: UPDATED datetime
 terneo_thermostat_values: ID int(10) unsigned NOT NULL auto_increment
 terneo_thermostat_values: TITLE varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: DESCRIPTION varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: VALUE varchar(255) NOT NULL DEFAULT ''
 terneo_thermostat_values: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 terneo_thermostat_values: DEVICE_SID varchar(10) NOT NULL DEFAULT ''
 terneo_thermostat_values: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: UPDATED datetime
EOD;
            parent::dbInstall($data);
        }
        // --------------------------------------------------------------------
    }
    /*
    *
    * TW9kdWxlIGNyZWF0ZWQgU2VwIDAxLCAyMDE4IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
    *
    */
