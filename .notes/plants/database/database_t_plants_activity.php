<?php

//CREATE-Statement
database_exec('CREATE TABLE IF NOT EXISTS plants_activity (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    date TEXT DEFAULT CURRENT_DATE NOT NULL,
    plantid INTEGER,
    activity TEXT,
    comment TEXT
);');
//INSERT
// [$id, $date, $plantid, $activity, $comment]

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
$database_t_plants_activity = [
    'TABLE' => 'plants_activity',
    'RW' => 1,
    'NAME' => 'Pflanzen',
    'ORDER' => 'date desc',
    'COLUMNS' => [
        'id' => ['NAME' => 'ID', 'RW' => 0, 'TYPE' => 'INT', 'SIZE' => 'smallest', 'HIDDEN' => 1],
        'date' => ['NAME' => 'Datum', 'RW' => 2, 'TYPE' => 'DATE', 'SIZE' => 'small'],
        'plantid' => ['NAME' => 'Pflanze', 'RW' => 2, 'TYPE' => 'INT', 'SIZE' => 'small', 'LIST' => ['_SQL' => "SELECT id as a, (room || ' - ' || plant) as b FROM plants ORDER BY sort"]],
        'activity' => ['NAME' => 'Aktivität', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'small', 'LIST' => ['Gießen' => 'Gießen', 'Düngen' => 'Düngen', 'Umtopfen' => 'Umtopfen']],
        'comment' => ['NAME' => 'Kommentar', 'RW' => 1, 'TYPE' => 'TEXT', 'SIZE' => 'big']
    ]
];

global $database_t_array;
array_push($database_t_array, $database_t_plants_activity);
