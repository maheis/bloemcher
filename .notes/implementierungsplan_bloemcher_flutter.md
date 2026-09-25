# Implementierungsplan Bloemcher Flutter

## Ziel

Bloemcher wird eine lokale Flutter-App zur Verwaltung von Zimmerpflanzen. Die App soll Android nativ laufen und zugleich als Linux-Desktop-App startbar sein. Stil und Architektur orientieren sich an den bestehenden Apps wie SimplePresent, VolleyAce, Playsheet und der neuen Fibu-App: lokale Datenhaltung, klare Material-3-Oberfläche, schnelle Aktionen, keine Serverpflicht.

## PHP-Referenz

Quelle: `.notes/plants`

Wesentliche PHP-Funktionen:

- `plants_table.php`: Pflanzenliste, Raumfilter, Sortierung, letzte Pflege, Aktionen pro Pflanze.
- `plants_drip.php`: fällige Pflanzen gießen, Wasserbedarf summieren, Gießaktivität speichern.
- `plants_fertilise.php`: Düngen für einzelne oder alle Pflanzen.
- `plants_repotting.php`: Umtopfen als Aktivität speichern.
- `plants_activity.php`: Aktivitäten pro Raum/Pflanze anzeigen und editieren.
- `database_t_plants.php`: Pflanzenstamm.
- `database_t_plants_activity.php`: Pflegeaktivitäten.

## Datenmodell

### Plant

- `id`
- `room`
- `name`
- `sortOrder`
- `latinName`
- `waterPeriodDays`
- `waterVolumeMl`
- `comment`
- `isArchived` oder Raum `☠️` als Legacy-Kompatibilität

### PlantActivity

- `id`
- `date`
- `plantId`
- `type`: `water`, `fertilise`, `repot`
- `comment`

## Architektur

- `lib/main.dart`: App-Start, lokaler Datenbankpfad, Repository und Controller initialisieren.
- `lib/app.dart`: Root-App, Navigation und Schnellaktionen.
- `lib/models.dart`: Domainmodelle und Formatierung.
- `lib/app_controller.dart`: Business-Logik.
- `lib/repository/app_repository.dart`: Sembast-Persistenz und Seed-Daten.
- `lib/pages/*`: Übersicht, Pflanzenliste, Pflegeaktionen, Aktivitätsverlauf, Editoren, Backup.

## Lokale Datenhaltung

- Sembast-Datenbank im App-Dokumentordner.
- Pfad: `bloemcher/bloemcher.db`.
- JSON-Export später analog Fibu möglich.

## Kernfunktionen Phase 1

1. Flutter-Projekt für Android und Linux anlegen.
2. Domainmodelle `Plant` und `PlantActivity` implementieren.
3. Repository mit lokalen Stores und Seed-Daten implementieren.
4. Controller mit Pflanzen-, Raum- und Pflege-Logik implementieren.
5. Dashboard mit fälligen Pflanzen und Wasserbedarf bauen.
6. Pflanzenliste mit Raumfilter und Statuskarten bauen.
7. Schnellaktionen für Gießen, Düngen und Umtopfen bauen.
8. Aktivitätsverlauf pro Pflanze bauen.
9. `flutter analyze` und fokussierte Tests grün bekommen.

## Kernlogik

### Fälliges Gießen

Eine Pflanze ist fällig, wenn keine Gießaktivität existiert oder das letzte Gießen länger als `waterPeriodDays` zurückliegt.

### Umtopfen

Die PHP-App nutzt ca. 730 Tage als Statusgrenze. Die Flutter-App übernimmt das als Standard für den Umtopf-Status.

### Düngen

Düngen kann für eine einzelne Pflanze oder gesammelt für alle aktiven Pflanzen erfolgen. Doppelte Düngungen am selben Tag sollen später optional verhindert werden.

## UI-Richtung

- Material 3, klare ruhige Karten, grün als Hauptfarbe, warme Akzente für Erde/Umtopfen.
- Startscreen ist direkt die Pflegeübersicht, keine Landingpage.
- Bottom Navigation: Übersicht, Pflanzen, Aktivitäten.
- Floating Action Button oder AppBar-Aktion für neue Pflanze/Pflege.
- Icons über Material Icons: Wasser, Blatt/Pflanze, Kompost/Erde, Historie.

## Phase 2

- Pflanzeneditor vollständig.
- Reihenfolge innerhalb eines Raums verschieben.
- Archivieren/Todestag analog PHP `☠️`.
- Backup/Export.
- Linux-Desktop-Polish.
- Android Build/Release-Konfiguration.

## Erste Umsetzung

Start mit Phase 1: Scaffold, Modelle, Repository, Controller, Übersicht, Pflanzenliste, Pflegeaktionen und Tests.
