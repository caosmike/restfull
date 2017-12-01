<?php
require_once "calificacionDB.php";

class calificacionAPI {    
    public function API(){
        header('Content-Type: application/JSON');                
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
        case 'GET'://consulta
            $this->getCalificaciones();
            break;     
        case 'POST'://inserta
            $this->saveCalificacion();
            break;                
        case 'PUT'://actualiza
            $this->updateCalificacion();
            break;      
        case 'DELETE'://elimina
            $this->deleteCalificacion();
            break;
        default://metodo NO soportado
            echo 'METODO NO SOPORTADO';
            break;
        }
    }
    
    /**
     * Respuesta al cliente
     * @param int $code: Codigo de respuesta HTTP
     * @param String $status: indica el estado de la respuesta puede ser "success" o "error"
     * @param String $msg: Descripcion de lo ocurrido
     */
    function response($code=200, $status="", $message="") {
        http_response_code($code);
        if( !empty($status) && !empty($message) ){
            $response = array("success" => $status ,"msg"=>$message);  
            echo json_encode($response,JSON_PRETTY_PRINT);    
        }            
    }

    /**
     * Metodo para guardar nuevo registro a la tabla t_calificaciones
     */
    function saveCalificacion(){
        if( isset($_GET['action']) && isset($_GET['idA']) && isset($_GET['idM']) ){
            if($_GET['action']=='calificaciones'){   
                //Decodifica un string de JSON
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){
                    $this->response(422,"error","Nada para agragar. Checar json");                           
                }else if(isset($obj->calificacion)){
                    $calificacion = new calificacionDB();
                    if($calificacion->insert( $_GET['idA'], $_GET['idM'], $obj->calificacion )){
                        $this->response(200,"ok","calificacion registrada");
                    }else{
                        $this->response(422,"error","Alumno no vallido");
                    }                             
                }else{
                    $this->response(422,"error","Campo no definido");
                }
            } else{               
                $this->response(400);
            }
        }  
    }

    /**
    * Metodo para mostrar las calificaciones del id_alumno:
    */
    function getCalificaciones(){ 
        if($_GET['action']=='calificaciones'){         
            $calificaciones = new calificacionDB();
            if(isset($_GET['idA'])){//muestra las calificaciones si existe el id                 
                $response = $calificaciones->getCalificaciones($_GET['idA']);                
                echo json_encode($response,JSON_PRETTY_PRINT);
            }else{ //id null                   
                $this->response(400);              
            }
        }else{
               $this->response(400);
        }       
    }

    /**
     * Metodo para actualizar calificacion
     */
    function updateCalificacion() {
        if( isset($_GET['action']) && isset($_GET['idA'])  && isset($_GET['idM']) ){
            if($_GET['action']=='calificaciones'){
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){                        
                    $this->response(422,"error","Nada para actualizar. Checar json");                        
                }else if(isset($obj->calificacion)){
                    $calificaciones = new calificacionDB();
                    if($calificaciones->updateCalificacion($_GET['idA'], $_GET['idM'], $obj->calificacion)){
                        $this->response(200,"ok","calificacion actualizada");
                    }else{
                        $this->response(200,"ok","No hay datos para actualizar");
                    }                             
                }else{
                    $this->response(422,"error","Campo no definido");                        
                }     
                exit;
           }
        }
        $this->response(400);
    }

    /**
     * Metodo para borrar calificacion
     */
    function deleteCalificacion(){
        if( isset($_GET['action']) && isset($_GET['idA'])  && isset($_GET['idM']) ){
            if($_GET['action']=='calificaciones'){                   
                $calificaciones = new CalificacionDB();
                $calificaciones->delete($_GET['idA'], $_GET['idM'] );
                $this->response(200,"ok","calificacion eliminada");                   
                exit;
            }
        }
        $this->response(400);
    }
     

}//fin class
?>