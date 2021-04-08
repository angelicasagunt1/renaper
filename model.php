<?php
ini_set("memory_limit","128M");
include('conexionAngelica.php');

class Punica extends Conexion{
    
    public function imprimir_punica(){
    /*
       parent::conectar();

        $sql = "SELECT idpersona,nrodni,apellido,nombres,sexo,fechanac,fallecido, procesado 
                FROM punica_2009,personas_2008 WHERE punica_2009.idpersona=personas_2008.ID_PERSONA AND procesado = 1 GROUP BY idpersona LIMIT 50000";
       $result = parent::query($sql);
    */
       $connect = mysqli_connect("10.10.99.15", "msanchez", "7A3B7F5995542953A8025CEDF6D0B0971E1A9C3F", "CDRNTEST");
    
       $result = mysqli_query($connect, "SELECT idpersona,nrodni,apellido,nombres,sexo,fechanac,fallecido, procesado 
       FROM punica_2009,personas_2008 WHERE punica_2009.idpersona=personas_2008.ID_PERSONA AND procesado = 1 GROUP BY idpersona LIMIT 50000") or die(mysqli_error()); 

        $headers = array(
            'idpersona',
            'nrodni',
            'apellido',
            'nombres',
            'sexo',
            'fechanac',
            'fallecido',
            'procesado'
        );

        $movimientos = parent::consultarTodos($result);
        $ids_procesados = "";
        $export = (isset($_GET['export'])) ? $_GET['export'] : "" ;
        $reset =  (isset($_GET['reset']))  ? $_GET['reset']  : "" ;

        $fp = fopen('php://output', 'w');
        if ($fp && $result && $export == 1) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($fp, $headers);

            for($i=0; $i<=count($movimientos)-1;$i++)
            {
                fputcsv($fp, $movimientos[$i]);
                $ids_procesados.=$movimientos[$i]['0'].",";
            }
            fclose($fp);
    
        }

        if (!empty($ids_procesados)){
            $ids_procesados = preg_replace("~,$~", "", $ids_procesados);
            // parent::query($sql_update);
            $sql_update = "UPDATE punica_2009 SET procesado = 2 WHERE idpersona IN(".$ids_procesados.")";
        
            $result = mysqli_query($connect, $sql_update) or die(mysqli_error()); 
        }

        if(empty($ids_procesados) && $export == 1){
            
            $sql_update = "UPDATE punica_2009 SET procesado = 1";
           // parent::query($sql_update);

            $result = mysqli_query($connect,  $sql_update) or die(mysqli_error()); 
        }

        if($reset == 1){
            $sql_update = "UPDATE punica_2009 SET procesado = 1";
            //parent::query($sql_update);
            $result = mysqli_query($connect,  $sql_update) or die(mysqli_error()); 

            echo '<script language="javascript">';
            echo 'alert("Se actualizaron todos los registros de PROCESADO en 1")'; 
            echo '</script>';
        }
    }

}

$punica = new Punica();
$punica->imprimir_punica();

?>
