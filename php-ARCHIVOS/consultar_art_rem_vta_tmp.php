<?
session_start();   // Iniciar sesi�n
$usuario_rem = $_SESSION['user_usuario']; //usuario conectado

include("conexion.php");
$consulta = "SELECT * FROM remito_vta_tmp where usuario = '$usuario_rem'"; // consulta sql
$result = mysql_query($consulta);            // hace la consulta
$nfilas = mysql_num_rows ($result);          		//indica la cantidad de resultados
if ($nfilas > 0){ 
	echo "si";
}else{
	echo "no";
}
?>