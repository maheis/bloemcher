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
if (isset($_POST['repotting'])) {
    $Comment = (isset($_POST['comment']) ? $_POST['comment'] : '');
    database_insert($database_t_plants_activity, [$filter_plant, 'Umtopfen', $Comment], ['plantid', 'activity', 'comment']);
}
$_SESSION['comment'] = $Comment;

$watervolume = 0;

$form = "";
$form = $form . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
$form = $form . '<font class="medium">';
$form = $form . '<form method="post" action="?ReURL=plants_repotting" name="filter">';
$form = $form . csrf_input();
$form = $form . '<table class="auto c">';
$form = $form . '<tr>';
$form = $form . '<td><select class="bigger" onchange="this.form.submit()" name="plant" required="">';
$resultset = database_select($database_t_plants, 'id, room, plant', "room not in ('☠️') and id not in (select plantid from plants_activity where activity = 'Umtopfen' and date(date) > date('now', '-1 days'))");
foreach ($resultset as $result) {
    $form = $form . '<option value="' . $result['id'] . '"' . ($filter_plant == $result['id'] ? ' selected' : '') . '>' . $result['room'] . ' - ' . $result['plant'] . '</option>';
}
$form = $form . '</select></td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><input class="bigger" name="comment" type="text" value="' . htmlspecialchars($Comment) . '"></td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><button class="brown rounded" name="repotting"><i class="fad fa-glass fa-fw" readonly></i></button></td>';
$form = $form . '</tr>';

$form = $form . '</table>';
$form = $form . '</form>';
$form = $form . '</font>';

echo $form;
?>

<?php include_once('core/footer.php'); ?>