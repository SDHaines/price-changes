#!/usr/local/bin/php
<?php

/* 
 * This script queries the database and outputs the 
 * item and new price for items that reside behind 
 * the sales counter. It's runs from cron on Friday 
 * afternoon at 3:00 to allow for all current price 
 * changes to be entered into the database. The 
 * output is directed to shared storage so that
 * the managers can print new price tickets. 
 */

require_once '/usr/local/valCommon/Counterpoint.php';

$startDT = date('Y/m/d h:i:s', strtotime('saturday last week'));
$tsql = "SELECT
P.ITEM_NO,
I.BARCOD,
I.ADDL_DESCR_1,
P.PRC_1, 
P.LST_MAINT_DT,
P.LST_MAINT_USR_ID
FROM dbo.IM_PRC P
LEFT JOIN IM_ITEM I ON P.ITEM_NO = I.ITEM_NO
WHERE  (I.ADDL_DESCR_1 LIKE '% 50[mM][lL]%' OR I.ADDL_DESCR_1 LIKE '% 375[mM][lL]%')
AND P.LST_MAINT_DT >= '$startDT'
ORDER BY P.LST_MAINT_DT DESC";

print "item,price\n";
$result = counterpointQuickQuery( $tsql, "callback" );
if ( $result === false ){
  die("something went wrong ^^^ those error messages should point you in the right direction\n" );
}

function callback( $row ) { 
    printf("%s,%.02f\n", $row['ADDL_DESCR_1'],$row['PRC_1'] );
}
?>