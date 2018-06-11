<?
//session_start();
//session_regenerate_id(true);
class CodigoSeguridad{
	function CodigoSeguridad(){}
	function generar(){ // String
		$numeros = range(0,9);
		$letras =range("a","z");
		srand(time());
		shuffle($letras);
		shuffle($numeros);
		$strTicket="";
		for($i=0;$i<=5;$i++){
			$letra = $letras[rand(0,25)];
			$numero = $numeros[rand(0,9)];
			$array=array($letra,$numero);
			$strTicket.=$array[rand(0,1)];
		}
		if(!isset($_SESSION["codigo_seguridad"])){
			$_SESSION["codigo_seguridad"] = strtolower($strTicket);
		}
	}
	function eliminar(){// Void
		if(isset($_SESSION["codigo_seguridad"])){
			unset($_SESSION["codigo_seguridad"]);
		}
	}
	function existe(){// Boolean
		$existe = false;
		if(isset($_SESSION["codigo_seguridad"])){
			$existe = true;
		}
		return $existe;
	}
	function get(){ // String
		$codigo = "";
		if(isset($_SESSION["codigo_seguridad"])){
			$codigo = $_SESSION["codigo_seguridad"];
		}
		return $codigo;
	}
	function getForm($nombre="Codigo",$class=""){//String
		$form = "<input name=\"".$nombre."\" type=\"text\" id=\"".$nombre."\"";
		if($class!=""){
			$form.=" class=\"".$class."\"";
		}
		$form.=" size=\"6\" maxlength=\"6\"";
		$form.="\"/>";
		return $form;
	}
	function iguales($campo){ //Bollena
		$igual=false;
		$codigo = $this->get();
		if($campo!="" && $codigo!=""){
			if(strtolower($campo)===$codigo){
				$igual=true;
			}
		}
		return $igual;
	}
	function getImagen(){
		$objUtil = new Util();
		return "<img src=\"/codigo_seguridad.php?r=".$objUtil->obtenerTicket()."\" align=\"top\" alt=\"\" style=\"margin-top:1px;\"/>";
	}
}

?>