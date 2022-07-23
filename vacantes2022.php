<?php

header('Content-Type: text/html; charset=iso-8859-1');

$resultado = array();
$resultado_final = array();

$entrada = "in/adjudicacionesesp2-20220724.csv";
$salida = "out/adjudicacionesesp2-20220724.csv";

if (($gestor = fopen($entrada, "r")) !== FALSE) {
    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {

        //echo "=================== FILA $k =======================<br>";
        $fila = array();
        $k++;
        $numero = count($datos);

        for ($c=0; $c < $numero; $c++) {
           //echo "=================== COLUMNA $c =======================<br>";
            if(substr($datos[$c], 0,4)== "****"){
               $fila['plaza'] = substr($datos[0],4,4);
               
               if (isset($fila) && !empty($fila)){
                $resultado[] = $fila;
                $fila = array();
             }
            }elseif(substr($datos[$c], 0,3)== "***"){
               $fila['plaza'] = substr($datos[0],3,4);
              
               if (isset($fila) && !empty($fila)){
                $resultado[] = $fila;
                $fila = array();
              }
            }

            if(substr($datos[$c], 4,3) == " - "){
                $fila['tipo'] = substr($datos[0], 0,4);
                
                if (isset($fila) && !empty($fila)){
                    $resultado[] = $fila;
                }
            }
            if(substr($datos[$c], 3,3) == " - " && substr($datos[$c], 0,3) != "SEC"){
                $fila['especialidad'] = substr($datos[0], 0,3);
                
                if (isset($fila) && !empty($fila)){
                    $resultado[] = $fila;
                }
            }

            if(substr($datos[$c], 0,3)== "***"){
                $fila['adjudicatario'] = trim(substr($datos[0],12)).", ".trim($datos[1]);
                
                if (isset($fila) && !empty($fila)){
                    $resultado[] = $fila;
                }
            }
           
           
        }
       
    }

    fclose($gestor);
}

//print_r($resultado);

// Cabecera
$final['tipo'] = "Tipo";
$final['especialidad'] = "Especialidad";
$final['plaza'] = "Plaza";
$final['adjudicatario'] = "Adjudicatario";
$resultado_final[] = $final;
unset($final);

foreach($resultado as $key=>$value){
    if(isset($value['plaza'])){
        $final['plaza'] = $value['plaza'];
    }
    if(isset($value['tipo'])){
        $final['tipo'] = $value['tipo'];
    }
    if(isset($value['especialidad'])){
        $final['especialidad'] = $value['especialidad'];
    }
    if(isset($value['adjudicatario'])){
        $final['adjudicatario'] = trim($value['adjudicatario']);
        $resultado_final[] = $final;
        unset($final);
    }
    
}

//print_r($resultado_final);

$fp = fopen($salida, 'w');

foreach ($resultado_final as $campos) {
    fputcsv($fp, $campos);
}

fclose($fp);

echo "Archivo generado con éxito: ".$salida;
