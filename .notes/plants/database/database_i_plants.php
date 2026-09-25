<?php

include('auth/auth.php');

// [$id, $room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment]

function database_i_plants($method, $id, $room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment)
{
    global $database_t_plants;

    if ($method == 'save') {
        database_update($database_t_plants, 'room = :1, plant = :2, sort = :3, latinname = :4, waterperiode = :5, watervolume = :6, comment = :7', [$room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment], 'id = :8', [$id]);
    } elseif ($method == 'add') {
        database_insert($database_t_plants, [$room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment]);
    } elseif ($method == 'delete') {
        database_delete($database_t_plants, 'id = :1', [$id]);
    }
}

$ReURL = 'index.php?ReURL=500';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
    $count = (isset($_POST['count']) ? intval(xss_filter($_POST['count'])) : -1);

    for ($cnt = 0; $cnt <= $count; $cnt++) {
        $id = intval(xss_filter($_POST['id' . $cnt]));
        $room = xss_filter($_POST['room' . $cnt]);
        $plant = xss_filter($_POST['plant' . $cnt]);
        $sort = intval(xss_filter($_POST['sort' . $cnt]));
        $latinname = xss_filter($_POST['latinname' . $cnt]);
        $waterperiode = xss_filter($_POST['waterperiode' . $cnt]);
        $watervolume = xss_filter($_POST['watervolume' . $cnt]);
        $comment = xss_filter($_POST['comment' . $cnt]);
        $ReURL = str_replace('&amp;', '&', xss_filter($_POST['ReURL']));
        if ($ReURL == '')
            $ReURL = 'index.php?ReURL=settings&database';
        $delete = intval(xss_filter($_POST['delete' . $cnt]));

        $method = ($delete == 1 ? 'delete' : 'save');
        if ($cnt == $count && $plant != '') {
            if (database_select_unique_value($database_t_plants, 'id', 'id = :1', [$id]) == '') {
                $method = 'add';
            } else {
                $method = 'error';
            }
        }

        database_i_plants($method, $id, $room, $plant, $sort, $latinname, $waterperiode, $watervolume, $comment);
    }
}

redirect_to('/' . $ReURL);
