<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Paginacion_Ajax
/////////////////////////////////////////////////////////////////////////////////*/

class Paginacion_Ajax{ 

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
	
	function Paginacion_Ajax($intPaginas,$objDb,$strConsulta,$strOrden,$strSentido){//int
		
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
		
		if(isset($_GET['or_ajax'])){
			$objSesion->crearVariable("or_ajax",$_GET['or_ajax']);
		}
		if (isset($_GET['se_ajax'])){
			$objSesion->crearVariable("se_ajax",$_GET['se_ajax']);
		}
		if($objSesion->existeVariable("or_ajax")){
			$this->strOrden= $objSesion->obtenerValor("or_ajax");
		}else{
			$this->strOrden= $strOrden;
		}
		if($objSesion->existeVariable("se_ajax")){
			$this->strSentido= $objSesion->obtenerValor("se_ajax");
		}else{
			$this->strSentido= $strSentido;
		}
		$rsRegistrosPrincipal = $this->objDb->obtenerResultset($this->strConsulta);
		$this->intTotal =  $this->objDb->total($rsRegistrosPrincipal);
		
		if(isset($_GET['pg_ajax'])){
			$objSesion->crearVariable("pg_ajax",$_GET['pg_ajax']);
		}
		if($objSesion->existeVariable("pg_ajax")){
			$this->intPagina= $objSesion->obtenerValor("pg_ajax");
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
	function getControl($funcion,$param,$strGet=NULL){//void
		$html = "";
		$path_img = "../mod_cm/img/global/";
		$this->strGet = $strGet;
		if($param!=""){
			$param = $param.",";
		}
		
		if($this->intPagina>1){
			$html.="<a href=\"javascript:".$funcion."(".$param."'1','".$this->strOrden."','".$this->strSentido."');\">";
			$html.="<img src=\"".$path_img."ico_pag_primera.gif\" alt=\"Primera\" title=\"Primera\" class=\"paginacion\" border=\"0\"></a>";
			$html.="<a href=\"javascript:".$funcion."(".$param."'".($this->intPagina-1)."','".$this->strOrden."','".$this->strSentido."');\">";
			$html.="<img src=\"".$path_img."ico_pag_anterior.gif\" alt=\"Anterior\" title=\"Anterior\" class=\"paginacion\" border=\"0\"></a>";
		}
		if($this->intPagina<$this->intNumPags){
			$html.="<a href=\"javascript:".$funcion."(".$param."'".($this->intPagina+1)."','".$this->strOrden."','".$this->strSentido."');\">";
			$html.="<img src=\"".$path_img."ico_pag_siguiente.gif\" alt=\"Siguiente\" title=\"Siguiente\" class=\"paginacion\" border=\"0\"></a>";
			$html.="<a href=\"javascript:".$funcion."(".$param."'".$this->intNumPags."','".$this->strOrden."','".$this->strSentido."');\">";
			$html.="<img src=\"".$path_img."ico_pag_ultima.gif\" alt=\"Última\" title=\"Última\" class=\"paginacion\" border=\"0\"></a>";
		}
		return $html;
	}
	function ordenar($funcion,$param,$strOrden,$strGet=NULL){//void
		$this->strGet = $strGet;
		$strSent = $this->strSentido;
		if($strSent=="ASC"){
			$strSent ="DESC";
		}else{
			$strSent ="ASC";
		}
		if($param!=""){
			$param = $param.",";
		}
		$html="";
		$html.="javascript:".$funcion."(".$param."'".$this->intPagina."','".$strOrden."','".$strSent."')";
		echo $html;
	}
	// fin 
	function obtenerTotal(){//int
		return $this->intTotal;
	}// fin de la función
	
	public function getOrden(){
		return $this->strOrden;
	}
	public function getSentido(){
		return $this->strSentido;
	}
	public function getPagina(){
		return $this->intPagina;
	}

} // fin de la clase

?>