<? 
// Obtiene el numero de remito (*1)	*********************************************************************************************
	include("conexion.php");
	
	$consulta = "SELECT * FROM tipo_talonario where descripcion LIKE '%recibo%' or descripcion like '%RECIBO%'";  // obtiene el cod del remito
    $result = mysql_query($consulta);           														   
   	$registro = mysql_fetch_row($result);        															
	$cod_tt= $registro[0]; 					// obtiene la letra que identifica al remito
		
	$nfilas = 0;
	$consulta = "SELECT num_recibo FROM cc_vta"; 				// verifica si se han hecho remitos a un cliente
    $result = mysql_query($consulta);            					
	$nfilas = mysql_num_rows ($result);          					
	

	if ($nfilas > 0){												// si existen registros en la tabla remitos de clientes
		$consulta = "SELECT max(num_recibo) FROM cc_vta"; 
    	$result = mysql_query($consulta);            
		$registro = mysql_fetch_row($result);        
		$num_recibo = $registro[0]+1;
	}
	
	$consulta = "SELECT max(num_talonario) FROM talonario where cod_talonario = '$cod_tt'"; // obtiene el numero del talonario
    $result = mysql_query($consulta);            // hace la consulta
   	$registro = mysql_fetch_row($result);        // toma el registro
	$numero_tal= $registro[0];
		
	$consulta = "SELECT n_sucursal,sig_num, ultimo_num FROM talonario where num_talonario = $numero_tal and cod_talonario = '$cod_tt' "; // obtiene el numero de sucursal
	$result = mysql_query($consulta);            // hace la consulta
   	$registro = mysql_fetch_row($result);        // toma el registro
	$num_sucursal = $registro[0];
	if(empty($num_recibo)){
		$num_recibo = $registro[1];
	}
	$ultimo_num = $registro[2];
	
// Completo con ceros para mostrar ******************************************************************************************************
	$len_numero_tal=strlen($numero_tal); // completo el numero del remito con ceros
	while ($len_numero_tal < 4){
		$ceros.="0";
		$len_numero_tal++;
	}
	$numero_tal=$ceros.$numero_tal;

	$len_num_recibo=strlen($num_recibo); // completo el numero del remito con ceros
	while ($len_num_recibo < 8){
		$ceros.="0";
		$len_num_recibo++;
	}
	$num_recibo=$ceros.$num_recibo;
	
	$len_num_sucursal=strlen($num_sucursal); // completo el numero de la sucursal con ceros
	while ($len_num_sucursal < 4){
		$ceros_2.="0";
		$len_num_sucursal++;
	}
	$num_sucursal=$ceros_2.$num_sucursal;


	if ($num_recibo > $ultimo_num){			//control para que no se facture un numero que exeda el talonario
					$error="1";
	}else{
					$error="0";
	}


// Creo el XML **************************************************************************************************************************
	header('Content-Type: text/xml'); 			// encabezado obligatorio XML
	echo "<remitos>\n"; 						// etiqueta superior
	if($numero_tal != 0){
		echo '<codigo_tal>' . $cod_tt . '</codigo_tal>';   
		echo '<numero_tal>' . $numero_tal . '</numero_tal>';   
		echo '<numero_suc>' . $num_sucursal . '</numero_suc>';         
		echo '<numero_rem>' . $num_recibo . '</numero_rem>';
		echo '<error>' . $error . '</error>';

	}else{
		$error_mxl='ERROR';
		echo '<codigo_tal>' . $error_mxl . '</codigo_tal>';   
		echo '<numero_tal>' . $error_mxl . '</numero_tal>';   
		echo '<numero_suc>' . $error_mxl . '</numero_suc>';         
		echo '<numero_rem>' . $error_mxl . '</numero_rem>';
		echo '<error>' . $error . '</error>';

	}
	echo "</remitos>";
?>