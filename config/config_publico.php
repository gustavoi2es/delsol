<? 
/**************************************************************************
          ARCHIVO DE CONFIGURACION PARA APLICACION PUBLICA
***************************************************************************/

////////////////////////////////////////////////////////////////////////////
// BASE DE DATOS ///////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

define("BD_HOST", "localhost"); // Servidor de base de datos
define("BD_NOMBRE", "focusums_ums_v1"); // Base de datos
define("BD_USUARIO_NAVEGANTE", "root"); // Usuario comun
define("BD_USUARIO_CLIENTE", "root"); // Usuario cliente
define("BD_CLAVE_NAVEGANTE", "wailers"); // Clave de usuario comun
define("BD_CLAVE_CLIENTE", "wailers"); // Clave para el cliente

////////////////////////////////////////////////////////////////////////////
// RUTAS DE LA APLICACION //////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

define("ROOT",$_SERVER['DOCUMENT_ROOT']."/"); // Ruta física absoluta
define("ROOT_PUB","/"); // Ruta física relativa

define("UI_DIR",$_SERVER['DOCUMENT_ROOT']."/"); // Ruta de templates (Smarty)
define("UI_COMPILE",true); // Compilación de templates

define("CLASS_DIR",$_SERVER['DOCUMENT_ROOT']."/clases/"); // Ruta física absoluta de clases

define("PATH_IMAGENES","imagenes/img_contenido/"); // Ruta física  realtiva de imagenes
define("URL_IMAGENES","http://lpdelsol.i2es.biz:85/imagenes/img_contenido/"); // Url absoluta de imagenes

////////////////////////////////////////////////////////////////////////////
// DATOS DE DOMINIO Y CONTROL DE ACCESO ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

define("DOMINIO", "http://lpdelsol.i2es.biz:85"); // Dominio de la aplicacion
define("DOMINIO_SSL", "http://lpdelsol.i2es.biz:85"); // Dominio seguro de la aplicación
define("CONTROL_SSL", 0); // Control de servidro seguro para información confidencial
define("NOMBRE_APLICACION", "DEL SOL"); // Nombre en barra de navegacion


////////////////////////////////////////////////////////////////////////////
// CASILLA DE CORREO ///////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

define("C_EMAIL","test@i2es.com");
define("C_USUARIO","test@i2es.com");
define("C_CLAVE","i2estest");
define("C_SMTP","mail.i2es.com");
define("C_PUERTO",25);
define("C_AUT",1);
define("C_RECIBE","gustavo@i2es.info");


////////////////////////////////////////////////////////////////////////////
// CONTROL DE ERRORES //////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

define("CONTROL_ERROR",1);
define("PATH_ERROR",ROOT."log/error_publico.log");
?>