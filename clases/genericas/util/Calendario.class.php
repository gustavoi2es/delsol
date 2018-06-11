<? class Calendario{
	
	var $tiempo_actual;
	var $dia_solo_hoy;
	var $mes;
	var $anio;
	var $formulario;
	var $campo;
	var $layer;
	var $abrir;
	var $persiste;
	var $interfase;
	
	//constructor
	function Calendario($interfase=""){
		
		if(isset($_GET["fr"])){
			$this->formulario = $_GET["fr"];
		}else{
			$this->formulario = "";
		}
		if(isset($_GET["c"])){
			$this->interfase = $_GET["c"];
		}else{
			$this->interfase = "";
		}
		
		if(isset($_GET["co"])){
			$this->campo = $_GET["co"];
		}else{
			$this->campo = "";
		}
		if(isset($_GET["ly"])){
			$this->layer= $_GET["ly"];
		}else{
			$this->layer= "";
		}
		if(isset($_GET["abr"])){
			$this->abrir = $_GET["abr"];
		}else{
			$this->abrir = "";
		}
		
		$this->persiste = "fr=".$this->formulario."&co=".$this->campo."&abr=".$this->abrir."&ly=".$this->layer."&c=".$this->interfase."&";
		
		$this->tiempo_actual = time();
		$this->dia_solo_hoy = date("d",$this->tiempo_actual);
		if (!$_POST && !isset($_GET["nm"]) && !isset($_GET["na"])){
			$this->mes = date("n", $this->tiempo_actual);
			$this->anio = date("Y", $this->tiempo_actual);
		}elseif ($_POST) {
			$this->mes = $_POST["nm"];
			$this->anio = $_POST["na"];
		}else{
			$this->mes = $_GET["nm"];
			$this->anio = $_GET["na"];
		}
		// datos del formulario y del lugar donde será abierto el calendario
		if($this->formulario<>""){
			if(isset($_GET["d"])){?>
				<script>
				var formulario_destino = '<? echo $this->formulario?>'
				var campo_destino = '<? echo $this->campo?>'
				var dia = '<? echo $_GET["d"]?>'
				var mes = '<? echo $this->mes?>'
				var anio = '<? echo $this->anio?>'
				<? if($this->abrir=="_top"){?>
					eval("window.parent.document." + formulario_destino + "." + campo_destino + ".value='" + dia + "-" + mes + "-" + anio + "'")
				<? }else if($this->abrir=="_blank"){ ?>
					eval("window.opener.document." + formulario_destino + "." + campo_destino + ".value='" + dia + "-" + mes + "-" + anio + "'");
				<? }else{?>
					eval("document." + formulario_destino + "." + campo_destino + ".value='" + dia + "-" + mes + "-" + anio + "'")
				<? } ?>
				if(window.parent.document.getElementById(campo_destino+"_delete")){
					if(window.parent.document.getElementById(campo_destino+"_delete").style.visibility == "hidden"){
						window.parent.document.getElementById(campo_destino+"_delete").style.visibility = "visible";
						window.parent.document.getElementById(campo_destino+"_delete").style.width = "16";
					}
				}
				</script>
			<? } }
			$this->mostrarCalendario();
			$this->formularioCalendario();

	}
	
	function calcula_numero_dia_semana($dia){
		$numerodiasemana = date('w', mktime(0,0,0,$this->mes,$dia,$this->anio));
		if ($numerodiasemana == 0) 
			$numerodiasemana = 6;
		else
			$numerodiasemana--;
		return $numerodiasemana;
	}

	//funcion que devuelve el último día de un mes y año dados
	function ultimoDia(){ 
		$ultimo_dia=28; 
		while (checkdate($this->mes,$ultimo_dia + 1,$this->anio)){ 
		   $ultimo_dia++; 
		} 
		return $ultimo_dia; 
	} 

	function dame_nombre_mes(){
		 
		 switch ($this->mes){
			case 1:
				$nombre_mes="Enero";
				break;
			case 2:
				$nombre_mes="Febrero";
				break;
			case 3:
				$nombre_mes="Marzo";
				break;
			case 4:
				$nombre_mes="Abril";
				break;
			case 5:
				$nombre_mes="Mayo";
				break;
			case 6:
				$nombre_mes="Junio";
				break;
			case 7:
				$nombre_mes="Julio";
				break;
			case 8:
				$nombre_mes="Agosto";
				break;
			case 9:
				$nombre_mes="Septiembre";
				break;
			case 10:
				$nombre_mes="Octubre";
				break;
			case 11:
				$nombre_mes="Noviembre";
				break;
			case 12:
				$nombre_mes="Diciembre";
				break;
		}
		return $nombre_mes;
	}

	function dame_estilo($dia_imprimir){
		//dependiendo si el día es Hoy, Domigo o Cualquier otro, devuelvo un estilo
		$estilo = array();
		if ($this->dia_solo_hoy == $dia_imprimir && $this->mes==date("n", $this->tiempo_actual) && $this->anio==date("Y", $this->tiempo_actual)){
			//si es hoy
			$estilo[0] = " class=\"stHoy\"";
			$estilo[1] = " class=\"stHrefFeriado\"";
		}else{
			$fecha=mktime(12,0,0,$this->mes,$dia_imprimir,$this->anio);
			if (date("w",$fecha)==0){
				//si es domingo 
				$estilo[0] = " class=\"stDomingo\"";
				$estilo[1] = " class=\"stHrefFeriado\"";
			}else{
				//si es cualquier dia
				$estilo[0] = " class=\"stComun\"";
				$estilo[1] = " class=\"stHrefDias\"";
			}
		}
		return $estilo;
	}
function crearEstilos(){
	
	if($this->interfase==1){
		$fondo = "F9FAFD";
		$oscuro = "68779F";
		$oscuroDos = "808CAD";
		$oscuroTres = "909CBC";
		$celdas = "E2E5EE";
		$borde = "DBDFEA";
	}else{
		$fondo = "F2F2F3";
		$oscuro = "787C88";
		$oscuroDos = "898D99";
		$oscuroTres = "9296A3";
		$celdas = "DEDFE1";
		$borde = "3b8e69";
	}
	
	echo "body {margin: 0px;}\n";
	//echo ".stTabla {border:1px solid #".$borde.";background-color:#".$fondo.";font-size:7pt;font-family: Arial, sans-serif;color:#".$oscuro.";}\n";
	echo ".stTabla {border-left:1px solid #".$borde.";border-right:1px solid #".$borde.";border-top:1px solid #".$borde.";background-color:#".$fondo.";font-size:7pt;font-family: Arial, sans-serif;color:#".$oscuro.";}\n";
	echo ".stManejador {border-top:1px solid #FBFBFC;border-bottom:1px solid #FBFBFC;border-right:1px solid #FBFBFC;background-color:#".$oscuro.";font-size:7pt;color:#FFFFFF; cursor:pointer;}\n";
	echo ".stManejadorDos {border-top:1px solid #FBFBFC;border-bottom:1px solid #FBFBFC;border-left:1px  solid #FBFBFC;background-color:#".$oscuro.";font-size:7pt;color:#FFFFFF;}\n";
	echo ".hrefManejador{color:#FFFFFF;text-decoration:none;font-weight:bold;}\n";
	echo ".stCerrar{border-left:1px solid #ed7717;background-color:#ed7717;font-size:7pt;color:#3b8e69;cursor:pointer;}\n";
	echo ".stHrefCerrar{color:#FFFFFF;text-decoration:none;font-weight:bold;}\n";
	echo ".stMes{border-bottom:1px solid #FBFBFC;border-top:1px solid #FBFBFC;background-color:#".$oscuroDos.";font-weight:bold;font-size:7pt;color:#FFFFFF;}\n";
	echo ".stSemanas{border-right:1px solid #FBFBFC;background-color:#".$oscuroTres.";font-size:7pt;font-weight:bold;color:#FFFFFF;}\n";
	echo ".stSemanasDos{background-color:#".$oscuroTres.";font-size:7pt;font-weight:bold;color:#FFFFFF;}\n";
	echo ".stHrefDias{color:#525E7D;text-decoration:none;font-weight:bold;}\n";
	echo ".stDomingo{border:1px solid #FBFBFC;background-color:#3b8e69;font-size:7pt;color:#FFFFFF;cursor:pointer;}\n";
	echo ".stHoy{border:1px solid #FBFBFC;background-color:#3b8e69;font-size:7pt;color:#FFFFFF;cursor:pointer;}\n";
	echo ".stComun{border:1px solid #FBFBFC;background-color:#".$celdas.";font-size:7pt;color:#".$oscuro.";cursor:pointer;}\n";
	//echo ".stTableForm{border-bottom:1px solid #DBDFE9;border-left:1px  solid #DBDFE9;border-right:1px solid #DBDFE9;background-color:#".$fondo.";font-family: Arial, sans-serif;}\n";
	echo ".stTableForm{border-left:1px solid #".$borde.";border-right:1px solid #".$borde.";border-bottom:1px solid #".$borde.";background-color:#".$fondo.";font-family: Arial, sans-serif;}\n";
	echo ".stLista{background-color:#".$fondo.";font-family: Arial, sans-serif;color:#4F4F50;font-size: 8pt;}\n";
	echo ".stTdLista{border:1px solid #".$oscuroTres.";background-color:#".$borde.";font-family: Arial, sans-serif;color:#".$oscuro.";font-size: 8pt;}\n";
	echo ".stBoton{border:1px solid #".$oscuro.";background-color:#".$celdas.";font-size:8pt;color:#".$oscuro.";cursor:pointer;font-weight:bold;}\n";
	echo ".stHrefFeriado{color:#FFFFFF;text-decoration:none;font-weight:bold;}\n";

}

function mostrarCalendario(){
	//tomo el nombre del mes que hay que imprimir
	$nombre_mes = $this->dame_nombre_mes($this->mes);
	//construyo la cabecera de la tabla
	if($this->abrir=="_blank"){
		$cerrar = "javascript:window.close()";
	}
	if($this->abrir=="_top"){
		$cerrar = "mostrarCalendarioInt('".$this->layer."')";
	}
	echo "<html><head>";
	echo "<title>Calendario</title>";
	echo "<style type=\"text/css\">";
	// estiolos
	$this->crearEstilos();
	///////////////
	echo "</style></head><body>";
	echo "<table width=\"192\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" class=\"stTabla\">";
	echo "<tr><td onclick=\"".$cerrar."\" style=\"cursor:pointer;\">";
	echo "Calendario</td><td width=20 align=\"center\" class=\"stCerrar\" onclick=\"".$cerrar."\">";
	echo "<img src=\"../mod_cm/img/global/ico_cerrar_calendario.gif\" alt=\"Cerrar\" title=\"Cerrar\" />";
	echo "</td></tr></table>";
	//echo "<a href=\"javascript:".$cerrar."\" class=\"stHrefCerrar\" title=\"Cerrar\"> X </a></td></tr></table>";
	//calculo el mes y ano del mes anterior
	$mes_anterior = $this->mes - 1;
	$ano_anterior = $this->anio;
	if ($mes_anterior==0){
		$ano_anterior--;
		$mes_anterior=12;
	}
	$url_ant = $_SERVER['PHP_SELF']."?".$this->persiste."nm=".$mes_anterior."&na=".$ano_anterior;
	
	echo "<table width=192 cellspacing=0 cellpadding=0 border=0 class=\"stTabla\"><tr><td colspan=7 align=center>";
	echo "<table width=100% height=23 cellspacing=0 cellpadding=0 border=0><tr><td align=\"center\" width=\"27\" class=\"stManejador\" style=\"cursor:pointer;\" onclick=\"window.location='".$url_ant."'\">";
	$mes_siguiente = $this->mes + 1;
	$ano_siguiente = $this->anio;
	if ($mes_siguiente==13){
		$ano_siguiente++;
		$mes_siguiente=1;
	}
	$url_sig = $_SERVER['PHP_SELF']."?".$this->persiste."nm=".$mes_siguiente."&na=".$ano_siguiente;
	echo "<a class=\"hrefManejador\" href=\"".$url_ant."\" title=\"Anterior\">";
	echo "<img src=\"../mod_cm/img/global/ico_calendario_anterior.gif\" border=\"0\" alt=\"Anterior\" title=\"Anterior\" /></a></td>";
	
	echo "<td align=center class=\"stMes\">$nombre_mes $this->anio</td>";
	echo "<td align=center width=27 class=\"stManejadorDos\" style=\"cursor:pointer;\" onclick=\"window.location='".$url_sig."'\">";
	//calculo el mes y ano del mes siguiente
	
	echo "<a class=\"hrefManejador\" href=\"".$url_sig."\" title=\"Siguiente\">";
	echo "<img src=\"../mod_cm/img/global/ico_calendario_siguiente.gif\" border=\"0\" alt=\"Siguiente\" title=\"Siguiente\" /></a></td></tr></table></td></tr>";
	echo "	<tr>
			    <td width=14% height=20 align=center class=\"stSemanas\">Lu</td>
			    <td width=14% height=20 align=center class=\"stSemanas\">Ma</td>
			    <td width=14% height=20 align=center class=\"stSemanas\">Mi</td>
			    <td width=14% height=20 align=center class=\"stSemanas\">Ju</td>
			    <td width=14% height=20 align=center class=\"stSemanas\">Vi</td>
			    <td width=14% height=20 align=center class=\"stSemanas\">Sa</td>
			    <td width=14% height=20 align=center class=\"stSemanasDos\">Do</td>
			</tr>";
	
	//Variable para llevar la cuenta del dia actual
	$dia_actual = 1;
	
	//calculo el numero del dia de la semana del primer dia
	$numero_dia = $this->calcula_numero_dia_semana(1);
	//echo "Numero del dia de demana del primer: $numero_dia <br>";
	
	//calculo el último dia del mes
	$ultimo_dia = $this->ultimoDia();
	
	//escribo la primera fila de la semana
	echo "<tr>";
	for ($i=0;$i<7;$i++){
		if ($i < $numero_dia){
			//si el dia de la semana i es menor que el numero del primer dia de la semana no pongo nada en la celda
			echo "<td></td>";
		} else {
			if(strlen($dia_actual)==1) $dia = "0".$dia_actual; else $dia = $dia_actual;
			if(strlen($this->mes)==1) $mese = "0".$this->mes; else $mese = $this->mes;
			$ar = $this->dame_estilo($dia_actual);
			$url_cal = $_SERVER['PHP_SELF']."?".$this->persiste."d=".$dia."&nm=".$mese."&na=".$this->anio;
			echo "<td height=18 align=\"center\" ".$ar[0]." onclick=\"window.location='".$url_cal."';\"><a href=\"".$url_cal."\"".$ar[1].">".$dia_actual."</a></td>";
			$dia_actual++;
		}
	}
	echo "</tr>";
	
	//recorro todos los demás días hasta el final del mes
	$numero_dia = 0;
	while ($dia_actual <= $ultimo_dia){
		//si estamos a principio de la semana escribo el <TR>
		if ($numero_dia == 0)
			echo "<tr>";
			if(strlen($dia_actual)==1) $dia = "0".$dia_actual; else $dia = $dia_actual;
			if(strlen($this->mes)==1) $mese = "0".$this->mes; else $mese = $this->mes;
			$ar = $this->dame_estilo($dia_actual);
			$url_cal = $_SERVER['PHP_SELF']."?".$this->persiste."d=".$dia."&nm=".$mese."&na=".$this->anio;
			echo "<td height=18 align=\"center\" ".$ar[0]." onclick=\"window.location='".$url_cal."';\"><a href=\"".$url_cal."\"".$ar[1].">".$dia_actual."</a></td>";

		$dia_actual++;
		$numero_dia++;
		//si es el uñtimo de la semana, me pongo al principio de la semana y escribo el </tr>
		if ($numero_dia == 7){
			$numero_dia = 0;
			echo "</tr>";
		}
	}
	
	//compruebo que celdas me faltan por escribir vacias de la última semana del mes
	
	for ($i=$numero_dia;$i<7;$i++){
		echo "<td></td>";
	}
	
	echo "</tr>";
	echo "</table>";
	

}	
function formularioCalendario(){
		echo "<table width=\"192\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" class=\"stTableForm\">";
		echo "<tr><form action=\"".$_SERVER['PHP_SELF']."?".$this->persiste."\" method=\"POST\">";
		echo "<td align=\"center\" valign=\"top\">
				<select name=nm class=\"stLista\">
				<option value=\"1\" ";
		if ($this->mes==1)
		 echo "selected";
		echo">Enero<option value=\"02\" ";
		if ($this->mes==2) 
			echo "selected";
		echo">Febrero<option value=\"03\" ";
		if ($this->mes==3) 
			echo "selected";
		echo">Marzo<option value=\"04\" ";
		if ($this->mes==4) 
			echo "selected";
		echo ">Abril<option value=\"05\" ";
		if ($this->mes==5) 
				echo "selected";
		echo ">Mayo<option value=\"06\" ";
		if ($this->mes==6) 
			echo "selected";
		echo ">Junio<option value=\"07\" ";
		if ($this->mes==7) 
			echo "selected";
		echo ">Julio<option value=\"08\" ";
		if ($this->mes==8) 
			echo "selected";
		echo ">Agosto<option value=\"09\" ";
		if ($this->mes==9) 
			echo "selected";
		echo ">Septiembre<option value=\"10\" ";
		if ($this->mes==10) 
			echo "selected";
		echo ">Octubre<option value=\"11\" ";
		if ($this->mes==11) 
			echo "selected";
		echo ">Noviembre<option value=\"12\" ";
		if ($this->mes==12) 
			echo "selected";
		echo ">Diciembre</select></td>";
		echo "<td align=\"center\" valign=\"top\">
				<select name=na class=\"stLista\">";
		
		for ($cont=1900;$cont<$this->anio+3;$cont++){
			echo "<option value='$cont'";
			if ($this->anio==$cont) 
				echo " selected";
			echo ">$cont";
		}
		echo "</select></td>";
		if($this->layer<>""){
			echo "<input type=\"hidden\" value=\"visible\" name=\"layer\">";
		}
		echo "<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\" Ir \" class=\"stBoton\"></td></tr></table>";
		

		echo "</form>";
		echo "</body></html>";
}

}
?>
