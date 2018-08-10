<?php

$handle = fopen("tongji/include/data/item.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $item_list[$data_csv[0]]= $data_csv[2];
        }
    }
    $num_row++;
}
fclose($handle);

$handle = fopen("tongji/include/data/mail_template.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $mail_titles[$data_csv[0]]= $data_csv[1];
            $mail_contents[$data_csv[0]]= $data_csv[2];
        }
    }
    $num_row++;
}
fclose($handle);

$handle = fopen("tongji/include/data/platform_channel.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $channel_list[$data_csv[1]]= $data_csv[2];
        }
    }
    $num_row++;
}
fclose($handle);

$handle = fopen("tongji/include/data/fields.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $field_list[$data_csv[1]]= $data_csv[3];
        }
    }
    $num_row++;
}
fclose($handle);

$handle = fopen("tongji/include/data/pet.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $pet_list[$data_csv[0]]= $data_csv[1];
        }
    }
    $num_row++;
}
fclose($handle);

$handle = fopen("tongji/include/data/sky_robot.csv","r");
$num_row = 0;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row > 3) {
        $id = (int)$data_csv[0];
        if ($id > 0) {
            $robot_list[$data_csv[0]]= $data_csv[1];
        }
    }
    $num_row++;
}
fclose($handle);

?>