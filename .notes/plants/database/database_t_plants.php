<?php

//TODO: name unique in combination with room

//CREATE-Statement
database_exec('CREATE TABLE IF NOT EXISTS plants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    room TEXT,
    plant TEXT,
    sort INTEGER,
    latinname TEXT,
    waterperiode INTEGER,
    watervolume INTEGER,
    comment TEXT
);');

//INSERT
// [$id, $room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment]

//[TABLE]TABLE => 'tablename'
//[TABLE]RW = 0-1 //0 = readonly ,1 = read/write (für select_table_edit())
//[TABLE]NAME => 'spokable tablename'
//[ORDER] => default order by, can be overriden in select-function, e.g. 'column desc, column desc' 
//[COLUMNS]NAME => 'spokable name'
//[COLUMNS]RW => 0-2 //0 = readonly, 1 = read/write, 2 = readonly/but in INSERT writeable!
//[COLUMNS]TYPE => TEXT, INT, BOOL, PASSWORD, FLOAT, DATETIME, DATE, TIME, TIMESTAMP(dt)
//[COLUMNS]SIZE => smallest, smaller, small, '', big, bigger, biggest
//[COLUMNS]OPTIONAL DEFAULT => Default-Value
//[COLUMNS]OPTIONAL TIP => TOOPTIP
//[COLUMNS]OPTIONAL LIST => SELECTION: ['VALUE' => 'SPOKEABLENAME', 'VALUE' => 'SPOKEABLENAME', '_SQL'...] ['_SQL' => 'SELECT <COLUMN_VALUE> as a [, <COLUMN_SPOKEABLENAME> as b] FROM <TABLE> ORDER BY <COL>']
//[COLUMNS]OPTIONAL REQUIRED => 1 //mandatory field
//[COLUMNS]OPTIONAL HIDDEN => 1 //hidden in 1Pager
//[VIRTUAL]NAME => 'spokable name'
//[VIRTUAL]CONTENT => 'content, will be replaced with the value, usefull for e.g. buttons...'
$database_t_plants = [
    'TABLE' => 'plants',
    'RW' => 1,
    'NAME' => 'Pflanzen',
    'ORDER' => 'sort',
    'COLUMNS' => [
        'id' => ['NAME' => 'ID', 'RW' => 0, 'TYPE' => 'INT', 'SIZE' => 'smallest', 'HIDDEN' => 1],
        'room' => ['NAME' => 'Raum', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'small', 'LIST' => ['_SQL' => 'SELECT DISTINCT room as a, room as b FROM plants ORDER BY room']],
        'plant' => ['NAME' => 'Pflanze', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'small'],
        'sort' => ['NAME' => 'Reihenfolge', 'RW' => 1, 'TYPE' => 'INT', 'SIZE' => 'small'],
        'latinname' => ['NAME' => 'Lateinischer Name', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'small'],
        'waterperiode' => ['NAME' => 'Gießperiode in Tagen', 'RW' => 1, 'TYPE' => 'INT', 'SIZE' => 'small'],
        'watervolume' => ['NAME' => 'Gießmenge in ml', 'RW' => 1, 'TYPE' => 'INT', 'SIZE' => 'small'],
        'comment' => ['NAME' => 'Kommentar', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'big']
    ]
];

global $database_t_array;
array_push($database_t_array, $database_t_plants);
