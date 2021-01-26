<?php
/**
 * DETERMINE THE DATABASE TYPE AND IF WE CAN CONNECT
 *
 */
$dbPrefExist = $lang['FRCA_N'];
$dbPrefLen = @strlen($instance['configDBPREF']);
$postgresql = $lang['FRCA_N'];
$confPrefTables = 0;
$notconfPrefTables = 0;

if ($instance['instanceCONFIGURED'] == $lang['FRCA_Y'] and ($instance['configDBCREDOK'] == $lang['FRCA_Y'] or $instance['configDBCREDOK'] == $lang['FRCA_PMISS'])) {
	$database['dbDOCHECKS'] = $lang['FRCA_Y'];

	// try and connect to the database server and table-space, using the database_host variable in the configuration.php
	// for J!1.0, it's not in the config, so we have assumed mysql, as mysqli wasn't available during it's support life-time
	if ($instance['configDBTYPE'] == 'mysql') {

		if (@function_exists('mysql_connect')) {
			$dBconn = @mysql_connect($instance['configDBHOST'], $instance['configDBUSER'], $instance['configDBPASS']);
			$database['dbERROR'] = mysql_errno() . ':' . mysql_error();

			@mysql_select_db($instance['configDBNAME'], $dBconn);
			$sql    = "select name,type,enabled from " . $instance['configDBPREF'] . "extensions where type='plugin' or type='component' or type='module' or type='template' or type='library'";
			$result = @mysql_query($sql);

			if ($result <> false) {
				if (mysql_num_rows($result) > 0) {

					for (
						$exset = array();
						$row = mysql_fetch_array($result);
						$exset[] = $row
					);
				}
				$sql = "select template, max(home) as home from " . $instance['configDBPREF'] . "template_styles group by template";
				$result = @mysql_query($sql);

				if (mysql_num_rows($result) > 0) {

					for (
						$tmpldef = array();
						$row = mysql_fetch_array($result);
						$tmpldef[] = $row
					);
				}
			}

			if ($dBconn) {
				@mysql_select_db($instance['configDBNAME'], $dBconn);
				$database['dbERROR'] = @mysql_errno() . ':' . @mysql_error();

				// if we can connect, try and collect some details
				$database['dbHOSTSERV']     = mysql_get_server_info($dBconn);      // SQL server version
				$database['dbHOSTINFO']     = mysql_get_host_info($dBconn);        // connection type to dB
				$database['dbHOSTPROTO']    = mysql_get_proto_info($dBconn);       // server protocol type
				$database['dbHOSTCLIENT']   = mysql_get_client_info();               // client library version
				$database['dbHOSTDEFCHSET'] = mysql_client_encoding($dBconn);      // this is the hosts default character-set
				$database['dbHOSTSTATS']    = explode("  ", mysql_stat($dBconn)); // latest statistics


				// get the database grants/privilidges
				// added @RussW 5-May-2020
				$privResult = mysql_query("SHOW GRANTS FOR CURRENT_USER");

				while ($row = @mysql_fetch_row($privResult)) {
					$database['dbPRIVS'] =  $row[0];
				}

				// find the database collation
				$coResult = mysql_query("SHOW VARIABLES LIKE 'collation_database'");

				while ($row = mysql_fetch_row($coResult)) {
					$database['dbCOLLATION'] =  $row[1];
				}

				// find the database character-set
				$csResult = mysql_query("SHOW VARIABLES LIKE 'character_set_database'");

				while ($row = mysql_fetch_array($csResult)) {
					$database['dbCHARSET'] =  $row[1];
				}

				// find all the dB tables and calculate the size
				mysql_select_db($instance['configDBNAME'], $dBconn);
				$tblResult = mysql_query("SHOW TABLE STATUS");

				$database['dbSIZE'] = 0;
				$rowCount = 0;

				while ($row = mysql_fetch_array($tblResult)) {
					$rowCount++;

					$tables[$row['Name']]['TABLE'] = $row['Name'];

					// count tables with/without same prefix as in config
					if (substr($row['Name'], 0, $dbPrefLen) === $instance['configDBPREF']) {
						$confPrefTables = $confPrefTables + 1;
					} else {
						$notconfPrefTables = $notconfPrefTables + 1;
					}

					$table_size = ($row['Data_length'] + $row['Index_length']) / 1024;
					$tables[$row['Name']]['SIZE'] = sprintf('%.2f', $table_size);
					$database['dbSIZE'] += sprintf('%.2f', $table_size);
					$tables[$row['Name']]['SIZE'] = $tables[$row['Name']]['SIZE'] . ' KiB';

					if ($showTables == '1') {
						$tables[$row['Name']]['ENGINE']     = $row['Engine'];
						$tables[$row['Name']]['VERSION']    = $row['Version'];
						$tables[$row['Name']]['CREATED']    = $row['Create_time'];
						$tables[$row['Name']]['UPDATED']    = $row['Update_time'];
						$tables[$row['Name']]['CHECKED']    = $row['Check_time'];
						$tables[$row['Name']]['COLLATION']  = $row['Collation'];
						$tables[$row['Name']]['FRAGSIZE']   = sprintf('%.2f', ($row['Data_free'] / 1024)) . ' KiB';
						$tables[$row['Name']]['MAXGROW']    = sprintf('%.1f', ($row['Max_data_length'] / 1073741824)) . ' GiB';
						$tables[$row['Name']]['RECORDS']    = $row['Rows'];
						$tables[$row['Name']]['AVGLEN']     = sprintf('%.2f', ($row['Avg_row_length'] / 1024)) . ' KiB';
					}
				}


				if ($database['dbSIZE'] > '1024') {
					$database['dbSIZE'] = sprintf('%.2f', ($database['dbSIZE'] / 1024)) . ' MiB';
				} else {
					$database['dbSIZE'] = $database['dbSIZE'] . ' KiB';
				}

				$database['dbTABLECOUNT'] = $rowCount;
				mysql_close($dBconn);
			} else {
				$database['dbERROR'] = mysql_errno() . ':' . mysql_error();
			} // end mysql if $dBconn is good

		} else {
			$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
			$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
			$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
			$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
			$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
			$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
			$database['dbCOLLATION']    = $lang['FRCA_U'];
			$database['dbCHARSET']      = $lang['FRCA_U'];
		}
	} elseif ($instance['configDBTYPE'] == 'mysqli' and $phpenv['phpSUPPORTSMYSQLI'] == $lang['FRCA_Y']) { // mysqli

		if (function_exists('mysqli_connect')) {
			$dBconn              = @new \mysqli($instance['configDBHOST'], $instance['configDBUSER'], $instance['configDBPASS'], $instance['configDBNAME']);
			$database['dbERROR'] = mysqli_connect_errno() . ':' . mysqli_connect_error();
			$sql                 = "select name,type,enabled from " . $instance['configDBPREF'] . "extensions where type='plugin' or type='component' or type='module' or type='template' or type='library'";
			$result              = @$dBconn->query($sql);

			if ($result <> false) {

				if ($result->num_rows > 0) {
					for (
						$exset = array();
						$row = $result->fetch_assoc();
						$exset[] = $row
					);
				}
			}
			$sql = "select template, max(home) as home from " . $instance['configDBPREF'] . "template_styles group by template";
			$result = @$dBconn->query($sql);

			if ($result <> false) {

				if ($result->num_rows > 0) {
					for (
						$tmpldef = array();
						$row = $result->fetch_assoc();
						$tmpldef[] = $row
					);
				}
			}

			if ($dBconn) {
				$database['dbHOSTSERV']     = @mysqli_get_server_info($dBconn);       // SQL server version
				$database['dbHOSTINFO']     = @mysqli_get_host_info($dBconn);         // connection type to dB
				$database['dbHOSTPROTO']    = @mysqli_get_proto_info($dBconn);        // server protocol type
				$database['dbHOSTCLIENT']   = @mysqli_get_client_info();                // client library version
				$database['dbHOSTDEFCHSET'] = @mysqli_character_set_name($dBconn);       // this is the hosts default character-set
				$database['dbHOSTSTATS']    = explode("  ", @mysqli_stat($dBconn));  // latest statistics

				// get the database grants/privilidges
				// added @RussW 5-May-2020
				$privResult = @$dBconn->query("SHOW GRANTS FOR CURRENT_USER");

				while ($row = @mysqli_fetch_row($privResult)) {
					$database['dbPRIVS'] =  $row[0];
				}

				// find the database collation
				$coResult = @$dBconn->query("SHOW VARIABLES LIKE 'collation_database'");

				while ($row = @mysqli_fetch_row($coResult)) {
					$database['dbCOLLATION'] =  $row[1];
				}

				// find the database character-set
				$csResult = @$dBconn->query("SHOW VARIABLES LIKE 'character_set_database'");

				while ($row = @mysqli_fetch_array($csResult)) {
					$database['dbCHARSET']  =  $row[1];
				}

				// find all the dB tables and calculate the size
				$tblResult = @$dBconn->query("SHOW TABLE STATUS");

				$database['dbSIZE'] = 0;
				$rowCount           = 0;

				while ($row = @mysqli_fetch_array($tblResult)) {
					$rowCount++;

					$tables[$row['Name']]['TABLE']  = $row['Name'];

					// count tables with/without same prefix as in config
					if (substr($row['Name'], 0, $dbPrefLen) === $instance['configDBPREF']) {
						$confPrefTables = $confPrefTables + 1;
					} else {
						$notconfPrefTables = $notconfPrefTables + 1;
					}
					$table_size = ($row['Data_length'] + $row['Index_length']) / 1024;
					$tables[$row['Name']]['SIZE'] = sprintf('%.2f', $table_size);
					$database['dbSIZE'] += sprintf('%.2f', $table_size);
					$tables[$row['Name']]['SIZE'] = $tables[$row['Name']]['SIZE'] . ' KiB';


					if ($showTables == '1') {
						$tables[$row['Name']]['ENGINE']     = $row['Engine'];
						$tables[$row['Name']]['VERSION']    = $row['Version'];
						$tables[$row['Name']]['CREATED']    = $row['Create_time'];
						$tables[$row['Name']]['UPDATED']    = $row['Update_time'];
						$tables[$row['Name']]['CHECKED']    = $row['Check_time'];
						$tables[$row['Name']]['COLLATION']  = $row['Collation'];
						$tables[$row['Name']]['FRAGSIZE']   = sprintf('%.2f', ($row['Data_free'] / 1024)) . ' KiB';
						$tables[$row['Name']]['MAXGROW']    = sprintf('%.1f', ($row['Max_data_length'] / 1073741824)) . ' GiB';
						$tables[$row['Name']]['RECORDS']    = $row['Rows'];
						$tables[$row['Name']]['AVGLEN']     = sprintf('%.2f', ($row['Avg_row_length'] / 1024)) . ' KiB';
					}
				}


				if ($database['dbSIZE'] > '1024') {
					$database['dbSIZE']     = sprintf('%.2f', ($database['dbSIZE'] / 1024)) . ' MiB';
				} else {
					$database['dbSIZE']     = $database['dbSIZE'] . ' KiB';
				}
				$database['dbTABLECOUNT']   = $rowCount;
			} else {
				// $database['dbERROR'] = mysqli_connect_errno( $dBconn ) .':'. mysqli_connect_error( $dBconn );
			} // end mysqli if $dBconn is good

		} else {
			$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
			$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
			$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
			$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
			$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // hosts default character-set
			$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
			$database['dbCOLLATION']    = $lang['FRCA_U']; // database collation
			$database['dbCHARSET']      = $lang['FRCA_U']; // database character-set
			$database['dbERROR']        = $lang['FRCA_MYSQLI_CONN'];
		} // end of dataBase connection routines


	} elseif ($instance['configDBTYPE'] == 'pdomysql') {

		try {
			$dBconn = new \PDO("mysql:host=" . $instance['configDBHOST'] . ";dbname=" . $instance['configDBNAME'], $instance['configDBUSER'], $instance['configDBPASS']);

			// set the PDO error mode to exception
			$dBconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			$dBconn = FALSE;
		}

		if ($dBconn) {
			$database['dbERROR'] = '0:';

			try {
				$sql = $dBconn->prepare("select name,type,enabled from " . $instance['configDBPREF'] . "extensions where type='plugin' or type='component' or type='module' or type='template' or type='library'");
				$sql->execute();
				$exset = $sql->setFetchMode(PDO::FETCH_ASSOC);
				$exset = $sql->fetchAll();

				$sql = $dBconn->prepare("select template, max(home) as home from " . $instance['configDBPREF'] . "template_styles group by template");
				$sql->execute();
				$tmpldef = $sql->setFetchMode(PDO::FETCH_ASSOC);
				$tmpldef = $sql->fetchAll();
			} catch (PDOException $e) {
				//
			}

			if ($dBconn) {
				$database['dbHOSTSERV']     = $dBconn->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));       // SQL server version
				$database['dbHOSTINFO']     = $dBconn->getAttribute(constant("PDO::ATTR_CONNECTION_STATUS"));         // connection type to dB
				$database['dbHOSTCLIENT']   = $dBconn->getAttribute(constant("PDO::ATTR_CLIENT_VERSION"));                // client library version
				$database['dbHOSTDEFCHSET'] = $dBconn->query("SELECT CHARSET('')")->fetchColumn();      // this is the hosts default character-set
				$database['dbHOSTSTATS']    = explode("  ", $dBconn->getAttribute(constant("PDO::ATTR_SERVER_INFO")));  // latest statistics
			}

			// get the database grants/privilidges
			// added @RussW 5-May-2020
			$privResult = @$dBconn->query("SHOW GRANTS FOR CURRENT_USER");

			while ($row = $privResult->fetch(PDO::FETCH_BOTH)) {
				$database['dbPRIVS'] =  $row[0];
			}

			// find the database collation
			$coResult = $dBconn->query("SHOW VARIABLES LIKE 'collation_database'");

			while ($row =  $coResult->fetch(PDO::FETCH_BOTH)) {
				$database['dbCOLLATION'] =  $row[1];
			}

			// find the database character-set
			$csResult = $dBconn->query("SHOW VARIABLES LIKE 'character_set_database'");

			while ($row = $csResult->fetch(PDO::FETCH_BOTH)) {
				$database['dbCHARSET']  =  $row[1];
			}

			// find all the dB tables and calculate the size
			$tblResult = $dBconn->query("SHOW TABLE STATUS");

			$database['dbSIZE'] = 0;
			$rowCount           = 0;

			while ($row =  $tblResult->fetch(PDO::FETCH_BOTH)) {
				$rowCount++;
				$tables[$row['Name']]['TABLE']  = $row['Name'];

				// count tables with/without same prefix as in config
				if (substr($row['Name'], 0, $dbPrefLen) === $instance['configDBPREF']) {
					$confPrefTables = $confPrefTables + 1;
				} else {
					$notconfPrefTables = $notconfPrefTables + 1;
				}
				$table_size = ($row['Data_length'] + $row['Index_length']) / 1024;
				$tables[$row['Name']]['SIZE'] = sprintf('%.2f', $table_size);
				$database['dbSIZE'] += sprintf('%.2f', $table_size);
				$tables[$row['Name']]['SIZE'] = $tables[$row['Name']]['SIZE'] . ' KiB';


				if ($showTables == '1') {
					$tables[$row['Name']]['ENGINE']     = $row['Engine'];
					$tables[$row['Name']]['VERSION']    = $row['Version'];
					$tables[$row['Name']]['CREATED']    = $row['Create_time'];
					$tables[$row['Name']]['UPDATED']    = $row['Update_time'];
					$tables[$row['Name']]['CHECKED']    = $row['Check_time'];
					$tables[$row['Name']]['COLLATION']  = $row['Collation'];
					$tables[$row['Name']]['FRAGSIZE']   = sprintf('%.2f', ($row['Data_free'] / 1024)) . ' KiB';
					$tables[$row['Name']]['MAXGROW']    = sprintf('%.1f', ($row['Max_data_length'] / 1073741824)) . ' GiB';
					$tables[$row['Name']]['RECORDS']    = $row['Rows'];
					$tables[$row['Name']]['AVGLEN']     = sprintf('%.2f', ($row['Avg_row_length'] / 1024)) . ' KiB';
				}
			}

			if ($database['dbSIZE'] > '1024') {
				$database['dbSIZE']     = sprintf('%.2f', ($database['dbSIZE'] / 1024)) . ' MiB';
			} else {
				$database['dbSIZE']     = $database['dbSIZE'] . ' KiB';
			}
			$database['dbTABLECOUNT']   = $rowCount;
		} else {
			$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
			$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
			$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
			$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
			$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
			$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
			$database['dbCOLLATION']    = $lang['FRCA_U'];
			$database['dbCHARSET']      = $lang['FRCA_U'];
			$database['dbERROR']        = $lang['FRCA_ECON'];
		}
	} elseif ($instance['configDBTYPE'] == 'postgresql') {

		if (function_exists('pg_connect')) {
			$dBconn = @pg_connect("host=" . $instance['configDBHOST'] . " dbname=" . $instance['configDBNAME'] . " user=" . $instance['configDBUSER'] . " password=" . $instance['configDBPASS']);

			if ($dBconn) {
				$database['dbERROR'] = '0:';
				$postgresql = $lang['FRCA_Y'];

				$sql = @pg_query($dBconn, "select name,type,enabled from " . $instance['configDBPREF'] . "extensions where type='plugin' or type='component' or type='module' or type='template' or type='library'");
				$exset = @pg_fetch_all($sql);

				$sql = @pg_query($dBconn, "select template, max(home) as home from " . $instance['configDBPREF'] . "template_styles group by template");
				$tmpldef = @pg_fetch_all($sql);

				if ($dBconn) {
					$database['dbHOSTSERV']     = pg_parameter_status($dBconn, "server_version");       // SQL server version
					$database['dbHOSTINFO']     = $lang['FRCA_U'];                                               // connection type to dB
					$database['dbHOSTCLIENT']   = PGSQL_LIBPQ_VERSION_STR;                              // client library version
					$database['dbHOSTDEFCHSET'] = pg_parameter_status($dBconn, "server_encoding");      // this is the hosts default character-set
					$database['dbHOSTSTATS']    = $lang['FRCA_U'];                                               // latest statistics
					$database['dbCHARSET']      =  pg_parameter_status($dBconn, "client_encoding");
					$sql = pg_fetch_array(pg_query($dBconn, "select encoding from pg_database"));
					$res = $sql[0];
					$val = pg_fetch_array(pg_query($dBconn, "select collname FROM pg_catalog.pg_collation where collencoding = " . $res));
					$database['dbCOLLATION'] =  $val[0];
				}
				$tblResult = pg_query($dBconn, " SELECT relname as name, pg_total_relation_size(relid) As size, pg_total_relation_size(relid) - pg_relation_size(relid) as externalsize FROM pg_catalog.pg_statio_user_tables WHERE relname LIKE '" . $instance['configDBPREF'] . "%' ORDER BY relname ASC");

				// find all the dB tables
				$database['dbSIZE'] = 0;
				$rowCount           = 0;

				while ($row =  pg_fetch_array($tblResult)) {
					$rowCount++;
					$tables[$row['name']]['TABLE']  = $row['name'];

					// count tables with/without same prefix as in config
					if (substr($row['name'], 0, $dbPrefLen) === $instance['configDBPREF']) {
						$confPrefTables = $confPrefTables + 1;
					} else {
						$notconfPrefTables = $notconfPrefTables + 1;
					}
					$cr = pg_fetch_array(pg_query($dBconn, " select count(*) from  " . $tables[$row['name']]['TABLE'] . ""));
					$table_size = ($row['size']) / 1024;
					$tables[$row['name']]['SIZE'] = sprintf('%.2f', $table_size);
					$tables[$row['name']]['SIZE'] = $tables[$row['name']]['SIZE'] . ' KiB';
					$database['dbSIZE'] += sprintf('%.2f', $table_size);

					if ($showTables == '1') {
						$tables[$row['name']]['ENGINE']     = $lang['FRCA_U'];
						$tables[$row['name']]['VERSION']    = $lang['FRCA_U'];
						$tables[$row['name']]['CREATED']    = $lang['FRCA_U'];
						$tables[$row['name']]['UPDATED']    = $lang['FRCA_U'];
						$tables[$row['name']]['CHECKED']    = $lang['FRCA_U'];
						$tables[$row['name']]['COLLATION']  = $database['dbCHARSET'];
						$tables[$row['name']]['FRAGSIZE']   = $lang['FRCA_U'];
						$tables[$row['name']]['MAXGROW']    = $lang['FRCA_U'];
						$tables[$row['name']]['RECORDS']    = $cr['count'];
						$tables[$row['name']]['AVGLEN']     = $lang['FRCA_U'];
					}
				}

				if ($database['dbSIZE'] > '1024') {
					$database['dbSIZE']     = sprintf('%.2f', ($database['dbSIZE'] / 1024)) . ' MiB';
				} else {
					$database['dbSIZE']     = $database['dbSIZE'] . ' KiB';
				}
				$database['dbTABLECOUNT']   = $rowCount;
			} else {
				$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
				$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
				$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
				$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
				$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
				$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
				$database['dbCOLLATION']    = $lang['FRCA_U'];
				$database['dbCHARSET']      = $lang['FRCA_U'];
				$database['dbERROR']        = $lang['FRCA_ECON'];
			}
		} else {
			$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
			$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
			$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
			$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
			$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
			$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
			$database['dbCOLLATION']    = $lang['FRCA_U'];
			$database['dbCHARSET']      = $lang['FRCA_U'];
			$database['dbERROR']        = $lang['FRCA_DI_PHP_FU'];
		}
	} elseif ($instance['configDBTYPE'] == 'pgsql') {

		try {
			$dBconn = new \PDO("pgsql:host=" . $instance['configDBHOST'] . ";dbname=" . $instance['configDBNAME'], $instance['configDBUSER'], $instance['configDBPASS']);

			// set the PDO error mode to exception
			$dBconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			$dBconn = FALSE;
		}

		if ($dBconn) {
			$database['dbERROR'] = '0:';
			$postgresql = $lang['FRCA_Y'];

			try {
				$sql = $dBconn->prepare("select name,type,enabled from " . $instance['configDBPREF'] . "extensions where type='plugin' or type='component' or type='module' or type='template' or type='library'");
				$sql->execute();
				$exset = $sql->setFetchMode(PDO::FETCH_ASSOC);
				$exset = $sql->fetchAll();

				$sql = $dBconn->prepare("select template, max(home) as home from " . $instance['configDBPREF'] . "template_styles group by template");
				$sql->execute();
				$tmpldef = $sql->setFetchMode(PDO::FETCH_ASSOC);
				$tmpldef = $sql->fetchAll();
			} catch (PDOException $e) {
				//
			}

			if ($dBconn) {
				$database['dbHOSTSERV']     = $dBconn->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));       // SQL server version
				$database['dbHOSTINFO']     = $dBconn->getAttribute(constant("PDO::ATTR_CONNECTION_STATUS"));         // connection type to dB
				$database['dbHOSTCLIENT']   = $dBconn->getAttribute(constant("PDO::ATTR_CLIENT_VERSION"));                // client library version
				$database['dbHOSTSTATS']    = $lang['FRCA_U'];
			}

			// find the database collation
			$coResult = $dBconn->query("SELECT * FROM information_schema.character_sets");

			while ($row =  $coResult->fetch(PDO::FETCH_BOTH)) {
				$database['dbCOLLATION'] =  $row['character_set_name'];
				$database['dbCHARSET']  =  $row['character_set_name'];
				$database['dbHOSTDEFCHSET'] =  $row['character_set_name'];
			}

			// find all the dB tables and calculate the size
			$tblResult = $dBconn->query(" SELECT relname as name, pg_total_relation_size(relid) As size, pg_total_relation_size(relid) - pg_relation_size(relid) as externalsize FROM pg_catalog.pg_statio_user_tables WHERE relname LIKE '" . $instance['configDBPREF'] . "%' ORDER BY relname ASC");

			$database['dbSIZE'] = 0;
			$rowCount           = 0;

			while ($row =  $tblResult->fetch(PDO::FETCH_BOTH)) {
				$rowCount++;
				$tables[$row['name']]['TABLE']  = $row['name'];

				// count tables with/without same prefix as in config
				if (substr($row['name'], 0, $dbPrefLen) === $instance['configDBPREF']) {
					$confPrefTables = $confPrefTables + 1;
				} else {
					$notconfPrefTables = $notconfPrefTables + 1;
				}

				$crsql                        = $dBconn->query(" select count(*) from  " . $tables[$row['name']]['TABLE'] . "");
				$cr                           = $crsql->fetch(PDO::FETCH_BOTH);
				$table_size                   = ($row['size']) / 1024;
				$tables[$row['name']]['SIZE'] = sprintf('%.2f', $table_size);
				$tables[$row['name']]['SIZE'] = $tables[$row['name']]['SIZE'] . ' KiB';
				$database['dbSIZE'] += sprintf('%.2f', $table_size);

 // @frostmakk 24.01.2021 This option missing ATM, so just set it.
$showTables = '1';

				if ($showTables == '1') {
					$tables[$row['name']]['ENGINE']     = $lang['FRCA_U'];
					$tables[$row['name']]['VERSION']    = $lang['FRCA_U'];
					$tables[$row['name']]['CREATED']    = $lang['FRCA_U'];
					$tables[$row['name']]['UPDATED']    = $lang['FRCA_U'];
					$tables[$row['name']]['CHECKED']    = $lang['FRCA_U'];
					$tables[$row['name']]['COLLATION']  = $database['dbCOLLATION'];
					$tables[$row['name']]['FRAGSIZE']   = $lang['FRCA_U'];
					$tables[$row['name']]['MAXGROW']    = $lang['FRCA_U'];
					$tables[$row['name']]['RECORDS']    = $cr['count'];
					$tables[$row['name']]['AVGLEN']     = $lang['FRCA_U'];
				}
			}

			if ($database['dbSIZE'] > '1024') {
				$database['dbSIZE']     = sprintf('%.2f', ($database['dbSIZE'] / 1024)) . ' MiB';
			} else {
				$database['dbSIZE']     = $database['dbSIZE'] . ' KiB';
			}
			$database['dbTABLECOUNT']   = $rowCount;
		} else {
			$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
			$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
			$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
			$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
			$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
			$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
			$database['dbCOLLATION']    = $lang['FRCA_U'];
			$database['dbCHARSET']      = $lang['FRCA_U'];
			$database['dbERROR']        = $lang['FRCA_ECON'];
		}
	} elseif ($instance['configDBTYPE'] == 'sqlsrv') {
		$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
		$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
		$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
		$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
		$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
		$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
		$database['dbCOLLATION']    = $lang['FRCA_U'];
		$database['dbCHARSET']      = $lang['FRCA_U'];
	} else {
		$database['dbHOSTSERV']     = $lang['FRCA_U']; // SQL server version
		$database['dbHOSTINFO']     = $lang['FRCA_U']; // connection type to dB
		$database['dbHOSTPROTO']    = $lang['FRCA_U']; // server protocol type
		$database['dbHOSTCLIENT']   = $lang['FRCA_U']; // client library version
		$database['dbHOSTDEFCHSET'] = $lang['FRCA_U']; // this is the hosts default character-set
		$database['dbHOSTSTATS']    = $lang['FRCA_U']; // latest statistics
		$database['dbCOLLATION']    = $lang['FRCA_U'];
		$database['dbCHARSET']      = $lang['FRCA_U'];
	}

	if (isset($dBconn) and $database['dbERROR'] == '0:') {
		$database['dbERROR'] = $lang['FRCA_N'];
	} elseif ($database['dbLOCAL'] == $lang['FRCA_N'] and substr($database['dbERROR'], 0, 4) == '2005') { // 2005 = can't access host
		// if this is a remote host, it might be firewalled or disabled from external or non-internal network access
		$database['dbERROR']    = $database['dbERROR'] . ' ( ' . $lang['FRCA_DBCONNNOTE'] . ' )';
	}
} else { // if no configuration or if configured but dBase credentials aren't valid
	$database['dbDOCHECKS']     = $lang['FRCA_N'];
	$database['dbLOCAL']        = $lang['FRCA_U'];
}

if (!@$dBconn and @$instance['configDBCREDOK'] == $lang['FRCA_PMISS']) {
	$instance['configDBCREDOK'] = $lang['FRCA_N'];
	$database['dbDOCHECKS']     = $lang['FRCA_N'];
}
?>
