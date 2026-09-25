<?php
switch ($ReURL) {
    case 'plants_activity':
        $ReURL_ = 'extensions/plants/plants_activity.php';
        break;
    case 'plants_drip':
        $ReURL_ = 'extensions/plants/plants_drip.php';
        break;
    case 'plants_fertilise':
        $ReURL_ = 'extensions/plants/plants_fertilise.php';
        break;
    case 'plants_repotting':
        $ReURL_ = 'extensions/plants/plants_repotting.php';
        break;
    case 'plants_table':
        $ReURL_ = 'extensions/plants/plants_table.php';
        break;
}

if (substr($ReURL, 0, 17) == 'database_i_plants') {
    $ReURL_ = 'extensions/plants/database/' . $ReURL . '.php';
}
