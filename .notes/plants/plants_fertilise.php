<?php include('auth/auth.php'); ?>

<?php include('core/head.php'); ?>

<?php include('extensions/plants/head-f_.php'); ?>

<?php
$filter_plant = (isset($_SESSION['filter_plant']) ? $_SESSION['filter_plant'] : -1);
if (isset($_POST['plant']))
    $filter_plant = xss_filter($_POST['plant']);
if (isset($_GET['plantid']))
    $filter_plant = xss_filter($_GET['plantid']);
$_SESSION['filter_plant'] = $filter_plant;

$Comment = (isset($_SESSION['comment']) ? $_SESSION['comment'] : '');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
}
if (isset($_POST['fertilise'])) {
    $Comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
    if ($filter_plant == 'ALL') {
        $resultset = database_select($database_t_plants, 'id', "room not in ('☠️') and id not in (select plantid from plants_activity where activity = 'Düngen' and date(date) > date('now', '-1 days'))");
        foreach ($resultset as $result) {
            database_insert($database_t_plants_activity, [$result['id'], 'Düngen', $Comment], ['plantid', 'activity', 'comment']);
        }
    } else {
        database_insert($database_t_plants_activity, [$filter_plant, 'Düngen', $Comment], ['plantid', 'activity', 'comment']);
    }
}
$_SESSION['comment'] = $Comment;

$watervolume = 0;

$form = "";
$form = $form . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
$form = $form . '<font class="medium">';
$form = $form . '<form method="post" action="?ReURL=plants_fertilise" name="filter">';
$form = $form . csrf_input();
$form = $form . '<table class="auto c">';
$form = $form . '<tr>';
$form = $form . '<td><select class="bigger" onchange="this.form.submit()" name="plant" required="">';
$form = $form . '<option value="ALL">Alle Pflanzen</option>';
$resultset = database_select($database_t_plants, 'id, room, plant', "room not in ('☠️') and id not in (select plantid from plants_activity where activity = 'Düngen' and date(date) > date('now', '-1 days'))");
foreach ($resultset as $result) {
    $form = $form . '<option value="' . $result['id'] . '"' . ($filter_plant == $result['id'] ? ' selected' : '') . '>' . $result['room'] . ' - ' . $result['plant'] . '</option>';
}
$form = $form . '</select></td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><input class="bigger" name="comment" type="text" value="' . htmlspecialchars($Comment) . '"></td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><button class="yellow rounded" name="fertilise"><i class="fad fa-hand-holding-seedling fa-fw" readonly></i></button></td>';
$form = $form . '</tr>';

$form = $form . '</table>';
$form = $form . '</form>';
$form = $form . '</font>';

echo $form;
?>

<?php include_once('core/footer.php'); ?>