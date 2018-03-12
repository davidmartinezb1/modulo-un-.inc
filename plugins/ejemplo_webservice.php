<?php 

/*  codigo de respuesta
    1xx – Informativo
    2xx – Éxito
    3xx – Redirección
    4xx – Error del Cliente
    5xx – Error del Servidor
*/


/*
    Ejemplo de metodos rest
    
    consultar informacion de todos usuarios: requiere claveApi (GET)
        =>  http://localhost:203/rest/avisos/listen/idusuario/3LH3r4ld0.2017

    Nuevo usuario: nombre de usuario a crear (sin espacios) , requiere claveApi de un usuario registrado (POST) - proceso para crear usuario administrador
        =>  http://localhost:203/rest/avisos/listen/usuario/3LH3r4ld0.2017

        {  	
            "name":"ejemplo",
            "claveApi":"1234567890976fdv43fdfgyt4f"
        }

    Nuevo proveedor: nombre del proveedor a crear (sin espacios) , requiere claveApi de un usuario registrado (POST)
        =>  http://localhost:203/rest/avisos/listen/proveedor/3LH3r4ld0.2017

        {  	
            "name":"sales"
        }

    consultar informacion de todos los proveedores :  requiere claveApi de un usuario 
        =>  http://localhost:203/rest/avisos/listen/idproveedor/3LH3r4ld0.2017   
        

    consultar informacion de un proveedore :  requiere claveApi del proveedor
        =>  http://localhost:203/rest/avisos/listen/idproveedor/123456789

    
        Nuevo inmuble: requiere claveApi de un proveedor y json con datos del inmueble (POST)
        => http://localhost:203/rest/avisos/listen/inmueble/0b3775d1d93ef774f89a8e1ceb66620d
        {
            "id":"3",
            "title":"prueba api rest 1", 
            "operacion":"Venta",  
        }

*/

$avisos= new avisos();
print $avisos->API($recurso,$auth);


class avisos{
    public function API($recurso,$auth){        
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                    //obtener información
                    if($auth){
                        $data=$this->get_data($recurso,$auth);
                        if($data!=NULL){                            
                            $this->response(200,"success",$data);  
                        }else{
                            $this->response(400,"error",utf8_encode("La petición no se ha completado, verifique el recurso solicitado y la claveApi suministrada "));  
                        }
                    }else{
                        $this->response(400,"error","acceso denegado, se requiere claveApi");
                    }                    
                break;
            case 'PUT':
                $data=$this->update($recurso,$auth);
                break;
            case 'POST': 
                //ingresar nueva información
                $data=$this->save($recurso,$auth);
                break;
            case 'DELETE':
                $this->response(405,"error",utf8_encode("Metodo no permitido."));
                break;
            default://metodo NO soportado
                $this->response(405,"error",utf8_encode("Metodo no permitido."));  
                break;
        }        
    }

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

    /**
     * obtiene registro dado una sentencia mysql
     * @param String $recurso, tipo de recurso a consumir
     * @param String $auth, claveApi de autentificación
     * @return Data 
     */
    public function get_data($recurso,$auth){
        $controlador = new controlador();
        $data=$controlador->get($recurso,$auth);
        return $data;        
    }

    /**
     * obtiene registro dado una sentencia mysql
     * @param String $recurso, tipo de recurso a consumir
     * @param String $auth, claveApi de autentificación
     * @return Data 
     */
    public function save($recurso,$auth){
        switch ($recurso) {
            case 'inmueble': 
                $this->saveInmueble($auth);
                break;
            case 'usuario':
                $this->saveUsuario($auth);          
                break;
            case 'proveedor':       
                $this->saveProveedor($auth);
                break;
            default:
                $this->response(400,"error",utf8_encode("acceso no autorizado, el recurso solicitado no es válido "));  
                break;
        }          
        return $data;        
    }

    /**
     * actualiza uel estado de un inmueble
     * @param String $recurso, tipo de recurso a consumir
     * @param String $auth, claveApi de autentificación
     * @return Data 
     */
    public function update($recurso,$auth){
        switch ($recurso) {
            case 'updateInmueble': 
                $this->updateInmueble($auth);
                break;
            default:
                $this->response(400,"error",utf8_encode("acceso no autorizado, el recurso solicitado no es válido "));  
                break;
        }          
        return $data;        
    }


    /**
    * metodo para guardar un nuevo inmueble
    * @param int $code Codigo de respuesta HTTP
    * @param $auth claveApi del proveedor que realizara el cargue de inmuebles
    */
    function saveInmueble($auth){ 
        if($auth){   
            $data=$this->get_data("idproveedor",$auth);
            if($data!=NULL){ 
                //Decodifica un string de JSON
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){
                    $this->response(422,"error","No se detecto información. por favor verifique la información enviada");                           
                }else if(isset($obj)){
                    $error=false;

                    /* ------- Nueva implementación ------- */
                    $nid_drupal_provedor= $data[0]->uid_drupal;
                    if($nid_drupal_provedor){
                        $inmobiliaria=get_client_b2b($nid_drupal_provedor);
                        if($inmobiliaria['status']=="0"){
                            $this->response(422,"error",$inmobiliaria['msj']);
                            $error=true;
                        }
                    }else{
                        $this->response(422,"error","El inmueble suministrado no posee un identificador que lo relacione a una inmobiliaria.");
                        $error=true;
                    }
                    /* ------- /Nueva implementación ------- */
                    
                    if($obj->departamento=="" || $obj->ciudad=="" || $obj->barrio==""){
                        $this->response(422,"error","El inmueble suministrado posee irregularidades en los datos de ubicación, verifique si este posee un departamento, ciudad o barrio asociado");
                        $error=true;
                    }else{
                        $lugar=$obj->departamento." ".$obj->ciudad." ".$obj->barrio;
                        $obj->departamento=get_ubicacion($obj->departamento);
                        $obj->ciudad=get_ubicacion($obj->ciudad,$obj->departamento);
                        $obj->barrio=get_ubicacion($obj->barrio,$obj->ciudad);
                    }                                  
                    // registrando petición del proveedor 
                    
                    $id_proveedor= $data[0]->id_proveedor;
                    
                    
                    if (!is_array(@getimagesize($obj->fotoPrincipal))) 
                    { 
                        $this->response(422,"error","El inmueble suministrado no posee, una url válida para la foto principal");
                        $error=true;
                    } 

                    if($obj->id==""){
                        $this->response(422,"error","El inmueble suministrado no posee, un id asociado");
                        $error=true;
                    }

                    if($obj->title==""){
                        $this->response(422,"error","El inmueble suministrado no posee, un titulo descriptivo");
                        $error=true;
                    }

                    if($obj->operacion!="Venta" && $obj->operacion!="Alquiler"){
                        $this->response(422,"error","El inmueble suministrado no posee, un tipo de operación válida");
                        $error=true;
                    }                   
                    
                    if(!$error){ 
                        $status=inmueble($obj,$id_proveedor,$nid_drupal_provedor,$inmobiliaria['orden'],$inmobiliaria['uid'],$lugar);                        
                        if($status['status']=="1"){
                            $url=url('node/'.$status['nid'], array('absolute' => TRUE));
                            $mensaje=array(
                                "AC"=>"AC".$status['nid'],
                                "id"=>$status['nid'],
                                "url"=> $url,
                                "estado"=>"publicado",
                                "message"=>"El inmueble ".$obj->title.", fue almacenado correctamente"
                            );
                            $this->response(200,"success",$mensaje);
                        }elseif($status['status']=="0"){
                            $this->response(400,"error","El inmueble que trata de ingresar ya se encuentra registrado, por favor verifique que las propiedades enviadas pertenezcan a un nuevo inmueble");
                        }elseif($status['status']=="2"){
                            $this->response(400,"error","El inmueble no fue almacenado, por favor verifique las propiedades enviadas");
                        }
                    }                                                                         
                }else{
                    $this->response(422,"error","Las propiedades de la petición no están definidas");
                }                 
            }else{
                $this->response(400,"error",utf8_encode("La petición no se ha completado, verifique el recurso solicitado y la claveApi ingresada"));  
            }            
        } else{               
            $this->response(400,"error","acceso denegado, se requiere claveApi");
        } 
    }

    /**
    * metodo para cambiar el estado de un inmueble
    * @param int $code Codigo de respuesta HTTP
    * @param $auth claveApi del proveedor que realizara el cargue de inmuebles
    */ 
    function updateInmueble($auth){
        if($auth){   
            $data=$this->get_data("idproveedor",$auth);
            if($data!=NULL){ 
                //Decodifica un string de JSON
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){
                    $this->response(422,"error","No se detecto información. por favor verifique la información enviada");                           
                }else if(isset($obj)){
                    $error=false;
                                        
                    $id_proveedor= $data[0]->id_proveedor;
                    

                    if($obj->id==""){
                        $this->response(422,"error","El inmueble suministrado no posee, un id asociado");
                        $error=true;
                    }

                    if($obj->status!="0" && $obj->status!="1"){
                        $this->response(422,"error","El estado suministrado no es válido");
                        $error=true;
                    }

                    
                    if(!$error){
                        $status=changeInmueble($obj,$id_proveedor);                   
                        if($status){
                            $estado=$status['status'];
                            if($estado=='1'){$estado="Publicado";}else{$estado="Despublicado";}
                            $this->response(200,"success","El inmueble con título' ".$status['title']." ', actualizó su estado a: ".$estado.", de forma correcta");
                        }else{
                            $this->response(400,"error","No fue posible cambiar el estado del inmueble, por favor verifique las propiedades enviadas");
                        }  
                    }                                                                         
                }else{
                    $this->response(422,"error","Las propiedades de la petición no están definidas");
                }                 
            }else{
                $this->response(400,"error",utf8_encode("La petición no se ha completado, verifique el recurso solicitado y la claveApi ingresada"));  
            }            
        } else{               
            $this->response(400,"error","acceso denegado, se requiere claveApi");
        } 
    }

    
    /**
    * metodo para guardar un nuevo usuario
    * @param int $code Codigo de respuesta HTTP
    * @param $auth claveApi de un usuario, el cual podrá crear nuevos usuarios
    */
    function saveUsuario($auth){ 
        if($auth){   
            $data=$this->get_data("idusuario",$auth);
            if($data!=NULL){ 
                //Decodifica un string de JSON
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){
                    $this->response(422,"error","No se detecto información. por favor verifique la información enviada");                           
                }else if(isset($obj->name)){
                    
                        $controlador = new controlador();
                        $newclaveApi =$this->generarClaveApi();
                        $sql = "INSERT INTO usuario (nombre_usuario, claveApi) VALUES ('$obj->name', '$newclaveApi')";
                        $data=$controlador->insert($sql);
                        if($data){
                            $this->response(200,"success","El usuario: ".$obj->name.", fue almacenado con exito, su claveApi es: ".$newclaveApi); 
                        }else{
                            $this->response(400,"error","El usuario: ".$obj->name.", no fue almacenado, verifique si ya no existe un usuario con esas caracteristicas"); 
                        }                                                                    
                }else{
                    $this->response(422,"error","Las propiedades de la petición no estan definidas");
                }                 
            }else{
                $this->response(400,"error",utf8_encode("La petición no se ha completado, verifique el recurso solicitado y la claveApi suministrada "));  
            }            
        } else{               
            $this->response(400,"error","acceso denegado, se requiere claveApi");
        }   
    }

    /**
    * metodo para guardar un nuevo proveedor
    * @param int $code Codigo de respuesta HTTP
    * @param $auth claveApi de un usuario, el cual podrá crear nuevos proveedores
    */
    function saveProveedor($auth){ 
         if($auth){   
            $data=$this->get_data("idusuario",$auth);
            if($data!=NULL){ 
                //Decodifica un string de JSON
                $obj = json_decode( file_get_contents('php://input') );   
                $objArr = (array)$obj;
                if (empty($objArr)){
                    $this->response(422,"error","No se detecto información. por favor verifique la información enviada");                           
                }else if(isset($obj->name)){                    
                        $controlador = new controlador();
                        $newclaveApi =$this->generarClaveApi();
                        $sql = "INSERT INTO proveedor (claveApi,nombre_proveedor, uid_drupal) VALUES ('$newclaveApi','$obj->name', '$obj->uid_drupal')";
                        $data=$controlador->insert($sql);
                        if($data){
                            $this->response(200,"success","El usuario: ".$obj->name.", fue almacenado con exito, su claveApi es: ".$newclaveApi); 
                        }else{
                            $this->response(400,"error","El usuario: ".$obj->name.", no fue almacenado, verifique si ya no existe un usuario con esas caracteristicas"); 
                        }                                                                    
                }else{
                    $this->response(422,"error","Las propiedades de la petición no estan definidas");
                }                 
            }else{
                $this->response(400,"error",utf8_encode("La petición no se ha completado, verifique el recurso solicitado y la claveApi suministrada "));  
            }            
        } else{               
            $this->response(400,"acceso denegado, se requiere claveApi");
        }   
    }

    /**
    * metodo para guardar un nuevo proveedor
    * @return token para un nuevo usuario o proveedor
    */
    function generarClaveApi()
    {
        return md5(microtime() . rand());
    }

}


class controlador{
    
    /**
     * obtiene un solo registro dado su ID
     * @param int $id identificador unico de registro
     * @return Array array con los registros obtenidos de la base de datos
     */
    public function get($recurso,$auth){ 
        $data=NULL;
        switch ($recurso) {
            case 'proveedor':
                $query="SELECT claveApi,nombre_usuario FROM usuario WHERE claveApi=:clave";
                $data=$this->select($query,array(':clave'=>$auth));        

                if($data['claveApi']){
                    $query="SELECT claveApi,nombre_proveedor FROM proveedor ";
                    $data=$this->select($query);  
                }                              
                break;
            case 'idproveedor':
                $query="SELECT id_proveedor,claveApi,nombre_proveedor,uid_drupal FROM proveedor WHERE claveApi=:clave";
                $data=$this->select($query,array(':clave'=>$auth));                
                break;
            case 'idusuario':
                $query="SELECT claveApi,nombre_usuario FROM usuario WHERE claveApi=:clave";
                $data=$this->select($query,array(':clave'=>$auth));              
                break;
            case 'logs':
                $id_proveedor=$auth['id_proveedor'];
                $id_aviso=$auth['id_aviso'];
                $query="SELECT id_inmuble_proveedor,node_id FROM logs WHERE id_inmuble_proveedor=:id_inmueble_proveedor and id_proveedor=:id_proveedor ORDER BY access ASC LIMIT 1  ";
                $data=$this->select($query,array(':id_inmueble_proveedor'=>$id_aviso,':id_proveedor'=>$id_proveedor));              
                break;
        }  
        return $data;         
    }

    public function select($query,  array $args = array(), array $options = array()){ 
        $record=array();
        Database::setActiveConnection("proveedor");
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
     * @param String $query sentencia sql para ingresar un nuevo usuario
     * @return bool TRUE|FALSE 
     */
    public function insert($query,  array $args = array(), array $options = array()){
        Database::setActiveConnection("proveedor");
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

/********************************************************************************************************************/

    /**
    * busca una ocurrencia pasada en string, retornando el tid asociado al termino pasado
    * @param $sting = Ej Barranquilla
    * @param $parent int tid del termino padre 
    * @return int TID of taxonomy search
    */
    function get_ubicacion($sting,$parent=0){    
        $result=db_query("SELECT d.tid,d.name FROM taxonomy_term_data d 
            LEFT JOIN taxonomy_term_hierarchy t ON t.tid = d.tid
            WHERE d.NAME LIKE'%$sting%' 
            AND d.vid=5 
            AND t.parent = $parent");
        foreach ($result as $record) {
            $tid=$record->tid;
        }
        return $tid;
    }

    /**
     * añade un nuevo inmueble al sitio
     * @param Object $obj coleccción de objetos con la informacion asociada a un inmueble
     * @return bool TRUE|FALSE 
     */
    function inmueble($obj,$id_proveedor, $uid_drupal_provedor,$nid_orden,$uid_orden,$lugar){
        $avisos= new avisos();
        $id=array("id_proveedor"=>$id_proveedor,"id_aviso"=>$obj->id);        
        $data= $avisos->get_data("logs",$id); // buscando en la tabla logs con el id_provedor y el id del inmueble provedor
        
        $node=node_load($data[0]->node_id);
        if($node){
                $controlador = new controlador();
                $timestamp=time();
                $logs = "INSERT INTO logs (id_proveedor, access, id_inmuble_proveedor, node_id, accion) VALUES ('$id_proveedor', '$timestamp','$obj->id','$node->nid','duplicate tried')";
                $log=$controlador->insert($logs);
                return array('status'=>0);
        }else{
            // crear inmueble
                $node = new stdClass();
                $node->title = isset($obj->title) ? $obj->title:'';
                $node->type = "inmueble";
                $node->language = "es";
                $node->field_propietario_del_inmueble["und"][0]["target_id"] =$uid_orden;
                $node->uid=$uid_orden;

               /*----------- Nueva implementación ------------------*/
                if($nid_orden){
                   $node->field_orden_a_asociar['und'][0]['target_id']=$nid_orden;
                   $orden=node_load($nid_orden);
                   $avisos_consumidos=isset($orden->field_integraciones['und'][0]['value']) ? $orden->field_integraciones['und'][0]['value'] : 0;
                   $orden->field_integraciones['und'][0]['value']=$avisos_consumidos+1;
                   node_save($orden);
                }
                // $node->field_asesor['und'][0]['target_id']="";
                
                /*----------- /Nueva implementación ------------------*/
                $node->field_fecha_expiracion['und'][0]['value']=$orden->field_fecha_expiracion['und'][0]['value'];
                $node->field_tipo_de_operacion['und'][0]['value']=isset($obj->operacion) ? $obj->operacion:'';
                $node->field_venta['und'][0]['value']=isset($obj->tipoVenta) ? $obj->tipoVenta: '';	
                $node->field_alquiler['und'][0]['value']=isset($obj->tipoArriendo) ? $obj->tipoArriendo: '';	
                $node->field_tipo_inmueble['und'][0]['tid']=isset($obj->tipInmueble) ? $obj->tipInmueble:'';

                // $node->field_asesor['und'][0]['value']=isset($obj->field_asesor) ? $obj->field_asesor:'';
                

                $estado_inmueble=isset($obj->estadoInmueble) ? $obj->estadoInmueble:'';
                if($estado_inmueble){ $node->field_estado_inmueble['und'][0]['tid']=$estado_inmueble; }
                        
                $v_venta=isset($obj->valorVenta) ?  $obj->valorVenta:'';
                if($v_venta!=""){$node->field_valor_venta['und'][0]['value']=number_format($obj->valorVenta, 0, ',', '.');}
                
                $v_arriendo=isset($obj->valorArriendo) ?  $obj->valorArriendo:'';
                if($v_arriendo!=""){ $node->field_valor_alquiler['und'][0]['value']=number_format($v_arriendo, 0, ',', '.');}      
                
                $node->field_administracion['und'][0]['value']=isset($obj->administracion) ? $obj->administracion:'No';
                
                $v_admin=isset($obj->valorAdministracion) ? number_format($obj->valorAdministracion, 0, ',', '.'):'';
                if($v_admin!=""){$node->field_valor_administracion['und'][0]['value']= number_format($v_admin, 0, ',', '.');}
                
                $node->field_lugar['und'][0]['tid']=isset($obj->barrio) ? $obj->barrio:'';
                $node->field_estrato['und'][0]['value']=isset($obj->estrato) ? $obj->estrato:'';
                $node->field_direccion['und'][0]['value']=isset($obj->direccion) ?$obj->direccion :'';
                $node->field_coordenadas_inmueble['und'][0]['value']=getCoordinatesGPS($lugar." ".$obj->direccion);
                
                $lugares=isset($obj->lugaresCercanos) ? $obj->lugaresCercanos:'';
                if($lugares!=""){$node->field_lugares_cercanos['und'][0]['tid']=$lugares;}        
                
                $node->body['und'][0]['value']=isset($obj->descripcionWeb) ? $obj->descripcionWeb:'';
                $node->field_num_habitaciones['und'][0]['value']=isset($obj->numHabitacion) ? $obj->numHabitacion:'';
                $node->field_banos['und'][0]['value']=isset($obj->numBanos) ? $obj->numBanos:'';
                $node->field_num_parqueaderos['und'][0]['value']=isset($obj->numParqueadero) ? $obj->numParqueadero:'';
            
                $node->field_area_construida['und'][0]['value']=isset($obj->areaConstruida) ? $obj->areaConstruida:'';
                $node->field_area_privada['und'][0]['value']=isset($obj->areaConstruida) ? $obj->areaConstruida:'';
                $node->field_tipo_uso['und'][0]['tid']=isset($obj->tipoUso) ? $obj->tipoUso:'';
                $node->field_muebles['und'][0]['value']=isset($obj->amoblado) ? $obj->amoblado:'No';
                $node->field_deposito_cuarto_util['und'][0]['value']=isset($obj->depositoCuartoutil) ? $obj->depositpoCuartouti:'No';
                $node->field_precio_negociable['und'][0]['value']=isset($obj->precioNegociable) ? $obj->precioNegociabl:'';
                $node->field_tiempo_construido['und'][0]['value']=isset($obj->tiempoConstruido) ? $obj->tiempoConstruido:'';
                
                $node->field_aprobar_bajada_papel['und'][0]['value']="si";

                $node->status = 1;
                $node->promote = 0;
                $node->comment = 0;
                $node = node_submit($node); 
                node_save($node);

                $node=node_load($node->nid);
                $node->title=$node->title." AC".$node->nid;
                node_save($node);

                if($obj->fotoPrincipal){
                    addImage($obj->fotoPrincipal,$node->nid,"field_foto_portada");
                }

                if($obj->fotoGaleria1){
                    addImage($obj->fotoGaleria1,$node->nid,"field_foto_1");
                }

                if($obj->fotoGaleria2){
                    addImage($obj->fotoGaleria2,$node->nid,"field_foto_2");
                }

                if($obj->fotoGaleria3){
                    addImage($obj->fotoGaleria3,$node->nid,"field_foto_3");
                }

                if($obj->fotoGaleria4){
                    addImage($obj->fotoGaleria4,$node->nid,"field_foto_4");
                }

                if($obj->fotoGaleria5){
                    addImage($obj->fotoGaleria5,$node->nid,"field_foto_5");
                }

                if($obj->fotoGaleria6){
                    addImage($obj->fotoGaleria6,$node->nid,"field_foto_6");
                }

                if($obj->fotoGaleria7){
                    addImage($obj->fotoGaleria7,$node->nid,"field_foto_7");
                }

                if($obj->fotoGaleria8){
                    addImage($obj->fotoGaleria8,$node->nid,"field_foto_8");
                }
                

                if($node->nid){
                    $timestamp=time();
                    $controlador = new controlador();
                    $logs = "INSERT INTO logs (id_proveedor, access, id_inmuble_proveedor, node_id, accion) VALUES ('$id_proveedor', '$timestamp','$obj->id','$node->nid','insert')";
                    $log=$controlador->insert($logs);
                            
                    return array('status'=>1,'nid'=>$node->nid);
                }else{
                    return array('status'=>2);
                }
            // crear inmueble
        }
    }

    /**
     * añade imagenes a un nodo
     * @param $imagepath url de la imagen
     * @param $nid identificador del nodo
     * @param $ft machine name del campo de foto
     * @return bool TRUE|FALSE 
     */
    function addImage($imagepath,$nid,$ft){
        // Create image File object and associate with Image field.
        // pendiente ajuste de datos, ver implementación de migracion pentagono para realizar los ajustes
        switch ($i) {
            case "field_foto_portada":
                $node=node_load($nid);
                $name_field=explode("/",$imagepath);
                $destino='sites/default/files/foto_portada/inmueble/'.date('Y/m/d').'/'.time().end($name_field);
                mkdir(dirname($destino), 0777, true);
                $file = copy($imagepath, $destino);
                
                $filepath = drupal_realpath($destino);
                $file = (object) array(
                'uid' => 1,
                'uri' => $filepath,
                'filemime' => file_get_mimetype($filepath),
                'status' => 1,
                );

                $file = file_copy($file, 'public://foto_portada/inmueble/'.date('Y/m/d').'/');
                chmod($destino,0777);
                $node->{$ft}['und'][0]=(array)$file;        
                node_save($node);
                break;
            default;
                $node=node_load($nid);
                $name_field=explode("/",$imagepath);
                $destino='sites/default/files/galerias/inmueble/'.date('Y/m/d').'/'.time().end($name_field);
                mkdir(dirname($destino), 0777, true);
                $file = copy($imagepath, $destino);
                
                $filepath = drupal_realpath($destino);
                $file = (object) array(
                'uid' => 1,
                'uri' => $filepath,
                'filemime' => file_get_mimetype($filepath),
                'status' => 1,
                );

                $file = file_copy($file, 'public://galerias/inmueble/'.date('Y/m/d').'/');
                chmod($destino,0777);
                $node->{$ft}['und'][0]=(array)$file;        
                node_save($node);
                break;
        }        
    }

    /**
     * cambia el estado de un nodo
     * @param Object $obj coleccción de objetos con la informacion asociada a un inmueble
     * @return bool TRUE|FALSE 
     */
    function changeInmueble($obj,$id_proveedor){      
        $avisos= new avisos();
        $id=array("id_proveedor"=>$id_proveedor,"id_aviso"=>$obj->id);
        $data= $avisos->get_data("logs",$id);
        $nid=$data[0]->node_id;        
        $node=node_load($nid);        
        if($node){ 
            $node->status=$obj->status;
            node_save($node);
            $controlador = new controlador();
            $timestamp=time();
            $logs = "INSERT INTO logs (id_proveedor, access, id_inmuble_proveedor, node_id, accion) VALUES ('$id_proveedor', '$timestamp','$obj->id','$node->nid','update')";
            $log=$controlador->insert($logs);
            $dat=array("status"=>$node->status,"title"=>$node->title);
            return $dat;
        }else{
            return FALSE;
        }       
    }


/********************************************************* B2B ************************************************************/

    /**
     * obtiene un usuario asociado a una inmobiliaria 
     * @param int $nid id de la inmobiliaria
     * @return Int uid del usuario relacionado a la inmobiliaria
     */
    function get_client_b2b($nid){ 
        $b2b=node_load($nid);
         
        if($b2b){            
            $user_id=isset($b2b->field_propietario_del_inmueble['und'][0]['target_id']) ? $b2b->field_propietario_del_inmueble['und'][0]['target_id']:false;            
            if($user_id){
                $orden = get_orden_by_cliente($user_id);
                $orden = node_load($orden['nid_orden']);               
                if($orden){
                    $activa=isset($orden->field_orden_activa['und'][0]['value']) ? $orden->field_orden_activa['und'][0]['value']:'no';
                    if($activa=="si"){
                        $cnt_maxi=isset($orden->field_numero_de_avisos['und'][0]['value']) ? $orden->field_numero_de_avisos['und'][0]['value'] : 0;
                        $cnt_integracion=isset($orden->field_integraciones['und'][0]['value']) ? $orden->field_integraciones['und'][0]['value']: 0;
                        if($cnt_maxi>$cnt_integracion){
                            return array("status"=>"1","msj"=>$orden->title,"orden"=>$orden->nid,"uid"=>$user_id);                            
                        }else{
                            return array("status"=>"0","msj"=>"La inmobiliaria ". ucfirst($b2c->title). ", contiene una orden activa, pero no cuenta con avisos disponibles para cargar mediante integración, por favor comuníquese con el aŕea comercial para la asignación de una nueva orden.");    
                        }                        
                    }else{
                        return array("status"=>"0","msj"=>"La inmobiliaria ". ucfirst($b2c->title). ", contiene una orden pero no se encuentra activa, por favor comuníquese con el aŕea comercial para la asignación de una nueva orden.");
                    }                    
                }else{
                    return array ("status"=>"0","msj"=>"No se encontró ninguna orden asociada a la inmobiliaria". ucfirst($b2c->title). ", por favor comuníquese con el aŕea comercial para la asignación de una orden.");     
                }              
            }else{
                return array ("status"=>"0","msj"=>"No pudo encontrar el usuario asociado a la inmobiliaria ". ucfirst($b2c->title). ", por favor comuníquese con el aŕea comercial para la asignación de usuario o asesor."); 
            }
        }else{
            return array ("status"=>"0","msj"=>"No pudo encontrar la inmobiliaria asociada al ID ".$nid." por favor verifique que el ID sea correcto, de lo contrario comuníquese con el equipo de soporte."); 
        }
    }

?>
