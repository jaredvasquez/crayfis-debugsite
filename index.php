<html>
<head>
<title> CRAYFIS : Debug Monitoring </title>
<meta http-equiv="refresh" content="30">
</head>
<body>
<!-- <BR> Content will refresh every 30 seconds. <BR> -->
<BR>
<?php 
  $username = 'debug';
  $password = 'test123';
  $database = 'debugstatus';

  $server = 'localhost';
  $table = 'STATUS';

  $connect = mysql_connect($server, $username, $password);
  $dbfound = mysql_select_db($database, $connect);

  if(!$dbfound) {
    print "ERROR: Database NOT Found";
    mysql_close($connect);
  } else {
    $sql = "SELECT * FROM STATUS";
    $result = mysql_query($sql);
    while ( $db_field = mysql_fetch_assoc($result) ) {
      print "<table border='0'>\n";
      print "  <tr><th align='left'> Device ID: " . $db_field['PHONE_ID'] . "  ( " . $db_field['PHONE_DESC'] . " ) </th></tr>\n";
      print "  <tr><td align='center' style='color:#FF0000'> Last Updated on " . $db_field['LAST_UPDATED'] . "</td></tr>\n";
      print "  <tr><td align='center'>\n";
      print "    <table>\n";
      print "      <tr>\n";
      print "        <td align='right'> Number of XBs : </td>\n";
      print "        <td width='45' align='right'>" . $db_field['NXBS'] . "</td>\n";
      print "      </tr>\n"; 
      print "      <tr>\n";
      print "        <td align='right'> Number of Events : </td>";
      print "        <td width='45' align='right'>" . $db_field['EVENTS'] . "</td>"; 
      print "      </tr>\n"; 
      print "      <tr>\n";
      print "        <td align='right'> L1 Threshold : </td>\n";
      print "        <td width='45' align='right'>" . $db_field['L1THRESH'] . "</td>\n"; 
      print "      </tr>\n"; 
      print "      <tr>\n";
      print "        <td align='right'> Frame Rate : </td>\n";
      print "        <td width='45' align='right'>" . $db_field['FPS'] . "</td>\n"; 
      print "      </tr>\n"; 
      print "    </table>\n";
      print "  </td></tr>\n";
      print "</table>\n";
      print "<BR>\n";
    }
  }
?>
</body>
</html>
