<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Util
/////////////////////////////////////////////////////////////////////////////////*/

class Util{
	function Util(){}
	public function procesaDato($dato,$cant=0,$tipo="string"){
		if($dato!=""){
			$dato = $this->verificarAtaque($dato);
			$dato = trim($dato);
			if($cant>0){
				$dato = substr($dato,0,$cant);
			}
			
			switch($tipo){
				case "int";$dato = (int) $dato;break;
				case "bool";$dato = (bool) $dato;break;
				case "float";$dato = (float) $dato;break;
				case "array";$dato = (array) $dato;break;
				case "object";$dato = (object) $dato;break;
				case "unset";$dato = (unset) $dato;break;
				default: $dato = (string) $dato;break;
			}
		}
		return $dato;
	}
	// FUNCIONES PARA TEXTO ///////////////////////////////////////////////////////
	function setComillas($txt){
		$txt = preg_replace("/\"/","&quot;",$txt);
		$txt = preg_replace("/\'/","\\&#39;",$txt);
		return $txt;
	}
	function quitarSaltos($texto){
		$texto=preg_replace("/[\n|\r|\n\r]/","",$texto); 
		return $texto;
	}
	function editor($texto){
		if(get_magic_quotes_gpc()) { 
			$texto = stripslashes($texto); 
		} 
		if(function_exists("real_escape_string" )){ 
			$texto = real_escape_string($texto); 
		}else{ 
		  $texto = addslashes($texto); 
		}  
		$texto=preg_replace("[\n|\r|\n\r]"," ",$texto); 
		return $texto;
	}
	function escape(){	
		//// Chekeo de variables de ingreso
		if(get_magic_quotes_gpc()==0){
			$numero = count($_GET);	
			$valores2 = array_values($_GET);// obtiene los valores de las varibles
			$tags2 = array_keys($_GET); 
			for($i=0;$i<$numero;$i++){
				if(isset($valores2[$i])){
					if(is_string($valores2[$i])){
						$_GET[$tags2[$i]]=addslashes($valores2[$i]);
					}
				}	
			}
			$numero = count($_POST);
			$valores2 = array_values($_POST);// obtiene los valores de las varibles
			$tags2 = array_keys($_POST);	
			for($i=0;$i<$numero;$i++){
				if(isset($valores2[$i])){
					if(is_string($valores2[$i])){
						$_POST[$tags2[$i]]=addslashes($valores2[$i]);
					}
				}	
			}
			$numero = count($_COOKIE);
			$valores2 = array_values($_COOKIE);// obtiene los valores de las varibles
			$tags2 = array_keys($_COOKIE);	
			for($i=0;$i<$numero;$i++){
				if(isset($valores2[$i])){
					if(is_string($valores2[$i])){
						$_COOKIE[$tags2[$i]]=addslashes($valores2[$i]);
					}
				}	
			}		
		}
	}
	function salidaTexto($str){
		$strComilla = chr(34);
		$strBarra = chr(92);
		$chr =0;
		$str = preg_replace("&","&amp;",$str);
		$str = preg_replace($strComilla,"&#8221",$str);
		$str = stripslashes($str);
		return $str;
	}
	function antiComilla($str){
		$comilla = chr(39);
		$strEnter = preg_replace("/".$comilla."/", "\\".chr(39), $str);
		return $strEnter;
	}
	// CONVERSION DEL EDITOR DE TEXTO ///////////////////////////////////////////////////
	function salidaParrafo($str,$tipo=0){
		switch($tipo){
			case 1:
				$str = $this->salidaParrafoFull($str);
			break;
			case 2:
				$str = $this->quitarFormato($str);
			break;
			case 3:
				$str = preg_replace("/<[^>]*>/","",$str);
			break;
			default:
				$str = $this->salidaParrafoMedio($str);
			break;
		}
		return $str;
	}
	function quitarFormato($texto){
		$patron0 = "</FONT>";
		$texto = preg_replace($patron0,"",$texto);
		$patron1 = "/<FONT([^>]+)>/";
		$texto = preg_replace($patron1,"",$texto);
		$patron2 = "</P>";
		$texto = preg_replace($patron2,"<br />",$texto);
		$patron3 = "/<P([^>]+)>/";
		$texto = preg_replace($patron3,"",$texto);
		return $texto;
	}
	public function quitarHtml($str){
		return preg_replace("/<[^>]*>/","",$str);
	}
	function salidaParrafoMedio($str){
		$str =preg_replace("</FONT>","</span>",$str);
		$patron = "/<FONT ([^>]+)>/";
		$can = preg_match_all($patron,$str,$coincidencias,PREG_PATTERN_ORDER);
		for($a=0;$a<count($coincidencias[0]);$a++){
			$rem = "<span style=\"";
			$ar = explode(" ",$coincidencias[1][$a]);
			for($i=0;$i<count($ar);$i++){
				$ar2=explode("=",$ar[$i]);
				switch(strtoupper($ar2[0])){
					case "COLOR":
						$rem.="color:".preg_replace("\"","",$ar2[1]).";";
					break;
					case "FACE":
						$rem.="";
					break;
					case "SIZE":
						$rem.="";
					break;
				}
			}
			$rem.="\">";
			$str = preg_replace($coincidencias[0][$a],$rem,$str);
		}
		$patron2 = "</P>";
		$str =preg_replace($patron2,"<br />",$str);
		$patron3 = "/<P([^>]+)>/";
		$str =preg_replace($patron3,"",$str);
		$patron5 = "<P>";
		$str =preg_replace($patron5,"",$str);
		$str = preg_replace("/&apos;/","'",$str);
		$str = preg_replace("/<B>/","<b>",$str);
		$str = preg_replace("</B>","</b>",$str);
		$str = preg_replace("/<U>/","<u>",$str);
		$str = preg_replace("</U>","</u>",$str);
		$str = preg_replace("/<A HREF=/","<a href=",$str);
		$str = preg_replace("</A>","</a>",$str);
		$str = preg_replace("/ TARGET=/"," target=",$str);
		$str = preg_replace("/<LI>/","<li>",$str);
		$str = preg_replace("</LI>","</li>",$str);
		//$str = preg_replace("&","&amp;",$str);
		$str = preg_replace("/<br>/","<br/>",$str);
		return $str;
	}
	function salidaParrafoFull($str){
		$p = "<P([^>]+)></P>";
		$str =preg_replace($p,"<br />",$str);
		$str =preg_replace("</FONT>","</span>",$str);
		$patron = "/<FONT ([^>]+)>/";
		$can = preg_match_all($patron,$str,$coincidencias,PREG_PATTERN_ORDER);
		for($a=0;$a<count($coincidencias[0]);$a++){
			$rem = "<span style=\"";
			$ar = explode(" ",$coincidencias[1][$a]);
			for($i=0;$i<count($ar);$i++){
				$ar2=explode("=",$ar[$i]);
				switch(strtoupper($ar2[0])){
					case "COLOR":
						$rem.="color:".preg_replace("\"","",$ar2[1]).";";
					break;
					case "FACE":
						$rem.="font-family:".preg_replace("\"","",$ar2[1]).";";
					break;
					case "SIZE":
						$rem.="font-size:".preg_replace("\"","",$ar2[1])."px;";
					break;
				}
			}
			$rem.="\">";
			$str = preg_replace($coincidencias[0][$a],$rem,$str);
		}
		$patronp = "/<P ([^>]+)>/";
		$p = preg_match_all($patronp,$str,$coincidenciasp,PREG_PATTERN_ORDER);
		for($a=0;$a<count($coincidenciasp[0]);$a++){
			$rem = "<p style=\"margin:0px;";
			$ar = explode(" ",$coincidenciasp[1][$a]);
			for($i=0;$i<count($ar);$i++){
				$ar2=explode("=",$ar[$i]);
				switch(strtoupper($ar2[0])){
					case "ALIGN":
						$rem.="text-align:";
						switch(strtoupper($ar2[1])){
							case "\"LEFT\"":
								$rem.="left;";
							break;
							case "\"CENTER\"";
								$rem.="center;";
							break;
							case "\"RIGHT\"":
								$rem.="right;";
							break;
							case "\"JUSTIFY\"":
								$rem.="justify;";
							break;
						}
					break;
				}
			}
			$rem.="\">";
			$str = preg_replace($coincidenciasp[0][$a],$rem,$str);
		}
		$patron2 = "</P>";
		$str =preg_replace($patron2,"</p>",$str);
		$str = preg_replace("&apos;","'",$str);
		$str =preg_replace("<P>","<p>",$str);
		$str = preg_replace("<B>","<b>",$str);
		$str = preg_replace("</B>","</b>",$str);
		$str = preg_replace("<U>","<u>",$str);
		$str = preg_replace("</U>","</u>",$str);
		$str = preg_replace("<I>","<i>",$str);
		$str = preg_replace("</I>","</i>",$str);
		$str = preg_replace("<A HREF=","<a href=",$str);
		$str = preg_replace("</A>","</a>",$str);
		$str = preg_replace(" TARGET="," target=",$str);
		$str = preg_replace("<br>","<br/>",$str);
		return $str;
	}
	//////////////////////////////////////////////////////////////////////////////////////
	function salidaParrafoPlano($str){
		/*$enter = chr(13);
		$strComilla = chr(34);
		$strBarra = chr(92);
		$strEnter = ereg_replace($enter, "<br />", $str);
		$strEnter2=ereg_replace($strComilla,"&#8221",$strEnter);
		$strEnter2 = stripslashes($strEnter2);
		$strEnter3="";
		return $strEnter2;*/
		return preg_replace("/\n/", "<br />", $str);
	}
	function textoSql($texto){
		if(function_exists("real_escape_string" )){ 
			$texto = mysql_real_escape_string($texto); 
		}else{ 
		  if(!get_magic_quotes_gpc()){
		  	$texto = addslashes($texto); 
		  }
		}
		return $texto;
	}
	function contarString($string,$cant){
		$cont = 0;
		$i=0;
		$stringCont="";
		if(strlen($string)>$cant){
			$puntos = "...";
			for($i=0; $i<$cant;$i++){
				$stringCont = $stringCont.$string[$i];
			}
		}else{
			$puntos = "";
			$stringCont = $string;
		}
		return $stringCont.$puntos;
	}
	// Entradas
	/*
	 * $valor = valor ingresado por el usuario
	 * $tipo = Tipo a verificar
	 * $maximoX = Maximo de caracteres contiguos (si es menor a 1 no verifica)
	 * $maximoY = Maximo de lineas
	 * $maximoCaracteres = Maximo de caracteres totales
	 */
	
	// Valores para Tipo 
	/*
	 * 0 = Texto plano
	 * 1 = Numerico (De no ser numerico sera 0)
	 * 2 = Texto enriquecido ( Permite imagenes y links)   
	 */

	function verificarAtaque($valor,$tipo=0,$maximoX=-1,$maximoY=-1,$maximoCaracteres=-1){
		if($tipo==1){
			if(!is_numeric($valor)){
				$valor=0;
			}else{
				if($valor>pow(10,$maximoX) && $maximoX>1){
					$count=0;
					$valor=0;
					while($valor<pow(10,$maximoX-1)){
						$valor=pow(10,$count)*9+$valor;
						$count++;
					}
				}
			}
		}else{
			
			if($tipo==2){
				$valor = strip_tags(stripslashes($valor), '<p><span><i><p><br><li><ul><ol><table><tr><td><u><a>');
				$valor = preg_replace("<( *)a(.*) href=\\\"javascript:(.*)>(.*)</a>","",$valor);
				$valor = preg_replace("<( *)a(.*) href=\\'javascript:(.*)>(.*)</a>","",$valor);
				$valor = preg_replace("(<.*) onclick=\\\".*\\\"","\\1",$valor);				
				$valor = addslashes($valor);								
			}	

			if($tipo==0){
				$valor = preg_replace("/([^ ]{".$maximoX.",})/e", "wordwrap('\\1', ".$maximoX.", '\n', true);", $valor);
				$valor=str_replace("&","&amp;",$valor);	
				$valor=preg_replace("/</","&#60;",$valor);
				$valor=preg_replace("/>/","&#62;",$valor);
				$array = explode("\n",$valor);
				$valor = "";
				
				$cantidad = count($array);
				if($cantidad>$maximoY && $maximoY>0){
					$cantidad=$maximoY;				
				}					
				$valor = $array[0];
				for($i=1;$i<$cantidad;$i++){
					$valor.="<br/>".$array[$i];
				}	
			}
			if(strlen($valor)>$maximoCaracteres && $maximoCaracteres>0){
				$valor2 = $valor;
				$valor = "";
				for($i=0; $i<$maximoCaracteres;$i++){
					$valor = $valor.$valor2[$i];
				}				
			}
		}		
		return $valor;
	}
	
	function invertirAtaque($valor){		
			$valor=str_replace("&amp;","&",$valor);	
			$valor=preg_replace("&#60;","<",$valor);
			$valor=preg_replace("&#62;",">",$valor);
			$valor=preg_replace("<br/>","\n",$valor);
			return $valor;
	}	
	function getUrlAmp($url){
		$url=preg_replace("&","&amp;",$url);
		return $url;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	// FUNCIONES PARA NUMEROS ////////////////////////////////////////////////////////////////
	function getDecimal($double,$sep_dec=",",$sep_miles="."){
		return number_format($double,2,$sep_dec,$sep_miles);
	}
	function getRedondeo($double){
		//return round($double,1); 
		return $double; 
	}
	function getNumeroMiles($double,$sep="."){
		return number_format($double,0,",",$sep);
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	// FUNCIONES PARA FECHAS /////////////////////////////////////////////////////////////////
	function convertirFecha($dateFecha){// string
			if($dateFecha!=""){
				list($strAnio,$strMes,$strDia)=explode("-", $dateFecha);
				return $strDia."-".$strMes."-".$strAnio;
			}else{
				return "";
			}
	}
	function invertirFecha($fecha,$larga=0){// string
			if($fecha==""){
				return "0000-00-00";
			}else{
				if($larga==0){
					$ar = explode("-",$fecha);
					if(count($ar)==3){
						return $ar[2]."-".$ar[1]."-".$ar[0];
					}else{
						return $fecha;
					}
				}else{
					$fecha = $this->invertirFechaHora($fecha);
					return(substr($fecha,0,10));
				}
			}
	}
	function invertirFechaHora($fecha){// string
			if($fecha==""){
				return "0000-00-00 00:00:00";
			}else{
				$ar_f = explode(" ",$fecha);
				$ar = explode("-",$ar_f[0]);
				if(count($ar)==3){
					//return $strAnio."-".$strMes."-".$strDia." ".$hora;
					return $ar[2]."-".$ar[1]."-".$ar[0]." ".$ar_f[1];
				}else{
					return $fecha;
				}
			}
	}
	function obtenerFecha(){//string
			return date("Y")."-".date("m")."-".date("d");
	}
	function obtenerHora(){//string
			return date("H").":".date("i");
	}
	function getMes($mes, $mayuscula=false){
		$mesD ="";
		switch($mes){
			case 1:$mesD = "enero";break;
			case 2:$mesD = "febrero";break;
			case 3:$mesD = "marzo";break;
			case 4:$mesD = "abril";break;
			case 5:$mesD = "mayo";break;
			case 6:$mesD = "junio";break;
			case 7:$mesD = "julio";break;
			case 8:$mesD = "agosto";break;
			case 9:$mesD = "setiembre";break;
			case 10:$mesD = "octubre";break;
			case 11:$mesD = "noviembre";break;
			case 12:$mesD = "diciembre";break;
		}
		if($mayuscula){
			$prim = substr($mesD,0,1);
			$prim = strtoupper($prim);
			$mesD = $prim.substr($mesD,1,strlen($mesD));
		}
		return $mesD;
	}
	function obtenerFechaCompleta($hoy=""){
		if($hoy==""){
			$hoy = $this->obtenerFecha();
		}
		list($a,$m,$d)=explode("-",$hoy);
		$dia = date("D",mktime(0,0,0,$m,$d,$a));
		$fc = "";
		switch($dia){
		  case "Mon":$fc.="Lunes ";break;
		  case "Tue":$fc.="Martes ";break;
		  case "Wed":$fc.="Miércoles ";break;
		  case "Thu":$fc.="Jueves ";break;
		  case "Fri":$fc.="Viernes "; break;
		  case "Sat":$fc.="Sábado ";break;
		  case "Sun":$fc.="Domingo ";break;
		}
		$fc.=$d." de ".$this->getMes($m)." de ".$a;
		return $fc;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	// FUNCIONES MISCELANIAS /////////////////////////////////////////////////////////////////
	function random($intMinimo, $intMaximo){
		$intNumero = rand($intMinimo,$intMaximo);
		return $intNumero;
	}
	function obtenerTicket(){
			$numero = md5(uniqid(rand(),time()));
			return $numero;
	}
	function enviarMensaje($strMensaje){?>
		<script>
            alert('<? echo $strMensaje;?>');
        </script>
	<? }
	function obtenerHttpGet($url=NULL){
		$strGet = "";
		if($url==NULL){
			$urlT = $_GET;
			foreach($urlT as $k => $v) {
				$strGet.=$k."=".$v;
				$strGet.="&";
			}
		}else{
			if(substr_count($url,"?")>0){
				$strGet = "si";	
			}
		}
		return $strGet;
	}
	function getWWW($str){
		
		$esWWW = false;
		
		$http = substr_count(strtolower($str),"http:");
		$https = substr_count(strtolower($str),"https:");
		$www = substr_count(strtolower($str),"www");
		
		if($http+$https+$www>0){
			$esWWW = true;
		}
		
		return $esWWW;
	}
	function verificarSSL(){
		$ssl = false;
		if(isset($_SERVER["HTTPS"])){
			if($_SERVER["HTTPS"]=="on"){
				$ssl = true;
			}
		}
		return $ssl;
	}
	function esEmail($email){
		$return = false;
		$regexp = "/^[^0-9][A-z0-9_\.]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
		if (preg_match($regexp, $email)) {
			$return = true;
		} 
		return $return;
	}
	function cedula($str){
			$strCedula = $str;
			$correcto = false;
			$intMax = 7;
			$intMin = 6;
			// controlar si es numérico
			$a=0;
			$boolEsNumerico=true;
			while($boolEsNumerico && $a<strlen($strCedula)){
				if(!is_numeric($strCedula[$a])){
					$coorecto=false;
					$boolEsNumerico=false;
				}
				$a++;
			}
			// controlar si está en el rango
			$boolRango = true;
			if(strlen($strCedula)-1<$intMin || strlen($strCedula)-1>$intMax) {
				$correcto = false;
				$boolRango = false;
			}
			// si es numerico y está en el rango
			if($boolEsNumerico &&  $boolRango){
				$arrayIngreso = array();
				$arrayMax = array(ord('2'),ord('9'),ord('8'),ord('7'), ord('6'), ord('3'),ord('4'));
				for($i=0;$i<strlen($strCedula);$i++){
					array_push($arrayIngreso,ord($strCedula[$i]));
				}
				$intDigitoControl = chr(array_pop($arrayIngreso));
		
				$j=0;
				$suma = 0;
				// algoritmo de control
				for($i=($intMax)-(count($arrayIngreso)); $i<$intMax; $i++) {
					$uno = $arrayMax[$i] - ord('0');
					$dos = $arrayIngreso[$j++] -ord('0');	
					$suma = ($suma+=$uno * $dos) % 10;
				}
				// suma es el dígito de control correcto
				$suma = (10 - $suma) % 10;
				if($suma == $intDigitoControl){
					$correcto = true;
				}else{
					$correcto = false;
				}
			}
		return $correcto;
	}
	function redireccionar($url,$local=0,$mod=3){
	 	if($local==1){
			$url = "../data/".$url;
		}
		if($mod==1){
			?><script>document.location.href = '<? echo $url;?>'</script><?
		}
		if($mod==2){
			print "<meta http-equiv=\"refresh\" content=\"0;URL=".$url."\" />";
		}
		if($mod==3){
			header("Location: ".$url);
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	
	// FUNCIONES EMAIL ///////////////////////////////////////////////////////////////////////
	function enviarEmail($para,$nombreFrom,$from,$subject,$mensaje,$returnPath){
		$headers="MIME-Version: 1.0\r\n"; 
		$headers.="Content-type: text/html; charset=iso-8859-1\r\n";
		$headers.="From: ".$nombreFrom."<".$from.">\r\n"; 
		$headers.="Reply-To: ".$from."\r\n"; 
		$headers.="Return-path: ".$returnPath; 
		//$headers.="X-Mailer: PHP/".phpversion();
		$texto = $this->reemplazarInfo($mensaje);
		//return mail($para,$subject,$texto,$headers,"-f".$returnPath);
		return mail($para,$subject,$texto,$headers);
	}
	
	function reemplazarInfo($texto){
		$texto = htmlentities($texto,ENT_COMPAT,'ISO-8859-1');
		$texto = str_replace("&lt;","<",$texto);  
   	 	$texto = str_replace("&gt;",">",$texto); 
		$texto = str_replace("&quot;","\"",$texto); 
		return $texto;
	}
	//////////////////////////////////////////////////////////////////////////////////////////
	// FUNCIONES INTERFASE ///////////////////////////////////////////////////////////////////
	function crearPaleta($nombrePaleta,$campo1,$campo2,$arPaleta,$campoA="nocampo"){
		$strPaleta="";
		$strPaleta.="<div id=\"".$nombrePaleta."\" style=\"visibility:hidden;display:none;\" class=\"stPaleta\" onclick=\"mostrarFormato('".$nombrePaleta."')\">";
		$strPaleta.="<table width=\"204\" border=\"0\" cellpadding=\"0\" cellspacing=\"3\"><tr><td>Seleccione un color:</td></tr>";
		$strPaleta.="</table>"; 
		$a=1;
		$strPaleta.="<table width=\"204\" border=\"0\" cellpadding=\"0\" cellspacing=\"3\">";
		if($a==1){
			$strPaleta.="<tr>";
		}
		for($i=0;$i<count($arPaleta);$i++){
		   $strPaleta.="<td bgcolor=\"#".$arPaleta[$i]."\" class=\"stPaletaColor\" ";
		   $strPaleta.="onClick=\"seleccionarColor('".$campo1."','".$campo2."','".$arPaleta[$i]."','".$nombrePaleta."','".$campoA."')\">&nbsp;</td>";
			if($a%8==0){
				$strPaleta.="</tr><tr>";
			}
			$a++;
		}
		$strPaleta.="</tr></table></div>";
		printf($strPaleta);
	}	
	function getRewriteString($sString) { 
    //echo $sString;
	 $string = strtolower(htmlentities($sString,ENT_COMPAT,'ISO-8859-1')); 
     $string = preg_replace("/&(.)(uml);/", "$1e", $string); 
     $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string); 
     $string = preg_replace("/([^a-z0-9_]+)/", "-", html_entity_decode($string)); 
     $string = trim($string, "-"); 
     return $string; 
  }  
	function invertirFechaFull($fecha = "2008-07-08 17:25:00"){
		$ar = explode(" ",$fecha);
		$arFecha = explode("-",$ar[0]);
		$arMinuto = explode(":",$ar[1]);
		$anio = $arFecha[0];
		$mes = $arFecha[1];
		$dia = $arFecha[2];
		$hora = $arMinuto[0];
		$minuto = $arMinuto[1];
		return $dia."-".$mes."-".$anio." | ".$hora.":".$minuto;
	}
	public function booleanTexto($numero,$idioma="es"){
		$txt = "Si";
		switch($idioma){
			default:
				if($numero==0){
					$txt = "No";
				}
			break;
		}	
		return $txt;
	}
	function obtenerCalendario($url,$formulario,$campo,$layer){
			$direccion= $url."?fr=".$formulario."&co=".$campo."&abr=_top&ly=".$layer."&c=";
			$str="";
			$str.="<div id=\"".$layer."\" style=\"position:absolute; width:192px; height:179px; z-index:10000; visibility: hidden;display:none;\">";
			$str.="<iframe width=\"192\" height=\"205\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\"";
			$str.=" scrolling=\"no\" src=\"".$direccion."\" title=\"\" name=\"cal_".$layer."\" id=\"cal_".$layer."\"";
			$str.="style=\"border:solid #000000 0.0em\" ></iframe>";
			$str.="</div>";
			return $str;
	}
	function reemplazarTildes($str){
		$a = array('Á','É','Í','Ó','Ú','Ñ','á', 'é', 'í', 'ó', 'ú', 'ñ','ä','ë','ï','ö','ü','à','è','ì','ò','ù','?','¿','´','º');
		$b = array(
			htmlentities('Á',ENT_COMPAT,'ISO-8859-1'), 
			htmlentities('É',ENT_COMPAT,'ISO-8859-1'), 
			htmlentities('Í',ENT_COMPAT,'ISO-8859-1'),
		 	htmlentities('Ó',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('Ú',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('Ñ',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('á',ENT_COMPAT,'ISO-8859-1'), 
			htmlentities('é',ENT_COMPAT,'ISO-8859-1'), 
			htmlentities('í',ENT_COMPAT,'ISO-8859-1'),
		 	htmlentities('ó',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ú',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ñ',ENT_COMPAT,'ISO-8859-1'),
		 	htmlentities('ä',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ë',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ï',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ö',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ü',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('à',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('è',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ì',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ò',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('ù',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('?',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('¿',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('´',ENT_COMPAT,'ISO-8859-1'),
			htmlentities('º',ENT_COMPAT,'ISO-8859-1')
			);
		$str = str_replace($a,$b,$str);
		return $str;
	}
	public function getDepartamentos(){
			$ar =  array(
			"Artigas",
			"Canelones",
			"Cerro Largo",
			"Colonia",
			"Durazno",
			"Flores",
			"Florida",
			"Lavalleja",
			"Maldonado",
			"Montevideo",
			"Paysandú",
			"Río Negro",
			"Rivera",
			"Rocha",
			"Salto",
			"San José",
			"Soriano",
			"Tacuarembó",
			"Treinta y Trés"
			);
		return $ar;
	}

	function LoadCURLPage($url, $agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4)
 	Gecko/20030624 Netscape/7.1 (ax)',
	$cookie = '', $referer = '', $post_fields = '', $return_transfer = 1,
	$follow_location = 1, $ssl = '', $curlopt_header = 0){
        
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url);
        if($ssl){
        	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
        }
        curl_setopt ($ch, CURLOPT_HEADER, $curlopt_header);
        if($agent){
        	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        }
        if($post_fields){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if($referer){
       	 	curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if($cookie){
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        }
        $result = curl_exec ($ch);
        curl_close ($ch);
        return $result;
	}
	public function getArrayWs($ar_items){
		$ar_ws = array();
		$unico = 0;
		$i=0;
		foreach($ar_items AS $clave=>$valor){
			if(is_array($valor)){
				array_push($ar_ws,$valor);
			}else{
				$unico = 1;
				break;
			}
		}
		if($unico==1){
			$ar_unico = array();
			foreach($ar_items AS $clave=>$valor){
				$ar_unico[$clave]=$valor;
			}
			array_push($ar_ws,$ar_unico);
		}
		return $ar_ws;
	}
	function youtubeSetTransparencia($texto){
		$t_1 = "<param name=\"wmode\" value=\"transparent\"></param>";
		$t_2 = " wmode=\"transparent\""; 
		$t_3 = "?wmode=transparent";
		$texto = str_replace("<embed",$t_1."<embed",$texto);
		$texto = str_replace("></embed>",$t_2."></embed>",$texto);
		$patron = "/src=\"([^\s]*)\"/";
		$can = preg_match_all($patron,$texto,$coincidencias,PREG_PATTERN_ORDER);
		for($a=0;$a<count($coincidencias[0]);$a++){
			$coin =  $coincidencias[1][$a];
			$texto = str_replace($coin,$coin.$t_3,$texto);
		}
		return $texto;
	}	
	public function validarNIFCIF($nif){
		$result=$this->valida_nif_cif_nie($nif);
		if($result==1 || $result==2 || $result==3){
			return true;
		}else{
			return false;
		}
		//return true;
	}
	public function getNIFCIF($nif){
		$tipo_doc = "";
		$result=$this->valida_nif_cif_nie($nif);
		switch($result){
			case 1:
				$tipo_doc = "NIF";
			break;
			case 2:
				$tipo_doc = "CIF";
			break;
			case 3:
				$tipo_doc = "NIE";
			break;
		}
		return $tipo_doc;
	}
	public function validarCUENTA($d1,$d2,$d3,$d4){
		return true;
	}
	
	// Validación de cif/nif
	function valida_nif_cif_nie($cif) {
		//Copyright ©2005-2011 David Vidal Serra. Bajo licencia GNU GPL.
		//Este software viene SIN NINGUN TIPO DE GARANTIA; para saber mas detalles
		//puede consultar la licencia en http://www.gnu.org/licenses/gpl.txt(1)
		//Esto es software libre, y puede ser usado y redistribuirdo de acuerdo
		//con la condicion de que el autor jamas sera responsable de su uso.
		//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
         $cif = strtoupper($cif);
         for ($i = 0; $i < 9; $i ++){
                  $num[$i] = substr($cif, $i, 1);
         }
		 //si no tiene un formato valido devuelve error
         if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)){
                return 0;
         }
		//comprobacion de NIFs estandar
         if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)){
                  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)){
                           return 1;
                  }
                  else{
                           return -1;
                  }
         }
		// algoritmo para comprobacion de codigos tipo CIF
         $suma = $num[2] + $num[4] + $num[6];
         for ($i = 1; $i < 8; $i += 2)
         {
                  $suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]), 1, 1);
         }
         $n = 10 - substr($suma, strlen($suma) - 1, 1);
		//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
         if (preg_match('/^[KLM]{1}/', $cif))
         {
                  if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1))
                  {
                           return 1;
                  }
                  else
                  {
                           return -1;
                  }
         }
		//comprobacion de CIFs
         if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif))
         {
                  if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1))
                  {
                           return 2;
                  }
                  else
                  {
                           return -2;
                  }
         }
		//comprobacion de NIEs
		//T
         if (preg_match('/^[T]{1}/', $cif))
         {
                  if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $cif))
                  {
                           return 3;
                  }
                  else
                  {
                           return -3;
                  }
         }
		//XYZ
         if (preg_match('/^[XYZ]{1}/', $cif))
         {
                  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1))
                  {
                           return 3;
                  }
                  else
                  {
                           return -3;
                  }
         }
		//si todavia no se ha verificado devuelve error
         return 0;
	}
	
}// fin de la clase
?>