<?php

declare(strict_types=1);
	class RSCP_Connect extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');


			$this->RegisterPropertyString("TOPIC", 'e3dc');

			$Variables = [];
        	foreach (static::$Variables as $Pos => $Variable) {
				$Variables[] = [
					'Ident'        => str_replace(' ', '', $Variable[0]),
					'Name'         => $this->Translate($Variable[0]),
					'Tag'		   => $Variable[1],
					'MQTT'		   => $Variable[2],
					'VarType'      => $Variable[3],
					'Profile'      => $Variable[4],
					'Factor'       => $Variable[5],
					'Action'       => $Variable[6],
					'Pos'          => $Pos + 1,
					'Keep'         => $Variable[7]
				];
        	}	
			$this->RegisterPropertyString('Variables', json_encode($Variables));
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
			
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();

			$this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

			//Setze Filter für ReceiveData
			$MQTTTopic = $this->ReadPropertyString('TOPIC');
			$this->SetReceiveDataFilter('.*' . $MQTTTopic . '.*');
	
			$this->registerProfiles();
			$this->registerVariables();
		}

		public function ReceiveData($JSONString)
		{
			$this->SendDebug('JSON', $JSONString, 0);
        	if (!empty($this->ReadPropertyString('TOPIC'))) {

				if ($JSONString == '') {
					$this->log('No JSON');
					return true;
				}

				$data = json_decode($JSONString);

				switch ($data->DataID) {
					case '{7F7632D9-FA40-4F38-8DEA-C83CD4325A32}': // MQTT Server
						$Buffer = $data;
						break;
					default:
						$this->LogMessage('Invalid Parent', KL_ERROR);
						return;
				}
				$this->SendDebug('MQTT Topic', $Buffer->Topic, 0);

				if (property_exists($Buffer, 'Topic')) {
					$Variables = json_decode($this->ReadPropertyString('Variables'), true);
					foreach ($Variables as $Variable) {
						if (fnmatch( $this->readPropertyString('TOPIC').$Variable['MQTT'], $Buffer->Topic)) {
							$this->SendDebug($Variable['MQTT'], $Buffer->Payload, 0);
							if ($Variable['Factor'] == 1){
								$this->SetValue($Variable['Ident'], $Buffer->Payload); 
							} 
							else {
								$this->SetValue($Variable['Ident'], $Buffer->Payload * $Variable['Factor']); 
							}   	
						}   
					}
				}
			}
		}

		public function resetVariables()
		{
			$NewRows = static::$Variables;
			$Variables = [];
			foreach ($NewRows as $Pos => $Variable) {
				$Variables[] = [
					'Ident'        => str_replace(' ', '', $Variable[0]),
					'Name'         => $this->Translate($Variable[0]),
					'Tag'		   => $Variable[1],
					'MQTT'		   => $Variable[2],
					'VarType'      => $Variable[3],
					'Profile'      => $Variable[4],
					'Factor'	   => $Variable[5],
					'Action'       => $Variable[6],
					'Pos'          => $Pos + 1,
					'Keep'         => $Variable[7]
				];
			}
			IPS_SetProperty($this->InstanceID, 'Variables', json_encode($Variables));
			IPS_ApplyChanges($this->InstanceID);
			return;
		}

		/////////// Commands for E3DC RSCP /////////////

		public function force_update()
		{
			$Topic = 'e3dc/set/force';
			$Payload = 'true';
			$this->sendMQTT($Topic, $Payload);
		}
		public function set_refresh_interval(int $value)
		{
			if ($value >=1 and $value <=10){
			$Topic = 'e3dc/set/interval';
			$Payload = strval($value);
			$this->sendMQTT($Topic, $Payload);
			}
		}

		public function set_power_mode_auto()
		{
			$Topic = 'e3dc/set/power_mode';
			$Payload = 'auto';
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_mode_idle(int $cycles)
		{
			$Topic = 'e3dc/set/power_mode';
			$Payload = 'idle:'.strval($cycles);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_mode_discharge(int $value, int $cycles)
		{
			$Topic = 'e3dc/set/power_mode';
			$Payload = 'discharge:'.strval($value).':'.strval($cycles);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_mode_charge(int $value, int $cycles)
		{
			$Topic = 'e3dc/set/power_mode';
			$Payload = 'charge:'.strval($value).':'.strval($cycles);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_mode_gridcharge(int $value, int $cycles)
		{
			$Topic = 'e3dc/set/power_mode';
			$Payload = 'grid_charge:'.strval($value).':'.strval($cycles);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_manual_charge(int $value)
		{
			$Topic = 'e3dc/set/manual_charge';
			$Payload = strval($value);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_weather_regulation(bool $value)
		{	
			$Topic = 'e3dc/set/weather_regulation';
			if ($value)
				$Payload = 'true';
			else
				$Payload = 'false';
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_limits_mode(bool $value)
		{	
			$Topic = 'e3dc/set/power_limits';
			if ($value)
				$Payload = 'true';
			else
				$Payload = 'false';
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_max_discharge_power(int $value)
		{
			$Topic = 'e3dc/set/max_discharge_power';
			$Payload = strval($value);
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_max_charge_power(int $value)
		{
			$Topic = 'e3dc/set/max_charge_power';
			$Payload = strval($value);
			$this->sendMQTT($Topic, $Payload);
		}


		// Private & Protected Methods
		private function registerProfiles()
		{
			//Create required Profiles

			if (!IPS_VariableProfileExists('RSCP.Power.Mode')) {
				IPS_CreateVariableProfile('RSCP.Power.Mode', 1);
				IPS_SetVariableProfileIcon('RSCP.Power.Mode', 'Ok');
				IPS_SetVariableProfileAssociation("RSCP.Power.Mode", 0, "Auto/Normal", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Power.Mode", 1, "Idle", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Power.Mode", 2, "Entladen", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Power.Mode", 3, "Laden", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Power.Mode", 4, "Netz Laden", "", 0xFFFFFF);
			}
			if (!IPS_VariableProfileExists('RSCP.Coupling.Mode')) {
				IPS_CreateVariableProfile('RSCP.Coupling.Mode', 1);
				IPS_SetVariableProfileIcon('RSCP.Coupling.Mode', 'Ok');
				IPS_SetVariableProfileAssociation("RSCP.Coupling.Mode", 0, "DC", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Coupling.Mode", 1, "DC-MultiWR", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Coupling.Mode", 2, "AC", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Coupling.Mode", 3, "Hybrid", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.Coupling.Mode", 4, "Insel", "", 0xFFFFFF);
			}
			if (!IPS_VariableProfileExists('RSCP.EMS.Mode')) {
				IPS_CreateVariableProfile('RSCP.EMS.Mode', 1);
				IPS_SetVariableProfileIcon('RSCP.EMS.Mode', 'Ok');
				IPS_SetVariableProfileAssociation("RSCP.EMS.Mode", 0, "Idle", "", 0xFFFFFF);
				IPS_SetVariableProfileAssociation("RSCP.EMS.Mode", 1, "Entladen", "", 0xFF0000);
				IPS_SetVariableProfileAssociation("RSCP.EMS.Mode", 2, "Laden", "", 0x008000);
			}
			if (!IPS_VariableProfileExists('RSCP.Power.W.i')) {
				IPS_CreateVariableProfile('RSCP.Power.W.i', 1);
				IPS_SetVariableProfileIcon('RSCP.Power.W.i', 'Energy');
				IPS_SetVariableProfileValues("RSCP.Power.W.i", 0, 50000, 500);
				IPS_SetVariableProfileText("RSCP.Power.W.i", "", " W");
			}
			if (!IPS_VariableProfileExists('RSCP.Power.W')) {
				IPS_CreateVariableProfile('RSCP.Power.W', 2);
				IPS_SetVariableProfileIcon('RSCP.Power.W', 'Energy');
				IPS_SetVariableProfileDigits("RSCP.Power.W", 0);
				IPS_SetVariableProfileValues("RSCP.Power.W", 0, 50000, 0 );
				IPS_SetVariableProfileText("RSCP.Power.W", "", " W");
			}
			if (!IPS_VariableProfileExists('RSCP.SOC')) {
				IPS_CreateVariableProfile('RSCP.SOC', 2);
				IPS_SetVariableProfileIcon('RSCP.SOC', 'Battery');
				IPS_SetVariableProfileDigits("RSCP.SOC", 1);
				IPS_SetVariableProfileValues("RSCP.SOC", 0, 100, 1);
				IPS_SetVariableProfileText("RSCP.SOC", "", "%");
			}
		}

		private function registerVariables()
		{

			$NewRows = static::$Variables;
			$NewPos = 0;
			$this->SendDebug('Variablen', $this->ReadPropertyString('Variables'), 0);
			$Variables = json_decode($this->ReadPropertyString('Variables'), true);
			foreach ($Variables as $Variable) {
				@$this->MaintainVariable($Variable['Ident'], $Variable['Name'], $Variable['VarType'], $Variable['Profile'], $Variable['Pos'], $Variable['Keep']);
				if ($Variable['Action'] && $Variable['Keep']) {
					$this->EnableAction($Variable['Ident']);
				}
				foreach ($NewRows as $Index => $Row) {
					if ($Variable['Ident'] == str_replace(' ', '', $Row[0])) {
						unset($NewRows[$Index]);
					}
				}
				if ($NewPos < $Variable['Pos']) {
					$NewPos = $Variable['Pos'];
				}
			}

			if (count($NewRows) != 0) {
				foreach ($NewRows as $NewVariable) {
					$Variables[] = [
						'Ident'        => str_replace(' ', '', $NewVariable[0]),
						'Name'         => $this->Translate($NewVariable[0]),
						'Tag'		   => $NewVariable[1],
						'MQTT'		   => $NewVariable[2],
						'VarType'      => $NewVariable[3],
						'Profile'      => $NewVariable[4],
						'Factor'       => $NewVariable[5],
						'Action'       => $NewVariable[6],
						'Pos'          => ++$NewPos,
						'Keep'         => $NewVariable[7]
					];
				}
				IPS_SetProperty($this->InstanceID, 'Variables', json_encode($Variables));
				IPS_ApplyChanges($this->InstanceID);
				return;
        	}
		}

		protected function sendMQTT($Topic, $Payload)
		{
			$mqtt['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
			$mqtt['PacketType'] = 3;
			$mqtt['QualityOfService'] = 0;
			$mqtt['Retain'] = false;
			$mqtt['Topic'] = $Topic;
			$mqtt['Payload'] = $Payload;
			$mqttJSON = json_encode($mqtt, JSON_UNESCAPED_SLASHES);
			$mqttJSON = json_encode($mqtt);
			$this->SendDebug(__FUNCTION__ . 'MQTT', $mqttJSON, 0);
			$result = @$this->SendDataToParent($mqttJSON);
			$this->SendDebug(__FUNCTION__ . 'MQTT', $result, 0);

			if ($result === false ) {
				$last_error = error_get_last();
				echo $last_error['message'];
			}
		}

		// Mapping Definition für die MQTT Werte
		private static $Variables = [
			// IDENT							RSCP TAG 									MQTT Topic (ohne e3dc)				Variablen Typ		Var Profil	  			Faktor  ACTION  KEEP
			['solar_power'					, 'TAG_EMS_POWER_PV'						, '/solar/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['battery_power'				, 'TAG_EMS_POWER_BAT' 						, '/battery/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['home_power'					, 'TAG_EMS_POWER_HOME'						, '/home/power'						, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['grid_power'					, 'TAG_EMS_POWER_GRID'						, '/grid/power'						, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['addon_power'					, 'TAG_EMS_POWER_ADD'						, '/addon/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			, -1	, false, true],
			['battery_rsoc'					, 'TAG_BAT_RSOC'							, '/battery/rsoc'					, VARIABLETYPE_FLOAT, 	'RSCP.SOC'				,  1	, false, true],
			['battery_cycles'				, 'TAG_BAT_CHARGE_CYCLES'					, '/battery/cycles'					, VARIABLETYPE_INTEGER, ''  		 			,  1	, false, true],
			['battery_status'				, 'TAG_BAT_STATUS_CODE'						, '/battery/status'					, VARIABLETYPE_INTEGER, ''  		 			,  1	, false, true],
			['ems_max_discharge_power'		, 'TAG_EMS_MAX_DISCHARGE_POWER'				, '/ems/max_discharge/power'		, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, false, true],
			['ems_max_charge_power'			, 'TAG_EMS_MAX_CHARGE_POWER'				, '/ems/max_charge/power'			, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, false, true],
			['ems_wetaher_charge_active'	, 'TAG_EMS_WEATHER_REGULATED_CHARGE_ENABLED', '/ems/weather_regulation'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_set_power_power'			, 'TAG_EMS_SET_POWER'						, '/ems/set_power/power'			, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, false, true],
			['system_software'				, 'TAG_INFO_SW_RELEASE'						, '/system/software'				, VARIABLETYPE_STRING, 	''  		 			,  1	, false, true],
			['system_peak_power'			, 'TAG_EMS_INSTALLED_PEAK_POWER'			, '/system/installed_peak_power'	, VARIABLETYPE_INTEGER, ''						,  1	, false, true],
			['ems_mode'						, 'TAG_EMS_MODE'							, '/mode'							, VARIABLETYPE_INTEGER, 'RSCP.EMS.Mode'  		,  1	, false, true],
			['ems_charging_lock'			, 'TAG_EMS_STATUS'							, '/ems/charging_lock'				, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_discharging_lock'			, 'TAG_EMS_STATUS'							, '/ems/discharging_lock'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_emergency_power_available', 'TAG_EMS_STATUS'							, '/ems/emergency_power_available'	, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_charging_throttled'		, 'TAG_EMS_STATUS'							, '/ems/charging_throttled'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['grid_in_limit'				, 'TAG_EMS_STATUS'							, '/grid_in_limit'					, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_charging_time_lock'		, 'TAG_EMS_STATUS'							, '/ems/charging_time_lock'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_discharging_time_lockr'	, 'TAG_EMS_STATUS'							, '/ems/discharging_time_lock'		, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['ems_coupling_mode'			, 'TAG_EMS_COUPLING_MODE'					, '/coupling/mode'					, VARIABLETYPE_INTEGER, 'RSCP.Coupling.Mode' 	,  1	, false, true],
			
		];
	}	
	