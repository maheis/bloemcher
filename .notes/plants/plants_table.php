<?php include('auth/auth.php'); ?>

<?php include('core/head.php'); ?>

<?php include('extensions/plants/head-f_.php'); ?>

<?php
$filter_room = (isset($_SESSION['filter_room']) ? $_SESSION['filter_room'] : '%');
if (isset($_POST['room']))
    $filter_room = xss_filter($_POST['room']);
$_SESSION['filter_room'] = $filter_room;

$filter_plant = -1;
if (isset($_GET['plantid']))
    $filter_plant = intval(xss_filter($_GET['plantid']));

if (isset($_GET['del']) && $filter_plant != -1) {
    $plant = database_select_unique_row($database_t_plants, 'id = :1', [$filter_plant]);
    $max = intval(database_select_unique_value($database_t_plants, 'max(sort)', 'room = :1', ['☠️']));
    database_update($database_t_plants, 'room = :1, comment = :2, sort = :3', ['☠️', ($plant['comment'] == '' ? '' : $plant['comment'] . ' | ') . $plant['room'] . ' | ' . 'Todestag: ' . date('Y-m-d'), $max + 10], 'id = :4', [$filter_plant]);
}

if (isset($_GET['up']) && $filter_plant != -1) {
    $current_sort = database_select_unique_value($database_t_plants, 'sort', 'id = :1', [$filter_plant]);
    $upper_plant = database_select_unique_row($database_t_plants,  'room like :1 and sort = :2', [$filter_room, $current_sort - 10]);

    if ($upper_plant) {
        database_update($database_t_plants, 'sort = :1', [$current_sort], 'id = :2', [$upper_plant['id']]);
        database_update($database_t_plants, 'sort = :1', [$upper_plant['sort']], 'id = :2', [$filter_plant]);
    }
}
if (isset($_GET['down']) && $filter_plant != -1) {
    $current_sort = database_select_unique_value($database_t_plants, 'sort', 'id = :1', [$filter_plant]);
    $downer_plant = database_select_unique_row($database_t_plants,  'room like :1 and sort = :2', [$filter_room, $current_sort + 10]);

    if ($downer_plant) {
        database_update($database_t_plants, 'sort = :1', [$current_sort], 'id = :2', [$downer_plant['id']]);
        database_update($database_t_plants, 'sort = :1', [$downer_plant['sort']], 'id = :2', [$filter_plant]);
    }
}


$filterform = "";
$filterform = $filterform . '<font class="medium">';
$filterform = $filterform . '<form method="post" action="?ReURL=plants_table" name="filter">';
$filterform = $filterform . '<table class="auto c">';
$filterform = $filterform . '<tr>';
$filterform = $filterform . '<td>';
$filterform = $filterform . '<select class="bigger" onchange="this.form.submit()" name="room" required="">';
$filterform = $filterform . '<option value=""></option>';
$resultset = database_select($database_t_plants, 'distinct(room)', '', [], 'sort ASC');
foreach ($resultset as $result) {
    $filterform = $filterform . '<option value="' . xss_filter($result['room']) . '"' . ($filter_room == xss_filter($result['room']) ? ' selected' : '') . '>' . $result['room'] . '</option>';
}
$filterform = $filterform . '</select>';
$filterform = $filterform . '</td>';
$filterform = $filterform . '</tr>';
$filterform = $filterform . '</table>';
$filterform = $filterform . '</form>';
$filterform = $filterform . '</font>';
echo $filterform;

if ($filter_room == '' || $filter_room == -1)
    $filter_room = '%';
?>

<form method="post" action="index.php?ReURL=database_i_plants" name="tableedit">
    <?php echo csrf_input(); ?>
    <table style="margin: auto; width: 0%; white-space: nowrap;" class="withBorder edit">
        <thead>
            <tr>
                <?php
                if ($filter_room == '%')
                    echo '<th>Raum</th>';
                ?>
                <th>Pflanze</th>
                <th>Lateinischer Name</th>
                <th>Gießperiode</th>
                <th>Gießmenge</th>
                <th>Kommentar</th>
                <th>letztes Gießen</th>
                <th>letztes Umtopfen</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $plants = database_select($database_t_plants, '*', 'room like :1', [$filter_room]);
            $cnt = 1;
            foreach ($plants as $plant) {
                $lastdrip = database_select_unique_value($database_t_plants_activity, 'MAX(date)', 'plantid = :1 AND activity = :2', [$plant['id'], 'Gießen'], '');
                $lastdripStyle = (strtotime($lastdrip . ' +' . intval($plant['waterperiode']) . ' days') >= time()) && $lastdrip != '' ? 'blue' : 'gray';
                $lastrepot = database_select_unique_value($database_t_plants_activity, 'MAX(date)', 'plantid = :1 AND activity = :2', [$plant['id'], 'Umtopfen'], '');
                $lastrepostStyle = (strtotime($lastrepot . ' +730 days') >= time()) && $lastrepot != '' ? 'brown' : 'gray';

                echo '<tr>';
                if ($filter_room == '%')
                    echo '<td>' . $plant['room'] . '</td>';
                echo '<td>' . $plant['plant'] . '</td>';
                echo '<td>' . $plant['latinname'] . '</td>';
                echo '<td>' . $plant['waterperiode'] . '</td>';
                echo '<td>' . $plant['watervolume'] . '</td>';
                echo '<td>' . $plant['comment'] . '</td>';

                echo '<td><font class="' . $lastdripStyle . '">' . ($lastdrip ? $lastdrip : '') . '</font></td>';
                echo '<td><font class="' . $lastrepostStyle . '">' . ($lastrepot ? $lastrepot : '') . '</font></td>';
                echo '<td style="width:1%;text-wrap: nowrap;">';
                echo ' <a class="button small orange rounded" onclick="dialogOpen(\'edit_' . $plant['id'] . '\')"><i class="fad fa-pen fa-fw"></i></a>';
                echo '<dialog id="edit_' . $plant['id'] . '">';
                echo database_select_1pager_edit($database_t_plants, 'id = :1', [$plant['id']], 'index.php?ReURL=plants_table');
                echo '</dialog>';
                if ($filter_room != '%') {
                    echo ' <a class="button small rounded ' . ($cnt == 1 ? 'gray"' : 'themecolor" href="index.php?ReURL=plants_table&plantid=' . $plant['id'] . '&up"') . '><i class="fad fa-chevron-up fa-fw"></i></a>';
                    echo ' <a class="button small rounded ' . ($cnt == count($plants) ? 'gray"' : 'themecolor" href="index.php?ReURL=plants_table&plantid=' . $plant['id'] . '&down"') . '><i class="fad fa-chevron-down fa-fw"></i></a>';
                }
                echo ' <a class="button small ' . $lastdripStyle . ' rounded" href="index.php?ReURL=plants_drip&plantid=' . $plant['id'] . '"><i class="fad fa-fill-drip fa-fw"></i></a>';
                echo ' <a class="button small yellow rounded" href="index.php?ReURL=plants_fertilise&plantid=' . $plant['id'] . '"><i class="fad fa-hand-holding-seedling fa-fw"></i></a>';
                echo ' <a class="button small ' . $lastrepostStyle . ' rounded" href="index.php?ReURL=plants_repotting&plantid=' . $plant['id'] . '"><i class="fad fa-glass fa-fw"></i></a>';
                echo ' <a class="button small green rounded" href="index.php?ReURL=plants_activity&room=' . urlencode($plant['room']) . '&plant=' . $plant['id'] . '"><i class="fad fa-leaf-heart fa-fw"></i></a>';
                if ($plant['room'] != '☠️') {
                    echo ' <a class="button small red rounded" onclick="dialogOpen(\'del_' . $plant['id'] . '\')"><i class="fad fa-skull-crossbones fa-fw"></i></a>';
                    echo '<dialog id="del_' . $plant['id'] . '">';
                    echo 'Soll die Pflanze wirklich für tot erklärt werden?<br>';
                    echo '<br><div style="text-align: center"><a class="button red" href=""><i class="fad fa-times fa-fw"></i></a> <a class="button green" href="index.php?ReURL=plants_table&plantid=' . $plant['id'] . '&del"><i class="fad fa-check fa-fw"></i></a></div>';
                    echo '</dialog>';
                } else {
                    echo ' <a class="button small gray rounded"><i class="fad fa-skull-crossbones fa-fw"></i></a>';
                }
                echo '</td>';
                echo '</tr>';

                $cnt++;
            }
            ?>
        </tbody>
    </table>
</form>

<?php include_once('core/footer.php'); ?>