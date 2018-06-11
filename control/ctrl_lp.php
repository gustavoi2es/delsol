<? 
session_start();
if(CONTROL_ERROR==1){
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	ini_set("error_log",PATH_ERROR);
}else{
	ini_set("display_errors",0);
}
require("clases/genericas/sql/Db.class.php");

try{
	$arConexion=array(1,BD_HOST,BD_USUARIO_NAVEGANTE,BD_CLAVE_NAVEGANTE,BD_NOMBRE);
	$obj_db = new Db($arConexion);
	$obj_db->conectar();
}catch(Exception $e){
	echo $e->getMessage();
}
			
require("clases/genericas/util/Util.class.php");

$objUtil = new Util();
$objUtil->escape();

		
if(isset($strEstado)){
	
	if($strEstado == "LP" || $strEstado == "EnviarLP"){
		
		$mostrarFormulario = true;
		$strMensaje = "";

		if($strEstado == "EnviarLP"){
				$mensaje = "";
				$error = 0;
				
				if($error==0){
						
						$nombre = $objUtil->verificarAtaque($_POST["nombre"]);
						$email = $objUtil->verificarAtaque(strtolower($_POST["email"]));
						
						$str_texto = "<a href='".DOMINIO."'><img src='".DOMINIO."/images/logo_email.png'/></a><br><br>";
						$str_texto.= "Nombre: <b>".$nombre."</b><br><br>";
						$str_texto.= "E-mail: <b>".$email."</b><br><br>";
						$str_texto.= "<a href='".DOMINIO."'>".NOMBRE_APLICACION."</a>";
						
						////////////////////////////////////////////////////////////////////////////////////////////////////
						// CONTACTO
						////////////////////////////////////////////////////////////////////////////////////////////////////
						
						// $str_up = "INSERT INTO cms_contacto (nombre,telefono,email,texto) VALUES ('[SORTEO] ".$nombre."','".$telefono."','".$email."','".$str_texto."')";
						// if(!$obj_db->executeUpdate($str_up)){
						// 	$mensaje.="1- Por motivos inesperados no se ha podido cumplir con la solicitud.";
						// 	$error++;
						// }
						
						////////////////////////////////////////////////////////////////////////////////////////////////////
						// UMS
						////////////////////////////////////////////////////////////////////////////////////////////////////
						
						$ingreso_ums = false;
						$str_sel = "SELECT * FROM ums_suscripcion_email WHERE email='".$email."'";

						$rs = $obj_db->obtenerResultset($str_sel);
						if(!$obj_db->executeResultset($rs)){
							$codigo = $objUtil->obtenerTicket();
							$clave = md5("sinclave");
							$id_grupo = 3;
							$str_up = "INSERT INTO ums_suscripcion (nombre,codigo,usuario,clave,id_grupo,fecha,fecha_ingreso) 
								VALUES ('".$nombre."','".$codigo."','".$email."','".$clave."',".$id_grupo.",NOW(),NOW())";
								// echo $str_up."echo";
								if($obj_db->executeUpdate($str_up)){
										$ultimo = 0;
										$strCon = "SELECT LAST_INSERT_ID() AS ultimo";
										$rs = $obj_db->obtenerResultset($strCon);
										if($ar = $obj_db->executeResultset($rs)){
											$ultimo = $ar["ultimo"];
										}
										$str_up = "INSERT INTO ums_suscripcion_email (id_suscripcion,email) VALUES ('".$ultimo."','".$email."')";
										if(!$obj_db->executeUpdate($str_up)){
											$mensaje.="2- Por motivos inesperados no se ha podido cumplir con la solicitud.";
											$error++;
										}
								}else{
									$mensaje.="3- Por motivos inesperados no se ha podido cumplir con la solicitud.";
									$error++;
								}
						
						// echo $mensaje;
						////////////////////////////////////////////////////////////////////////////////////////////////////
						// ENVIO DE EMAIL
						////////////////////////////////////////////////////////////////////////////////////////////////////
						if($error==0){
							$msg_email = "";
							$msg_email.="<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
							$msg_email.="<tr><td style=\"font-family:Arial; font-size:12px;color:#121212;background:#ffffff;\">";
							$msg_email.="<h3>Se ha suscripto un nuevo usuario en ".NOMBRE_APLICACION."</h3><br><br>";
							$msg_email.=$str_texto;
							$msg_email.="</td></tr></table>";
		
							require_once("clases/genericas/util/Mail.class.php");
							$Mail = new Mail();
							$Mail->crearMail(NOMBRE_APLICACION,C_EMAIL,"","Nueva sucripcion en ".NOMBRE_APLICACION."",$msg_email,array(),array(),0);
							if(C_AUT==1){
								$Mail->autentificar(C_USUARIO,C_CLAVE);
							}
							$coneccion = $Mail->conectar(C_SMTP,C_PUERTO,30);
							$Mail->agregarDestinatario(C_RECIBE,"");
							$enviar = $Mail->enviar();

							if(!$enviar){
								echo "Error en el envío de correo 1";
							}
							// ---- //

							// RECEPCIONA EL CLIENTE
							$msg_email = "";
							$msg_email.="<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
							$msg_email.="<tr><td style=\"font-family:Arial; font-size:12px;color:#121212;background:#ffffff;\">";
							$msg_email.="Estimado/a ".$nombre.", te has suscripto en ".NOMBRE_APLICACION."<br><br>";
							$msg_email.=$str_texto;
							$msg_email.="</td></tr></table>";
		
							$Mail_cliente = new Mail();
							$Mail_cliente->crearMail(NOMBRE_APLICACION,C_EMAIL,"","Te has suscripto correctamente",$msg_email,array(),array(),0);
							if(C_AUT==1){
								$Mail_cliente->autentificar(C_USUARIO,C_CLAVE);
							}
							$coneccion = $Mail_cliente->conectar(C_SMTP,C_PUERTO,30);
							$Mail_cliente->agregarDestinatario($email,$nombre);
							$enviar_cliente = $Mail_cliente->enviar();

							if(!$enviar_cliente){
								echo "Error en el envío de correo 2";
							}
						}
						/////////////////////////////////////////////////////////////////////////////////////////////////////
						echo $mensaje;
					}else{
						echo "Tu ya te encuentras suscripto para recibir nuestros newsletters. Muchas gracias.";
					}
				}
			
		}
	}
}

?>