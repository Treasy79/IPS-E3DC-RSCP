### IP-Symcon Library für die Steuerung des E3DC Hauskraftwerkes über das RSCP Protokoll
 
Die Nutzung des Moduls geschieht auf eigene Gefahr ohne Gewähr.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Module](#4-module)
5. [ChangeLog](#5-changelog)

## 1. Funktionsumfang
Die E3DC-RSCP Library stellt aktuell 1 Modul zur verfügung mit denen das E3DC Hauskraftwerk über das RSCP Protokoll ausgelesen und gesteuert werden kann. Da für das RSCP Protokoll leider keine direkte Implementierung in PHP zur Verfügung steht, nutzen diese Module das MQTT Protokoll um die Daten zu empfangen und zu senden.

Hierzu wird immer eine zusätzliche Software benötigt, welche das RSCP Protokoll auf MQTT und umgekehrt umsetzt.

Einen genauen Funktionsumfang des jeweiligen Moduls und die benötigten Voraussetzungen wird in der Modul Readme detailiert beschrieben.

## 2. Systemanforderungen
- IP-Symcon ab Version 6.0

## 3. Installation

### Installation des Moduls
Das Modul ist im Symcon Modul Store verfügbar und kann von dort einfach installiert werden. Solang das Modul noch im Beta Kanal veröffentlich ist muss mit dem genauen Namen "E3DC RSCP Connect (MQTT)" gesucht werden.

## 4. Module

### 4.1. RSCP2MQTT_Connect
Modul um die Steuerung es E3DC Hauskraftwerkes über die RSCP2MQTT Bridge zu implementieren.
https://github.com/Treasy79/IPS-E3DC-RSCP/blob/main/RSCP2MQTT_Connect/README.md

## 5. ChangeLog
Änderungshistorie

### Version 1.1 Beta Build 20231113
* Implementierung Wallbox Funktionen
* Neue Option zur Emulation der geänderten Variablen Stati

### Version 1.0 Beta Build 20231103
* Neue Modulstruktur ( zur möglichen Unterstützung von unterschiedlichen RSCP-MQTT Bridges)
* Variablen Auswahl zum Update der RSCP Werte erfolgt nun strukturiert über eine Tree-Liste

### Version 0.8 Beta
* Initialer Commit
  