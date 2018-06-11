<?
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Xml
/////////////////////////////////////////////////////////////////////////////////*/
class Xml{
	
	private $doc;
	private $xml;
	private $xsd;
	
	public function Xml($xml,$xsd=NULL){
		$this->doc = new DomDocument('1.0', 'UTF-8');
		$this->xml = $xml;
		$this->xsd = $xsd;
		libxml_use_internal_errors(true);
		$this->doc->load($this->xml);
		$xm = file_get_contents($this->xml);
		$cod = mb_detect_encoding($xm);
		$xm = mb_convert_encoding($xm,"ISO-8859-1");
		/*if($cod=="UTF-8"){
			$xm = file_get_contents(utf8_decode($this->xml));
			$this->doc->loadXML(utf8_encode($xm));
		}else{
			
			$this->doc->loadXML(utf8_encode($xm));
		}*/
		$this->doc->loadXML($xm);
		//$this->doc->loadXML(utf8_encode($xm));
		//$this->doc->load($this->xml);
	}
	public function validar(){
		
		if(!$this->doc->schemaValidate($this->xsd)){
			$errors = libxml_get_errors();
			throw new Exception("No válido");
		}
	}
	public function getCantidad($etiqueta){
		$elemento = $this->doc->getElementsByTagName($etiqueta);
		return $elemento->length;
	}
	public function getDoc(){
		return $this->doc;
	}
	public function getArchivo(){
		$x explode
= explode("/",$this->xml);
		return $x[count($x)-1];
	}
	private function libxml_display_error($error){
		$return = "<br/>\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "- <b>Advertencia $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "- <b>Error $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "- <b>Error Fatal $error->code</b>: ";
				break;
		}
		$return .= trim($error->message);
		if ($error->file) {
			$return .=    " en <b>$error->file</b>";
		}
		$return .= " en línea <b>$error->line</b><br/>";
	
		return $return;
	}
	private function libxml_display_errors() {
		$errors = libxml_get_errors();
		$er ="";
		foreach ($errors as $error) {
			$er.=$this->libxml_display_error($error);
		}
		libxml_clear_errors();
		return $er;
	}
	public function getErrores(){
		return $this->libxml_display_errors();
	}
}
?>