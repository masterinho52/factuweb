<?
if($cod_repartidor){
	include("conexion.php");
	
	$consulta = "SELECT cod_pais FROM fletero where cod_flero = $cod_repartidor"; // consulta sql
	$result = mysql_query($consulta);            // hace la consulta
	$registro = mysql_fetch_row($result);        // toma el registro
	$cod_pais= $registro[0];
	
	$consulta = "SELECT * FROM pais order by nombre"; // consulta sql 
	$result = mysql_query($consulta);            // hace la consulta
	$registro = mysql_fetch_row($result);        // toma el registro
	echo"<select name='lista_pais_mod' id='lista_pais_mod' class='caja'  onKeyUp='pasar_foco_rep_5_mod(event,this.value)'>"; 
		do{
				$codigo=$registro[0];
				$nombre=$registro[1];
				if ( $codigo == $cod_pais){
					echo "<option selected value=$codigo>$nombre</option>";
				}else{
					echo "<option value=$codigo>$nombre</option>";
				}
		}while($registro = mysql_fetch_row($result)); // obtengo los resultados 
	echo"</select>";
}
?>