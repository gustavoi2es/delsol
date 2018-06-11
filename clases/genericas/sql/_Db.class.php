<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
									Db
/////////////////////////////////////////////////////////////////////////////////*/

class Db{ 

	private $intManejador;
	private $strConexion; 
	private $strUser; 
	private $strPassword; 
	private $strDataBase; 
	private $intConexion;
	private $boolConexion;
	private $strError;
	private $mysql;
	private $res;
	
	// Manejadores
	// 1 = MySql
	
	// CONTENIDO
	// conectar // boolean
	// cerrarConexion // void
	// obtenerResultset // int
	// total // int
	// executeResultset // array
	// executeUpdate // boolean 
	// cerrarResultset // void
	// printError // string (private)

	////////////////////////////////////////////////////////////////////////////////////////////

	// NOTA: constructor posee las propiedades para la conexión a una base de datos
	// ENTRADA: un array:
		// index 0: entero tipo de manejador
		// index 1: string de la conexion
		// index 2:	string usuario
		// index 3: string password
		// index 4: string base de datos
	// SALIDA: construye un bd dependiendo del tipo de manejador
	
	public function Db($arrayConexionDb){// constructor
		for($i=0;$i<count($arrayConexionDb);$i++){
			switch($i){
				case 0:
					$this->intManejador = $arrayConexionDb[$i];
				break;
				case 1:
					$this->strConexion = $arrayConexionDb[$i];
				break;
				case 2:
					$this->strUser = $arrayConexionDb[$i];
				break;
				case 3:
					$this->strPassword = $arrayConexionDb[$i];
				break;
				case 4:
					$this->strDataBase = $arrayConexionDb[$i];
				break;
			}
		}
	}
	// NOTA: conecta a una base de datos 
	// ENTRADA: ninguna
	// SALIDA: devuelve true si se conectó, false en caso contrario
	public function conectar(){//boolean
		//echo "hola";
		$this->boolConexion = false;
		switch($this->intManejador){
		// si es mysql
			case 1:
				$this->mysql = new mysqli($this->strConexion,$this->strUser,$this->strPassword,$this->strDataBase);
				if(mysqli_connect_errno()){
					throw new Exception("Falló la conección: ".mysqli_connect_error()."\n");
				}else{
					$this->intConexion=1;
				}
			break;
			// si es SQL SERVER
			case 2:
				$this->intConexion = @mssql_connect($this->strConexion, $this->strUser, $this->strPassword);
			break;
			// si es ODBC
			case 3:
				$this->intConexion = odbc_connect($this->strConexion, $this->strUser, $this->strPassword);
			break;
		}
		
		if($this->intConexion){
			switch($this->intManejador){
				// si es SQL SERVER
				case 1:
					$boolConectarBase = true;
				break;
				case 2:
					$boolConectarBase = @mssql_select_db($this->strDataBase,$this->intConexion);
				break;
				// si es ODBC
				case 3:
					$boolConectarBase = true;
				break;
			}
			if($boolConectarBase){
				$this->boolConexion=true;
			}
		}
		return $this->boolConexion;
	}//fin de la funcion
			
	public function error(){
		switch($this->intManejador){
			// si es mysql
			case 1:
				return $this->mysql->error;
				//return mysqli_error($this->res);
			break;
			case 3:
				return odbc_error($this->intConexion);
			break;
		}
	}
	// NOTA: cierra la conexión con la base de datos
	// ENTRADA: ninguna
	
	public function cerrarConexion(){//void
		
		switch($this->intManejador){
			// si es mysql
			case 1:
				try{
					$this->mysql->close();
				}catch(Exception $e){}
			break;
			// si es mysql
			case 2:
				mssql_close($this->intConexion);
			break;
			// si es Odbc
			case 3:
				odbc_close($this->intConexion);
			break;
		}
	
	} // fin de la función
	
	// NOTA: ejecuta una sentencia sql y obtiene el resultset
	// ENTRADA: Recibe un string de la consulta sql
	// SALIDA: Ejecuta una sentencia sql y devuelve el resultset
	
	public function obtenerResultset($strSql) {//int
		$resultado = NULL;
		if($this->boolConexion){
			switch($this->intManejador){
				// si es mysql
				case 1:
					//try{
						$resultado = $this->mysql->query($strSql);
						if(!$resultado){
							throw new Exception("Falló: ".$this->mysql->error."\n");
						}
					//}catch(Exception $e){}
				break;
				// si es SQL SERVER
				case 2:
					$resultado = @mssql_query($strSql,$this->intConexion);
				break;
				// si es ODBC
				case 3:
					$resultado = odbc_exec($this->intConexion,$strSql);
				break;
			}
		}
		$this->res = $resultado;	
		return $resultado;
	} // fin de la función
	// NOTA: función adquiere el array del resultset especificado por parámetro
	// ENTRADA: Un resultset
	// SALIDA: retorna el array con las filas del campo
	public function executeResultset($resultado){//array
		$arrayFilas = NULL;
		switch($this->intManejador){
			// si es mysql
			case 1:
				//try{
					if(!$resultado){
						throw new Exception("No existe el objeto Resultset.\n");
					}else{
						$arrayFilas = $resultado->fetch_array(MYSQLI_BOTH);
					}
				//}catch(Exception $e){}
			break;
			// si es SQL SERVER
			case 2:
				$arrayFilas = @mssql_fetch_array($resultado);
			break;
			case 3:
				$arrayFilas = odbc_fetch_array($resultado);
			break;
		}
		return $arrayFilas;
	}
	
	// ENTRADA: un entero resultset
	// SALIDA: retorna int total de filas de la tabla
	
	public function total($resultado){//int
		$intTotal=NULL;
		switch($this->intManejador){
			// si es mysql
			case 1:
				//try{
					if(!$resultado){
						throw new Exception("No existe el objeto Resultset.\n");
					}else{
						$intTotal =  $resultado->num_rows; 
					}
				//}catch(Exception $e){}
			break;
			// si es SQL SERVER
			case 2:
				$intTotal =  @mssql_num_rows($intResultset); 
			break;
			// si es SODBC
			case 3:
				/*$count=0;
				$int = $intResultset;
  				 while($temp = odbc_fetch_into($int, &$counter)){
       				$count++;
   			    }
				$intTotal = $count;*/
			break;
		}
		return $intTotal;
	}// fin de la funcion
	

	// NOTA: ejecuta una sentencia sql ( similar a obtenerResultset)
	// ENTRADA: string de la consulta sql
	// SALIDA: devuelve true si se ejecuta correctamente, false en caso contrario

	public function executeUpdate($strSql){//boolean
		$resultado=0;
		if($this->boolConexion){
			switch($this->intManejador){
				// si es mysql
				case 1:
					//try{
						$resultado = $this->mysql->query($strSql);
						if(!$resultado){
							throw new Exception("Falló: ".$this->mysql->error);
							
						}
					//}catch(Exception $e){}
				break;
				// si es SQL SERVER
				case 2:
					$resultado = @mssql_query($strSql,$this->intConexion);
				break;
				// si es ODBC
				case 3:
					$arrayFilas = odbc_fetch_array($intResultset);
				break;

			}
		}
		$this->res = $resultado;
		return $resultado;
			
	} // fin de la función
	
	public function executeUpdateMulti($strSql){//boolean
		$resultado=0;
		if($this->boolConexion){
			switch($this->intManejador){
				// si es mysql
				case 1:
					//try{
						$resultado = $this->mysql->multi_query($strSql);
						if(!$resultado){
							throw new Exception("Falló: ".$this->mysql->error);
						}
				//	}catch(Exception $e){}
				break;
			}
		}
		return $resultado;
			
	} // fin de la función
	// NOTA: Cierra un resultset
	// ENTRADA:Un resultset
	// SALIDA: es procedimiento
	
	public function cerrarResultset($resultado) { //void
		switch($this->intManejador){
			// si es mysql
			case 1:
			//	try{
					if(!$resultado){
						throw new Exception("No existe el objeto Resultset.\n");
					}else{
						$resultado->close();
					}
				//}catch(Exception $e){}
			break;
			// si es SQL SERVER
			case 2:
				@mssql_free_result($resultado);
			break;
		}
	} // fin de la función
	
	// TRANSACCIONES ////////////////////////////////////////////////////////////////////////////

	public function begin(){ // void
		$this->mysql->autocommit(FALSE);
	}
	public function commit(){ // void
		$this->mysql->commit();
	}
	public function rollback(){ // void
		$this->mysql->rollback();
	}
	public function bloquear($table,$tipo="WRITE",$alias=""){
		$strCon = "LOCK TABLES ".$table;
		if($tipo!=""){
			$strCon.=" AS ".$alias;
		} 
		$strCon.=" ".$tipo;
		try{
			$res = $this->executeUpdate($strCon);
			if(!$res){
				throw new Exception("Falló: ".$this->mysql->error);
			}
		}catch(Exception $e){}
		
	}
	public function desbloquear(){
		$strCon = "UNLOCK TABLES";
		try{
			$res = $this->executeUpdate($strCon);
			if(!$res){
				throw new Exception("Falló: ".$this->mysql->error);
			}
		}catch(Exception $e){}
	}
	// NOTA: captura los errores desplegados por el manejador de base de datos
	// ENTRADA: ninguna
	// SALIDA: retorna el string del error
	
} // fin de la clase
?>