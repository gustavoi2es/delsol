<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Ordenar
/////////////////////////////////////////////////////////////////////////////////*/

class Ordenar{ 
	
	private $objDb;
	private $tabla;
	
	public function Ordenar($objDb,$tabla){ //constructor
		
			$this->objDb = $objDb;
			$this->tabla = $tabla;
			
	}
	public function acomodarOrden($id,$filtros=array()){
		$i=0;
		$orden = 1;
		$cadenaFiltro = $this->obtenerFiltros($filtros);
		$strConsulta ="SELECT orden FROM ".$this->tabla." WHERE id=".$id;
		$rs = $this->objDb->obtenerResultset($strConsulta);
		if($ar = $this->objDb->executeResultset($rs)){
			$orden = $ar["orden"];
			
			$strCon2 = "SELECT id,orden FROM ".$this->tabla." WHERE ";
			if($cadenaFiltro!=""){
				$strCon2.=$cadenaFiltro." AND ";
			}
			$strCon2.=" id<>".$id." ORDER BY orden ASC";
			
			$rs2 = $this->objDb->obtenerResultset($strCon2);
			while($ar2 = $this->objDb->executeResultset($rs2)){
				$i++;
				if($i==$orden){
					$strConUD = "UPDATE ".$this->tabla." SET orden = ".($orden+1)." WHERE id=".$ar2["id"];
					$this->objDb->executeUpdate($strConUD);
					$i++;
				}else{
					$strConUD = "UPDATE ".$this->tabla." SET orden = ".$i." WHERE id=".$ar2["id"];
					$this->objDb->executeUpdate($strConUD);
				}
			}
		}
	}
	public function modificarOrden($tipo,$id,$filtros=array()){
		
		$cadenaFiltro = $this->obtenerFiltros($filtros);
		$strConsulta ="SELECT orden FROM ".$this->tabla." WHERE id=".$id;
		$rs = $this->objDb->obtenerResultset($strConsulta);
		
		if($ar = $this->objDb->executeResultset($rs)){
			if($tipo=="subir"){
				$orden = $ar["orden"]-1;
			}else{
				$orden = $ar["orden"]+1;
			}	
			
			$strConsulta2 = "SELECT orden,id FROM ".$this->tabla." WHERE orden=".$orden;
			if($cadenaFiltro!=""){
				$strConsulta2.=" AND ";
				$strConsulta2.=$cadenaFiltro;
			}
			$rs2 = $this->objDb->obtenerResultset($strConsulta2);
			
			if($ar2 = $this->objDb->executeResultset($rs2)){
				if($tipo=="subir"){
					$strCosnultaUP = "UPDATE ".$this->tabla." SET orden=".($ar2["orden"]+1)." WHERE id=".$ar2["id"];
					$this->objDb->executeUpdate($strCosnultaUP);
				}else{
					$strCosnultaUP = "UPDATE ".$this->tabla." SET orden=".($ar2["orden"]-1)." WHERE id=".$ar2["id"];
					$this->objDb->executeUpdate($strCosnultaUP);
				}
			}
			$strCosnultaUP = "UPDATE ".$this->tabla." SET orden=".$orden." WHERE id=".$id;
			$this->objDb->executeUpdate($strCosnultaUP);
		}
	}
	
	public function existeOrden($orden,$id,$filtros=array()){
		$hay = false;
		$cadenaFiltro = $this->obtenerFiltros($filtros);
		$strCon = "SELECT id FROM ".$this->tabla." WHERE id<>".$id." AND orden=".$orden;
		if($cadenaFiltro!=""){
			$strCon.=" AND ";
			$strCon.=$cadenaFiltro;
		}
		$rs = $this->objDb->obtenerResultset($strCon);
		if($ar = $this->objDb->executeResultset($rs)){
			$hay = true;
		}
		return $hay;
	}
	public function obtenerOrden($filtros=array(),$nuevo=1){
		$orden = 0;
		$cadenaFiltro = $this->obtenerFiltros($filtros);
		$strConsulta = "SELECT MAX(orden) AS ord FROM ".$this->tabla;
		if($cadenaFiltro!=""){
			$strConsulta.=" WHERE ";
			$strConsulta.=$cadenaFiltro;
		}
		$rs = $this->objDb->obtenerResultset($strConsulta);
		if($ar=$this->objDb->executeResultset($rs)){
			$orden = $ar["ord"];
			if(!$orden){
				$orden = 0;
			}
		}
		if($nuevo==1){
			$orden++;
		}
		return $orden;
	}
	
	private function obtenerFiltros($filtros){
		$cadena = "";
		for($i=0;$i<count($filtros);$i++){
			$cadena.= $filtros[$i][0]."=";
			if($filtros[$i][1]!="int"){
				$cadena.= "'".$filtros[$i][2]."'";
			}else{
				$cadena.= $filtros[$i][2];
			}
			if($i<count($filtros)-1){
				$cadena.=" AND ";
			}
		}
		return $cadena;
	}
} 

?>