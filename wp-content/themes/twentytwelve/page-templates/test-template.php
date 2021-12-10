<?php 


/* Template Name: Test */

get_header();

ob_start();
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=User_Sample.csv");
// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$filename = "test_file";
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");

$data = array(
    array('User Type', 'User Name', 'Category', 'Mobile Number'),
    array('I', 'Anuj Kumar', 'Building Construction', '8500000001'),
    array('I', 'Arvind Kumar', 'Carpentary', '8500000002'),
    array('I', 'Mridul Ohja', 'Civil Engineering', '8500000003'),
    array('I', 'Naman Kumar', 'Electrical', '8500000004'),
    array('I', 'Sumati', 'Faucets', '8500000005'),
    array('I', 'Anjum', 'Flooring Tiles / Marbles', '8500000006'),
    array('I', 'Rajat', 'Painting', '8500000007'),
    array('C', 'Arvind', 'Plumbing', '8500000008'),
    array('C', 'Rohit', 'Sanitaryware', '8500000009'),
    array('C', 'Gaurav', 'Soil Test Analyst', '8500000010')
);

ob_get_clean();

$output = fopen("php://output", "w");
foreach ($data as $row) {
    fputcsv($output, $row); // here you can change delimiter/enclosure
}



print $csv_output;


fclose($output);

 ?>