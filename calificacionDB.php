<?php
class calificacionDB {
    
    protected $mysqli;
    const LOCALHOST = '127.0.0.1';
    const USER = 'escuela';
    const PASSWORD = 'escuela';
    const DATABASE = 'escuela';
    
    /**
     * Constructor de clase
     */
    public function __construct() {           
        try{
            //conexión a base de datos
            $this->mysqli = new mysqli(self::LOCALHOST, self::USER, self::PASSWORD, self::DATABASE);
        }catch (mysqli_sql_exception $e){
            //Si no se puede realizar la conexión
            http_response_code(500);
            exit;
        }     
    } 
    
    /**
     * Añade nuevo registro en la tabla t_calificaciones
     * @param int $id_alumno Identificador del alumno
     * @param int $id_materia Identificador de la materia
     * @param float $calificacion calificacion
     * @return bool TRUE|FALSE 
     */
    public function insert($id_alumno, $id_materia, $calificacion=''){
        $registro = date('Y-m-d');
        $stmt = $this->mysqli->prepare("INSERT INTO t_calificaciones VALUES (default, ?, ?, ?, '$registro'); ");
        $stmt->bind_param('iid', $id_materia, $id_alumno, $calificacion);
        $r = $stmt->execute();
        $stmt->close();
        return $r;        
    }

    /**
     * Obtiene las calificaciones del id_alumno
     * @param int $id identificador del alumno
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function getCalificaciones($id=0){
        $calificaciones = ["success"=>"ok","msg"=>"No hay datos"];      
        $stmt = $this->mysqli->prepare("SELECT id_t_calificaciones, id_t_materias, id_t_usuarios, calificacion, DATE_FORMAT(fecha_registro,'%d-%m-%Y') fecha_registro FROM t_calificaciones WHERE id_t_usuarios=? ; ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0){        
            $calificaciones = $result->fetch_all(MYSQLI_ASSOC); 
    
            $stmt = $this->mysqli->prepare("SELECT AVG(calificacion) promedio FROM t_calificaciones WHERE id_t_usuarios=? ; ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $calificaciones[] = $result->fetch_all(MYSQLI_ASSOC); 
        }
        $stmt->close();
        return $calificaciones;              
    }
    
    /**
     * Actualiza calificacion de id_alumno en id_materia
     * @param int $id identificador del alumno
     * @param int $id_materia identificador de la materia
     * @param float $calificacion calificacion
     */
    public function updateCalificacion($id_alumno, $id_materia, $calificacion='') {
        if($this->checkID($id_alumno, $id_materia)){
            $stmt = $this->mysqli->prepare("UPDATE t_calificaciones SET calificacion= ? WHERE id_t_usuarios = ? AND id_t_materias = ? ; ");
            $stmt->bind_param('dii', $calificacion,$id_alumno,$id_materia);
            $r = $stmt->execute(); 
            $stmt->close();
            return $r;
        }    
        return false;
    }
    
    /**
     * Verifica si existe registro con id_alumno y id_materia en t_calificaciones 
     * @param int $id_alumno Identificador de alumno  
     * @param int $id_materia Identificador de la materia
     * @return Bool TRUE|FALSE
     */
    public function checkID($id_alumno, $id_materia){
        $stmt = $this->mysqli->prepare("SELECT * FROM t_calificaciones WHERE id_t_usuarios = ? AND id_t_materias = ?");
        $stmt->bind_param("ii", $id_alumno, $id_materia);
        if($stmt->execute()){
            $stmt->store_result();    
            if ($stmt->num_rows > 0){                
                return true;
            }
        }        
        return false;
    }
    
     /**
     * Elimina registro de la tabla t_calificaciones para id_alumno en id_materia
     * @param int $id_alumno Identificador del alumno
     * @param int $id_materia Identificador de la materia
     * @return Bool TRUE|FALSE
     */
    public function delete($id_alumno=0, $id_materia=0) {
        $stmt = $this->mysqli->prepare("DELETE FROM t_calificaciones WHERE id_t_usuarios = ? AND id_t_materias = ? ; ");
        $stmt->bind_param('ii', $id_alumno, $id_materia);
        $r = $stmt->execute(); 
        $stmt->close();
        return $r;
    }
}//fin class
?>