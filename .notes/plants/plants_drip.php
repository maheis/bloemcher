<?php include('auth/auth.php'); ?>

<?php include('core/head.php'); ?>

<?php include('extensions/plants/head-f_.php'); ?>

<?php
$plantid = -1;
$filter_plant = (isset($_SESSION['filter_plant']) ? $_SESSION['filter_plant'] : -1);
if (isset($_POST['plant']))
    $filter_plant = xss_filter($_POST['plant']);
if (isset($_GET['plantid'])) {
    $filter_plant = xss_filter($_GET['plantid']);
    $plantid = $filter_plant;
}
$_SESSION['filter_plant'] = $filter_plant;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_or_die();
}

if (isset($_POST['drip'])) {
    $volume = (isset($_POST['volume']) ? $_POST['volume'] : 0);
    database_insert($database_t_plants_activity, [$filter_plant, 'Gießen', $volume . ' ml'], ['plantid', 'activity', 'comment']);
}

$watervolume = 0;
$watervolume_sum = 0;
$planttable = "";
$planttable = $planttable . '<table class="withBorder auto c">';
$planttable = $planttable . '<tr>';
$planttable = $planttable . '<th>Pflanze</th>';
$planttable = $planttable . '<th>Wasserbedarf</th>';
$planttable = $planttable . '</tr>';

$form = "";
$form = $form . '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>';
$form = $form . '<font class="medium">';
$form = $form . '<form method="post" action="?ReURL=plants_drip" name="filter">';
$form = $form . csrf_input();
$form = $form . '<table class="auto c">';
$form = $form . '<tr>';
$form = $form . '<td><select class="bigger" onchange="this.form.submit()" name="plant" required="">';
$resultset = database_select($database_t_plants, 'id, room, plant, watervolume', "room not in ('☠️') and id not in (select plantid from plants_activity where plantid <> " . $plantid . " and activity = 'Gießen' and date(date) > date('now', '-'|| waterperiode||' days'))");
foreach ($resultset as $result) {
    $form = $form . '<option value="' . $result['id'] . '"' . ($filter_plant == $result['id'] ? ' selected' : '') . '>' . $result['room'] . ' - ' . $result['plant'] . '</option>';
    // isset($_POST['next'])

    if ($filter_plant == $result['id']) {
        $watervolume = $result['watervolume'];
    }
    $result['watervolume'] > 0 ? $watervolume_sum += $result['watervolume'] : '';

    $planttable = $planttable . '<tr>';
    $planttable = $planttable . '<td>' . $result['room'] . ' - ' . $result['plant'] . '</td>';
    $planttable = $planttable . '<td>' . $result['watervolume'] . ' ml</td>';
    $planttable = $planttable . '</tr>';
}
if ($watervolume == 0 && !empty($resultset) && isset($resultset[0]['watervolume'])) {
    $watervolume = $resultset[0]['watervolume'];
}
$form = $form . '</select></td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><input class="r" name="volume" type="number" value="' . $watervolume . '">&nbsp;ml</td>';
$form = $form . '</tr>';
$form = $form . '<tr>';
$form = $form . '<td class="c"><button class="blue rounded" name="drip"><i class="fad fa-raindrops fa-fw" readonly></i></button></td>';
$form = $form . '</tr>';

$form = $form . '</table>';
$form = $form . '</form>';
$form = $form . '</font>';

$planttable = $planttable . '</table>';

echo $form;
echo $watervolume_sum > 0 ? '<br><font class="medium">Insgesamt ' . $watervolume_sum . ' ml gießen</font><br><br>' : '';
echo $planttable;
?>

<?php include_once('core/footer.php'); ?>