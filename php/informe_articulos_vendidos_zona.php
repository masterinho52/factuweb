<?
$codigo = strtoupper($codigo); 
if($codigo){	
	//---------------------- INCLUYE CONEXION A BD -----------------------------------------------// 
	include("conexion.php");
	//include("sql_inicio.php");	//obtiene el microtime() de inicio
	
	// Obtiene el detalle de todos los comprobantes Factura Vta Cliente
    $consulta ="select cod, descripcion, SUM(cant), (SUM(cant)* unidad_bulto)AS total_envase ,envase, grupo, sum(pesos), medida, zona, fecha FROM ( 
				
				select concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)as cod, descripcion,SUM(cantidad) as cant, unidad_bulto, envase, fecha, cod_repartidor,(producto.cod_grupo)as grupo, sum(producto.peso)as pesos, medida, factura_vta.cod_zona as zona, concat(cliente.cod_cliente,' - ', cliente.razon_social) as nombre_cliente  from cliente inner join (factura_vta inner join (factura_vta_detalle inner join producto on concat(factura_vta_detalle.cod_grupo, factura_vta_detalle.cod_marca, factura_vta_detalle.cod_variedad, factura_vta_detalle.cod_prod) = concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)) on factura_vta_detalle.n_factura = factura_vta.n_factura AND factura_vta_detalle.cod_talonario = factura_vta.cod_talonario AND factura_vta_detalle.num_talonario = factura_vta.num_talonario) on factura_vta.cod_cliente = cliente.cod_cliente 
				where observacion <> 'ANULADO' and observacion <> 'N/C'";
							
				if (!empty($codigo)){
							$consulta .= " and factura_vta.cod_zona = $codigo "; 
				}		
				if (!empty($fecha_desde)){
							$consulta .= " and fecha >= $fecha_desde"; 
				}
				if (!empty($fecha_hasta)){
							$consulta .= " and fecha <= $fecha_hasta"; 
				}
				if (!empty($cod_art)){
							$consulta .= " and concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod) = $cod_art"; 
				}
				if ( $cod_grupo != 'TODOS'){
							$consulta .= " and producto.cod_grupo = $cod_grupo"; 
				}		
				if ($cod_marca != 'TODOS'){
							$consulta .= " and producto.cod_marca = $cod_marca"; 
				}		
				if ($cod_variedad != 'TODOS'){
							$consulta .= " and producto.cod_variedad = $cod_variedad"; 
				}		
	$consulta .= " GROUP BY  factura_vta.fecha,concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)";  //factura_vta_no_cliente.cod_zona , concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)
	$consulta .= " UNION ALL
				select concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)as cod, descripcion,SUM(cantidad) as cant, unidad_bulto, envase, fecha, cod_repartidor,(producto.cod_grupo)as grupo, sum(producto.peso)as pesos, medida, factura_vta_no_cliente.cod_zona as zona, factura_vta_no_cliente.razon_social as nombre_cliente from factura_vta_no_cliente inner join (factura_vta_no_cliente_detalle inner join producto on concat(factura_vta_no_cliente_detalle.cod_grupo, factura_vta_no_cliente_detalle.cod_marca, factura_vta_no_cliente_detalle.cod_variedad, factura_vta_no_cliente_detalle.cod_prod) = concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)) on factura_vta_no_cliente_detalle.n_factura = factura_vta_no_cliente.n_factura AND factura_vta_no_cliente_detalle.cod_talonario = factura_vta_no_cliente.cod_talonario AND factura_vta_no_cliente_detalle.num_talonario = factura_vta_no_cliente.num_talonario 
				where observacion <> 'ANULADO' and observacion <> 'N/C' "; 
							
							if (!empty($codigo)){
									$consulta .= " and factura_vta_no_cliente.cod_zona = $codigo "; 
							}
							if (!empty($fecha_desde)){
									$consulta .= " and fecha >= $fecha_desde"; 
							 }		
							if (!empty($fecha_hasta)){
									$consulta .= " and fecha <= $fecha_hasta"; 
							}
							if (!empty($cod_art)){
									$consulta .= " and concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod) = $cod_art"; 
							}
							if ( $cod_grupo != 'TODOS'){
									$consulta .= " and producto.cod_grupo = $cod_grupo"; 
							}		
							if ($cod_marca != 'TODOS'){
									$consulta .= " and producto.cod_marca = $cod_marca"; 
							}		
							if ($cod_variedad != 'TODOS'){
									$consulta .= " and producto.cod_variedad = $cod_variedad"; 
							}		
			$consulta .=" GROUP BY  factura_vta_no_cliente.fecha,concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)";  //factura_vta_no_cliente.cod_zona , concat(producto.cod_grupo, producto.cod_marca, producto.cod_variedad, producto.cod_prod)
	$consulta .= ") AS carga_articulos GROUP BY  fecha,cod ORDER BY fecha,cod"; // grupo, SUM(cant)

	//include("sql_fin.php");	//obtiene el microtime() de fin, retorna: $tiempo_final

	//echo 'Tiempo de Carga: '.$tiempo_final.'<br>';
	
	function formatear_fecha($fecha){
		$ano=substr($fecha,0,4);
		$mes=substr($fecha,4,2);
		$dia=substr($fecha,-2);
		return $dia."/".$mes."/".$ano;
	}

	//echo $consulta ;
	$result = mysql_query($consulta);            // hace la consulta
	$registro = mysql_fetch_row($result);        // toma el registro
	$filas = mysql_num_rows ($result);          //indica la cantidad de resultados

	if($filas > 0){
		//---------------------abre la tabla--------------------------------------------------------------------------------------//
		echo "<br>";
		echo "<div  align='right' class='seccion'>";
			echo "<img src='../imagenes/pdf.gif' width='18' height='18' border='0' class='iconos'  title='Exportar' onClick=\"javascript: exportar_informe_ranking_articulos_zona('exportar_informe_ranking_art_zona.php')\" /> pdf  &nbsp;&nbsp;<img src='../imagenes/imprimir.png' width='18' height='18' title='Imprimir' class='iconos' onClick=\"javascript: imprimir_informe_ranking_art_zona('exportar_informe_ranking_art_zona.php')\" /> imprimir";
		echo "</div>";
		echo "<br>";
		
		echo "<table width='100%'  border='0'cellspacing='1' cellpadding='0'>";
			echo "<tr class='top'>";
				echo "<td width='7%' ><div align='center' class='seccion'>CODIGO</div></td>";
				echo "<td width='72%' ><div align='center' class='seccion'>DESCRIPCION</div></td>";
				echo "<td width='7%' ><div align='center' class='seccion'>BULTOS</div></td>";
				echo "<td width='7%' ><div align='center' class='seccion'>ENVASES</div></td>";
				echo "<td width='7%' ><div align='center' class='seccion'>CAJONES</div></td>";
			echo "</tr>";
			$clase="class='filas'"; 							//defino la clase para las filas
			
			$fecha_anterior = '';
			do{ 					// obtengo los resultados 
					$codigo = $registro[0];
					$desc = $registro[1];
					$bulto = $registro[2];
					$tiene_envase = $registro[4];
					$peso = $registro[6];
					$zona = $registro[8];
					$fecha = $registro[9];
										
					if($tiene_envase == "SI"){
							$envase=$registro[3];
							$envase = number_format($envase,0,'.','');
							$total_envase= $total_envase + $envase;
							
							$cajon = round($bulto);
							if($bulto > $cajon){
								$cajon++;
							}
							$cajon = number_format($cajon,0,'.','');
							$total_cajon= $total_cajon + $cajon;
					}else{
							$envase=' ';
							$total_envase= $total_envase + 0;				
							$cajon = ' ';
							$total_cajon= $total_cajon + 0;
					}
					
					$total_bulto = $total_bulto + $bulto;
					$total_peso = (($total_peso + $peso)*100)/100;
					
					if($fecha_anterior == ''){
							$fecha_anterior = $fecha;
							echo"<tr bgcolor='#E0E0E0' >";			
								echo "<td colspan='5' class = 'content' align='left'>";
									echo formatear_fecha($fecha_anterior) ;										// maqueta la fecha para imprimir
								echo"</td>";
							echo "<tr>";
	
							echo "<tr onMouseOver=color_seleccion(this,'E0E0E0'); onMouseOut=color_defecto(this,'EAEAEA'); bgcolor='#EAEAEA'>"; //efecto ded color en las filas
								echo "<td $clase align='left'>";
										echo $espacio_izq.$codigo;     
								echo"</td>";
								echo "<td $clase align='left'>";
										echo $espacio_izq.$desc;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo number_format($bulto,1,'.','').$espacio_izq;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo $envase.$espacio_izq;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo $cajon.$espacio_izq;     
								echo"</td>";
							echo"</tr>";
					}else{
							if($fecha_anterior != $fecha){
								$fecha_anterior = $fecha;
								echo"<tr bgcolor='#E0E0E0' >";			
									echo "<td colspan='5' class = 'content' align='left'>";
											echo formatear_fecha($fecha_anterior) ;   
									echo"</td>";
								echo "<tr>";
							}	
	
							echo "<tr onMouseOver=color_seleccion(this,'E0E0E0'); onMouseOut=color_defecto(this,'EAEAEA'); bgcolor='#EAEAEA'>"; //efecto ded color en las filas
								echo "<td $clase align='left'>";
										echo $espacio_izq.$codigo;     
								echo"</td>";
								echo "<td $clase align='left'>";
										echo $espacio_izq.$desc;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo number_format($bulto,1,'.','').$espacio_izq;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo $envase.$espacio_izq;     
								echo"</td>";
								echo "<td $clase align='right'>";
										echo $cajon.$espacio_izq;     
								echo"</td>";
							echo"</tr>";
	
					}
					
			}while($registro = mysql_fetch_row($result)); // obtengo los resultados 		
					$total_bulto = number_format($total_bulto,1,'.','');
					$total_envase = number_format($total_envase,0,'.','');
					$total_cajon = number_format($total_cajon,0,'.','');
	
					echo"<tr bgcolor='#E0E0E0' >";			
						echo"<td colspan='2' align='left'>&nbsp;&nbsp;TOTALES	</td>";
						echo"<td align='right'>$total_bulto$espacio_izq</td>";
						echo"<td align='right'>$total_envase$espacio_izq</td>";
						echo"<td align='right'>$total_cajon$espacio_izq</td>";
						echo"<td align='center'></td>";
					echo"</tr>";
		echo "</table>";   
		//---------------------cierra tabla   BULTOS  --------------------------------------------------------------------------------------//

	}else{
		echo 'NO se encontraron ventas';
	}
}else{
	require("smarty.php");  // requiere la pag "smarty.php" para crear una instancia de Smarty 
	$smarty = new ClaseSmarty; //crea una instancia
	$smarty->assign('dia',date("d",time()));  //asigna una cadena a la variable "nombre"
	$smarty->assign('mes',date("m",time()));  //asigna una cadena a la variable "nombre"
	$smarty->assign('ano',date("Y",time()));  //asigna una cadena a la variable "nombre"

	//=============CONTROL DE PERMISO PARA EL ACCESO AL MODULO=============//
	$modulo="informes";
	$plantilla = "informe_articulos_vendidos_zona.tpl";
	require("validar_permiso.php");	 
}
?>
