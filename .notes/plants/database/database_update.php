<?php

$DatabaseVersion = intval(config('database_extension_version_plants', -99));
if ($DatabaseVersion == -99) {
    $DatabaseVersion = intval(config('database_extension_version_plants', -99));
    if ($DatabaseVersion >= 0) {
        database_insert($database_t_core_config, ['database_extension_version_plants', $DatabaseVersion, 'INT', 'main', '']);
        database_delete($database_t_core_config, 'key = :1', ['database_extension_version_plants']);
    } else {
        database_insert($database_t_core_config, ['database_extension_version_plants', '-1', 'INT', 'main', '']);
    }
}

if ($DatabaseVersion <= 0) {
    database_update($database_t_core_config, 'value = :1', [1], 'key = :2', ['database_extension_version_plants']);
}

if ($DatabaseVersion <= 1) {
    database_exec('CREATE TABLE IF NOT EXISTS plants_new (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        room TEXT,
        plant TEXT,
        sort INTEGER,
        latinname TEXT,
        waterperiode INTEGER,
        watervolume INTEGER,
        comment TEXT
    );');

    database_exec('INSERT INTO plants_new (id, room, plant, sort, latinname, waterperiode, watervolume, comment) SELECT id, room, plant, sort, latinname, waterperiode, CAST(watervolume AS INTEGER), comment FROM plants;');

    database_exec('DROP TABLE plants;');

    database_exec('ALTER TABLE plants_new RENAME TO plants;');

    database_update($database_t_core_config, 'value = :1', [2], 'key = :2', ['database_extension_version_plants']);
}



if ($DatabaseVersion <= 2) {
    database_exec('CREATE TABLE IF NOT EXISTS plants_activity_new (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        date TEXT DEFAULT CURRENT_DATE NOT NULL,
        plantid INTEGER,
        activity TEXT,
        comment TEXT
    );');

    database_exec('INSERT INTO plants_activity_new (id, date, plantid, activity, comment) SELECT id, date(date), plantid, activity, comment FROM plants_activity;');

    database_exec('DROP TABLE plants_activity;');

    database_exec('ALTER TABLE plants_activity_new RENAME TO plants_activity;');

    database_update($database_t_core_config, 'value = :1', [3], 'key = :2', ['database_extension_version_plants']);
}
