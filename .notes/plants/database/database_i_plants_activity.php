<?php

include('auth/auth.php');

// [$id, $date, $plantid, $activity, $comment]

function database_i_plants_activity($method, $id, $date, $plantid, $activity, $comment)
{
    global $database_t_plants_activity;

    if ($method == 'save') {
        database_update($database_t_plants_activity, 'date = :1, plantid = :2, activity = :3, comment = :4', [$date, $plantid, $activity, $comment], 'id = :5', [$id]);
    } elseif ($method == 'add') {
        database_insert($database_t_plants_activity, [$date, $plantid, $activity, $comment]);
    } elseif ($method == 'delete') {
        database_delete($database_t_plants_activity, 'id = :1', [$id]);
    }
}

$ReURL = 'index.php?ReURL=500';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
    $count = (isset($_POST['count']) ? intval(xss_filter($_POST['count'])) : -1);

    for ($cnt = 0; $cnt <= $count; $cnt++) {
        $id = intval(xss_filter($_POST['id' . $cnt]));
        $date = xss_filter($_POST['date' . $cnt]);
        $plantid = xss_filter($_POST['plantid' . $cnt]);
        $activity = xss_filter($_POST['activity' . $cnt]);
        $comment = xss_filter($_POST['comment' . $cnt]);
        $ReURL = str_replace('&amp;', '&', xss_filter($_POST['ReURL']));
        if ($ReURL == '')
            $ReURL = 'index.php?ReURL=settings&database';
        $delete = intval(xss_filter($_POST['delete' . $cnt]));

        $method = ($delete == 1 ? 'delete' : 'save');
        if ($cnt == $count && $plantid != '') {
            if (database_select_unique_value($database_t_plants, 'id', 'id = :1', [$id]) == '') {
                $method = 'add';
            } else {
                $method = 'error';
            }
        }

        database_i_plants_activity($method, $id, $date, $plantid, $activity, $comment);
    }
}

redirect_to('/' . $ReURL);
