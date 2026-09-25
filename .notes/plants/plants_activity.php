<?php include('auth/auth.php'); ?>

<?php include('core/head.php'); ?>

<?php include('extensions/plants/head-f_.php'); ?>

<?php

$filter_room = (isset($_SESSION['filter_room']) ? $_SESSION['filter_room'] : -1);
if (isset($_POST['room']))
    $filter_room = xss_filter($_POST['room']);
if (isset($_GET['room']))
    $filter_room = xss_filter($_GET['room']);
$_SESSION['filter_room'] = $filter_room;

$filter_plant = (isset($_SESSION['filter_plant']) ? $_SESSION['filter_plant'] : -1);
if (isset($_POST['plant']))
    $filter_plant = xss_filter($_POST['plant']);
if (isset($_GET['plant']))
    $filter_plant = xss_filter($_GET['plant']);
$plant_room = database_select_unique_value($database_t_plants, 'room', 'id = :1', [$filter_plant], '');
if ($plant_room != $filter_room)
    $filter_plant = database_select_unique_value($database_t_plants, 'id', 'room = :1 and sort = (select min(sort) from plants where room = :1)', [$filter_room], '');
$_SESSION['filter_plant'] = $filter_plant;

$filterform = "";
$filterform = $filterform . '<font class="medium">';
$filterform = $filterform . '<form method="post" action="?ReURL=plants_activity" name="filter">';
$filterform = $filterform . '<table class="auto c">';
$filterform = $filterform . '<tr>';
$filterform = $filterform . '<td>';
$filterform = $filterform . '<select class="bigger" onchange="this.form.submit()" name="room" required="">';
$resultset = database_select($database_t_plants, 'distinct(room)', '', [], 'sort ASC');
foreach ($resultset as $result) {
    $filterform = $filterform . '<option value="' . $result['room'] . '"' . ($filter_room == $result['room'] ? ' selected' : '') . '>' . $result['room'] . '</option>';
}
if (($filter_room == -1 || $filter_room == '' || $filter_room == '%') && !empty($resultset) && isset($resultset[0]['room'])) {
    $filter_room = $resultset[0]['room'];
}
$filterform = $filterform . '</select>';
$filterform = $filterform . '</td>';
$filterform = $filterform . '</tr>';
$filterform = $filterform . '<tr>';
$filterform = $filterform . '<td>';
$filterform = $filterform . '<select class="bigger" onchange="this.form.submit()" name="plant" required="">';
$resultset = database_select($database_t_plants, 'id, plant', 'room = :1', [$filter_room], 'sort ASC');
foreach ($resultset as $result) {
    $filterform = $filterform . '<option value="' . $result['id'] . '"' . ($filter_plant == $result['id'] ? ' selected' : '') . '>' . $result['plant'] . '</option>';
}
$filterform = $filterform . '</select>';
$filterform = $filterform . '</td>';
$filterform = $filterform . '</tr>';
$filterform = $filterform . '</table>';
$filterform = $filterform . '</form>';
$filterform = $filterform . '</font>';
echo $filterform;

// TODO: Filter nach Aktivität
?>


<form method="post" action="index.php?ReURL=database_i_plants" name="tableedit">
    <?php echo csrf_input(); ?>
    <table style="margin: auto; width: 0%; white-space: nowrap;" class="withBorder edit">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Aktivität</th>
                <th>Kommentar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $activitys = database_select($database_t_plants_activity, '*', 'plantid = :1', [$filter_plant]);
            foreach ($activitys as $activity) {
                echo '<tr>';
                echo '<td>' . date('Y-m-d', strtotime($activity['date'])) . '</td>';
                echo '<td>' . $activity['activity'] . '</td>';
                echo '<td>' . $activity['comment'] . '</td>';
                echo '<td>';
                echo ' <a class="button small orange rounded" onclick="dialogOpen(\'edit_' . $activity['id'] . '\')"><i class="fad fa-pen fa-fw"></i></a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</form>

<?php
foreach ($activitys as $activity) {
    echo '<dialog id="edit_' . $activity['id'] . '">';
    echo database_select_1pager_edit($database_t_plants_activity, 'id = :1', [$activity['id']], 'index.php?ReURL=plants_activity');
    echo '</dialog>';
}
?>

<?php include_once('core/footer.php'); ?>