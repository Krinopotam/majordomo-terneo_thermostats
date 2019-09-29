<?php
    /**
     * Термостаты Terneo
     * @package project
     * @author Krinopotam <omegatester@gmail.com>
     * @copyright http://majordomo.smartliving.ru/ (c)
     * @version 0.2 (wizard, 16:09:19 [Sep 01, 2018])
     *
     * @property string|int|null id
     * @property string|int|null mode
     * @property  string|int|null edit_mode
     * @property  string|int|null view_mode
     * @property  array|string|int|null data_source
     * @property  array|string|int|null tab
     * @property  array|string|int|null action
     */
    //
    //
    class terneo_thermostats extends module
    {
        const API_URL = "https://api.hmarex.com";
        const API_MODE_CLOUD = 0;
        const API_MODE_LOCAL = 1;

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
         * @param int $data
         * @return string|void
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
         * @param $out
         */
        function admin(&$out)
        {
            $this->getConfig();
            $out['API_URL'] = self::API_URL;
            if (!$out['API_URL'])
            {
                $out['API_URL'] = 'http://';
            }

            $out['API_MODE'] = $this->config['API_MODE'];
            $out['API_KEY'] = $this->config['API_KEY'];
            $out['API_USERNAME'] = $this->config['API_USERNAME'];
            $out['API_PASSWORD'] = $this->config['API_PASSWORD'];
            $out['UPDATE_PERIOD'] = $this->config['UPDATE_PERIOD'];
            $out['API_LOG_DEBMES'] = $this->config['API_LOG_DEBMES'];
            //if (!$out['UPDATE_PERIOD']) { $out['UPDATE_PERIOD'] = '60'; }

            if ($this->view_mode == 'update_settings')
            {
                global $api_mode;
                $this->config['API_MODE'] = $api_mode;
                global $api_url;
                $this->config['API_URL'] = self::API_URL;
                global $api_key;
                $this->config['API_KEY'] = $api_key;
                global $api_username;
                $this->config['API_USERNAME'] = $api_username;
                global $api_password;
                $this->config['API_PASSWORD'] = $api_password;
                global $update_period;
                $this->config['UPDATE_PERIOD'] = $update_period;
                global $api_log_debmes;
                $this->config['API_LOG_DEBMES'] = $api_log_debmes;

                $this->saveConfig();
                setGlobal('cycle_terneo_thermostats', 'restart');
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
                if ($this->view_mode == 'discover_local_thermostats')
                {
                    $this->discover_local_thermostats($out);
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
         * @param $out
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
         * @param $out
         */
        function update_terneo_thermostats(&$out)
        {
            $this->getConfig();
            if ($this->config['API_MODE']==self::API_MODE_CLOUD) {$this->updateDevicesStatusFromCloud();}
            elseif ($this->config['API_MODE']==self::API_MODE_LOCAL) {$this->updateDevicesStatusFromLocal();}
            $this->redirect("?data_source=terneo_thermostats");
        }

        function discover_local_thermostats(&$out)
        {
            $this->discoverLocalThermostats();
            $this->redirect("?data_source=terneo_thermostats");
        }

        /**
         * terneo_thermostats edit/add
         *
         * @access public
         * @param $out
         * @param $id
         */
        function edit_terneo_thermostats(&$out, $id)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostats_edit.inc.php');
        }

        /**
         * terneo_thermostats delete record
         *
         * @access public
         * @param $id
         */
        function delete_terneo_thermostats($id)
        {
            // some action for related tables
            SQLExec("DELETE FROM terneo_thermostats WHERE ID='" . $id . "'");
            SQLExec("DELETE FROM terneo_thermostat_values WHERE DEVICE_ID='" . $id . "'");
        }

        /**
         * terneo_thermostat_values search
         *
         * @access public
         * @param $out
         */
        function search_terneo_thermostat_values(&$out)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostat_values_search.inc.php');
        }

        /**
         * terneo_thermostat_values edit/add
         *
         * @access public
         * @param $out
         * @param $id
         */
        function edit_terneo_thermostat_values(&$out, $id)
        {
            require(DIR_MODULES . $this->name . '/terneo_thermostat_values_edit.inc.php');
        }

        /**
         * Передача значений из привязанных объектов в термостаты
         * Метод автоматически вызывается при изменении свойства любого привязанного объекта
         * @param $object
         * @param $property
         * @param $value
         */
        function propertySetHandle($object, $property, $value)
        {
            $this->getConfig();
            if ($this->config['API_MODE']==self::API_MODE_CLOUD) {$this->propertySetHandleToCloud($object, $property, $value);}
            elseif ($this->config['API_MODE']==self::API_MODE_LOCAL) {$this->propertySetHandleToLocal($object, $property, $value);}
        }

        /**
         * Метод, периодически вызываемый циклом
         */
        function processCycle()
        {
            $this->getConfig();

            if ($this->config['API_MODE']==self::API_MODE_CLOUD) {$this->updateDevicesStatusFromCloud();}
            elseif ($this->config['API_MODE']==self::API_MODE_LOCAL) {$this->updateDevicesStatusFromLocal();}
        }

        /**
         * Install
         *
         * Module installation routine
         *
         * @access private
         * @param string $data
         */
        function install($data = '')
        {
            parent::install();
        }

        //*************************** My Functions **************************
        //===========================Cloud Functions===========================
        //<editor-fold desc="Методы для облачного режима">
        /**
         * Получает токен для подключения к API Terneo
         * @return string имя токена
         */
        private function  getCloudToken()
        {
            if (!isset($this->config['API_USERNAME']) or $this->config['API_USERNAME'] == "" or !isset($this->config['API_PASSWORD']) or $this->config['API_PASSWORD'] == "")
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
            $result = file_get_contents(self::API_URL . "/login/", FALSE, $context);
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
         * Возвращает информацию о всех термостатах из облака
         * @return mixed|null
         */
        private function getAllDevicesInfoFromCloud()
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getCloudToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGetFromCloud("/device/");

            return $result;
        }

        /**
         * Возвращает информацию из облака о конкретном термостате
         * @param int $sid идентификатор термостата (серийный номер)
         * @return mixed|null
         */
        private function getDeviceInfoFromCloud($sid)
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getCloudToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGetFromCloud("/device/" . $sid . "/");

            return $result;
        }

        /**
         * Возвращает дополнительные параметры конкретного термостата (зачем то они сделаны отдельно)
         * @param int $id
         * @return mixed|null
         */
        private function getDeviceParametersFromCloud($id)
        {
            if ($this->config['API_KEY'] == "")
            {
                $this->getCloudToken();
            }

            if ($this->config['API_KEY'] == "" || $this->config['API_KEY'] == "Error")
            {
                return NULL;
            }

            $result = $this->ApiGetFromCloud("/device/" . $id . "/parameters/");

            return $result;
        }

        /**
         * Загружает и обновляет значения термостатов из облака
         */
        function updateDevicesStatusFromCloud()
        {
            $equipments =  $this->getAllDevicesInfoFromCloud();

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
                    $rec['SID'] = $value['id'];
                    $rec['SN'] = $value['sn'];
                    $rec['UPDATED'] = date('Y-m-d H:i:s');
                    SQLUpdate('terneo_thermostats', $rec);
                }

                if (!$rec['ID'])
                {
                    return;
                }

                $this->loadValuesFromCloud($rec['ID'], $rec['SID']);
            }
        }

        /**
         * Загрузка значений термостатов из облака и установка этих значений привязанным объектам
         * @param int $deviceId устройства в БД
         * @param int $sid системный ID устройства на сайте Terneo
         */
        function loadValuesFromCloud($deviceId, $sid)
        {
            $rec_vals=array();
            $rec_val=array();

            $deviceParameters =  $this->getDeviceParametersFromCloud($sid);

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
                    elseif ($parameterValue['key'] == "childrenLock")
                    {
                        $rec_val['DEVICE_ID'] = $deviceId;
                        $rec_val['DEVICE_SID'] = $sid;
                        $rec_val['TITLE'] = "childrenLock";
                        $rec_val['DESCRIPTION'] = "(R/W) Защита от детей (0 - вкл, 1 - выкл.)";
                        $rec_val['VALUE'] = ($parameterValue['value'] ? 1 : 0);
                        $rec_vals[] = $rec_val;
                    }
                }
            }

            $device =  $this->getDeviceInfoFromCloud($sid);

            if (is_array($device) && isset($device['data']) && is_array($device['data']))
            {
                if (isset($device['data']['temp_setpoint']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_setpoint";
                    $rec_val['DESCRIPTION'] = " (R/W) Установленная температура °С";
                    $rec_val['VALUE'] = $device['data']['temp_setpoint'];
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['temp_current']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_current";
                    $rec_val['DESCRIPTION'] = " (R/O) Текущая температура °С";
                    $rec_val['VALUE'] = $device['data']['temp_current'];
                    $rec_vals[] = $rec_val;
                }

                 if (isset($device['data']['setpoint_state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "setpoint_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Установленная температура достигнута";
                    $rec_val['VALUE'] = ($device['data']['setpoint_state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['temperature_limits']) && isset($device['data']['temperature_limits']['max_value']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temperature_max_limit";
                    $rec_val['DESCRIPTION'] = "(R/O) MAX лимит температуры °С";
                    $rec_val['VALUE'] = $device['data']['temperature_limits']['max_value'];
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['temperature_limits']) && isset($device['data']['temperature_limits']['min_value']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temperature_min_limit";
                    $rec_val['DESCRIPTION'] = "(R/O) MIN лимит температуры °С";
                    $rec_val['VALUE'] = $device['data']['temperature_limits']['min_value'];
                    $rec_vals[] = $rec_val;
                }

                /*
                if (isset($device['data']['temp_mode_type']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "temp_mode_type";
                    $rec_val['DESCRIPTION'] = "(R/O) Вид режима температуры";
                    $rec_val['VALUE'] = $device['data']['temp_mode_type'];
                    $rec_vals[] = $rec_val;
                }*/

                if (isset($device['data']['overheat_state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "overheat_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Статус перегрева";
                    $rec_val['VALUE'] = ($device['data']['overheat_state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['is_online']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "is_online";
                    $rec_val['DESCRIPTION'] = "(R/O) Онлайн";
                    $rec_val['VALUE'] = ($device['data']['is_online'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['connection_state']) && isset($device['data']['connection_state']['state']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "connection_state";
                    $rec_val['DESCRIPTION'] = "(R/O) Статус подключения";
                    $rec_val['VALUE'] = ($device['data']['connection_state']['state'] ? 1 : 0);
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['connection_state']) && isset($device['data']['connection_state']['offline_minutes']))
                {
                    $rec_val['DEVICE_ID'] = $deviceId;
                    $rec_val['DEVICE_SID'] = $sid;
                    $rec_val['TITLE'] = "offline_minutes";
                    $rec_val['DESCRIPTION'] = "(R/O) Минут offline";
                    $rec_val['VALUE'] = $device['data']['connection_state']['offline_minutes'];
                    $rec_vals[] = $rec_val;
                }

                if (isset($device['data']['connection_state']) && isset($device['data']['connection_state']['last_connection_time']))
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
         * @param string $apiPath путь в Api
         * @param array $data [optional] дополнительные данные для отправки
         * @return mixed|null
         */
        private function ApiGetFromCloud($apiPath, array $data = NULL)
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
            $result = file_get_contents(self::API_URL . $apiPath, FALSE, $context);

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
        private function ApiPutToCloud($apiPath, $data)
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
            $result = file_get_contents(self::API_URL . $apiPath, FALSE, $context);

            if (!$result)
            {
                return NULL;
            }

            return $this->json_array($result);
        }

        /**
         * Передача значений из привязанных объектов в термостаты (через облако)
         * Метод автоматически вызывается при изменении свойства любого привязанного объекта
         * @param $object
         * @param $property
         * @param $value
         */
        function propertySetHandleToCloud($object, $property, $value)
        {
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
                        $result = $this->ApiPutToCloud("/device/" . $properties[$i]['DEVICE_SID'] . "/parameters/", '[{"key": "powerOff", "value": ' . ($value==1 ? "true" : "false") . '}]');

                        if (is_array($result) && isset($result[0]) && $result[0]['key'] == "powerOff")
                        {
                            $confirmedValue = ($result[0]['value']=="False" ? 0 : 1);
                            $isUpdated = TRUE;
                        }
                    }
                    elseif ($properties[$i]['TITLE'] == 'mode')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPutToCloud("/device/" . $properties[$i]['DEVICE_SID'] . "/parameters/", '[{"key": "mode", "value": ' . $value . '}]');

                        if (is_array($result) && isset($result[0]) && $result[0]['key'] == "mode")
                        {
                            $confirmedValue = $result[0]['value'];
                            $isUpdated = TRUE;
                        }
                    }
                    elseif ($properties[$i]['TITLE'] == 'childrenLock')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPutToCloud("/device/" . $properties[$i]['DEVICE_SID'] . "/parameters/", '[{"key": "childrenLock", "value": ' . ($value==1 ? "true" : "false") . '}]');

                        if (is_array($result) && isset($result[0]) && $result[0]['key'] == "childrenLock")
                        {
                            $confirmedValue = ($result[0]['value']=="False" ? 0 : 1);
                            $isUpdated = TRUE;
                        }
                    }
                    elseif ($properties[$i]['TITLE'] == 'temp_setpoint')
                    {
                        $properties[$i]['VALUE'] = $value;
                        $result = $this->ApiPutToCloud("/device/" . $properties[$i]['DEVICE_SID'] . "/setpoint/", '{"value": ' . $value . '}');

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
        //</editor-fold>
        //================================Local mode===============================
        //<editor-fold desc="Методы для локального режима">
        /**
         * Находит в локальной сети термостаты
         * Слушает сеть на порту UDP 23500, на который несколько раз в минуту термостаты отправляют широковещательный пакет с информацией о себе.
         * Добавляет найденные термостаты базу данных. А для уже существующих - обновляет IP
         */
        function discoverLocalThermostats()
        {
            set_time_limit(0);
            if ($this->config['API_LOG_DEBMES']) DebMes("========Start discoverLocalThermostats===========", 'terneo_thermostats');
            //Create a UDP socket
            if ($this->config['API_LOG_DEBMES']) DebMes("Creating sockets SOL_UDP", 'terneo_thermostats');
            if(!($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)))
            {
                $errorcode = socket_last_error();
                $errormsg = "Couldn't create socket: [$errorcode] ".socket_strerror($errorcode);
                if ($this->config['API_LOG_DEBMES']) DebMes($errormsg, 'terneo_thermostats');
                //die($errormsg."\n");
                return;
            }

            try
            {
                // Bind the source address
                if ($this->config['API_LOG_DEBMES']) DebMes("Binding socket (0.0.0.0:23500)", 'terneo_thermostats');
                if( !socket_bind($socket, "0.0.0.0" , 23500) ){
                    $errorcode = socket_last_error();
                    $errormsg = "Could not bind socket : [$errorcode]".socket_strerror($errorcode);
                    if ($this->config['API_LOG_DEBMES']) DebMes($errormsg, 'terneo_thermostats');
                    //die($errormsg."\n");
                    return;
                }

                if ($this->config['API_LOG_DEBMES']) DebMes("Socket binded OK", 'terneo_thermostats');

                socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1, "usec"=>100));
                socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=>1, "usec"=>100));

                $startDiscover = time();
                if ($this->config['API_LOG_DEBMES']) DebMes("Listening socket", 'terneo_thermostats');

                while(TRUE)
                {
                    if (time()-$startDiscover>=10) {break;} //ищем устройства не более 10 секунд

                    usleep(0.5 * 1000000); // 0.5 seconds

                    if (($bytes = @socket_recvfrom($socket, $r, 127, 0, $remote_ip, $remote_port)) === false)
                    {
                        $errorcode = socket_last_error();
                        $errormsg = "Error reading: [$errorcode] ".socket_strerror($errorcode);
                        if ($errorcode!='11')
                        {
                            if ($this->config['API_LOG_DEBMES']) DebMes($errormsg, 'terneo_thermostats');
                        }
                    }

                    if ($r=="") {continue;}

                    if ($this->config['API_LOG_DEBMES']) DebMes($r, 'terneo_thermostats');
                    $deviceInfo=$this->json_array($r);
                    if (!isset($deviceInfo["sn"]) || $deviceInfo["sn"]=="") {continue;}

                    $rec = SQLSelectOne("SELECT * FROM terneo_thermostats WHERE SN='".DbSafe($deviceInfo["sn"])."'");

                    if ($rec['ID']) //если устройство с таким sn уже есть, просто обновляем IP
                    {
                        $rec['IP'] = $remote_ip;
                        SQLUpdate('terneo_thermostats', $rec);
                        if ($this->config['API_LOG_DEBMES']) DebMes("Update existed thermostat IP: " . $remote_ip, 'terneo_thermostats');
                    }
                    else //если нет, добавляем
                    {
                        $rec['TITLE'] = $deviceInfo["sn"];
                        $rec['SN'] = $deviceInfo["sn"];
                        $rec['IP'] = $remote_ip;
                        $rec['ID'] = SQLInsert('terneo_thermostats', $rec);
                        if ($this->config['API_LOG_DEBMES']) DebMes("Added new thermostat: " . $deviceInfo["sn"]." [".$remote_ip."]", 'terneo_thermostats');
                    }
                }
            }
            finally
            {
                socket_close($socket);
                if ($this->config['API_LOG_DEBMES']) DebMes("Socket closed", 'terneo_thermostats');
            }

            if ($this->config['API_LOG_DEBMES']) DebMes("========End discoverLocalThermostats===========", 'terneo_thermostats');
        }

        /**
         * Загружает и обновляет значения термостатов из локальной сети
         */
        function updateDevicesStatusFromLocal()
        {
            $devices = SQLSelect("SELECT * FROM terneo_thermostats WHERE SN<>'' AND IP<>''");
            $deviceVal=array();

            foreach ($devices as $key => $device)
            {
                if (!$device['ID']) {continue;}

                $deviceInfo =  $this->getDeviceInfoFromLocal($device["IP"]);
                if (!is_array($deviceInfo)) {continue;}

                $device['FIRMWARE'] = $deviceInfo['firmware'];
                $device['UPDATED'] = date('Y-m-d H:i:s');
                SQLUpdate('terneo_thermostats', $device);

                $deviceVal['DEVICE_ID'] = $device['ID'];
                $deviceVal['DEVICE_SID'] = $device['SID'];
                $deviceVal['DEVICE_SN'] = $device['SN'];
                $deviceVal['DEVICE_IP'] = $device['IP'];

                if (isset($deviceInfo["mode"]))
                {
                    $deviceVal['TITLE'] = "mode";
                    $deviceVal['DESCRIPTION'] = "(R/W) Режим (0 - по расписанию, 1 - ручной)";
                    $deviceVal['VALUE'] = $deviceInfo["mode"];
                    $this->processValues($device['ID'], $deviceVal);
                }
                if (isset($deviceInfo["powerOff"]))
                {
                    $deviceVal['TITLE'] = "powerOff";
                    $deviceVal['DESCRIPTION'] = "(R/W) Выключение (0 - вкл, 1 - выкл.)";
                    $deviceVal['VALUE'] = $deviceInfo["powerOff"];
                    $this->processValues($device['ID'], $deviceVal);
                }
                if (isset($deviceInfo["childrenLock"]))
                {
                    $deviceVal['TITLE'] = "childrenLock";
                    $deviceVal['DESCRIPTION'] = "(R/W) Защита от детей (0 - вкл, 1 - выкл.)";
                    $deviceVal['VALUE'] = $deviceInfo["childrenLock"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['upperLimit']))
                {
                    $deviceVal['TITLE'] = "temperature_max_limit";
                    $deviceVal['DESCRIPTION'] = "(R/W) MAX лимит температуры °С";
                    $deviceVal['VALUE'] =  $deviceInfo["upperLimit"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['lowerLimit']))
                {
                    $deviceVal['TITLE'] = "temperature_min_limit";
                    $deviceVal['DESCRIPTION'] = "(R/W) MIN лимит температуры °С";
                    $deviceVal['VALUE'] =  $deviceInfo["lowerLimit"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['controlType']))
                {
                    $deviceVal['TITLE'] = "control_type";
                    $deviceVal['DESCRIPTION'] = "(R/W) Режим контроля";
                    $deviceVal['VALUE'] =  $deviceInfo["controlType"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['histeresis']))
                {
                    $deviceVal['TITLE'] = "histeresis";
                    $deviceVal['DESCRIPTION'] = " (R/W) Гистерезис °С";
                    $deviceVal['VALUE'] = $deviceInfo["histeresis"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['brightness']))
                {
                    $deviceVal['TITLE'] = "brightness";
                    $deviceVal['DESCRIPTION'] = "(R/W) Уровень яркости экрана (0-9)";
                    $deviceVal['VALUE'] =  $deviceInfo["brightness"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['manualFloorTemperature']))
                {
                    $deviceVal['TITLE'] = "manual_temp_setpoint";
                    $deviceVal['DESCRIPTION'] = " (R/W) Текушая уставка в ручном режиме °С";
                    $deviceVal['VALUE'] = $deviceInfo["manualFloorTemperature"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['currentSetpoint']))
                {
                    $deviceVal['TITLE'] = "temp_setpoint";
                    $deviceVal['DESCRIPTION'] = " (R/O) Текушая уставка °С";
                    $deviceVal['VALUE'] = $deviceInfo["currentSetpoint"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['floorTemperature']))
                {
                    $deviceVal['TITLE'] = "temp_current";
                    $deviceVal['DESCRIPTION'] = " (R/O) Текущая температура пола °С";
                    $deviceVal['VALUE'] = round($deviceInfo["floorTemperature"], 1);
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['currentSetpoint']) && isset($deviceInfo['floorTemperature']))
                {
                    $deviceVal['TITLE'] = "setpoint_state";
                    $deviceVal['DESCRIPTION'] = "(R/O) Установленная температура достигнута";
                    $deviceVal['VALUE'] = ($deviceInfo['floorTemperature']>=$deviceInfo['currentSetpoint'] ? 1 : 0);
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['loadState']))
                {
                    $deviceVal['TITLE'] = "load_state";
                    $deviceVal['DESCRIPTION'] = "(R/O) Состояние нагрузки";
                    $deviceVal['VALUE'] =  $deviceInfo["loadState"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['internalOverheating']))
                {
                    $deviceVal['TITLE'] = "overheat_state";
                    $deviceVal['DESCRIPTION'] = "(R/O) Статус перегрева";
                    $deviceVal['VALUE'] =  $deviceInfo["internalOverheating"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['wifiSignalLevel']))
                {
                    $deviceVal['TITLE'] = "wifi_signal";
                    $deviceVal['DESCRIPTION'] = "(R/O) Сила сигнала Wi-Fi в dBm";
                    $deviceVal['VALUE'] =  $deviceInfo["wifiSignalLevel"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['managementType']))
                {
                    $deviceVal['TITLE'] = "management_type";
                    $deviceVal['DESCRIPTION'] = "(R/O) Тип управления";
                    $deviceVal['VALUE'] =  $deviceInfo["managementType"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['lockType']))
                {
                    $deviceVal['TITLE'] = "lock_type";
                    $deviceVal['DESCRIPTION'] = "(R/O) Тип блокировки управления";
                    $deviceVal['VALUE'] =  $deviceInfo["lockType"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['lastRebootReason']))
                {
                    $deviceVal['TITLE'] = "last_reboot_reason";
                    $deviceVal['DESCRIPTION'] = "(R/O) Причина последней перезагрузки";
                    $deviceVal['VALUE'] =  $deviceInfo["lastRebootReason"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['floorSensorGap']))
                {
                    $deviceVal['TITLE'] = "floor_sensor_gap";
                    $deviceVal['DESCRIPTION'] = "(R/O) Обрыв термодатчика пола";
                    $deviceVal['VALUE'] =  $deviceInfo["floorSensorGap"];
                    $this->processValues($device['ID'], $deviceVal);
                }

                if (isset($deviceInfo['floorSensorShortCircuit']))
                {
                    $deviceVal['TITLE'] = "floor_sensor_short_circuit";
                    $deviceVal['DESCRIPTION'] = "(R/O) Короткое замыкание термодатчика пола";
                    $deviceVal['VALUE'] =  $deviceInfo["floorSensorShortCircuit"];
                    $this->processValues($device['ID'], $deviceVal);
                }
            }
        }

        //<editor-fold desc="Константы - локальные параметры термостатов">
        const PARAM_startAwayTime=0;  // в секундах от 01.01.2000, время начала отъезда (uint32) - тип 6
        const PARAM_endAwayTime=1;  // в секундах от 01.01.2000, время конца отъезда (uint32) - тип 6
        const PARAM_mode=2;  // режим работы: расписание=0, ручной=1 (uint8) - тип 2
        const PARAM_controlType=3;  // режим контроля: по полу=0, по воздуху=1, расширенный=2 (uint8) - тип 2
        const PARAM_manualAir=4;  // в °C, уставка ручного режима по воздуху (int8) - тип 1
        const PARAM_manualFloorTemperature=5;  // в °C, уставка ручного режима по полу (int8) - тип 1
        const PARAM_awayAirTemperature=6;  // в °C, уставка режима отъезда по воздуху (int8) - тип 1
        const PARAM_awayFloorTemperature=7;  // в °C, уставка режима отъезда по полу (int8) - тип 1
        const PARAM_minTempAdvancedMode=14;  // в °C, минимальная температура пола в расширенном режиме (uint8) - тип 2
        const PARAM_maxTempAdvancedMode=15;  // в °C, максимальная температура пола в расширенном режиме (uint8) - тип 2
        const PARAM_power=17;  // в у.е., P=(power<=150)?(power*10):(1500+power*20), подключенная мощность (uint16) - тип 4
        const PARAM_sensorType=18;  // тип подключенного аналогового датчика температуры: 4,7кОм=0, 6,8кОм=1, 10кОм=2, 12кОм=3, 15кОм=4, 33кОм=5, 47кОм=6 (uint8) - тип 2
        const PARAM_histeresis=19;  // в 1/10 °C, гистерезис (uint8) - тип 2
        const PARAM_airCorrection=20;  // в 1/10 °C, поправка датчика воздуха (int8) - тип 1
        const PARAM_floorCorrection=21;  // в 1/10 °C, поправка датчика пола (int8) - тип 1
        const PARAM_brightness=23;  // в у.е. (от 0 до 9) яркость (uint8) - тип 2
        const PARAM_propKoef=25;  // в минутах включенной нагрузки в пределах 30 минутного цикла работы пропорционального режима (uint8) - тип 2
        const PARAM_upperLimit=26;  // в °C, максимальное значение уставки пола (int8) - тип 1
        const PARAM_lowerLimit=27;  // в °C, минимальное значение уставки пола (int8) - тип 1
        const PARAM_maxSchedulePeriod=28;  // максимальное число периодов расписания в сутки (uint8) - тип 2
        const PARAM_tempTemperature=29;  // в °C, температура временного режима (uint8) - тип 2
        const PARAM_upperAirLimit=33;  // в °C, максимальное значение уставки воздуха (int8) - тип 1
        const PARAM_lowerAirLimit=34;  // в °C, минимальное значение уставки воздуха (int8) - тип 1
        const PARAM_upperU=35;  // в вольтах, верхний порог срабатывания по напряжению (uint16) - тип 4
        const PARAM_lowerU=36;  // в вольтах, нижний порог срабатывания по напряжению (uint8) - тип 2
        const PARAM_upperP=37;  // в 100 ватт, порог срабатывания по мощности (uint8) - тип 2
        const PARAM_upperI=38;  // в 1/10 ампера, верхний порог срабатывания по току (uint16) - тип 4
        const PARAM_middleI=39;  // в 1/10 ампера, средний порог срабатывания по току (uint16) - тип 4
        const PARAM_lowerI=40;  // в 1/10 ампера, нижний порог срабатывания по току (uint16) - тип 4
        const PARAM_tOnDelay=41;  // в секундах, задержка на включение реле (uint16) - тип 4
        const PARAM_tOffDelay=42;  // в секундах, задержка на выключение реле при превышении верхнего предела по току или мощности (uint8) - тип 2
        const PARAM_middleITime=43;  // в 1/10 секунды, задержка на выключение реле при превышении среднего предела тока (uint8) - тип 2
        const PARAM_lowerITime=44;  // в 1/10 секунды, задержка на выключение реле при токе ниже нижнего предела (uint8) - тип 2
        const PARAM_lowVoltageTime=45;  // в 1/10 секунды, длительность провала напряжения (uint16) - тип 4
        const PARAM_correctionsU=46;  // в вольтах, поправка вольтметра (int8) - тип 1
        const PARAM_correctionsI=47;  // в процентах, поправка амперметра (int8) - тип 1
        const PARAM_repTimes=48;  // количество отлючений реле по току или напряжению до блокировки устройства (uint8) - тип 2
        const PARAM_powerType=49;  // тип контролируемой мощности: активная(Вт)=0, реактивная(ВАР)=1, полная(ВА)=2 (uint8) - тип 2
        const PARAM_showType=50;  // тип отображаемого параметра: ток=0, акт. мощн.=1, реакт. мощн.=2, полная мощн.=3, косинус фи=4 (uint8) - тип 2
        const PARAM_sensorСontrolNumber=51;  // номер удалённого датчика для контроля температуры (uint8) - тип 2
        const PARAM_proMode=112;  // профессиональная модель задержки на выключение по напряжению (bool) - тип 7
        const PARAM_voltageStableDelay=113;  // задержка на включение реле считает с момента нормализации напряжения (bool) - тип 7
        const PARAM_androidBlock=114;  // блокировка любых изменений настроек через offlineApi (bool) - тип 7
        const PARAM_cloudBlock=115;  // блокировка любых изменений настроек и перепрошивки через облако (bool) - тип 7
        const PARAM_useContactorControl=116;  // нагрузка через контактор (только учёт электроэнергии) (bool) - тип 7
        const PARAM_NCContactControl=117;  // инвертированное реле (bool) - тип 7
        const PARAM_coolingControlWay=118;  // режим нагрев/охлаждения (bool) - тип 7
        const PARAM_preControl=121;  // предварительный нагрев (bool) - тип 7
        const PARAM_windowOpenControl=122;  // режим открытого окна (bool) - тип 7
        const PARAM_childrenLock=124;  // защита от детей (bool) - тип 7
        const PARAM_powerOff=125;  // выключение (bool) - тип 7
        //</editor-fold>

        /**
         * Возвращает информацию из локальной сети о конкретном термостате
         * @param string $ip IP адрес термостата в локальной сети
         * @return mixed|null
         */
        private function getDeviceInfoFromLocal($ip)
        {   $request = array("cmd"=>1);
            $rowParameters = $this->ApiGetFromLocal($ip, $request);

            $result = array();
            if (is_array($rowParameters) && isset($rowParameters["par"]))
            {
                foreach ($rowParameters["par"] as $key => $parameter)
                {
                    //<editor-fold desc="Значения свойств">
                    if ($parameter[0] == self::PARAM_startAwayTime) {$result["startAwayTime"] = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, 2000) + $parameter[2]);} // в секундах от 01.01.2000, время начала отъезда (uint32) - тип 6
                    elseif ($parameter[0] == self::PARAM_endAwayTime) {$result["endAwayTime"] = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, 2000) + $parameter[2]);} // в секундах от 01.01.2000, время конца отъезда (uint32) - тип 6
                    elseif ($parameter[0] == self::PARAM_mode) {$result["mode"] = $parameter[2];} // режим работы: расписание=0, ручной=1 (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_controlType) {$result["controlType"] = $parameter[2];} // режим контроля: по полу=0, по воздуху=1, расширенный=2 (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_manualAir) {$result["manualAir"] = $parameter[2];} // в °C, уставка ручного режима по воздуху (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_manualFloorTemperature) {$result["manualFloorTemperature"] = $parameter[2];}  // в °C, уставка ручного режима по полу (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_awayAirTemperature) {$result["awayAirTemperature"] = $parameter[2];}  // в °C, уставка режима отъезда по воздуху (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_awayFloorTemperature) {$result["awayFloorTemperature"] = $parameter[2];} // в °C, уставка режима отъезда по полу (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_minTempAdvancedMode) {$result["minTempAdvancedMode"] = $parameter[2];}  // в °C, минимальная температура пола в расширенном режиме (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_maxTempAdvancedMode) {$result["maxTempAdvancedMode"] = $parameter[2];} // в °C, максимальная температура пола в расширенном режиме (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_power) {$result["power"] = ($parameter[2]<=150)?($parameter[2]*10):(1500+$parameter[2]*20);} // в у.е., P=(power<=150)?(power*10):(1500+power*20), подключенная мощность (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_sensorType) // тип подключенного аналогового датчика температуры: 4,7кОм=0, 6,8кОм=1, 10кОм=2, 12кОм=3, 15кОм=4, 33кОм=5, 47кОм=6 (uint8) - тип 2
                    {
                        if ($parameter[2] == 0) {$result["sensorType"] = "4,7кОм";}
                        elseif ($parameter[2] == 1) {$result["sensorType"] = "6,8кОм";}
                        elseif ($parameter[2] == 2) {$result["sensorType"] = "10кОм";}
                        elseif ($parameter[2] == 3) {$result["sensorType"] = "12кОм";}
                        elseif ($parameter[2] == 4) {$result["sensorType"] = "15кОм";}
                        elseif ($parameter[2] == 5) {$result["sensorType"] = "33кОм";}
                        elseif ($parameter[2] == 6) {$result["sensorType"] = "47кОм";}
                        else {$result["sensorType"]="";}
                    }
                    elseif ($parameter[0] == self::PARAM_histeresis) {$result["histeresis"] = $parameter[2]/10;} // в 1/10 °C, гистерезис (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_airCorrection) {$result["airCorrection"] = $parameter[2]/10;} // в 1/10 °C, поправка датчика воздуха (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_floorCorrection) {$result["floorCorrection"] = $parameter[2]/10;} // в 1/10 °C, поправка датчика пола (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_brightness) {$result["brightness"] = $parameter[2];} // в у.е. (от 0 до 9) яркость (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_propKoef) {$result["propKoef"] = $parameter[2];} // в минутах включенной нагрузки в пределах 30 минутного цикла работы пропорционального режима (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_upperLimit) {$result["upperLimit"] = $parameter[2];} // в °C, максимальное значение уставки пола (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_lowerLimit) {$result["lowerLimit"] = $parameter[2];} // в °C, минимальное значение уставки пола (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_maxSchedulePeriod) {$result["maxSchedulePeriod"] = $parameter[2];} // максимальное число периодов расписания в сутки (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_tempTemperature) {$result["tempTemperature"] = $parameter[2];} // в °C, температура временного режима (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_upperAirLimit) {$result["upperAirLimit"] = $parameter[2];} // в °C, максимальное значение уставки воздуха (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_lowerAirLimit) {$result["lowerAirLimit"] = $parameter[2];} // в °C, минимальное значение уставки воздуха (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_upperU) {$result["upperU"] = $parameter[2];} // в вольтах, верхний порог срабатывания по напряжению (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_lowerU) {$result["lowerU"] = $parameter[2];} // в вольтах, нижний порог срабатывания по напряжению (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_upperP) {$result["upperP"] = $parameter[2];} // в 100 ватт, порог срабатывания по мощности (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_upperI) {$result["upperI"] = $parameter[2]/10;} // в 1/10 ампера, верхний порог срабатывания по току (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_middleI) {$result["middleI"] = $parameter[2]/10;} // в 1/10 ампера, средний порог срабатывания по току (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_lowerI) {$result["lowerI"] = $parameter[2]/10;} // в 1/10 ампера, нижний порог срабатывания по току (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_tOnDelay) {$result["tOnDelay"] = $parameter[2];} // в секундах, задержка на включение реле (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_tOffDelay) {$result["tOffDelay"] = $parameter[2];} // в секундах, задержка на выключение реле при превышении верхнего предела по току или мощности (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_middleITime) {$result["middleITime"] = $parameter[2]/10;} // в 1/10 секунды, задержка на выключение реле при превышении среднего предела тока (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_lowerITime) {$result["lowerITime"] = $parameter[2]/10;} // в 1/10 секунды, задержка на выключение реле при токе ниже нижнего предела (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_lowVoltageTime) {$result["lowVoltageTime"] = $parameter[2]/10;} // в 1/10 секунды, длительность провала напряжения (uint16) - тип 4
                    elseif ($parameter[0] == self::PARAM_correctionsU) {$result["correctionsU"] = $parameter[2];} // в вольтах, поправка вольтметра (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_correctionsI) {$result["correctionsI"] = $parameter[2];} // в процентах, поправка амперметра (int8) - тип 1
                    elseif ($parameter[0] == self::PARAM_repTimes) {$result["repTimes"] = $parameter[2];} // количество отлючений реле по току или напряжению до блокировки устройства (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_powerType) // тип контролируемой мощности: активная(Вт)=0, реактивная(ВАР)=1, полная(ВА)=2 (uint8) - тип 2
                    {
                        if ($parameter[2] == 0) {$result["powerType"] = "активная(Вт)";}
                        elseif ($parameter[2] == 1) {$result["powerType"] = "реактивная(ВАР)";}
                        elseif ($parameter[2] == 2) {$result["powerType"] = "полная(ВА)";}
                        else {$result["powerType"]="";}
                    }
                    elseif ($parameter[0] == self::PARAM_showType) // тип отображаемого параметра: ток=0, акт. мощн.=1, реакт. мощн.=2, полная мощн.=3, косинус фи=4 (uint8) - тип 2
                    {
                        if ($parameter[2] == 0) {$result["showType"] = "ток";}
                        elseif ($parameter[2] == 1) {$result["showType"] = "акт. мощн.";}
                        elseif ($parameter[2] == 2) {$result["showType"] = "реакт. мощн.";}
                        elseif ($parameter[2] == 3) {$result["showType"] = "полная мощн.";}
                        elseif ($parameter[2] == 4) {$result["showType"] = "косинус фи";}
                        else {$result["showType"]="";}
                    }
                    elseif ($parameter[0] == self::PARAM_sensorСontrolNumber) {$result["sensorСontrolNumber"] = $parameter[2];} // номер удалённого датчика для контроля температуры (uint8) - тип 2
                    elseif ($parameter[0] == self::PARAM_proMode) {$result["proMode"] = $parameter[2];} // профессиональная модель задержки на выключение по напряжению (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_voltageStableDelay) {$result["voltageStableDelay"] = $parameter[2];} // задержка на включение реле считает с момента нормализации напряжения (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_androidBlock) {$result["androidBlock"] = $parameter[2];} // блокировка любых изменений настроек через offlineApi (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_cloudBlock) {$result["cloudBlock"] = $parameter[2];} // блокировка любых изменений настроек и перепрошивки через облако (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_useContactorControl) {$result["useContactorControl"] = $parameter[2];} // нагрузка через контактор (только учёт электроэнергии) (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_NCContactControl) {$result["NCContactControl"] = $parameter[2];} // инвертированное реле (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_coolingControlWay) {$result["coolingControlWay"] = $parameter[2];} // режим нагрев/охлаждения (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_preControl) {$result["preControl"] = $parameter[2];} // предварительный нагрев (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_windowOpenControl) {$result["windowOpenControl"] = $parameter[2];} // режим открытого окна (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_childrenLock) {$result["childrenLock"] = $parameter[2];} // защита от детей (bool) - тип 7
                    elseif ($parameter[0] == self::PARAM_powerOff) {$result["powerOff"] = $parameter[2];} // выключение (bool) - тип 7
                    //</editor-fold>
                }
            }

            usleep(100000);
            $request = array("cmd"=>4);
            $rowTelemetry = $this->ApiGetFromLocal($ip, $request);

            if (is_array($rowTelemetry))
            {
                foreach ($rowTelemetry as $key => $telemetry)
                {
                    //<editor-fold desc="Значения телеметрии">
                    //t - температура в 1/16 °C
                    if ($key == 't.0') {$result["internalOverheatingSensorTemperature"] = $telemetry/16;} //0 - внутрений датчик перегрева
                    elseif ($key == 't.1') {$result["floorTemperature"] = $telemetry/16;} //1 - пола
                    elseif ($key == 't.2') {$result["airTemperature"] = $telemetry/16;} //2 - воздуха
                    elseif ($key == 't.3') {$result["precipitationSensorTemperature"] = $telemetry/16;} //3 - датчика осадков
                    elseif ($key == 't.4') {$result["externalObjectTemperature"] = $telemetry/16;} //4 - внешнего объекта
                    elseif ($key == 't.5') {$result["currentSetpoint"] = $telemetry/16;} //5 - текущая уставка
                    elseif ($key == 't.6') {$result["offsetTemperature"] = $telemetry/16;} //6 - поправка
                    //u - напряжение
                    elseif ($key == 'u.0') {$result["maxDetectedVoltage"] = $telemetry;} //0 - максимальное напряжение за период телеметрии, вольт
                    elseif ($key == 'u.1') {$result["minDetectedVoltage"] = $telemetry;} //1 - минимальное напряжение за период телеметрии, вольт
                    elseif ($key == 'u.2') {$result["supplyVoltage3_3v"] = $telemetry/100;} //2 - напряжения питания 3.3V, 1/100 вольта
                    elseif ($key == 'u.3') {$result["batteryVoltage"] = $telemetry/10;} //3 - напряжение батарейки, 1/10 вольта
                    elseif ($key == 'u.4') {$result["upperThresholdVolt"] = $telemetry;} //4 - верхний порог срабатывания, вольт
                    elseif ($key == 'u.5') {$result["lowerThresholdVolt"] = $telemetry;} //5 - нижний порог срабатывания, вольт
                    elseif ($key == 'u.6') {$result["avgVoltage"] = $telemetry;} //6 - среднее напряжение, вольт
                    //i - ток
                    elseif ($key == 'i.0') {$result["maxDetectedCurrent"] = $telemetry;} //0 - максимальный ток за период телеметрии, 1/10 ампера
                    elseif ($key == 'i.1') {$result["avgDetectedCurrent"] = $telemetry;} //1 - средний ток за период телеметрии, 1/10 ампера
                    elseif ($key == 'i.2') {$result["minDetectedCurrent"] = $telemetry/100;} //2 - минимальный ток за период телеметрии, 1/10 ампера
                    elseif ($key == 'i.3') {$result["upperThresholdCurrent"] = $telemetry/10;} //3 - верхний предел тока, 1/10 ампера
                    elseif ($key == 'i.4') {$result["avgThresholdCurrent"] = $telemetry;} //4 - средний предел тока, 1/10 ампера
                    elseif ($key == 'i.5') {$result["lowerThresholdCurrent"] = $telemetry;} //5 - нижний предел тока, 1/10 ампера
                    //w - мощность
                    elseif ($key == 'w.0') {$result["upperThresholdPower"] = $telemetry;} //0 - верхний предел мощности, ватт
                    elseif ($key == 'w.1') {$result["maxFullLoadDetecterdPower"] = $telemetry;} //1 - максимальная полная мощность нагрузки за период телеметрии, ВА
                    elseif ($key == 'w.2') {$result["avgFullLoadDetecterdPower"] = $telemetry/100;} //2 - средняя полная мощность нагрузки за период телеметрии, ВА
                    elseif ($key == 'w.3') {$result["minFullLoadDetecterdPower"] = $telemetry/10;} //3 - минимальная полная мощность нагрузки за период телеметрии, ВА
                    elseif ($key == 'w.4') {$result["maxCosFiDetected"] = $telemetry/100;} //4 - максимальный косинус фи за период телеметрии, 1/100 градуса
                    elseif ($key == 'w.5') {$result["avgCosFiDetected"] = $telemetry/100;} //5 - средний косинус фи за период телеметрии, 1/100 градуса
                    elseif ($key == 'w.6') {$result["minCosFiDetected"] = $telemetry/100;} //6 - минимальный косинус фи за период телеметрии, 1/100 градуса
                    //r - сопротивление
                    elseif ($key == 'r.0') {$result["soilMoistureSensor,"] = $telemetry/100;} //0 - грунтовый датчик влаги, в 100 Ом
                    //p - активные и реактивные параметры
                    elseif ($key == 'p.0') {$result['maxActiveLoadPower'] = $telemetry;} //0 - максимальная активная мощность нагрузки за период телеметрии, ватт
                    elseif ($key == 'p.1') {$result['avgActiveLoadPower'] = $telemetry;} //1 - средняя активная мощность нагрузки за период телеметрии, ватт
                    elseif ($key == 'p.2') {$result['minActiveLoadPower'] = $telemetry;} //2 - минимальная активная мощность нагрузки за период телеметрии, ватт
                    elseif ($key == 'p.3') {$result['maxReactiveLoadPower'] = $telemetry;} //3 - максимальная реактивная мощность нагрузки за период телеметрии, ВАР
                    elseif ($key == 'p.4') {$result['avgReactiveLoadPower'] = $telemetry;} //4 - средняя реактивная мощность нагрузки за период телеметрии, ВАР
                    elseif ($key == 'p.5') {$result['minReactiveLoadPower'] = $telemetry;} //5 - минимальная реактивная мощность нагрузки за период телеметрии, ВАР
                    //m - режимы
                    elseif ($key == 'm.0') //0 - тип контроля: пол = 0, воздух = 1, расширенный = 2
                    {
                        if ($telemetry == 0) { $result['controlType'] = "по полу";}
                        elseif ($telemetry == 1) { $result['controlType'] = "по воздуху";}
                        elseif ($telemetry == 2) { $result['controlType'] = "расширенный";}
                    }
                    elseif ($key == 'm.1') //1 - тип управления: по расписанию = 0, ручной = 3, отъезд = 4, временный = 5
                    {
                        if ($telemetry == 0) { $result['managementType'] = "по расписанию";}
                        elseif ($telemetry == 3) { $result['managementType'] = "ручной";}
                        elseif ($telemetry == 4) { $result['managementType'] = "отъезд";}
                        elseif ($telemetry == 5) { $result['managementType'] = "временный";}
                    }
                    elseif ($key == 'm.2') {$result['currentSchedulePeriod'] = $telemetry;} //2 - номер текущего периода расписания (первый период понедельника = 0, вторника = maxSchedulePeriod…)
                    elseif ($key == 'm.3') //3 - тип блокировки: нет блокировок = 0, блокировка изменений из облака = 1, блокировка изменений из локальной сети = 2, обе = 3
                    {
                        if ($telemetry == 0) { $result['lockType'] = "нет блокировок";}
                        elseif ($telemetry == 1) { $result['lockType'] = "блокировка изменений из облака";}
                        elseif ($telemetry == 2) { $result['lockType'] = "блокировка изменений из локальной сети";}
                        elseif ($telemetry == 3) { $result['lockType'] = "обе блокировки";}
                    }
                    elseif ($key == 'm.4') //4 - тип контролируемой мощности: активная мощность = 0, реактивная мощность = 1, полная мощность = 2
                    {
                        if ($telemetry == 0) { $result['managementPowerType'] = "активная мощность";}
                        elseif ($telemetry == 1) { $result['managementPowerType'] = "реактивная мощность";}
                        elseif ($telemetry == 2) { $result['managementPowerType'] = "полная мощность";}
                    }
                    //o - прочие параметры
                    elseif ($key == 'o.0') {$result['wifiSignalLevel'] = $telemetry;} //0 - уровень сигнала Wi-Fi в dBm (-127..128)
                    elseif ($key == 'o.1') //1 - причина последней перезагрузки, маска: выключение питания = 0x04, програмный сброс = 0x08, сторожевой таймер = 0x10, низкое напряжение = 0x40
                    {
                        if ($telemetry == 4 || $telemetry == 6) { $result['lastRebootReason'] = "выключение питания";}
                        elseif ($telemetry == 8) { $result['lastRebootReason'] = "програмный сброс";}
                        elseif ($telemetry == 10) { $result['lastRebootReason'] = "сторожевой таймер";}
                        elseif ($telemetry == 64 || $telemetry == 40) { $result['lastRebootReason'] = "низкое напряжение";}
                        else { $result['lastRebootReason'] = $telemetry;}
                    }
                    //te - дополнительная температура
                    elseif ($key == 'te') {$result['additionalTemperature'] = $telemetry;} //te - дополнительная температура
                    //f - битовые параметры
                    elseif ($key == 'f.0') {$result['loadState'] = $telemetry;} //0 - состояние нагрузки
                    elseif ($key == 'f.1') {$result['waitingForLoad'] = $telemetry;} //1 - ожидание включения нагрузки
                    elseif ($key == 'f.2') {$result['floorRestrictionAction'] = $telemetry;} //2 - действие ограничения по полу
                    elseif ($key == 'f.3') {$result['floorSensorGap'] = $telemetry;} //3 - обрыв датчика пола
                    elseif ($key == 'f.4') {$result['floorSensorShortCircuit'] = $telemetry;} //4 - короткое замыкания датчика пола
                    elseif ($key == 'f.5') {$result['airSensorGap'] = $telemetry;} //5 - обрыв датчика воздуха
                    elseif ($key == 'f.6') {$result['airSensorShortCircuit'] = $telemetry;} //6 - к.з. датчика воздуха
                    elseif ($key == 'f.7') {$result['preheatingAction'] = $telemetry;} //7 - действие предварительного прогрева
                    elseif ($key == 'f.8') {$result['openWindowFunctionAction'] = $telemetry;} //8 - действие ф-ции открытого окна
                    elseif ($key == 'f.9') {$result['internalOverheating'] = $telemetry;} //9 - внутренний перегрев
                    elseif ($key == 'f.10') {$result['lowBattery'] = $telemetry;} //10 - села батарейка
                    elseif ($key == 'f.11') {$result['clockProblem'] = $telemetry;} //11 - проблемы с часами
                    elseif ($key == 'f.12') {$result['noOverheatingControl'] = $telemetry;} //12 - нет контроля перегрева
                    elseif ($key == 'f.13') {$result['proportionalLoadOperation'] = $telemetry;} //13 - пропорциональный режим работы нагрузки
                    elseif ($key == 'f.14') {$result['digitalFloorSensorUsed'] = $telemetry;} //14 - используется цифровой датчик пола
                    elseif ($key == 'f.15') {$result['watchdogReboot'] = $telemetry;} //15 - перезагрузка по сторожевому таймеру
                    //</editor-fold>
                }
            }

            return $result;
        }

        /**
         * Получает данные из термостата с помощью запроса к API по локальной сети
         * @param string $ip IP адрес
         * @param array $data [optional] комманда для отправки
         * @return mixed|null
         * @internal param string $apiPath путь в Api
         */
        private function ApiGetFromLocal($ip, array $data = NULL)
        {
            if ($this->config['API_LOG_DEBMES']) DebMes("========Start ApiGetFromLocal(ip: ".$ip.", data: ".$data.")===========", 'terneo_thermostats');

            if ($ip == '' || $data == NULL) {return NULL;}

            $url = 'http://'.$ip.'/api.cgi';
            $content = trim(json_encode($data,JSON_UNESCAPED_UNICODE));

            if ($this->config['API_LOG_DEBMES']) DebMes("Send request url: ".$url." content: ".$content, 'terneo_thermostats');

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: text/plain;charset=UTF-8", //"Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' =>$content
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($this->config['API_LOG_DEBMES']) DebMes("========End ApiGetFromLocal result: ".trim($result)."===========", 'terneo_thermostats');

            if (!$result) {return NULL;}

            return $this->json_array(trim($result));
        }

        /**
         * Устанавливает параметры термостата с помощью запроса к API по локальной сети
         * @param string $ip IP адрес
         * @param string $sn SN номер
         * @param array $data [optional] комманда для отправки
         * @return mixed|null
         * @internal param string $apiPath путь в Api
         */
        private function ApiSetToLocal($ip, $sn, array $data = NULL)
        {
            if ($this->config['API_LOG_DEBMES']) DebMes("========Start ApiSetToLocal(ip: ".$ip.",sn: ".$sn.", data: ".$data.")===========", 'terneo_thermostats');

            if ($ip == '' || $sn=='' || $data == NULL) {return NULL;}

            $url = 'http://'.$ip.'/api.cgi';

            $contentArr = array();
            $contentArr['sn'] = $sn;
            $contentArr['par'] = array($data);
            $content = trim(json_encode($contentArr,JSON_UNESCAPED_UNICODE));

            if ($this->config['API_LOG_DEBMES']) DebMes("Send request url: ".$url." content: ".$content, 'terneo_thermostats');

            // use key 'http' even if you send the request to https://...
            $options = array(
                'http' => array(
                    'header'  => "Content-type: text/plain;charset=UTF-8", //"Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' =>$content
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            if ($this->config['API_LOG_DEBMES']) DebMes("========End ApiSetToLocal result: ".trim($result)."===========", 'terneo_thermostats');

            if (!$result) {return NULL;}

            return $this->json_array(trim($result));
        }

        /**
         * Передача значений из привязанных объектов в термостаты (через локальную сеть)
         * Метод автоматически вызывается при изменении свойства любого привязанного объекта
         * @param $object
         * @param $property
         * @param $value
         */
        function propertySetHandleToLocal($object, $property, $value)
        {
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

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_powerOff, 7, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'mode')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_mode, 2, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'childrenLock')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_childrenLock, 7, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'manual_temp_setpoint')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_manualFloorTemperature, 1, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'histeresis')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_histeresis, 2, (string)($value*10)]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'brightness')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_brightness, 2, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'temperature_max_limit')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_upperLimit, 1, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'temperature_min_limit')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_lowerLimit, 1, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
                            $isUpdated = TRUE;
                        }
                    }

                    if ($properties[$i]['TITLE'] == 'control_type')
                    {
                        $properties[$i]['VALUE'] = $value;

                        $result = $this->ApiSetToLocal($properties[$i]['DEVICE_IP'], $properties[$i]['DEVICE_SN'], [self::PARAM_controlType, 2, (string)$value]);

                        if (is_array($result) && isset($result['success']) && $result['success'] == "true")
                        {
                            $confirmedValue = $value;
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
        //</editor-fold>

        //================================Common====================================
        //<editor-fold desc="Общие методы">
        /**
         * Сохраняет новые значения от термостатов в базу данных и вызывает привязанные события или свойства
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
         * Возвращает массив из JSON строки
         * @param string $str  JSON строка в текстовом виде
         * @return mixed массив, преобразованный из JSON строки
         */
        function json_array($str)
        {
            $result = json_decode($str, TRUE);

            return $result;
        }
        //</editor-fold>
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
         * @param string $data
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
 terneo_thermostats: IP varchar(15) NOT NULL DEFAULT ''
 terneo_thermostats: UPDATED datetime
 terneo_thermostat_values: ID int(10) unsigned NOT NULL auto_increment
 terneo_thermostat_values: TITLE varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: DESCRIPTION varchar(100) NOT NULL DEFAULT ''
 terneo_thermostat_values: VALUE varchar(255) NOT NULL DEFAULT ''
 terneo_thermostat_values: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 terneo_thermostat_values: DEVICE_SID varchar(10) NOT NULL DEFAULT ''
 terneo_thermostat_values: DEVICE_SN varchar(255) NOT NULL DEFAULT ''
 terneo_thermostat_values: DEVICE_IP varchar(15) NOT NULL DEFAULT ''
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
