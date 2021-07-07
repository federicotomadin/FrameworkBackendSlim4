<?php

namespace Components;

use Exception;

class FuncionesGenerales
{

    public static function ValidarHorario($horario) {

        date_default_timezone_set('America/Argentina/Buenos_Aires');

            if (isset($horario)) {
                $horario = date("H:i");
            } else  {
                return $horario;
            }

        return $horario;   
    }

    public static function ValidarCampo($campo) {
       
        if(isset($campo)) {
            return $campo;
        } else {
            return '';
        }

    }


    public static function after_last($caracter, $inthat)
    {
        if (!is_bool(FuncionesGenerales::strrevpos($inthat, $caracter)))
        return substr($inthat, FuncionesGenerales::strrevpos($inthat, $caracter)+strlen($caracter));
    }

    public static function strrevpos($instr, $needle)
    {
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
    }

    public static function before ($caracter, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $caracter));
    }

    public static function after($caracter, $inthat)
    {
        if (!is_bool(strpos($inthat, $caracter)))
        return substr($inthat, strpos($inthat,$caracter)+strlen($caracter));
    }

    public static function diverse_array($vector) {
        $result = array();
        foreach($vector as $key1 => $value1)
            foreach($value1 as $key2 => $value2)
                $result[$key2][$key1] = $value2;
        return $result;
    }

    public static function NuevaRutaArchivosTemporales($archivoTemporal, $name) {

        $directorioActual = getcwd();
      
        $dir_subida =  $directorioActual . '/temporal/';
        $fichero_subido = $dir_subida . basename($name);
    
       $nuevaDireccionTemporal = move_uploaded_file($archivoTemporal,  $fichero_subido);
       
       if ($nuevaDireccionTemporal) {
 
        //retorno esta url que donde se encuentra el archivo sin el file .pdf
        return $_SERVER['DOCUMENT_ROOT'] . '/appformadores/backend/public/temporal/';

       }
    }

    public static function ValidarTipoArchivo($extension) {

      $extensiones = array('jpg', 'jpeg', 'pdf', 'docx', 'doc', 'png','txt', 
      'vnd.openxmlformats-officedocument.wordprocessingml.document', 'plain');

      $resp = in_array($extension, $extensiones) ?  true : false;

       return $resp;
  
    }

    public static function ObtenerExtension($base64_string) {

        $data = explode(',', $base64_string)[0];

        $primeraExtraccion = explode('/',$data)[1];
        $extension = explode(';',$primeraExtraccion)[0];

        if($extension == 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $extension = 'docx';
        }

        if($extension == 'plain') {
            $extension = 'txt';
        }

        return $extension;

    }


    public static function base64_to_pdf($base64_string, $output_file) {

        // open the output file for writing
        $ifp = fopen($output_file, 'wb' ); 

        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string );

        fwrite($ifp, base64_decode($data[1]));
        //fwrite( $ifp, base64_decode($base64_string) );
    
        // clean up the file resource 
        fclose($ifp); 
    
        return $output_file; 
    }

    public static function SubirPdf() {

        $array_archivo = FuncionesGenerales::diverse_array($_FILES);
       
        $name = $array_archivo["name"]["cv"];
        $type = $array_archivo["type"]["cv"];
        $size = $array_archivo["size"]["cv"];
        $tipo =  FuncionesGenerales::after('/', $type);
        $tmp_name = FuncionesGenerales::NuevaRutaArchivosTemporales($array_archivo["tmp_name"]["cv"], $name);      
              
        // $url_cv = FuncionesGenerales::SubirAAmazonS3($name, $size, $tmp_name, $tipo);

        return $tmp_name;

    }

   public static function SubirAServer($name, $archivoTemporal, $id_usuario) {
        
        $dir_subida = 'https://servidordeprueba.xyz/backend/public/curriculums/'. $id_usuario . $name ;
        $fichero_subido = $dir_subida . basename($name);

        move_uploaded_file($archivoTemporal,  $fichero_subido);

   }

public static function obtener_estructura_directorios($ruta, $id_usuario){

     $arrayArchivos = [];

    // Se comprueba que realmente sea la ruta de un directorio
    if (is_dir($ruta)){
        // Abre un gestor de directorios para la ruta indicada
        $gestor = opendir($ruta);

        // Recorre todos los elementos del directorio
        while (($archivo = readdir($gestor)) !== false)  {
                
            $ruta_completa = $ruta . "/" . $archivo;

            // Se muestran todos los archivos y carpetas excepto "." y ".."
            if ($archivo != "." && $archivo != "..") {
                // Si es un directorio se recorre recursivamente
                if (is_dir($ruta_completa)) {
                     
                    self::obtener_estructura_directorios($ruta_completa, $id_usuario);
                
                } else {

                 $id_usuario_archivo = explode('_', $archivo)[0];

                 if($id_usuario == $id_usuario_archivo) {

                    array_push($arrayArchivos, $archivo); 

                 }
                 
                }
            }
        }
        
        // Cierra el gestor de directorios
        closedir($gestor);

        return $arrayArchivos;
    } else{
     
        throw new Exception("No existe la carpeta del usuario");

    }
}

    public static function rmDir_rf($carpeta)
    {
        $eliminada = false;
        foreach(glob($carpeta . "/*") as $archivos_carpeta){             
            if (is_dir($archivos_carpeta)){
            self::rmDir_rf($archivos_carpeta);
            } else {
            unlink($archivos_carpeta);
            }
        }
        $eliminada = rmdir($carpeta);

        return $eliminada;
        }
    }
