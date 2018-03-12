<?php 
    drupal_add_css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',array('type' => 'external'));
    drupal_add_js('https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js',array('type' => 'external'));
    drupal_add_js('https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js',array('type' => 'external'));
    drupal_add_js('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',array('type' => 'external'));
    drupal_add_js(drupal_get_path("module","tedaMas").'/assets/js/validator.js');
    drupal_add_css(drupal_get_path("module","tedaMas").'/assets/css/tedamas.css');
    drupal_add_css(drupal_get_path("module","tedaMas").'/assets/css/progressBar.css');
    drupal_add_js(drupal_get_path("module","tedaMas").'/assets/js/scripts.js');
?>
<main id="tedaMas" class="codigo">
    <section id="campana">
        <div id="info">
            <h2 class="title">
				<img src="sites/all/modules/custom/tedaMas/assets/image/logo.png" alt="">
			</h2>
            <div id="tu-puedes">
                <p>Tú puedes ser uno de los 2 ganadores* semanales de</p>
                <h3>$1.000.000</h3>
            </div>
            <p class="sumario">Compra EL HERALDO, guarda la primera página y con el código <br> que aparece en la parte superior derecha puedes participar. <br> Cada periódico es una oportunidad diferente de ganar.</p>
			<div class="botones">
				<a href="">¿Cómo participar?</a>
				<a href="/te-damos-mas/terminos-condiciones">Términos y condiciones</a>    
			</div>
        </div>

        <div id="form">
            <h4 class="title-registra">Registra tus códigos aquí</h4>
            <div id="ProgressWrapper">
                <div class="row bs-wizard" style="border-bottom:0;">
                        
                    <div class="col-xs-3 bs-wizard-step complete here">
                        <div class="text-center bs-wizard-stepnum">Paso 1</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Códigos</div>
                    </div>
                    
                    <div class="col-xs-3 bs-wizard-step"><!-- complete -->
                        <div class="text-center bs-wizard-stepnum">Paso 2</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Registro</div>
                    </div>

                    <div class="col-xs-3 bs-wizard-step"><!-- complete -->
                        <div class="text-center bs-wizard-stepnum">Finalizar</div>
                        <div class="progress"><div class="progress-bar"></div></div>
                        <a href="#" class="bs-wizard-dot"></a>
                        <div class="bs-wizard-info text-center">Concursar</div>
                    </div>

                </div>
            </div>

            <form id="teda-mas" autocomplete="off" name="tedaMas" > 
                
                <div id="step-1" class="step-div active">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="cedula" autocomplete="off"  title="El número de documento deberá tener mínimo 5 dígitos" onpaste="return false;" type="text" class="form-control frm" name="cedula" maxlength="15" placeholder="Cédula">
                    </div>

                    <div class="box-code">
                        <div class="input-group form-group codigo">
                            <span class="input-group-addon">Código</span>
                            <input type="text" autocomplete="off"  disabled onpaste="return false;" class="codigo form-control frm" maxlength="8" name="code[]" placeholder="xxxxxxxx">
                            <span class="glyphicon form-control-feedback"></span>
                            <a href="javascript:void(0);" class="add_button" title="Deseo agregar más códigos"><img class="btn-frm" src="/sites/all/modules/custom/tedaMas/assets/image/add-icon.png"></a>
                        </div>
                    </div>   

                    <div class="box-alert">
                        <span class="group-addon"> Su código es inválido, por favor verifíquelo</span>
                    </div>          
                </div> 
                
                <div id="step-2" class="step-div">
                    <aside>
                        <p>Por favor ingrese sus datos personales, todos los campos marcados con * son obligatorios</p>
                    </aside>
                                    
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input id="mail" autocomplete="off"  type="text" class="form-control frm" name="mail" maxlength="250" placeholder="E-mail *">
                        <span class="glyphicon form-control-feedback"></span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="nombre" autocomplete="off"  type="text" class="form-control frm person" name="nombre" maxlength="50" placeholder="Nombre (s) *">
                        <span class="glyphicon form-control-feedback"></span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="apellido" autocomplete="off"  type="text" class="form-control frm person" name="apellido" maxlength="150" placeholder="Apellidos *">
                        <span class="glyphicon form-control-feedback"></span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-phone"></i></span>
                        <input id="telefono" autocomplete="off"  type="text" class="form-control frm person" name="telefono" maxlength="15" placeholder="Teléfono ó Celular *">
                        <span class="glyphicon form-control-feedback"></span>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                        <input id="direccion" autocomplete="off"  type="text" class="form-control frm person" name="direccion" maxlength="115" placeholder="Dirección *">
                        <span class="glyphicon form-control-feedback"></span>
                    </div>

                    <aside>
                        <span class="inf">Para participar usted deberá aceptar:</span>
                        
                        <div class="input-group terminos terminos">
                            <input type="checkbox" name="terminos" id="terminos">
                            <span class=""><a href="/te-damos-mas/terminos-condiciones">Términos y condiciones</a></span>
                        </div>

                        <div class="input-group habeas">
                            <input type="checkbox" name="habeas" id="habeas">
                            <span class=""><a href="/politicas-de-tratamiento-de-datos-personales">Políticas de protección de datos personales</a></span>                    
                        </div>                    
                    </aside>

                
                </div>


                <!--<div id="step-3" class="step-div">
                    <p>Estas a un paso de aumentar tus posiblidades de ser el ganador, recuerda en el Heraldo te damos más</p>
                </div>  -->

                <div id="step-3" class="step-div">
                    <aside>
                        <p>Gracias por participar, <span id="uid_send"></span></p> 
                        <div>
                            <span>Estos son los códigos que registraste</span>
                            <ul id="code_send"></ul>
                        </div>                       
                        <p>Sigue ingresando más códigos y aumentarás tus posiblidades de ser el ganador</p>
                    </aside>
                    
                    
                    <a id="volver-concurso" href="/te-damos-mas">Registra más códigos</a>
                </div> 
                
                <!-- acciones-->
                <div class="input-group form-group btn next">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-saved"></i></span>
                    <button disabled id="next-step" data-step="2" class="codigo form-control">Siguiente Paso</button>    
                </div> 
                
                <div class="input-group form-group btn send">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-send"></i></span>
                    <input id="concursar" type="submit" class="codigo form-control"  value="Concursar">                
                </div>
        
            </form>

        </div>

    </section>
    
    <section id="mecanica">
        <div id="paso">
            <h4>Mecánica del concurso</h4>
            <ul>
                <li>
                    <i>1</i>
                    <span>Compra tuperiódico<br>EL HERALDO todos los días.</span>
                </li>
                <li>
                    <i>2</i>
                    <span>Registra <b>aquí</b> el código<br>que aparece en la portada.</span>
                </li>
                <li>
                    <i>3</i>
                    <span>Todos los viernes<br>sortearemos dos ganadores*.</span>
                </li>
            </ul>
        </div>

        <div id="voceador">
            <img src="sites/all/modules/custom/tedaMas/assets/image/voceador.png" alt="">
        </div>
    </section>
</main>