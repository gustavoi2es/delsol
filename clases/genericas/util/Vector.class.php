<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Vector
/////////////////////////////////////////////////////////////////////////////////*/

class Vector{ 
	
	var $arrayObj;
	
	function Vector(){ //constructor
		$this->arrayObj = array();
	}
	function agregar($obj){//void
		array_push($this->arrayObj,$obj);
	}
	function getObj($intIndex){//void
		$obj = NULL;
		if(isset($this->arrayObj[$intIndex])){
			$obj = $this->arrayObj[$intIndex];
		}
		return $obj;
	}
	function total(){//int
		return count($this->arrayObj);		
	}
	function borrar($indice){//void
		array_slice($this->arrayObj, $indice);
	}	
	
} // fin de la clase

?>