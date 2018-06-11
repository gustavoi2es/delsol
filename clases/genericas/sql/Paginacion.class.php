<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Paginacion
/////////////////////////////////////////////////////////////////////////////////*/

class Paginacion{ 

	var $intPaginas;
	var $strConsulta;
	var $strOrden;
	var $strSentido;
	var $objDb;
	var $intPagina;
	var $intNumPags;
	var $intInicio;
	var $intFinal;
	var $intResultado;
	var $intTotal;
	var $strGet;
	var $rs;

	// Contenido
	// crearPaginacion // int (resultset)
	// crearManejador // void
	// crearManejadorCoumun // void
	// ordenar // void
	// obtenerResultado // void
	// obtenerTotal // int
	

	/////////////////////////////////////////////////////////////////////////////////
	
	//	Constructor: crea un objeto paginacion con el tipo de manejador la cantidad de registros a paginar
	// 	Nota: Crea el objeto paginacion
	//	Entrada: int con la cantidad de registros a paginar
	//	Salida: Crea un objeto con el número de registros
	
	function Paginacion($intPaginas,$objDb,$strConsulta,$strOrden,$strSentido){//int
		
		$this->intPaginas = $intPaginas;
		$this->objDb = $objDb;
		$this->strConsulta = $strConsulta;
		$this->intPagina= 1;
		$this->intNumPags = 1;
		$this->intInicio = 1;
		$this->intFinal = 1;
		$this->intResultado = 1;
		$this->intTotal = 1;
		$this->strGet = "";
		
		$objSesion = new Sesion($this->objDb);
		
		if(isset($_GET['or'])){
			$objSesion->crearVariable("or",$_GET['or']);
		}
		if (isset($_GET['se'])){
			$objSesion->crearVariable("se",$_GET['se']);
		}
		if($objSesion->existeVariable("or")){
			$this->strOrden= $objSesion->obtenerValor("or");
		}else{
			$this->strOrden= $strOrden;
		}
		if($objSesion->existeVariable("se")){
			$this->strSentido= $objSesion->obtenerValor("se");
		}else{
			$this->strSentido= $strSentido;
		}
		$rsRegistrosPrincipal = $this->objDb->obtenerResultset($this->strConsulta);
		$this->intTotal =  $this->objDb->total($rsRegistrosPrincipal);
		
		if(isset($_GET['pg'])){
			$objSesion->crearVariable("pg",$_GET['pg']);
		}
		
		if($objSesion->existeVariable("pg")){
			$this->intPagina= $objSesion->obtenerValor("pg");
		}else{
			$this->intPagina= 1;
		}
		if(!isset($this->intPagina)){
			$this->intPagina=1;
			$this->intInicio=1;
			$this->intFinal=$this->intPaginas;
		}
		//calculo del limite inferior
		$limitInf=($this->intPagina-1)*$this->intPaginas;
		//calculo del numero de paginas
		$this->intNumPags=ceil($this->intTotal/$this->intPaginas);
		if(!isset($this->intPagina)){
			$this->intPagina=1;
			$this->intInicio=1;
			$this->intFinal=$this->intPaginas;
		}else{
			$seccionActual=intval(($this->intPagina-1)/$this->intPaginas);
			$this->intInicio=($seccionActual*$this->intPaginas)+1;
	
			if($this->intPagina<$this->intNumPags){
				$this->intFinal=$this->intInicio+$this->intPaginas-1;
			}else{
				$this->intFinal=$this->intNumPags;
			}
                
			if ($this->intFinal>$this->intNumPags){
				$this->intFinal=$this->intNumPags;
			}
		}
		$this->strConsulta.=" ORDER BY ".$this->strOrden." ".$this->strSentido." LIMIT ".$limitInf.",".$this->intPaginas;
		$rsRegistrosPrincipal =  $this->objDb->obtenerResultset($this->strConsulta);
		$this->intResultado = $limitInf+$this->intPaginas;
		if($this->intPagina==$this->intNumPags){
			$this->intResultado = $this->intTotal;
		}
		$this->rs = $rsRegistrosPrincipal;
		//echo $this->strConsulta;

	}// fin de la función
	
	function getResultset(){
		return $this->rs;
	}
	// CONTROLES ///////////////////////////////////////////////////////////////////////////////////////////////
	
	public function getResultado(){
		return $this->intResultado;
	}
	public function getTotal(){
		return $this->intTotal;
	}
	function getControl($strGet=NULL){//void
		
		$html = "";
		$path_img = "../mod_cm/img/global/";
		$this->strGet = $strGet;
		if($this->intPagina>1){
			$html.="<a href=\"".$_SERVER["PHP_SELF"]."?pg=1&se=".$this->strSentido."&or=".$this->strOrden.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_pag_primera.gif\" alt=\"Primera\" title=\"Primera\" class=\"paginacion\" border=\"0\"></a>";
			$html.="<a href=\"".$_SERVER["PHP_SELF"]."?pg=".($this->intPagina-1)."&se=".$this->strSentido."&or=".$this->strOrden.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_pag_anterior.gif\" alt=\"Anterior\" title=\"Anterior\" class=\"paginacion\" border=\"0\"></a>";
		}
		if($this->intPagina<$this->intNumPags){
			$html.="<a href=\"".$_SERVER["PHP_SELF"]."?pg=".($this->intPagina+1)."&se=".$this->strSentido."&or=".$this->strOrden.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_pag_siguiente.gif\" alt=\"Siguiente\" title=\"Siguiente\" class=\"paginacion\" border=\"0\"></a>";
			$html.="<a href=\"".$_SERVER["PHP_SELF"]."?pg=".$this->intNumPags."&se=".$this->strSentido."&or=".$this->strOrden.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_pag_ultima.gif\" alt=\"Última\" title=\"Última\" class=\"paginacion\" border=\"0\"></a>";
		}
		return $html;
	}
	function getControlCombo($strGet=NULL){//void
		$html = "";
		$path_img = "../mod_cm/img/global/";
		$this->strGet = $strGet;
		$ar_get = explode("&",$this->strGet);
		if($this->intNumPags>1){
			$html.= "<form name=\"_paginacion\" method=\"get\">";
			$html.="Ir a página: ";
			$html.="<input type=\"hidden\" name =\"se\" value=\"".$this->strSentido."\">";
			$html.="<input type=\"hidden\" name =\"or\" value=\"".$this->strOrden."\">";
			for($e=0;$e<count($ar_get);$e++){
				$ar_g = explode("=",$ar_get[$e]);
				$html.="<input type=\"hidden\" name =\"".$ar_g[0]."\" value=\"".$ar_g[1]."\">";
			}
			$html.="<select name=\"pg\" onchange=\"document._paginacion.submit()\" class=\"paginacion\">";
			for($i=1;$i<=$this->intNumPags;$i++){
				if($i==$this->intPagina){
					$html.="<option value=\"".$i."\" selected=\"selected\">".$i."</option>";
				}else{
					$html.="<option value=\"".$i."\">".$i."</option>";
				}
			}
			$html.= "</select></form>";
		}
		return $html;
	}
	function ordenar($strOrden,$strGet=NULL){//void
		$this->strGet = $strGet;
		$strSent = $this->strSentido;
		if($strSent=="ASC"){
			$strSent ="DESC";
		}else{
			$strSent ="ASC";
		}
		$html="";
		$html.=$_SERVER["PHP_SELF"]."?se=".$strSent."&or=".$strOrden."&pg=".$this->intPagina.$this->strGet;
		echo $html;
	}
	// fin 
	function obtenerTotal(){//int
		return $this->intTotal;
	}// fin de la función
	

} // fin de la clase

?>