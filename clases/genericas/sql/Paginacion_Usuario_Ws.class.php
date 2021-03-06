<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Paginacion
/////////////////////////////////////////////////////////////////////////////////*/

class Paginacion_Usuario_Ws{ 

	var $intPaginas;
	//var $strConsulta;
	var $strOrden;
	var $strSentido;
	//var $objDb;
	var $intPagina;
	var $intNumPags;
	var $intInicio;
	var $intFinal;
	var $intResultado;
	var $intTotal=0;
	var $strGet;
	var $respuesta_ws;
	var $url;
	var $url_amigable=1;
	
	function Paginacion_Usuario_Ws($oSoapClient,$ar_query_ws,$ar_paginacion){//int
		
		$intPaginas = $ar_paginacion["cantidad_registros"];
		$strOrden = $ar_paginacion["orden"];
		$strSentido = $ar_paginacion["sentido"];
		
		$this->intPaginas = $intPaginas;
		$this->intPagina= 1;
		$this->intNumPags = 1;
		$this->intInicio = 1;
		$this->intFinal = 1;
		$this->intResultado = 1;
		$this->intTotal = 0;
		$this->strGet = "";
		
		$objSesion = new Sesion();
		
		if(isset($_GET['ord'])){
			$objSesion->crearVariable("ord",$_GET['ord']);
		}
		if (isset($_GET['sen'])){
			$objSesion->crearVariable("sen",$_GET['sen']);
		}
		if($objSesion->existeVariable("ord")){
			$this->strOrden= $objSesion->obtenerValor("ord");
		}else{
			$this->strOrden= $strOrden;
		}
		if($objSesion->existeVariable("sen")){
			$this->strSentido= $objSesion->obtenerValor("sen");
		}else{
			$this->strSentido= $strSentido;
		}
		if(isset($_GET['pag'])){
			$objSesion->crearVariable("pag",$_GET['pag']);
		}
		if($objSesion->existeVariable("pag")){
			$this->intPagina= $objSesion->obtenerValor("pag");
		}else{
			$this->intPagina= 1;
		}
		if(!isset($this->intPagina)){
			$this->intPagina=1;
			$this->intInicio=1;
			$this->intFinal=$this->intPaginas;
		}
		$ar_query_ws["Nropaginasolicitada"]=$this->intPagina;
		$ar_query_ws["Maxregistrosporpagina"]=$this->intPaginas;
				
		$this->respuesta_ws = $oSoapClient->call("Execute",$ar_query_ws);
		$error = 0;
		if($oSoapClient->fault) {     
			$error=1;
		}else {  
			$sError = $oSoapClient->getError();  
			if($sError){     
				$error = 1;
			} 
		}
		if($error==1){
			$this->respuesta_ws = array();
		}else{
			$total = $this->respuesta_ws["Sdttotalelementos"];
			$total = $total["PaginarElementos.PaginarElementosItem"];
			$total = $total["RegistrosTotales"];
			$this->intTotal = $total;
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
		$this->intResultado = $limitInf+$this->intPaginas;
		if($this->intPagina==$this->intNumPags){
			$this->intResultado = $this->intTotal;
		}

	} // fin de la funci�n
	public function getRespuesta(){
		return $this->respuesta_ws;
	}
	public function setUrl($url,$url_amigable=1){
		$this->url = $url;
		$this->url_amigable = $url_amigable;
	}
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
		$path_img = "/imagenes/img_presentacion/";
		$this->strGet = $strGet;
		$html.= "<form name=\"_paginacion\" method=\"get\" action=\"\">";
		
		$pag_texto = "pag/";
		$pag_sep = "/";
		if($this->url_amigable==0){
			$pag_texto = "&pag=";
			$pag_sep = "";	
		}
		if($this->intPagina>1){
			$html.="<div><a href=\"".$this->url.$pag_texto."1".$pag_sep.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_primera.gif\" alt=\"Primera\" title=\"Primera\" class=\"paginacion\" align=\"top\"></a>";
			$html.="<a href=\"".$this->url.$pag_texto.($this->intPagina-1).$pag_sep.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_anterior.gif\" alt=\"Anterior\" title=\"Anterior\" class=\"paginacion\" align=\"top\"></a>&nbsp;";
		}
		if($this->intNumPags>1){
			
			$html.="<select name=\"pag\" class=\"paginacion\"onchange=\"paginar('".$this->url.$pag_texto.$pag_sep."',this.value,'".$pag_sep."')\" class=\"paginacion\">";
			for($i=1;$i<=$this->intNumPags;$i++){
				if($i==$this->intPagina){
					$html.="<option value=\"".$i."\" selected=\"selected\">".$i."</option>";
				}else{
					$html.="<option value=\"".$i."\">".$i."</option>";
				}
			}
			$html.= "</select>";
		}
		if($this->intPagina<$this->intNumPags){
			$html.="&nbsp;<a href=\"".$this->url.$pag_texto.($this->intPagina+1).$pag_sep.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_siguiente.gif\" alt=\"Siguiente\" title=\"Siguiente\" class=\"paginacion\" align=\"top\"></a>";
			$html.="<a href=\"".$this->url.$pag_texto.$this->intNumPags.$pag_sep.$this->strGet."\">";
			$html.="<img src=\"".$path_img."ico_ultima.gif\" alt=\"�ltima\" title=\"�ltima\" class=\"paginacion\" align=\"top\"></a>";
		}
		$html.= "</form>";
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
	}// fin de la funci�n
	

} // fin de la clase

?>