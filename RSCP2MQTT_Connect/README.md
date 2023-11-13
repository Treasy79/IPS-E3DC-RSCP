### IP-Symcon Modul für die Steuerung des E3DC Hauskraftwerkes über das RSCP Protokoll auf Basis von RSCP2MQTT
 
Die Nutzung des Moduls geschieht auf eigene Gefahr ohne Gewähr.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Module](#4-module)
5. [ChangeLog](#5-changelog)

## 1. Funktionsumfang

Mit diesem Modul kann man das E3DC Hauskraftwerk über das RSCP Protokoll steuern. Da für das RSCP Protokoll leider keine direkte Implementierung in PHP zur Verfügung steht, nutzt dieses Modul die von der RSCP2MQTT - Bridge (https://github.com/pvtom/rscp2mqtt) von PVTOM zur Verfügung gestellten MQTT-Topics zum empfangen und zum senden. RSCP2MQTT ist auf einem Raspbery PI lauffähig.
Danke an dieser Stelle auch an PVTOM für die Implementierung der SET_POWER Befehle.

Der Hauptaugenmerk liegt zu Beginn bei dem Modul auf der Steuerung des Hauskraftwerkes, da hier viel mehr Möglichkeiten zur Verfügung stehen im Vergleich zu der alternativ möglichen Anbindung über Modbus TCP.

Es werden aber nach und nach weitere Status Variablen/Werte integriert werden.

## 2. Systemanforderungen
- IP-Symcon ab Version 6.0

## 3. Installation

### RSCP2MQTT
Als Voraussetzung für die Nutzung diese Moduls muss die RSCP2MQTT - Bridge die Werte per MQTT an den Symcon MQTT Server senden. Eine detaillierte Installationsanleitung für den RSCP2MQTT findet ihr unter:

https://github.com/pvtom/rscp2mqtt/blob/main/README.md

### Erstellen der Modul-Instanz
Die Modul Instanz RSCP2MQTT_Connect kann in der Verwaltungskonsole erstellt werden.

### Einrichten der Modul-Instanz
Nachdem eine Instanz des Moduls angelegt wurde, muss diese eingerichtet werden.

Da das Modul die Werte direkt von dem MQTT Server als Parent erhält, sind keine weiteren Einstellungen für die Verbindung in der Modul Instanz notwendig.

### Bestandteile der Instanz Konfiguration
#### Auswahl Liste
Für die möglichen Statusvariablen gibt es eine strutkurierte Auswahlliste in Baumstruktur, in der man Variablen, welche man benötigt, einfach auswählen kann kann. Sobald man eine Variable wieder deaktiviert, wird diese auch gelöscht! Also vorsichtig damit umgehen, um nicht geloggte Daten und Objektverweise zu verlieren

#### Variablen Namen mit Namespace anlegen
Mit dem Schlater kann man entscheiden ob die Variablen im Namen den Namespace vom jeweiligen Knoten (EMA, BATTERY, ...) im Text vorangestellt bekommen, um die Übersichtlichkeit zu verbessern.

#### Button SORT VARIABLES
Mit dieser Funktion werden die Sortierpositionen der Variablen gemäß der neuen vorgegebenen Deafult Liste aktualisiert.

#### Button RENAME VARIABLES
Mit dieser Funktion werden die Variablen Namen gemäß der neuen Liste und Überstzung aktualisiert

## 4. Module

### 4.1. RSCP2MQTT_Connect

Das Modul "RSCP2MQTT_Connect" dient als Schnittstelle zu einem lokal installierten E3DC Hauskraftwerk über das RSCP Protokoll via MQTT Server. Es liefert die Daten des E3DC Hauskraftwerkes als Statusvariablen und bietet die Möglichkeit Einstellungen des Hauskraftwerkes zu ändern. Vor allem bietet das Modul auch die Möglichkeit über die "SET_POWER" Befehle das EMS vom Hauskraftwerk direkt zu steuern, um so das Laden/Entladen direkt zu beeinflussen.

#### 4.1.1 Status Variablen
Die RSCP2MQTT Bridge sendet Updates zu den verschiedenen Variablen nur bei Änderung einer Variablen. Somit kann es sein das Variablen länger kein Update erhalten, wenn keine Änderung vorliegt.

Im folgenden werden die verfügbaren Statusvariablen aufgelistet:

!!!! Muss noch ergänzt werden!!!!

	"Leistung PV" 
	"Leistung Batterie" 
	"Leistung Haus" 
	"Leistung Netz" 
	"Leistung Ext. Quelle" 
	"SOC" 
	"Batterie Zyklen" 
	"Batterie Status" 
	"Maximale Entladeleistung" 
	"Maximale Ladeleistung" 
	"Wettergeführtes Laden" 
	"Aktueller Set Power Wert" 
	"Software Version" 
	"Installierte Leistung" 
	"EMS Modus" 
	"Ladesperre" 
	"Entladesperre" 
	"Notsrom verfügbar" 
	"Ladung begrenz" 
	"Einspeisung Limitiert" 
	"Lade Sperrzeit" 
	"Entlade Sperrzeit" 
	"Betriebsmodus" 


#### 4.1.2. Funktionen

#### force_update()
Mit der Funktion sendet die RSCP2MQTT-Bridge für alle Werte die Daten. Somit auch für Variablen welche sich nicht geändert haben.

#### set_refresh_interval(int $value)
Diese Funktion ändert das Update-Intervall mit der die RSCP2MQTT-Bridge die Werte sendet. Es sind Werte zwischen 1 und 10 Sekunden möglich. Damit werden zu Laufzeit die in der Konfiguration der RSCP2MQTT-Bridge definierten Update Zeit.

#### set_manual_charge(int $value)
Mit dieser Funktion kann man eine manuelle Speicherladung starten. Es muss der Wert in Wh übergeben werden. Dieser kann in 100Wh Schritten erfolgen.

#### set_weather_regulation(bool $value)
Mit der Funktion kann man das Wetter geführte Laden aktivieren/deaktivieren

#### set_power_limits_mode(bool $value)
Die Funktion steuert ob die Lade-/Entladeleistung gesetzt werden können oder ob die Werte vom E3DC automatisch gesetzt werden.

#### set_max_discharge_power(int $value)
Mit dieser Funktion kann man den Wert für die Maximale Entladeleistung des Hauskraftwerkes in Watt setzen. Es können Werte in 100 Watt Schritten gesetzt werden. Der Höchstwert hängt vom vorhandenen Hauskraftwerk ab und muss dementsprechend gesetzt werden.

#### set_max_charge_power(int $value)
Mit dieser Funktion kann man den Wert für die Maximale Entladeleistung des Hauskraftwerkes in Watt setzen. Es können Werte in 100 Watt Schritten gesetzt werden. Der Höchstwert hängt vom vorhandenen Hauskraftwerk ab und muss dementsprechend gesetzt werden.

### SET_POWER Funktionen
Mit den SET_Power Funktionen kann man dass EMS direkt beeinflussen. Somit hat man alle Möglichkeiten das Lade/Entladeverhalten des Systems komplett selbst zu steuern. Mit diesen Befehlen werden keine Werte fix im Hauskraftwerk gesetzt, sondern die Werte von diesen Befehlen sind nur wenige Sekunden gültig und müssen daher zur Steuerung ständig wiederholt werden. Kommt nach ca 5 Sekunden nicht ein erneuter Wert, fällt das Hauskraftwerk wieder in seinen normnalen Modus zurück.
Über den Parameter $cycles kann man übergeben, für wie viele Intervalle () der Befehl wiederholt werden soll.. Somit muss man nicht alle paar Sekunden den Befehl wiederholen, wenn man den Modus und die Leistungswerte nicht ändern will. Die Intervalldauer entspricht der im RSCP2MQTT eingestellten Intervallzeit.  Wenn die Intervalle von einem Befehl noch nicht abgelaufen sind, können diese mit einem neuen Befehl einfach überschrieben werden.

!!!!! Mit diesen Befehlen muss man sehr behutsam umgehen, da damit auch evtl. Einspeise-Limits übersteuert werden können. Zusätzlich ist es möglich dass die Ladeleistung höher als die PV Leistung eingestellt wird, und somit auch Netzstrom in den Akku geladen wird. Das Verwenden der Befehle erflogt auf eigene Verantwortung !!! 

#### set_power_mode_auto()
Der Befehl setzt das Hauskraftwerk in den Auto Modus zurück, so dass es wieder im "Normalen" Modus läuft.

#### set_power_mode_idle(int $cycles)
Dieser Befehl versetz das Hauskraftwerk in den "IDLE" Mode, es wird in dem Modus dann der Akku weder geladen noch entladen.
Über den Parameter $cycles kann man übergeben, für wie viele Intervalle () der Befehl wiederholt werden soll.

#### set_power_mode_discharge(int $value, int $cycles)
Dieser Befehl versetzt das Hauskraftwerk in den "ENTLADE" Mode, es wird in dem Modus, der Akku mit dem übergebenen Wert entladen.
Über den Parameter $value übergibt man die Entladeleistung in Watt.
Über den Parameter $cycles kann man übergeben, für wie viele Intervalle () der Befehl wiederholt werden soll.

#### set_power_mode_charge(int $value, int $cycles)
Dieser Befehl versetzt das Hauskraftwerk in den "LADE" Mode, es wird in dem Modus, der Akku mit dem übergebenen Wert geladen.
Über den Parameter $value übergibt man die Ladeleistung in Watt. 
Über den Parameter $cycles kann man übergeben, für wie viele Intervalle () der Befehl wiederholt werden soll.
!! Hier können bei nicht reinen DC Systemen auch Werte übergeben werden, welche höher als die PV Leistung sind, und somit eine Netzladung auslösen!!

#### set_power_mode_gridcharge(int $value, int $cycles)
Dieser Befehl versetzt das Hauskraftwerk in den "NETZLADE" Mode, es wird in dem Modus, der Akku mit dem übergebenen Wert aus dem Netz geladen.
Über den Parameter $value übergibt man die Ladeleistung in Watt. 
Über den Parameter $cycles kann man übergeben, für wie viele Intervalle () der Befehl wiederholt werden soll.
!! Hier wird aus dem NETZ geladen !!