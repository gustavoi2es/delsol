<?php 
require("config/config_publico.php");
// EDIT THE 2 LINES BELOW AS REQUIRED
// $send_email_to = "focuse1099@gmail.com";
$send_email_to = C_RECIBE;
$email_subject = "NUEVO CONTACTO DE ".NOMBRE_APLICACION;
function send_email($nombre,$email,$phone,$email_message)
{
  global $send_email_to;
  global $email_subject;
  $headers = "MIME-Version: 1.0" . "\r\n";
  $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
  $headers .= "From: ".$email. "\r\n";
  // $message = "<strong>Email = </strong>".$email."<br>";
  // $message .= "<strong>Nombre = </strong>".$nombre."<br>";  
  // $message .= "<strong>Teléfono = </strong>".$phone."<br>";  
  // $message .= "<strong>Mensaje = </strong>".$email_message."<br>";
  $str_texto = "<a href='".DOMINIO."'><img src='".DOMINIO."/images/logo_email.png'/></a><br><br>";
  $str_texto.= "Nombre: <b>".$nombre."</b><br><br>";
  $str_texto.= "E-mail: <b>".$email."</b><br><br>";
  $str_texto.= "Telefono: <b>".$phone."</b><br><br>";
  $str_texto.= "Mensaje: <b>".$email_message."</b><br><br>";
  $str_texto.= "<a href='".DOMINIO."'>".NOMBRE_APLICACION."</a>";

  $msg_email = "";
  $msg_email.="<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $msg_email.="<tr><td style=\"font-family:Arial; font-size:12px;color:#121212;background:#ffffff;\">";
  $msg_email.="<h3>Se ha enviado un contacto a ".NOMBRE_APLICACION."</h3><br><br>";
  $msg_email.=$str_texto;
  $msg_email.="</td></tr></table>";

  require_once("clases/genericas/util/Mail.class.php");
  $Mail = new Mail();
  $Mail->crearMail(NOMBRE_APLICACION,C_EMAIL,"",$email_subject,$msg_email,array(),array(),0);
  if(C_AUT==1){
    $Mail->autentificar(C_USUARIO,C_CLAVE);
  }
  $coneccion = $Mail->conectar(C_SMTP,C_PUERTO,30);
  $Mail->agregarDestinatario(C_RECIBE,"");
  $enviar = $Mail->enviar();

  // RECEPCIONA EL CLIENTE
  $msg_email = "";
  $msg_email.="<table width=\"600\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
  $msg_email.="<tr><td style=\"font-family:Arial; font-size:12px;color:#121212;background:#ffffff;\">";
  $msg_email.="Estimado/a ".$nombre.", tu mensaje ha sido recibido en ".NOMBRE_APLICACION."<br><br>";
  $msg_email.=$str_texto;
  $msg_email.="</td></tr></table>";

  $Mail_cliente = new Mail();
  $Mail_cliente->crearMail(NOMBRE_APLICACION,C_EMAIL,"","Tu mensaje ha sido recibido correctamente",$msg_email,array(),array(),0);
  if(C_AUT==1){
    $Mail_cliente->autentificar(C_USUARIO,C_CLAVE);
  }
  $coneccion = $Mail_cliente->conectar(C_SMTP,C_PUERTO,30);
  $Mail_cliente->agregarDestinatario($email,$nombre);
  $enviar_cliente = $Mail_cliente->enviar();

  // @mail($send_email_to, $email_subject, $message,$headers);
  return true;
}

function validate($name,$email,$message)
{
  $return_array = array();
  $return_array['success'] = '1';
  $return_array['name_msg'] = '';
  $return_array['email_msg'] = '';
  $return_array['message_msg'] = '';
  if($email == '')
  {
    $return_array['success'] = '0';
    $return_array['email_msg'] = 'email is required';
  }
  else
  {
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if(!preg_match($email_exp,$email)) {
      $return_array['success'] = '0';
      $return_array['email_msg'] = 'enter valid email.';  
    }
  }
  if($name == '')
  {
    $return_array['success'] = '0';
    $return_array['name_msg'] = 'name is required';
  }
  else
  {
    $string_exp = "/^[A-Za-z .'-]+$/";
    if (!preg_match($string_exp, $name)) {
      $return_array['success'] = '0';
      $return_array['name_msg'] = 'enter valid name.';
    }
  }
		
  if($message == '')
  {
    $return_array['success'] = '0';
    $return_array['message_msg'] = 'message is required';
  }
  else
  {
    if (strlen($message) < 2) {
      $return_array['success'] = '0';
      $return_array['message_msg'] = 'enter valid message.';
    }
  }
  return $return_array;
}

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];


$return_array = validate($name,$email,$phone,$message);

if($return_array['success'] == '1')
{
	send_email($name,$email,$phone,$message);
}
header('Content-type: text/json');
echo json_encode($return_array);
die();
?>

