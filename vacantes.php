<?php

header('Content-Type: text/html; charset=iso-8859-1');

$resultado = array();
$resultado_final = array();
$vacante = false;
$plazaIncorrecta = false;
$k = 0;
//$entrada = "vacantes20190907_in2.csv";
//$salida = "vacantes20190907_out.csv";
//$entrada = "in/20200901esp.csv";
//$salida = "out/20200901esp.csv";
$entrada = "in/vacantes20210902.csv";
$salida = "out/vacantes20210902.csv";

if (($gestor = fopen($entrada, "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {

        //echo "=================== FILA $k =======================<br>";
        $fila = array();
        $k++;
        $numero = count($datos);

        for ($c=0; $c < $numero; $c++) {
           //echo "=================== COLUMNA $c =======================<br>";
            
            if(substr($datos[$c], 0,11)== "- Vacante -"){
                unset($fila);
                $tok = strtok($datos[$c], "\n");
                for($i=0;$i<=3;$i++){
                    if($i == 1){ // Plaza
                        if(substr($tok,0,4)>1000){
                            $fila['plaza'] = substr($tok,0,4);
                        }else{
                            $plazaIncorrecta = true;
                        }
                    }
                    
                    if($i == 2){ // Especialidad
                        if($plazaIncorrecta==true){
                            $fila['plaza'] = substr($tok,0,4);
                            $fila['especialidad'] = substr($tok,6,4);
                            $plazaIncorrecta=false;
                        }else{
                            $fila['especialidad'] = substr($tok,0,3);
                        }
                    }

                    $tok = strtok("\n");
                }
                $vacante=true;
            }

            // Adjudicatario
            if(substr($datos[$c], 0,17)== "- Adjudicatario -" && $vacante==true){
                
                if(str_replace(";;;;","",substr($datos[$c],30)!="")){
                    $fila['adjudicatario'] = str_replace(";;;;","",substr($datos[$c],30));
                    $vacante = false;
                }
            }
            if(substr($datos[$c], 0,3)== "***"){
                print_r($datos[$c]);
                echo "<br>";
                if(str_replace(";;;;","",substr($datos[$c],12)!="" && $vacante==true)){
                    $fila['adjudicatario'] = str_replace(";;;;","",substr($datos[$c],12));
                    $vacante = false;
                }
            }
           
        }
        if (isset($fila) && !empty($fila)){
            $resultado[] = $fila;
        }
      
    }

    fclose($gestor);
}

//print_r($resultado);

foreach($resultado as $key=>$value){
    if(isset($value['plaza'])){
        unset($final);
        $final['plaza'] = $value['plaza'];
        $final['especialidad'] = $value['especialidad'];
    }else{
        $final['adjudicatario'] = trim(str_replace(';','',$value['adjudicatario']));
    }
    if(isset($final['adjudicatario']) && isset($final['plaza']) && isset($final['especialidad'])){
        $resultado_final[] = $final;
    }  
}

//print_r($resultado_final);

$fp = fopen($salida, 'w');

foreach ($resultado_final as $campos) {
    fputcsv($fp, $campos);
}

fclose($fp);

echo "Archivo generado con éxito: ".$salida;
