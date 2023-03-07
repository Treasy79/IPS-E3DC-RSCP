<?php

declare(strict_types=1);
	class RSCPModule extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();
			$this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

			$this->RegisterPropertyString('TOPIC', 'e3dc');
			$this->RegisterPropertyBoolean('Name', false);

			$Variables = [];
        	foreach (static::$Variables as $Pos => $Variable) {
				$Variables[] = [
					'Namespace'	  	=> $this->Translate($Variable[0]),
					'Ident'        	=> str_replace(' ', '', $Variable[2]),
					'Name'         	=> $this->Translate($Variable[2]),
					'Tag'		   	=> $Variable[3],
					'MQTT'		   	=> $Variable[4],
					'VarType'      	=> $Variable[5],
					'Profile'      	=> $Variable[6],
					'Factor'       	=> $Variable[7],
					'Action'       	=> $Variable[8],
					'Pos'          	=> $Variable[1],
					'Keep'         	=> $Variable[9],
					'rowColor'     	=> $this->set_color($Variable[0]),
					'editable'     	=> $this->set_editable($Variable[0])
				];
        	}	
			$this->RegisterPropertyString('Variables', json_encode($Variables));
			$this->RegisterAttributeString('Variables', json_encode($Variables));
			$this->SendDebug('Variablen_Create', json_encode($Variables), 0);
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
			$this->UpdateFormField('Variables', "value", $this->ReadAttributeString('Variables'));
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
						if ($Variable['Keep']){
							if (fnmatch( $Variable['MQTT'], $Buffer->Topic)) {
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
		}

		public function resetVariables()
		{
			$NewRows = static::$Variables;
			$Variables = [];
			foreach ($NewRows as $Pos => $Variable) {
				$Variables[] = [
					'Namespace'	  	=> $this->Translate($Variable[0]),
					'Ident'      	=> str_replace(' ', '', $Variable[2]),
					'Name'         	=> $this->Translate($Variable[2]),
					'Tag'		   	=> $Variable[3],
					'MQTT'		   	=> $Variable[4],
					'VarType'      	=> $Variable[5],
					'Profile'      	=> $Variable[6],
					'Factor'	   	=> $Variable[7],
					'Action'       	=> $Variable[8],
					'Pos'          	=> $Variable[1],
					'Keep'         	=> $Variable[9],
					'rowColor'	   	=> $this->set_color($Variable[0]),
					'editable'     	=> $this->set_editable($Variable[0])
				];
			}
			$this->SendDebug("Variabel_Reset", json_encode($Variables) ,0 );
			IPS_SetProperty($this->InstanceID, 'Variables', json_encode($Variables));
			IPS_ApplyChanges($this->InstanceID);
			return;
		}

		public function update_Variable_position()
		{
			$Variables = json_decode($this->ReadPropertyString('Variables'), true);
			foreach ($Variables as $Variable) {
				if ($Variable['Keep']){
					if ( IPS_GetObjectIDByIdent($Variable['Ident'], $this->InstanceID) != false); {
						IPS_SetPosition(IPS_GetObjectIDByIdent($Variable['Ident'], $this->InstanceID), $Variable['Pos'] );
					}
				}
			}
		}

		public function update_Variable_name()
		{
			$Variables = json_decode($this->ReadPropertyString('Variables'), true);
			foreach ($Variables as $Variable) {
				if ($Variable['Keep']){
					if ( IPS_GetObjectIDByIdent($Variable['Ident'], $this->InstanceID) != false); {
						IPS_SetName(IPS_GetObjectIDByIdent($Variable['Ident'], $this->InstanceID), $this->set_name($Variable['Namespace'], $Variable['Name']) );
					}
				}
			}
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
			if (!IPS_VariableProfileExists('RSCP.Percent')) {
				IPS_CreateVariableProfile('RSCP.Percent', 2);
				IPS_SetVariableProfileDigits("RSCP.Percent", 1);
				IPS_SetVariableProfileValues("RSCP.Percent", 0, 100, 1);
				IPS_SetVariableProfileText("RSCP.Percent", "", "%");
			}


		}

		private function registerVariables()
		{

			$NewRows = static::$Variables;
			$this->SendDebug('Variablen_Reg1', $this->ReadPropertyString('Variables'), 0);
			$Variables = json_decode($this->ReadPropertyString('Variables'), true);
			foreach ($Variables as $pos => $Variable) {
				@$this->MaintainVariable($Variable['Ident'], $this->set_name($Variable['Namespace'], $Variable['Name']), $Variable['VarType'], $Variable['Profile'], $Variable['Pos'], $Variable['Keep']);
				if ($Variable['Action'] && $Variable['Keep']) {
					$this->EnableAction($Variable['Ident']);
				}
				foreach ($NewRows as $Index => $Row) {
					if ($Variable['Ident'] == str_replace(' ', '', $Row[2])) {
						unset($NewRows[$Index]);
					}
				}
			}



			if (count($NewRows) != 0) {
				foreach ($NewRows as $NewVariable) {
					$Variables[] = [
						'Namespace'	  	=> $this->Translate($NewVariable[0]),
						'Ident'        => str_replace(' ', '', $NewVariable[2]),
						'Name'         => $this->Translate($NewVariable[2]),
						'Tag'		   => $NewVariable[3],
						'MQTT'		   => $NewVariable[4],
						'VarType'      => $NewVariable[5],
						'Profile'      => $NewVariable[6],
						'Factor'       => $NewVariable[7],
						'Action'       => $NewVariable[8],
						'Pos'          => $NewVariable[1],
						'Keep'         => $NewVariable[9],
						'rowColor'	   => $this->set_color($NewVariable[0]),
						'editable'     => $this->set_editable($NewVariable[0])
					];
				}
				IPS_SetProperty($this->InstanceID, 'Variables', json_encode($Variables));
				$this->SendDebug('Variablen Register', json_encode($Variables), 0);
				IPS_ApplyChanges($this->InstanceID);
				return;
        	}
			
		}

		private function set_color($ident)
		{
			if ($ident == 'HEADER'){
				return '#FFFFC0';
			}
			else{
				return '';
			}	
		}

		private function set_editable($ident)
		{
			if ($ident == 'HEADER'){
				return false;
			}
			else{
				return true;
			}	
		}

		private function set_name($ns , $name)
		{
			if ($this->ReadPropertyBoolean('Name')){
				return $ns.'_'.$name;
			}
			else{
				return $name;
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
	}	
	