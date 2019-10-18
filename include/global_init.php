<?php

$handle = fopen("../include/data/invest_history.csv","r");
$num_row = 1;
while ($data_csv = fgetcsv($handle, 1000, ",")) {
    if ($num_row >= 3) {
        $date = $data_csv[0];
        if ((int)$data_csv[5] > 0) {
            $invest_list[]= array(
                'date'=>$date,
                'total_money'=>$data_csv[1],
                'profit_money'=>$data_csv[3],
                'profit_percent'=>$data_csv[5],
            );
        }
    }
    $num_row++;
}
fclose($handle);
?>
