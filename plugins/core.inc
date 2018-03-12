<?php 

class tedaMas{
    /**
    * Respuesta al cliente
    * @param int $code Codigo de respuesta HTTP
    * @param String $status indica el estado de la respuesta puede ser "success" o "error"
    * @param String $message Descripcion de lo ocurrido
    */
    function response($code=200, $status="", $message="") {
        http_response_code($code);
        if( !empty($status) && !empty($message) ){
            $response = array("code"=>$code,"status" => $status ,"message"=>$message);  
            print json_encode($response,JSON_PRETTY_PRINT);    
        }            
    } 

    public function API($recurso,$id){        
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $data=$this->get_data($recurso,$id);
                if($data!=NULL){                            
                    $this->response(200,"success",utf8_encode("found"));  
                }else{
                    $this->response(200,"error",utf8_encode("not found"));  
                }
                break;

            case 'POST': 
                $data=$this->get_data("idusuario",$id);
                if($data!=NULL){   // EL usuario existe                         
                    $this->save("concurso",$auth);
                }else{ // El usuario no existe
                    $this->save("new",$auth);
                }               
                break;
            
            default://metodo NO soportado
                $this->response(200,"error",utf8_encode("Metodo no permitido."));  
                break;
        } 
    }

    /**
     * obtiene registro dado una sentencia mysql
     * @param String $recurso, tipo de recurso a consumir
     * @param String $auth, claveApi de autentificación
     * @return Data 
     */
    public function get_data($recurso,$id){
        $controlador = new controlador();
        $data=$controlador->get($recurso,$id);
        return $data;        
    }

    /**
     * enruta las operaciones de guardado
     * @param String $recurso, tipo de recurso a consumir
     * @param Int $id, cédula del concursante
     * @return Data 
     */
    public function save($recurso,$id){
        switch ($recurso) {
            case 'concurso':  // EL usuario existe             
                $this->saveCodigo($id);
                break;
            case 'new': // El usuario no existe
                $this->saveUsuario($id);          
                break;
            default:
                $this->response(200,"error",utf8_encode("El recurso solicitado no es válido "));  
                break;
        }          
        return $data;        
    }

    /**
    * metodo para guardar un nuevo usuario
    * @param Int $id, cédula del concursante aguardar
    */
    function saveUsuario($id){ 
        if ($_POST){
            
            $cedula=isset($_POST['cedula']) ? $_POST['cedula']: null;

            $mail=isset($_POST['mail']) ? $_POST['mail'] :'';
            $nombre=isset($_POST['nombre']) ? $_POST['nombre'] :'';
            $apellido=isset($_POST['apellido']) ? $_POST['apellido'] :'';
            $telefono=isset($_POST['telefono']) ? $_POST['telefono'] :'';
            $direccion=isset($_POST['direccion']) ? $_POST['direccion'] :'';

            $controlador = new controlador();
            $sql = "INSERT INTO usuarios (cedula, nombres, apellidos, telefono , direccion, correo) VALUES ('$cedula', '$nombre','$apellido','$telefono','$direccion','$mail')";
            $data=$controlador->insert($sql);
            if($data){
                $this->saveCodigo($id);
            }  
            
        }else if(isset($obj)){
            $this->response(200,"error","No se detecto información");                           
        }

    }

    /**
    * metodo para guardar códigos, para usuarios registrados
    * @param Int $id, cédula del concursante aguardar
    */
    function saveCodigo($id){ 
        if ($_POST){
            
            $cedula=isset($_POST['cedula']) ? $_POST['cedula']: null;
            $code1=isset($_POST['code1']) ? $_POST['code1']: '';
            $code2=isset($_POST['code2']) ? $_POST['code2']: '';
            $code3=isset($_POST['code3']) ? $_POST['code3']: '';
            $code4=isset($_POST['code4']) ? $_POST['code4']: '';
            $code5=isset($_POST['code5']) ? $_POST['code5']: '';

            $codigos=array($code1,$code2,$code3,$code4,$code5);
            $controlador = new controlador();
            $i=0;
            foreach ($codigos as $cod) {
                if($cod!=""){
                    $sql = "INSERT INTO codigos (id_usuario, codigo) VALUES ('$cedula', '$cod')";
                    $data=$controlador->insert($sql);
                    if($data){
                        if($i==0){
                            $this->response(200,"success","almacenado");
                        }
                        $i++;
                    }else{
                        $this->response(200,"error","no almacenado"); 
                    }
                }                   
            }
        }else if(isset($obj)){
            $this->response(200,"error","No se detecto información");                           
        }

    }

}

class controlador{
      /**
     * obtiene un solo registro dado un ID
     * @param int $id identificador unico de usuarios (cédula)
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function get($recurso,$id){ 
        $data=NULL;
        switch ($recurso) {
            case 'idusuario':
                $query="SELECT cedula FROM usuarios WHERE cedula=:cedula";
                $data=$this->select($query,array(':cedula'=>$id));              
                break;
        }  
        return $data;         
    }

    public function select($query,  array $args = array(), array $options = array()){ 
        $record=array();
        Database::setActiveConnection("tedamosmas");
            if (empty($options['target'])) {
                $options['target'] = 'default';
            }
            $data=Database::getConnection($options['target'])->query($query, $args, $options);
            foreach ($data as $value) {
                array_push($record,$value);
            }
        Database::setActiveConnection();
        return $record;
    }

    /**
     * añade un nuevo registro 
     * @param String $query sentencia sql para ingresar un nuevo registro
     * @return bool TRUE|FALSE 
     */
    public function insert($query,  array $args = array(), array $options = array()){
        Database::setActiveConnection("tedamosmas");
            if (empty($options['target'])) {
                $options['target'] = 'default';
            }
            $data=Database::getConnection($options['target'])->query($query, $args, $options);
        Database::setActiveConnection();
        if($data){
            return true;
        }else{
            return false;
        }      
    }
    
}


?>