<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Paginacion_Usuario
/////////////////////////////////////////////////////////////////////////////////*/

class Paginacion_Selector{ 

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
	
	function Paginacion_Selector($intPaginas,$objDb,$strConsulta,$strOrden,$strSentido,$strGet){//int
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
		
		if(isset($_GET['ordSel'])){
			$objSesion->crearVariable("ordSel",$_GET['ordSel']);
		}
		if (isset($_GET['sentSel'])){
			$objSesion->crearVariable("sentSel",$_GET['sentSel']);
		}
		if($objSesion->existeVariable("ordSel")){
			$this->strOrden= $objSesion->obtenerValor("ordSel");
		}else{
			$this->strOrden= $strOrden;
		}
		if($objSesion->existeVariable("sentSel")){
			$this->strSentido= $objSesion->obtenerValor("sentSel");
		}else{
			$this->strSentido= $strSentido;
		}
		$rsRegistrosPrincipal = $this->objDb->obtenerResultset($this->strConsulta);
		$this->intTotal =  $this->objDb->total($rsRegistrosPrincipal);
		
		if(isset($_GET['pagSel'])){
			$objSesion->crearVariable("pagSel",$_GET['pagSel']);
		}
		
		if($objSesion->existeVariable("pagSel")){
			$this->intPagina= $objSesion->obtenerValor("pagSel");
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
	}
	function getResultset(){
		return $this->rs;
	}
	function ordenar($intAncho, $intLargo, $imagenAscendente, $imagenDescendante, $strOrden, $strGet=NULL){//void
		$this->strGet = $strGet;
		$strTabla="";
		$strTabla.="<a href=\"".$_SERVER["PHP_SELF"]."?sentSel=DESC&ordSel=".$strOrden."&pagSel=".$this->intPagina.$this->strGet."\">";
		$strTabla.="<img src=\"".$imagenDescendante."\" alt=\"Desc.\" width=\"".$intAncho."\" height=\"".$intLargo."\" border=\"0\"></a>";
		$strTabla.="<a href=\"".$_SERVER["PHP_SELF"]."?sentSel=ASC&ordSel=".$strOrden."&pagSel=".$this->intPagina.$this->strGet."\">";
		$strTabla.="<img src=\"".$imagenAscendente."\" alt=\"Asc.\" width=\"".$intAncho."\" height=\"".$intLargo."\" border=\"0\"></a>";
		print $strTabla;
	}
	function crearManejador($tipo,$id,$bus){//void
		$strTabla="";
		if($this->intFinal==1){
			$strTabla.="Página 1";
		}else{
			$strTabla.= "<form name=\"formPag\" method=\"get\" action=\"\" style=\"margin:0px;\">";
			if($this->intPagina>1){
				
				$strTabla.="<a href=\"javascript:mostrarRegistrosLocal('".$bus."','".$tipo."','".$id."','".$this->intInicio."')\" title=\"Primera\">";
				$strTabla.="<img src=\"img/selector_primera.gif\" alt=\"Primera Página\" width=\"11\" height=\"11\" align=\"middle\" border=\"0\">";
				$strTabla.="</a>";
				
				$strTabla.=" <a href=\"javascript:mostrarRegistrosLocal('".$bus."','".$tipo."','".$id."','".($this->intPagina-1)."')\" title=\"Anterior\">";
				$strTabla.="<img src=\"img/selector_anterior.gif\" alt=\"Anterior\" width=\"11\" height=\"11\" align=\"middle\" border=\"0\">";
				$strTabla.="</a>";
			}
			//$strTabla.= "&nbsp;&nbsp;";
			$strTabla.="<select align=\"middle\"  name=\"pagSel\" onChange=\"javascript:mostrarRegistrosLocal('".$bus."','".$tipo."','".$id."',this.value);\">";
			for($i=$this->intInicio;$i<=$this->intFinal;$i++){
					if($i==$this->intPagina){
						$strTabla.= "<option value=\"".$i."\" selected >".$i."</option>";
					}else{
						$strTabla.= "<option value=\"".$i."\">".$i."</option>";
					}
			}
			$strTabla.= "</select>";
			$strTabla.="<input name=\"tipo\" value=\"".$tipo."\" type=\"hidden\">";
			$strTabla.="<input name=\"id\" value=\"".$id."\" type=\"hidden\">";
			if($this->intPagina<$this->intNumPags){
				$strTabla.= "<a href=\"javascript:mostrarRegistrosLocal('".$bus."','".$tipo."','".$id."','".($this->intPagina+1)."')\" title=\"Siguiente\">";
				$strTabla.="<img src=\"img/selector_siguiente.gif\" alt=\"Anterior\" width=\"11\" height=\"11\" align=\"middle\" border=\"0\">";
				$strTabla.="</a>";
				$strTabla.=" <a href=\"javascript:mostrarRegistrosLocal('".$bus."','".$tipo."','".$id."','".$this->intFinal."');\" title=\"Ultima\">";
				$strTabla.="<img src=\"img/selector_ultima.gif\" alt=\"Anterior\" width=\"11\" height=\"11\" align=\"middle\" border=\"0\">";
				$strTabla.="</a>&nbsp;";
			}
			$strTabla.= "</form>";
		}
		print $strTabla;
	}
	function obtenerResultado(){//void
		$strTabla=" ";
		if($this->intTotal>0){
			$strTabla.= "Total: ".$this->intResultado." ";
		}else{
			$strTabla.="0";
		}
		$strTabla.=" - ".$this->intTotal;	
		print $strTabla;
	}
	function obtenerTotal(){//int
		return $this->intTotal;
	
	}
}
?>