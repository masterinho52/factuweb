<?

		$nombre = strtoupper($nombre);
		if($nombre == "TODOS"){
			$pag_consulta = "SELECT * FROM vendedor"; 									// consulta sql
		}else{
			if (!empty($nombre)){
				$pag_consulta = "SELECT * FROM vendedor where nombre LIKE '%$nombre%'"; 									// consulta sql
			}else{
				$pag_consulta = "SELECT * FROM vendedor INNER JOIN cliente ON cliente.cod_vendedor = vendedor.cod_vendedor where cliente.cod_cliente = $cliente"; 									// consulta sql
				$nombre=$cliente;
			}
			//$pag_consulta .=" order by vendedor.cod_vendedor";
	 	}
		
		if(!empty($campo) && !empty($orden)) {
			$pag_consulta .=" order by $campo $orden";
		}else{
			//por defecto
			$pag_consulta .=" order by vendedor.cod_vendedor";		
		}


	//---------------------Paginacion----------------------------------------------------------------------------------------//
	$estilo_pag_nav = "barra_nav";		//OPCIONAL Cadena. Contiene el nombre del estilo CSS para los enlaces de paginación.
	$estilo_pag_actual = "barra_nav_actual";		//OPCIONAL Cadena. Contiene el nombre del estilo CSS para la pagina actual.
	$estilo_pag_info = "caja";			//OPCIONAL Cadena. Contiene el nombre del estilo CSS para la pagina actual.
	
	include("paginador.php");			//OBLIGATORIO Incluimos el script de paginación. Éste ya ejecuta la consulta automáticamente
	//---------------------Fin Paginacion------------------------------------------------------------------------------------//
if($pag_filas > 0){
	echo $pag_info;  					//info de paginacion
	//---------------------abre la tabla--------------------------------------------------------------------------------------//
	echo "<table width='100%'  border='0'cellspacing='1' cellpadding='0'>";
      	
		echo "<tr class='top'>";
        	echo "<td width='10%'><div align='center' class='seccion'>Codigo</div></td>";
        	echo "<td width='37%'><div align='center' class='seccion'>Nombre y Apellido</div></td>";
        	echo "<td width='15%'><div align='center' class='seccion'>Direccion</div></td>";
			echo "<td width='15%'><div align='center' class='seccion'>Localidad</div></td>";
			echo "<td width='15%'><div align='center' class='seccion'>Telefono</div></td>";
			echo "<td width='8%'><div align='center' class='seccion'>Modificar</div></td>";
		echo "</tr>";
				
		$clase="class='filas'"; 							//defino la clase para las filas

		while($registro = mysql_fetch_array($pag_result)){ 	// obtengo los resultados 
				$codigo=$registro[0];		
				$nombre=$registro[2];
				$dir=$registro[3];
				$tel=$registro[4];
				$cod_localidad=$registro[5];
				//$cod_provincia=$registro[6];
				
				$consulta ="select * from localidad where cod_localidad = $cod_localidad";
				$result = mysql_query($consulta);            // hace la consulta
				$reg = mysql_fetch_row($result);        // toma el registro
				$localidad= $reg[3];
				
				
				//$consulta ="select * from provincia where cod_prov = $cod_provincia";
				//$result = mysql_query($consulta);            // hace la consulta
				//$reg = mysql_fetch_row($result);        // toma el registro
				//$provincia= $reg[2];

				//echo "<tr onmouseover=color_seleccion(this,'EAEAEA'); onmouseout=color_defecto(this,'F7F7F7'); bgcolor='#F7F7F7'>"; //efecto ded color en las filas
				echo "<tr onMouseOver=color_seleccion(this,'E0E0E0'); onMouseOut=color_defecto(this,'EAEAEA'); bgcolor='#EAEAEA'>"; //efecto ded color en las filas
					echo "<td $clase align='center'>";
							echo $codigo;     
					echo"</td>";
					echo "<td $clase align='left'>";
							echo $espacio_izq.$nombre;     
					echo"</td>";
					echo "<td $clase align='left'>";
							echo $espacio_izq.$dir;     
					echo"</td>";
					echo "<td $clase align='left'>";
							echo $espacio_izq.$localidad;     
					echo"</td>";
					echo "<td $clase align='left'>";
							echo $espacio_izq.$tel;     
					echo"</td>";
					
					$consulta_cliente =  "select cod_cliente from cliente where cod_vendedor = $codigo";	 // consulta en la base de datos si ya no se ha registrado el stock inicial
					$result_cliente = mysql_query($consulta_cliente);            // hace la consulta
					$nfilas_cliente = mysql_num_rows ($result_cliente);          //indica la cantidad de resultados

					$consulta_comision =  "select cod_vendedor from comision_vendedor where cod_vendedor = $codigo";	 // consulta en la base de datos si ya no se ha registrado el stock inicial
					$result_comision = mysql_query($consulta_comision);            // hace la consulta
					$nfilas_comision = mysql_num_rows ($result_comision);          //indica la cantidad de resultados

					$consulta_devolucion =  "select n_devolucion from devolucion where cod_vendedor = $codigo";	 // consulta en la base de datos si ya no se ha registrado el stock inicial
					$result_devolucion = mysql_query($consulta_devolucion);            // hace la consulta
					$nfilas_devolucion = mysql_num_rows ($result_devolucion);          //indica la cantidad de resultados

					echo"<td $clase align='center'>";	
							if ( $nfilas_cliente == 0 && $nfilas_comision == 0 && $nfilas_devolucion == 0){     						 // si existe el usuario inicia la sesion
									echo "<img  class='iconos' src='../imagenes/modificar.png' border='0' title='Modificar' onClick='javascript: modificar_vendedor($codigo)'>"; 
							}else{
									echo "<img  src='../imagenes/modificar_no.png' border='0'>";
							}
					echo"</td>"; 

					/*
					echo"<td $clase align='center'>"; 
							echo "<img class='iconos' src='../imagenes/eliminar.png' border='0' title='Eliminar' onClick='javascript: eliminar_pais($codigo)'>"; //onClick='javascript: abreventanaprecio($codigo)'
					echo"</td>"; 		
					*/
				echo"</tr>";
		} //end while
		
	echo "</table>";   
	//---------------------cierra tabla--------------------------------------------------------------------------------------//
	echo $pag_navegacion;
	
	//================================ OBTIENE LA CONSULTA PARA IMPRIMIR ==================================================================================//				
	$posicion_limit = strrpos ($pag_consulta, "limit"); 		
	$consulta_informe = substr($pag_consulta, 0,$posicion_limit); 		// obtiene solo la info de la impresora
	$consulta_informe = ereg_replace("'","@@",$consulta_informe);
	
	include('consulta_session.php');		// crea el div y el id de session del sql
	//================================ FIN DE OBTIENE LA CONSULTA PARA IMPRIMIR ===========================================================================//				

	
}else{
echo "<div class='$estilo_pag_info'>NO se han encontrado Registros</div>";
}	
?>
