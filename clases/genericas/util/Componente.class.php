<? 
/*/////////////////////////////////////////////////////////////////////////////////
					Desarrollado por i2es | www.i2es.com
/////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////
								Componente
/////////////////////////////////////////////////////////////////////////////////*/

class Componente{
	function Componente(){}
	
	function combo($ar,$nivel=3,$n1="combo1",$n2="combo2",$n3="combo3",$sel1=0,$sel2=0,$sel3=0,$ancho=150,$mostrarUsa=NULL,$mostrarPais=1){
		$arNivel1 = $ar;
		$combo1 = "";
		$combo2 = "";
		$combo3 = "";
		
		if($nivel>1){
			$index1 = 0;
			for($i=0;$i<count($ar);$i++){
				if($arNivel1[$i]["id"]==$sel1){
					$index1 = $i;
					break;	
				}
			}
			$arNivel2=$arNivel1[$index1]["nivel"];
			$combo1.="\n<script type=\"text/javascript\">\n";
			$combo1.="var ar".$n1."=".$this->comboJavaScript($arNivel1,$nivel)."\n";
			if($nivel>2){
				$index2 = 0;
				for($i=0;$i<count($arNivel2);$i++){
					if($arNivel2[$i]["id"]==$sel2){
						$index2 = $i;
						break;	
					}
				}
				
				$arNivel3=$arNivel2[$index2]["nivel"];
			}
			$combo1.="</script>\n";
		}
		// COMBO 1
		if($mostrarPais==1){
			$combo1.="\n<select name=\"".$n1."\" id=\"".$n1."\" style=\"width:".$ancho."px;\"";
			if($nivel>1){
				$combo1.=" onchange=\"crearComboNivel1('".$n2."',ar".$n1.",this.options.selectedIndex)";
				if($nivel>2){
					$combo1.=";crearComboNivel2('".$n3."','".$n1."',ar".$n1.")";
				}
				if($mostrarUsa==1){
					$combo1.=";mostrarUsa('".$n1."','".$n2."','".$n3."',1)";
				}
				$combo1.="\"";
			}
			
			$combo1.=">\n";
			for($i=0;$i<count($arNivel1);$i++){
				$combo1.="<option value=\"".$arNivel1[$i]["id"]."\"";
				if($sel1==$arNivel1[$i]["id"]){
					$combo1.=" selected=\"selected\" ";
				}
				$combo1.=">".$arNivel1[$i]["nombre"]."</option>\n";
			}
			$combo1.="</select>\n";
		}else{
			 $combo1.="<input name=\"".$n1."\" id=\"".$n1."\" type=\"hidden\" value=\"".$sel1."\" />";
		}
		
		// COMBO 2
		if($nivel>1){
			$combo2.="\n<select name=\"".$n2."\" id=\"".$n2."\" style=\"width:".$ancho."px;\"";
			if($nivel>2){
				$combo2.=" onchange=\"crearComboNivel3('".$n3."','".$n1."',ar".$n1.",this.options.selectedIndex)\"";
			}
			
			$combo2.=">\n";
			for($i=0;$i<count($arNivel2);$i++){
				$combo2.="<option value=\"".$arNivel2[$i]["id"]."\"";
				if($sel2==$arNivel2[$i]["id"]){
					$combo2.=" selected=\"selected\" ";
				}
				$combo2.=">".$arNivel2[$i]["nombre"]."</option>\n";
			}
			$combo2.="</select>\n";
		}
		// COMBO 3
		if($nivel>2){
			$combo3.="\n<select name=\"".$n3."\" id=\"".$n3."\" style=\"width:".$ancho."px\">\n";
			for($i=0;$i<count($arNivel3);$i++){
				$combo3.="<option value=\"".$arNivel3[$i]["id"]."\"";
				if($sel3==$arNivel3[$i]["id"]){
					$combo3.=" selected=\"selected\" ";
				}
				$combo3.=">".$arNivel3[$i]["nombre"]."</option>\n";
			}
			$combo3.="</select>\n";
		}
		$arCombo = array();
		$arCombo["nivel1"] = $combo1;
		$arCombo["nivel2"] = $combo2;
		$arCombo["nivel3"] = $combo3;
		return $arCombo;
	}
	function comboJavaScript($ar,$nivel=3){
		$tx = "[";
		for($i=0;$i<count($ar);$i++){
			$ar2 = $ar[$i]["nivel"];
			$tx.="[";
			for($u=0;$u<count($ar2);$u++){
				$tx.="[";
				$tx.="'".$ar2[$u]["id"]."',";
				$tx.="'".$ar2[$u]["nombre"]."'";
				
				// NIVEL 3 ////////////////////////
				if($nivel==3){
					$tx.=",";
					$ar3 = $ar2[$u]["nivel"];
					$tx.="[";
					for($e=0;$e<count($ar3);$e++){
						$tx.="[";
						$tx.="'".$ar3[$e]["id"]."',";
						$tx.="'".$ar3[$e]["nombre"]."'";
						$tx.="]";
						if($e<count($ar3)-1){
							$tx.=",";
						}
					}
					$tx.="]";
				}
				/////////////////////////////////
				$tx.="]";
				if($u<count($ar2)-1){
					$tx.=",";
				}
			}
			$tx.="]\n";
			if($i<count($ar)-1){
				$tx.=",";
			}
		}
		$tx.="]";
		return $tx;
	}
	// MULTIPLE OPCION ///////////////////////////////////////////////////////
	
	function multipleopcion($arMultiple,$arSel,$nombreId,$ancho=500,$dividir=3){
	   $anchofila = ceil(($ancho-(20*$dividir))/$dividir)-10;
	   $txtSel = "";
	   for($i=0;$i<count($arSel);$i++){
		$txtSel.=$arSel[$i]."_";
	   }
	   $varSel = "";
	   $multi="";
	   $multi.="<div style=\"width:".$ancho."px;visibility:hidden;display:none;position:absolute;\" id=\"".$nombreId."_master\">";
	   $multi.="<div class=\"multiple_cabezal\" style=\"width:".($ancho-6)."px;\"><a href=\"javascript:mostrarMulti('".$nombreId."')\" class=\"multi\">Close x</a></div>";
	   $multi.="<div class=\"multiple_cuerpo\" style=\"width:".($ancho-10)."px;\" id=\"".$nombreId."_master2\">";
	   $e=1;
	   if($e==1){
			$multi.="<div style=\"width:".($ancho-10)."px;\">";
	   }
	   for($u=0;$u<count($arMultiple);$u++){
			$nombre = $arMultiple[$u]["nombre"];
			$id = $arMultiple[$u]["id"];
			$multi.="<div class=\"multiple_fila\" style=\"width:".$anchofila."px;";
			if($e%$dividir==0){
				$multi.="border:0px;";
			}
			$multi.="\">";
			for($i=0;$i<count($arSel);$i++){
				$varSel = "";
				if($arSel[$i]==$id){
					$varSel = " checked=\"checked\" ";
					break;
				}
			}
			$multi.="<div class=\"muliple_check\">";
			$multi.="<input type=\"checkbox\" ".$varSel." onclick=\"seleccionMulti('".$nombreId."')\" value=\"".$id."\" id=\"".$nombreId."_".$id."\" />";
			$multi.="</div>";
			$multi.="<div class=\"multiple_texto\" onclick=\"seleccionMultiCheck('".$nombreId."_".$id."')\">";
			$multi.=$nombre;
			$multi.="</div><br />";
			$multi.="</div>";
			if($e%$dividir==0){
				$multi.="</div>";
				if($u<count($arMultiple)-1){
					$multi.="<div style=\"width:".($ancho-10)."px;\">";
				}
			}else{
				if($u==count($arMultiple)-1){
					$multi.="</div>";
				}
			}
			$e++; 
		}
		$multi.="</div>";
		$multi.="<input type=\"hidden\" name=\"".$nombreId."\" id=\"".$nombreId."\" value=\"".$txtSel."\" />";
		$multi.="</div>";
		return $multi;
	}
}// fin de la clase
?>