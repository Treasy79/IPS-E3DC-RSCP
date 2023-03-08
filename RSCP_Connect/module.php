<?php

declare(strict_types=1);
require_once __DIR__ . '/../libs/RSCPModule.php';

	class RSCP2MQTT_Connect extends RSCPModule
	{
		

		/////////// Commands for E3DC RSCP2MQTT /////////////

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
				$Payload = '1';
			else
				$Payload = '0';
			$this->sendMQTT($Topic, $Payload);
		}

		public function set_power_limits_mode(bool $value)
		{	
			$Topic = 'e3dc/set/power_limits';
			if ($value)
				$Payload = '1';
			else
				$Payload = '0';
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

		public function RequestAction($Ident, $Value)
		{
			switch ($Ident){
				case "ems_power_limits_used":
					$this->set_power_limits_mode($Value);
					break;

				case "ems_wetaher_charge_active":
					$this->set_weather_regulation($Value);
					break;
				
				case "ems_max_discharge_power":
					$this->set_max_discharge_power($Value);
					break;
					
				case "ems_max_charge_power":
					$this->set_max_charge_power($Value);
					break;

				default:
					throw new Exception("Invalid Ident");

			}
		}

		// Mapping Definition für die MQTT Werte - RSCP2MQTT
		public static $Variables = [
		// 	NSPACE  	POS		IDENT							RSCP TAG 									MQTT Topic								Variablen Typ			Var Profil	  			Faktor  ACTION  KEEP
			// EMS
			['HEADER'	,100	,'EMS'							, ''										, ''									, ''				, 	''						,  1	, false, false],
			['EMS'		,101	,'solar_power'					, 'TAG_EMS_POWER_PV'						, 'e3dc/solar/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['EMS'		,102	,'battery_power'				, 'TAG_EMS_POWER_BAT' 						, 'e3dc/battery/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['EMS'		,103	,'home_power'					, 'TAG_EMS_POWER_HOME'						, 'e3dc/home/power'						, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['EMS'		,104	,'grid_power'					, 'TAG_EMS_POWER_GRID'						, 'e3dc/grid/power'						, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			,  1	, false, true],
			['EMS'		,105	,'addon_power'					, 'TAG_EMS_POWER_ADD'						, 'e3dc/addon/power'					, VARIABLETYPE_FLOAT, 	'RSCP.Power.W'			, -1	, false, true],
			['EMS'		,110	,'ems_max_discharge_power'		, 'TAG_EMS_MAX_DISCHARGE_POWER'				, 'e3dc/ems/max_discharge/power'		, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, true , true],
			['EMS'		,111	,'ems_max_charge_power'			, 'TAG_EMS_MAX_CHARGE_POWER'				, 'e3dc/ems/max_charge/power'			, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, true , true],
			['EMS'		,112	,'ems_power_limits_used'		, 'TAG_EMS_POWER_LIMITS_USED'				, 'e3dc/ems/power_limits'				, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, true , true],
			['EMS'		,113	,'ems_wetaher_charge_active'	, 'TAG_EMS_WEATHER_REGULATED_CHARGE_ENABLED', 'e3dc/ems/weather_regulation'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, true , true],
			['EMS'		,120	,'ems_charging_lock'			, 'TAG_EMS_STATUS'							, 'e3dc/ems/charging_lock'				, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,121	,'ems_discharging_lock'			, 'TAG_EMS_STATUS'							, 'e3dc/ems/discharging_lock'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,122	,'ems_emergency_power_available', 'TAG_EMS_STATUS'							, 'e3dc/ems/emergency_power_available'	, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,123	,'ems_charging_throttled'		, 'TAG_EMS_STATUS'							, 'e3dc/ems/charging_throttled'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,124	,'grid_in_limit'				, 'TAG_EMS_STATUS'							, 'e3dc/grid_in_limit'					, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,125	,'ems_charging_time_lock'		, 'TAG_EMS_STATUS'							, 'e3dc/ems/charging_time_lock'			, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,126	,'ems_discharging_time_lockr'	, 'TAG_EMS_STATUS'							, 'e3dc/ems/discharging_time_lock'		, VARIABLETYPE_BOOLEAN, '~Switch'	 			,  1	, false, true],
			['EMS'		,130	,'autarky'						, 'TAG_EMS_AUTARKY'							, 'e3dc/autarky'						, VARIABLETYPE_FLOAT, 	'RSCP.Percent' 			,  1	, false, true],
			['EMS'		,131	,'consumed'						, 'TAG_EMS_CONSUMED'						, 'e3dc/consumed'						, VARIABLETYPE_FLOAT, 	'RSCP.Percent' 			,  1	, false, true],
			['EMS'		,140	,'ems_set_power_power'			, 'TAG_EMS_SET_POWER'						, 'e3dc/ems/set_power/power'			, VARIABLETYPE_INTEGER, 'RSCP.Power.W.i'		,  1	, false, true],
			['EMS'		,150	,'ems_mode'						, 'TAG_EMS_MODE'							, 'e3dc/mode'							, VARIABLETYPE_INTEGER, 'RSCP.EMS.Mode'  		,  1	, false, true],
			['EMS'		,151	,'ems_coupling_mode'			, 'TAG_EMS_COUPLING_MODE'					, 'e3dc/coupling/mode'					, VARIABLETYPE_INTEGER, 'RSCP.Coupling.Mode' 	,  1	, false, true],
			['EMS'		,152	,'system_peak_power'			, 'TAG_EMS_INSTALLED_PEAK_POWER'			, 'e3dc/system/installed_peak_power'	, VARIABLETYPE_INTEGER, ''						,  1	, false, true],

			// Battery
			['HEADER'	,200	,'BATTERY'						, ''										, ''									, ''				, 	''						,  1	, false, false],
			['BAT'		,201	,'battery_rsoc'					, 'TAG_BAT_RSOC'							, 'e3dc/battery/rsoc'					, VARIABLETYPE_FLOAT, 	'RSCP.SOC'				,  1	, false, true],
			['BAT'		,202	,'battery_cycles'				, 'TAG_BAT_CHARGE_CYCLES'					, 'e3dc/battery/cycles'					, VARIABLETYPE_INTEGER, ''  		 			,  1	, false, true],
			['BAT'		,203	,'battery_status'				, 'TAG_BAT_STATUS_CODE'						, 'e3dc/battery/status'					, VARIABLETYPE_INTEGER, ''  		 			,  1	, false, true],
			
			// PVI
			['HEADER'	,300	,'PVI'							, ''										, ''									, ''				, 	''						,  1	, false, false],
			['PVI'		,301	,'pvi_power_string1'			, 'TAG_PVI_DC_POWER'						, 'e3dc/pvi/power/string_1'				, VARIABLETYPE_FLOAT, 	'RSCP.Power.W' 			,  1	, false, false],
			['PVI'		,302	,'pvi_power_string2'			, 'TAG_PVI_DC_POWER'						, 'e3dc/pvi/power/string_2'				, VARIABLETYPE_FLOAT, 	'RSCP.Power.W' 			,  1	, false, false],

			// DATABASE VALUES
			['HEADER'	,800	,'DATABASE'						, ''										, ''									, ''				, 	''						,  1	, false, false],
			['DB'		,830	,'year_solar_energy'			, 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/solar/energy'				, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],
			['DB'		,831	,'year_battery_energy_charge'	, 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/battery/energy/charge'		, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],
			['DB'		,832	,'year_battery_energy_discharge', 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/battery/energy/discharge'	, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],
			['DB'		,833	,'year_home_energy'				, 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/home/energy'				, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],
			['DB'		,834	,'year_grid_energy_in'			, 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/grid/energy/in'			, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],
			['DB'		,835	,'year_grid_energy_out'			, 'TAG_DB_HISTORY_DATA_YEAR'				, 'e3dc/year/grid/energy/out'			, VARIABLETYPE_FLOAT, 	'~Electricity' 			,  1	, false, true],

			// INFO
			['HEADER'	,900	,'INFO'		 					, ''										, ''									, ''				, 	''						,  1	, false, false],
			['INFO'		,901	,'system_software'				, 'TAG_INFO_SW_RELEASE'						, 'e3dc/system/software'				, VARIABLETYPE_STRING, 	''  		 			,  1	, false, true],
			
		];
	}	
	