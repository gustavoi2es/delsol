<? 
require ("class.smtp.php");
require ("class.phpmailer.php");
class Mail{
	
	var $coneccion;
	
	var $servidorSMTP;
	var $puerto;
	var $tiempo;
	var $host;
	
	var $auth = 0;
	var $usuario="";
	var $clave = "";
	
	var $error = 0;
	var $strError = "";
	
	var $nombreFrom;
	var $emailFrom;
	var $replyTo;
	var $asunto;
	var $cuerpo;
	var $arAttach=array();
	var $arImg=array();
	
	var $nombreRcpt;
	var $emailRcpt;
	
	var $smtp;
	var $phpmailer;
	var $idMsg;
	
	function Mail(){
		$this->smtp = new SMTP();
	}
	
	function crearMail($nombreFrom,$emailFrom,$replyTo,$asunto,$cuerpo,$arAttach,$arImg,$idMsg,$host=""){ // Void
		
		$this->phpmailer = new phpmailer();
		$this->nombreFrom = $this->phpmailer->EncodeHeader($this->phpmailer->SecureHeader($nombreFrom));
		$this->emailFrom = $emailFrom;
		$this->replyTo = $replyTo;
		$this->asunto =  $this->phpmailer->EncodeHeader($this->phpmailer->SecureHeader($asunto));
		$this->cuerpo = $cuerpo;
		$this->arAttach = $arAttach;
		$this->arImg = $arImg;
		$this->idMsg = md5(uniqid(time()));
		if($host==""){
			$this->host = $_SERVER['SERVER_NAME'];
		}else{
			$this->host = $host;
		}
	}
	
	function agregarDestinatario($emailRcpt,$nombreRcpt){ // Void
		$this->nombreRcpt = $nombreRcpt;
		$this->emailRcpt = $emailRcpt;
	}
	
	function quitarDestinatario(){ // Void
		$this->nombreRcpt = "";
		$this->emailRcpt = "";
	}
	
	function autentificar($usuario, $clave){
		$this->usuario = $usuario;
		$this->clave = $clave;
		$this->auth = 1;
	}
	
	function conectar($servidorSMTP,$puerto,$tiempo){
				
				if(!$this->smtp->Connect($servidorSMTP,$puerto,$tiempo)){
					$this->error=1;
					$this->strError = "SMTP-> No se pudo conectar al servidor.";
					return false;
				}
				if(!$this->smtp->Hello()){
					$this->error=1;
					$this->strError = "SMTP-> No se identificar ante el servidor.";
					return false;
				}
				if($this->auth==1){
					if(!$this->smtp->Authenticate($this->usuario, $this->clave)){
						$this->error=1;
						$this->strError = "SMTP-> La autentificacion ha fallado.";
						return false;
					}
				}
				return true;
	
	}
	
	function enviar(){ // Boolean
				if(!$this->smtp->Mail($this->emailFrom)){
						$this->error=1;
						$this->strError = "SMTP-> La direccion de envio no fue aceptada.";
						return false;
				}
				if(!$this->smtp->Recipient($this->emailRcpt)){
						$this->error=1;
						$this->strError = "SMTP-> La direccion del destinatario no fue aceptada.";
						return false;
				}
				if(!$this->smtp->Data($this->getCuerpo())){
						$this->error=1;
						$this->strError = "SMTP-> No se pudieron enviar los datos.";
						$this->smtp->Reset();
						return false;
				}
				return true;
	}
	
	function cerrar(){
		if($this->smtp->Connected()){
			$this->smtp->Quit();
			$this->smtp->Close();
		}
	}
	
	function resetear(){
		$this->smtp->Reset();
	}
	
	function getEncabezado(){
		$encabezado="";
		$encabezado.="MIME-Version: 1.0\n";
		if($this->nombreRcpt==""){
			$encabezado.="To: ".$this->emailRcpt."\n"; 
		}else{
			$encabezado.="To: \"".$this->nombreRcpt."\"<".$this->emailRcpt.">\n"; 
		}
		$encabezado.="From: ".$this->nombreFrom."<".$this->emailFrom.">\n";
		$encabezado.="Subject: ".$this->asunto."\n"; 
		$encabezado.="Date: ".date("r")."\n";
		$encabezado.="Message-ID: <".$this->idMsg."@".$this->host.">\n";
		return $encabezado;
	}
	function getCuerpo(){
		$texto=$this->getEncabezado();
		$idBoundary = md5(uniqid(time()));
			$texto.= "Content-type: multipart/related; "; 
			$texto.= "boundary=\"".$idBoundary."\"\n\n";
			$texto.="--".$idBoundary."\n";
			$texto.="Content-transfer-encoding: 8BIT\n";
			$texto.="Content-type: text/html; charset=iso-8859-1\n\n";
			$texto.= $this->reemplazarInfo($this->cuerpo);
			
			if(count($this->arAttach)>0 || count($this->arImg)>0){
			if(count($this->arImg)>0){
				for($i=0;$i<count($this->arImg);$i++){
					$archivo = $this->arImg[$i];
					if(is_file($archivo)){
						if($fp=fopen($archivo,"rb")){  
							$strName=basename($archivo);
							// tipo de la imagen
							$tipo = "image/jpeg";
							$arEx = explode(".",$strName);
							$tipoEx = array_pop($arEx);
							if($tipoEx == "gif"){
								$tipo = "image/gif";
							}else if($tipoEx == "png"){
								$tipo = "image/png";
							}
							$content=fread($fp,filesize($archivo));  
							$texto.="\n\n--".$idBoundary."\n"  
							."Content-Type: ".$tipo.";name=\"$strName\"\n"  
							."Content-Transfer-Encoding: base64\n" 
							."Content-ID: <ac_".strtolower($strName).">\n\n"
							.chunk_split(base64_encode($content))."\n";  
							 fclose($fp); 
						}
					}
				}
			}
			if(count($this->arAttach)>0){
				for($i=0;$i<count($this->arAttach);$i++){
					$archivo = $this->arAttach[$i][0];
					if(is_file($archivo)){
						if($fp=@fopen($archivo,"rb")){  
							$strName=basename($archivo);
							$content=@fread($fp,filesize($archivo));  
							$texto.="\n\n--".$idBoundary."\n"  
							."Content-Type: ".$this->arAttach[$i][1]."; name=\"$strName\"\n"  
							."Content-Transfer-Encoding: base64\n"  
							."Content-Disposition: attachment; filename=\"$strName\"\n\n"  
							.chunk_split(base64_encode($content))."\n";  
							 fclose($fp);  
						} 
					}
				}
			}
		}
		$texto.= "\n\n--".$idBoundary."--\n\n";
		return $texto;
	}
	function getError(){
		$ar_error = $this->smtp->getError();
		return $ar_error["error"];
	}
	/*function reemplazarInfo($texto){
		$texto = htmlentities($texto);
		$texto = str_replace("&lt;","<",$texto);  
   	 	$texto = str_replace("&gt;",">",$texto); 
		$texto = str_replace("&quot;","\"",$texto); 
		
		$texto = str_replace("&amp;nbsp;","&nbsp;",$texto);
		$texto = str_replace("&amp;amp;","&",$texto); 
		//$texto = str_replace("&quot;","'",$texto); 
		return $texto;
	}*/
	
	
	function reemplazarInfo($texto){
		$texto = htmlentities($texto,ENT_COMPAT,'ISO-8859-1');
		$texto = str_replace("&lt;","<",$texto);  
   	 	$texto = str_replace("&gt;",">",$texto); 
		$texto = str_replace("&quot;","\"",$texto); 
		
		$texto = str_replace("&amp;lt;","<",$texto);  
   	 	$texto = str_replace("&amp;gt;",">",$texto); 
		$texto = str_replace("&amp;quot;","\"",$texto); 
		
		$texto = str_replace("&amp;nbsp;","&nbsp;",$texto);
		$texto = str_replace("&amp;amp;","&",$texto); 
		
		$texto = str_replace("&amp;#8364;","&#8364;",$texto);
		
		$texto = str_replace("&amp;#60;","&#60;",$texto);
		$texto = str_replace("&amp;#62;","&#62;",$texto); 
		//$texto = str_replace("&quot;","'",$texto); 
		return $texto;
	}
}
?>