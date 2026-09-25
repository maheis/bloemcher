# Bloemcher

Bloemcher ist eine lokale App zur Verwaltung von Zimmerpflanzen. Sie hilft beim Ueberblick ueber Pflanzen, Raeume und Pflegeaktivitaeten wie Giessen, Duengen und Umtopfen.

## Funktionen

- Pflanzen mit Raum, Name, Giessperiode und Wassermenge verwalten
- Pflegeaktivitaeten erfassen
- Faellige Pflanzen zum Giessen anzeigen
- Gesamt-Wasserbedarf berechnen
- Pflanzen nach Raum filtern
- Pflanzen archivieren
- Aktivitaetsverlauf anzeigen
- Gemeinsame UI-Settings fuer Schrift, Textgroesse, Akzentfarbe, Highlight-Farbe und hell/dunkel

## Plattformen

- Android
- Linux Desktop

## Technik

- Flutter / Dart
- Sembast fuer lokale Persistenz
- `path_provider` fuer App-Dokumentordner
- `intl` fuer Datum und Formatierung

## Entwicklung

```bash
flutter pub get
flutter analyze
flutter test test/widget_test.dart --reporter compact
flutter build linux --debug
```

Android-Builds benoetigen lokal ein vollstaendiges JDK mit `javac`.

## Datenschutz

Bloemcher speichert Pflanzendaten und Pflegeaktivitaeten lokal auf dem Geraet. Es gibt derzeit keine Cloud-Synchronisierung und keine automatische Serveruebertragung. Details stehen in `PRIVACY.md`.

## Rechtliches

Der Source Code steht unter MIT-Lizenz. Name, Logo, Icons und sonstige Brand Assets sind separat geschuetzt. Details stehen in `LICENSE`, `TRADEMARK.md` und `THIRD_PARTY_LICENSES.md`.

## footnote

Developed with the kind support of Copilot
