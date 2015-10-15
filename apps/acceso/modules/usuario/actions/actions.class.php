<?php

require_once dirname(__FILE__).'/../lib/usuarioGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/usuarioGeneratorHelper.class.php';

/**
 * usuario actions.
 *
 * @package    sigla-(institution)
 * @subpackage xxxxx
 * @author     Livio López. liviolopez@gmail.com. (058)426-511.42.50. Venezuela-Caracas
 * @version    0.1 $
 */
class usuarioActions extends autoUsuarioActions
{
  public function executeIndex(sfWebRequest $request)
  {
        // ########### LIMPIESA DE SESSIONES Y DATOS DE LOGEO ############
        $this->getUser()->setAuthenticated(false);
        $this->getUser()->clearCredentials();
  }
  
  public function executeResetToken(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('session_count_token', 0);
    exit();
  }
  
  public function executeUpdateToken(sfWebRequest $request)
  {
    echo "<script>$('#token_update').html('token');</script>";
    
    $usuario = Doctrine::getTable('Acceso_Usuario')->find($this->getUser()->getAttribute('usuario_id'));
    $usuario->setUltimoStatus(date('Y-m-d h:i:s'));
    $usuario->save();

    $this->getUser()->setAttribute('session_count_token', $this->getUser()->getAttribute('session_count_token')+1);

    $session_expira = $this->getUser()->getAttribute('sf_session_expira')/2;
    
    if($this->getUser()->getAttribute('session_count_token')>=$session_expira){
        echo "<script>location.href='".sfConfig::get('sf_app_acceso_url')."usuario/expiraSession';</script>";
        exit();
    }
    
    $ultimo_intento = $session_expira-1;
    if($this->getUser()->getAttribute('session_count_token')==$ultimo_intento){
        echo "<script>
                $('#expira_minutos').html('1');
                $('#expira_segundos').html('59');
              </script>";
    }
        
    exit();
  }
  
  public function executeExpiraSession(sfWebRequest $request)
  {
        // ########### LIMPIESA DE SESSIONES Y DATOS DE LOGEO ############
        $this->getUser()->setAuthenticated(false);
        $this->getUser()->clearCredentials();
        $this->getUser()->getAttributeHolder()->clear();
  }
  
  public function executeCambiarTiempoSession(sfWebRequest $request){
        $this->getUser()->setAttribute('session_count_token', 0);
        $this->getUser()->setAttribute('sf_session_expira', $request->getParameter('minutos'));
        
        $usuario = Doctrine::getTable('Acceso_Usuario')->find($this->getUser()->getAttribute('usuario_id'));
        
        $variables_entorno = sfYaml::load($usuario->getVariablesEntorno());
        $variables_entorno['tiempo_expira_session'] = $request->getParameter('minutos');
        $variables_entorno = sfYAML::dump($variables_entorno);
        
        $usuario->setVariablesEntorno($variables_entorno);
        $usuario->save();
        
        exit();
  }

  public function executeNew(sfWebRequest $request)
  {
        $this->getUser()->getAttributeHolder()->clear();
        $this->getUser()->setFlash('error', 'Parece que se ha intentado violar el sistema. Se ha registrado este evento.');
        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
  }

  public function executeOlvidoClave(sfWebRequest $request)
  {
  }

  public function executePrimerIngreso(sfWebRequest $request)
  {
  }

  public function executeFindUser(sfWebRequest $request)
  {
      $cedula = $request->getParameter('cedula');
      $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($cedula);

      if(count($funcionario) > 1) {
            $this->funcionario = $funcionario;
      } else {
            exit;
      }
  }

  public function executeTercerPaso(sfWebRequest $request)
  {
      $cedula = $request->getParameter('cedula');
      $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($cedula);

      $this->usuario_clavetemporal = Doctrine::getTable('Acceso_Usuario')->findOneByUsuarioEnlaceIdAndEnlaceId($funcionario->getId(),1);
      $this->funcionario= $funcionario;
  }

  public function executeChangeEmailorTelf(sfWebRequest $request) {
      $act = $request->getParameter('act');
      $cedula = $request->getParameter('cedula');
      $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($cedula);
      if($act== 'email') {
            $email = trim($request->getParameter('email'));
            $funcionario->setEmailPersonal($email);
            $funcionario->setIdUpdate('999999');
            $funcionario->save();
            echo $email;
            exit;
      }else {
            $telf = $request->getParameter('telf');
            $funcionario->setTelfMovil($telf);
            $funcionario->setIdUpdate('999999');
            $funcionario->save();
            exit;
      }

  }

  public function executeEnviarCodigoValidador(sfWebRequest $request)
  {
        $email_escrito= trim($request->getParameter('email'));
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($this->getUser()->getAttribute('funcionario_id'));

        if($funcionario->getEmailPersonal()==$email_escrito) {
            $chars = "abcdefghijkmnopqrstuvwxyz023456789";
            srand((double)microtime()*1000000);
            $i = 0;
            $temporal = '';

            while ($i <= 7) {
                $num = rand() % 33;
                $tmp = substr($chars, $num, 1);
                $temporal = $temporal . $tmp;
                $i++;
            }

            $codigo_crypt = crypt($temporal,$funcionario->getCi());

            $funcionario->setCodigoValidadorEmail($codigo_crypt);
            $funcionario->save();

            $mensaje['mensaje'] = 'Gracias por validar tu correo electrónico.<br/><br/>'.
                    'Ingresa el siguiente código en el campo "Código validador"<br/><br/>'.
                    '<b>Codigo:</b> '.$temporal;

            $mensaje['emisor'] = 'Validador de email SIGLAS';
            $mensaje['receptor'] = $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido();

            Email::notificacion_libre('validacion', $funcionario->getEmailPersonal(), $mensaje);

//            echo '<script>alert("En su correo electrónico encontrará el Codigo Validador para ingresarlo y poder continuar.");</script>';
        } else {
//            echo '<script>alert("El correo electrónico fue modificado, por tanto no podra ejecutar esta acción, por favor ingrese su correo electrónico e intentelo de nuevo.");</script>';
        }

        exit();
  }

  public function executeConfirmarCodigoValidador(sfWebRequest $request)
  {
        $email_escrito= trim($request->getParameter('email'));
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->find($this->getUser()->getAttribute('funcionario_id'));

        $codigo_crypt = crypt(trim($request->getParameter('codigo_validador')),$funcionario->getCi());

        if($funcionario->getCodigoValidadorEmail()==$codigo_crypt) {
            $funcionario->setEmailValidado(TRUE);
            $funcionario->save();

            $this->getUser()->setFlash('notice', ' Gracias por validar tu correo electrónico.');
        } else {
            $this->getUser()->setFlash('error_validacion', ' El código validador no corresponde con el enviado a su correo electrónico.');
        }

        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
  }

  public function executeClaveTemporal(sfWebRequest $request)
  {
        $app= 'contraseña';
        if($request->getParameter('act')) {
            $firstime= true;
            $envio_sms= $request->getParameter('celphone');
            $app= 'bienvenido';
        }else {
            $firstime= false;
        }

        $cedula = $request->getParameter('cedula');
        $funcionario = Doctrine::getTable('Funcionarios_Funcionario')->findOneByCi($cedula);

        if($funcionario)
        {
            if($funcionario->getEmailInstitucional()!=null || $funcionario->getEmailPersonal()!=null || $funcionario->getTelfMovil()!=null)
            {
                $usuario_clavetemporal = Doctrine::getTable('Acceso_Usuario')->findOneByUsuarioEnlaceIdAndEnlaceId($funcionario->getId(),1);

                $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                srand((double)microtime()*1000000);
                $i = 0;
                $temporal = '';

                while ($i <= 7) {
                    $num = rand() % 33;
                    $tmp = substr($chars, $num, 1);
                    $temporal = $temporal . $tmp;
                    $i++;
                }

                $temporal_crypt = crypt($temporal,$usuario_clavetemporal->getNombre());

                $this->getUser()->setAttribute('usuario_id', 0);
                $usuario_clavetemporal->setClaveTemporal($temporal_crypt);
                $usuario_clavetemporal->save();
                $this->getUser()->getAttributeHolder()->remove('usuario_id');

                if($firstime == false) {
                    $mensaje['mensaje'] = 'Se ha generado una contraseña temporal automaticamente.<br/><br/>'.
                            'Para ingresar al SIGLAS introduzca los siguientes datos: <br/><br/>'.
                            '<b>Usuario:</b> '.$usuario_clavetemporal->getNombre().'<br/>'.
                            '<b>Contraseña:</b> '.$temporal;

                    $mensaje['emisor'] = 'Recuperación de contraseña';
                    $mensaje['receptor'] = $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido();

                    $recipiente = '';
                    if ($funcionario->getEmailInstitucional()!=null)
                    {
                        Email::notificacion_libre($app, $funcionario->getEmailInstitucional(), $mensaje);
                        $recipiente = 'correo electronico institucional';
                    }
                }else {
                    $mensaje['mensaje'] = 'Bienvenid@ al SIGLAS. Al ingresar con la contraseña temporal el sistema le pedir&aacute; cambiarla por una propia.<br/><br/>'.
                            'Para ingresar al SIGLAS introduzca los siguientes datos: <br/><br/>'.
                            '<b>Usuario:</b> '.$usuario_clavetemporal->getNombre().'<br/>'.
                            '<b>Contraseña:</b> '.$temporal;

                    $mensaje['emisor'] = 'Bienvenido al SIGLAS';
                    $mensaje['receptor'] = $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido();
                }

                if ($funcionario->getEmailPersonal()!=null)
                {
                    Email::notificacion_libre($app, $funcionario->getEmailPersonal(), $mensaje);
                    if($recipiente=='')
                        $recipiente = 'correo electronico personal';
                    elseif ($funcionario->getTelfMovil()!=null)
                        $recipiente .= ', su correo electronico personal';
                    else
                        $recipiente .= ' y su correo electronico personal.';
                }

                if($firstime == false || $envio_sms== 'on') {
                    if ($funcionario->getTelfMovil()!=null)
                    {
                        $mensaje['mensaje'] = 'usuario: '.$usuario_clavetemporal->getNombre().' - contrasena temporal: '.$temporal;

                        Sms::notificacion_sistema($app, $funcionario->getTelfMovil(), $mensaje);
                        if($recipiente=='')
                            $recipiente = 'telefono movil.';
                        else
                            $recipiente .= ' y su telefono movil.';
                    }
                }

                if($firstime == false)
                    $this->getUser()->setFlash('notice', ' Se ha enviado la clave temporal a su '.$recipiente);
                else
                    $this->getUser()->setFlash('notice', ' En su correo electrónico encontrará Usuario y Clave temporal para ingresar.');
            } else {
                $this->getUser()->setFlash('error', ' La cédula no tiene ningún correo electronico o telefono movil asociado, por lo cual no podra recuperar la contraseña de esta manera, por favor comuniquese con la Oficina de Tecnologia.');
            }
        } else {
            $this->getUser()->setFlash('error', ' La cédula que ingreso no se encuentra registrada en el sistema.');
        }

        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
  }

  public function executeCambioClave(sfWebRequest $request)
  {
      $this->usuario = $request->getParameter('user');

      // la variable nuevo viene del template cambioClave
      if($request->getParameter('nuevo'))
      {
          $actual = strtolower(trim($request->getParameter('actual')));
          $nuevo = strtolower(trim($request->getParameter('nuevo')));
          $repite = strtolower(trim($request->getParameter('repite')));

          $abc = 'abcdefghijklmnñopqrstuvwxyz012345678909876543210';

          if($nuevo != $repite)
          {
              $this->getUser()->setFlash('error', ' Error en la contraseña nueva y su repetición');
          }
          elseif (strlen($nuevo) < 6) // CLAVE MAYOR A 5 CARACTERES
          {
              $this->getUser()->setFlash('error', ' La contraseña nueva tiene que tener al menos 6 caracteres');
          }
          elseif(preg_match('/'.$nuevo.'/', $abc)) // CLAVE NO PUEDE SER CADENAS 123456 ABCDEF
          {
              $this->getUser()->setFlash('error', ' La contraseña nueva no puede ser cadenas como "abcdef" o "123456"');
          }
          else
          {
              $usuario = $request->getParameter('user');
              $actual_tmp = $actual;

              $actual = crypt($actual_tmp,$usuario);
              $usuario_buscar = Doctrine::getTable('Acceso_Usuario')->findOneByNombreAndClave($usuario,$actual);

              if(!$usuario_buscar)
              {
                  $usuario_buscar = Doctrine::getTable('Acceso_Usuario')->findOneByNombreAndClaveTemporal($usuario,$actual);
              }

              if($usuario_buscar)
              {
                  $nuevo = crypt($nuevo,$usuario);

		    $conn = Doctrine_Manager::connection();
		    try
		    {
                        $conn->beginTransaction();

    			$usuario_cambioclave = Doctrine::getTable('Acceso_Usuario')->find($usuario_buscar->getId());

    			$usuario_cambioclave -> setClave($nuevo);
                        $usuario_cambioclave -> setStatus('A');
                        $usuario_cambioclave -> setVisitas($usuario_cambioclave -> get("visitas") + 1);
                        $usuario_cambioclave -> setUltimocambioclave(date('Y-m-d H:i:s'));
                        $usuario_cambioclave -> setClaveTemporal(null);

                        if($this->getUser()->getAttribute('usuario_id'))
                            $usuario_cambioclave -> setIdUpdate($this->getUser()->getAttribute('usuario_id'));
                        else
                            $usuario_cambioclave -> setIdUpdate($usuario_buscar->getId());

                        $usuario_cambioclave -> save();

                        $conn->commit();

                        if($this->getUser()->getAttribute('usuario_id')) {
                            $this->getUser()->setFlash('notice', ' El cambio se a efectuado correctamente. La proxima vez que ingrese al sistema introduzca su nueva contraseña');
                            $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');

                        } else {
                            $this->getUser()->setFlash('notice', ' El cambio se a efectuado correctamente, por favor ingrese al sistema con su nueva contraseña');

                            $this->getUser()->setAuthenticated(false);
                            $this->getUser()->clearCredentials();
                            $this->getUser()->getAttributeHolder()->clear();

                            $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
                        }
		    }
		    catch (Exception $e){
		        $conn->rollBack();
		        throw $e;

                        $this->getUser()->setFlash('error', ' El cambio de contraseña no se pudo efectuar, por favor intente nuevamente o comuniquese a la Direccion de Informática');
		    }
              }
              else
              {
                  $this->getUser()->setFlash('error', ' Error en el usuario o contraseña actual');
              }
          }
      }
  }

  public function executeSession(sfWebRequest $request)
  {
        // ########### DATOS PARA PANTALLA INICIAL ############
        if($this->getUser()->hasAttribute('session_usuario')) {
            // ########### PERSONAS ############
            $this->datospersona_list = Doctrine::getTable('Personas_Persona')->find($this->getUser()->getAttribute('persona_id'));
        } else {
            // ########### ERROR NO TIENE ENLACE A TABLAS DE PERSONA ############
            $this->getUser()->getAttributeHolder()->clear();
            $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
        }
  }

  public function executeSalir()
  {
        // ########### LIMPIESA DE SESSIONES Y DATOS DE LOGEO ############
        $this->getUser()->setAuthenticated(false);
        $this->getUser()->clearCredentials();
        $this->getUser()->getAttributeHolder()->clear();

        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
  }

  public function executeLogin(sfWebRequest $request)
  {
        // ########### ADMIN MODULE ############
        $correspondencia= TRUE;
        $archivo= TRUE;
        $rrhh= TRUE;
        $inventario= FALSE;
        $vehiculo= TRUE;
        
        // ########### LIMPIESA DE SESSIONES Y DATOS DE LOGEO ############
        $this->getUser()->setAuthenticated(false);
        $this->getUser()->clearCredentials();
        $this->getUser()->getAttributeHolder()->clear();
        
        // ########### CAPTURAR IP, PUERTA Y PC ##############
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
            $puerta=$_SERVER["REMOTE_ADDR"];
            $pc = gethostbyaddr($ip);
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
            $pc = gethostbyaddr($ip);
            $puerta='NINGUNA';
        }
        
        // ########### VERIFICAR IP VALIDA DE ACCESO ##############
        $rangos_ip = explode(';',sfConfig::get('sf_rangos_ip'));
        $rangos_ip[] = '127.0.0.1';
        
        $ip_i = 'i'.$ip;
        $ip_valida = FALSE;
        foreach ($rangos_ip as $rango_ip) {
            $rango_ip_i = 'i'.$rango_ip;
            if(preg_match('/'.$rango_ip_i.'/', $ip_i)){
               $ip_valida = TRUE; 
            }
        }
        
        // ########### PARAMETROS DEL FORMULARIO ############
        $usuario=strtolower(trim($request->getParameter('usuario')));
        $contrasena_tmp=strtolower(trim($request->getParameter('contrasena')));
        $contrasena_ldap = trim($request->getParameter('contrasena'));

        // ########### ENCRIPTAMIENTO DE LA CONTRASEÑA PARA LA BUSQUEDA EN TABLA ############
        
        $sf_autenticacion = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/autenticacion.yml");
        
        // ########### AUTENTICACION INTERNA ############
        $usuario_session = '';
        $metodo_ingreso = '';
        if($sf_autenticacion['metodo']=='incret' || $sf_autenticacion['metodo']=='ambos'){
            $contrasena = crypt($contrasena_tmp,$usuario);
            $usuario_session = Doctrine::getTable('Acceso_Usuario')->findOneByNombreAndClave($usuario,$contrasena);
            $metodo_ingreso = 'incret';
        }

        // ########### SI EL USUARIO FUE ENCONTRADO ############
        if($usuario_session)
        {
            // ########### VERIFICACION DEL ULTIMO CAMBIO DE CLAVE (MAXIMO 180 DIAS) ############
            $dias_ultimocambio = floor((time() - strtotime($usuario_session->getUltimocambioclave())) / 86400 );
            
            if ($usuario_session->getStatus() == 'I') {
                // ########### USUARIO INACTIVO ############
                $this->getUser()->setFlash('error', 'El usuario se encuentra inactivo o no tiene un perfil asociado.');
                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
            } elseif ($usuario_session->getStatus() == 'R' &&  $metodo_ingreso != 'ldap') {
                $this->getUser()->setAttribute('usuario_id', $usuario_session->getId());
                // ########### REINICIO DE CLAVE ############
                $this->getUser()->setFlash('notice', 'La contraseña se ha reiniciado. Para comenzar el uso del sistema es necesario crear una contraseña nueva.');
                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/cambioClave?user='.$usuario_session->getNombre());
            } elseif ($usuario_session->getVisitas() == 0  &&  $metodo_ingreso != 'ldap') {
                $this->getUser()->setAttribute('usuario_id', $usuario_session->getId());
                // ########### PEDIR CAMBIO DE CLAVE SI ES PRIMERA VES QUE INGRESA ############
                $this->getUser()->setFlash('notice', 'Bienvenido al SADI-'.sfConfig::get('sf_siglas').', por motivos de seguridad, resguardo de sus datos y para comenzar el uso del sistema es necesario crear una contraseña nueva.');
                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/cambioClave?user='.$usuario_session->getNombre());
            } else {
                // ########### SI TODO ESTA BIEN BUSCAR LOS PERFILES DEL USUARIO ############

                $liga_actual= Doctrine::getTable('Operaciones_Liga')->ligaActual();
                $this->getUser()->setAttribute('liga_actual', $liga_actual[0]->getLiga());
                
                $usuario_perfiles = Doctrine::getTable('Acceso_UsuarioPerfil')->buscarPerfiles($usuario_session->getId());

                if(count($usuario_perfiles)>0)
                {
                    // ########### SI TIENE PERFILES AGREGAR LOS NOMBRES COMO CREDENCIALES PARA EL ACCESO ############
                    // ########### Y GENERAR UNA CADENA CON LOS ID Y AGREGARLOS COMO SESSION PARA EL MENU ############

                    $perfiles = count($usuario_perfiles);

                    $perfiles_ids = array();

                    for($i=0;$perfiles>$i;$i++)
                    {
                        $perfiles_ids[$i] = $usuario_perfiles[$i]['id'];

                        $this->getUser()->addCredential($usuario_perfiles[$i]['nombre']);
                    }

                    // ########### SI TODO ESTA BIEN CREAR DATOS DE LOGEO ############

                    $this->getUser()->setAuthenticated(true);
                    $this->getUser()->setCulture('es');
                    
                    // CONTADOR DE CANTIDAD DE ACTUALIZACIONES DE ULTIMO STATUS
                    $this->getUser()->setAttribute('session_count_token', 0);

                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE USUARIO
                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE USUARIO
                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE USUARIO
                    $this->getUser()->setAttribute('usuario_id', $usuario_session->getId());
                    
                    $session_usuario['usuario_nombre'] = $usuario_session->getNombre();
                    $session_usuario['visitas'] = $usuario_session->getVisitas();
                    $session_usuario['ultima_conexion'] = $usuario_session->getUltimaconexion();
                    $session_usuario['tema'] = $usuario_session->getTema();
                    
                    $this->getUser()->setAttribute('session_usuario', $session_usuario);
                    
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE USUARIO
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE USUARIO
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE USUARIO

                    // ########### BUSCAR DATOS DE PERSONA ############

                    $funcionario_persona = Doctrine::getTable('Personas_Persona')->find($usuario_session->getPersonaId());
                    
                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE PERSONA
                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE PERSONA
                    // INICIO NUEVO FORMATO DE SESSION DE DATOS DE PERSONA
                    $this->getUser()->setAttribute('persona_id', $funcionario_persona->getId());

                    $session_persona['cedula'] = $funcionario_persona->getCi();
                    $session_persona['primer_nombre'] = $funcionario_persona->getPrimerNombre();
                    $session_persona['segundo_nombre'] = $funcionario_persona->getSegundoNombre();
                    $session_persona['primer_apellido'] = $funcionario_persona->getPrimerApellido();
                    $session_persona['segundo_apellido'] = $funcionario_persona->getSegundoApellido();
                    $session_persona['sexo'] = $funcionario_persona->getSexo();

                    $this->getUser()->setAttribute('session_persona', $session_persona);
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE PERSONA
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE PERSONA
                    // FIN NUEVO FORMATO DE SESSION DE DATOS DE PERSONA

                    // ########### INCREMENTAR EL NUMERO DE VISITAS Y LA FECHA DE LA UNTIMA VISITA ############

                    $usuario_registrarvisita = Doctrine::getTable('Acceso_Usuario')->find($usuario_session->getId());

                    $usuario_registrarvisita->setVisitas($usuario_registrarvisita->getVisitas() + 1);
                    $usuario_registrarvisita->setUltimaconexion(date('Y-m-d H:i:s'));
                    $usuario_registrarvisita->setClaveTemporal(null);
                    $usuario_registrarvisita->setIp($ip);
                    $usuario_registrarvisita->setPuerta($puerta);
                    $usuario_registrarvisita->setPc($pc);

                    $agente = $_SERVER["HTTP_USER_AGENT"];

                    // Detección del Sistema Operativo
                    $so = "Otro";
                    if(preg_match("/Win/i", $agente))$so = "Windows";
                    elseif((preg_match("/Mac/i", $agente)) || (preg_match("/PPC/i", $agente))) $so = "Mac";
                    elseif(preg_match("/Linux/i", $agente))$so = "Linux";
                    elseif(preg_match("/FreeBSD/i", $agente))$so = "FreeBSD";
                    elseif(preg_match("/SunOS/i", $agente))$so = "SunOS";
                    elseif(preg_match("/IRIX/i", $agente))$so = "IRIX";
                    elseif(preg_match("/BeOS/i", $agente))$so = "BeOS";
                    elseif(preg_match("/OS\/2/i", $agente))$so = "OS/2";
                    elseif(preg_match("/AIX/i", $agente))$so = "AIX";

                    $usuario_registrarvisita->setSo($so);
                    $usuario_registrarvisita->setAgente($agente);
                    
                    $usuario_registrarvisita->save();
                    
                    // INICIO REVISION DE VARIABLES DE ENTORNO
                    $sf_seguridad = sfYaml::load(sfConfig::get("sf_root_dir")."/config/siglas/seguridad.yml");
                    $this->getUser()->setAttribute('sf_session_expira', $sf_seguridad['session']['expira']);

                    if(preg_match("/Firefox\/2\./", $agente) ||
                       preg_match("/Firefox\/3\./", $agente) ||
                       preg_match("/Firefox\/4\./", $agente) ||
                       preg_match("/Firefox\/5\./", $agente) ||
                       preg_match("/Firefox\/6\./", $agente) ||
                       preg_match("/Firefox\/7\./", $agente) ||
                       preg_match("/Firefox\/8\./", $agente) ||
                       preg_match("/Firefox\/9\./", $agente) ||
                       preg_match("/Firefox\/10\./", $agente) ||
                       preg_match("/Firefox\/11\./", $agente) ||
//                       preg_match("/Firefox\/12\./", $agente) ||
//                       preg_match("/Firefox\/13\./", $agente) ||
//                       preg_match("/Firefox\/14\./", $agente) ||
//                       preg_match("/Firefox\/15\./", $agente) ||
//                       preg_match("/Firefox\/16\./", $agente) ||
//                       preg_match("/Firefox\/17\./", $agente) ||
//                       preg_match("/Firefox\/18\./", $agente) ||
//                       preg_match("/Firefox\/19\./", $agente) ||
//                       preg_match("/Firefox\/20\./", $agente) ||
//                       preg_match("/Firefox\/21\./", $agente) ||
//                       preg_match("/Firefox\/22\./", $agente) ||
//                       preg_match("/Firefox\/23\./", $agente) ||
                       preg_match("/Chrome\/6\./", $agente) ||
                       preg_match("/Chrome\/7\./", $agente) ||
                       preg_match("/Chrome\/8\./", $agente) ||
                       preg_match("/Chrome\/9\./", $agente) ||
                       preg_match("/Chrome\/10\./", $agente) ||
                       preg_match("/Chrome\/11\./", $agente) ||
                       preg_match("/Chrome\/12\./", $agente) ||
                       preg_match("/Chrome\/13\./", $agente) ||
                       preg_match("/Chrome\/14\./", $agente) ||
                       preg_match("/Chrome\/15\./", $agente) ||
                       preg_match("/Chrome\/16\./", $agente) ||
                       preg_match("/Chrome\/17\./", $agente) ||
                       preg_match("/Chrome\/18\./", $agente) ||
                       preg_match("/Chrome\/19\./", $agente) ||
//                       preg_match("/Chrome\/20\./", $agente) ||
//                       preg_match("/Chrome\/21\./", $agente) ||
//                       preg_match("/Chrome\/22\./", $agente) ||
//                       preg_match("/Chrome\/23\./", $agente) ||
//                       preg_match("/Chrome\/24\./", $agente) ||
//                       preg_match("/Chrome\/25\./", $agente) ||
//                       preg_match("/Chrome\/26\./", $agente) ||
//                       preg_match("/Chrome\/27\./", $agente) ||
//                       preg_match("/Chrome\/28\./", $agente) ||
//                       preg_match("/Chrome\/29\./", $agente) ||
//                       preg_match("/Chrome\/30\./", $agente) ||
                       preg_match("/MSIE 4\./",$agente) ||
                       preg_match("/MSIE 5\./",$agente) ||
                       preg_match("/MSIE 6\./",$agente) ||
                       preg_match("/MSIE 7\./",$agente) ||
                       preg_match("/MSIE 8\./",$agente) ||
                       preg_match("/MSIE 9\./",$agente) ||
                       preg_match("/Opera\/8\./",$agente) ||
                       preg_match("/Opera\/9\./",$agente) ||
                       preg_match("/Opera\/10\./",$agente) ||
                       preg_match("/Opera\/11\./",$agente)
                            )
                    {   $this->getUser()->setAuthenticated(false);
                        $this->getUser()->clearCredentials();
                        $this->getUser()->getAttributeHolder()->clear();
                        $this->getUser()->setFlash('actualizar', 't');
                        $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario'); }

                    // #####################################################
                    // ########### INICIO GENERAR MENU DINAMICO ############
                    // #####################################################


                    $usuario_menu = Doctrine::getTable('Acceso_ModuloPerfil')->buscarModuloPerfil($perfiles_ids);


                    $modulos = count($usuario_menu);

                    $cadena_script = '';
                    $cadena_modulos = '';
                    $cadena_perfiles = '';

                    $perfil = 0;
                    $mayor = 0;
                    $c = 1;

                    for($i=0;$modulos>$i;$i++)
                    {
                        if($perfil!=$usuario_menu[$i]['perfil_id'])
                        {
                            $perfil = $usuario_menu[$i]['perfil_id'];

                            $cadena_script .= 'document.getElementById("perfil'.$usuario_menu[$i]['perfil_id'].'").style.visibility="hidden"; ';

                            $cadena_perfiles .= '<option value="perfil'.$usuario_menu[$i]['perfil_id'].'">'.$usuario_menu[$i]['pnombre'].'</option>';

                            if($i!=0)
                                $cadena_modulos .= '</table></div><div id="perfil'.$usuario_menu[$i]['perfil_id'].'" style="visibility:hidden; position:absolute; left:0px; top:0px;"><table class="vns">';
                            else
                                $cadena_modulos .= '<div id="perfil'.$usuario_menu[$i]['perfil_id'].'" style="visibility:visible; position:absolute; left:0px; top:0px;"><table>';

                            if($mayor<$c)
                                $mayor = $c;

                            $c = 0;
                        }

                        $c++;

                        $app = 'sf_app_'.$usuario_menu[$i]['aplicacion'].'_url';

                        $cadena_modulos .= '<tr>
                                                <td>
                                                    <div style="position: relative; width:185px;">
                                                    <a href="'.sfConfig::get($app).$usuario_menu[$i]['vinculo'].'">
                                                        <div style="position: absolute; width: 24px; text-align: center;"><img src="/images/icon/'.$usuario_menu[$i]['imagen'].'"></div>
                                                        <div style="position: absolute; left: 24px;" class="barra_herramientas_fond_text">'.$usuario_menu[$i]['mnombre'].'</div>
                                                    </a>
                                                    </div><br/><br/>
                                                </td>
                                            </tr>';
                    }

                    if($mayor<$c)
                        $mayor = $c;

                    $mayor = ($mayor * 17) + 15;
                    $credencial= $this->getUser()->getCredentials();
                    $cadena_perfiles = '<div style="padding: 10px; background-color: #DADADA"><font style="color: #666">Perfil:&nbsp;</font><font style="color: #666; font-size: 15px; font-weight: bold">'. strtoupper($credencial[0]) .'</font></div>';

                    $cadena_script = '<script> function mostrarcombo(){'
                                     .$cadena_script.
                                     'cual = document.getElementById("perfiles_menu").value; document.getElementById(cual).style.visibility="visible"; } </script>';

                    $perfil= Doctrine::getTable('Acceso_UsuarioPerfil')->findOneByUsuarioIdAndStatus($this->getUser()->getAttribute('usuario_id'), 'A');
                    if(!$perfil) {
                        $perfil= 0;
                    }
                    
                    $modulos_autorizados = Doctrine::getTable('Acceso_Modulo')->moduloPerPerfil($perfil->getId());
                    $cadena_autorizados = '';

                    foreach ($modulos_autorizados as $modulo) {
                        $app = 'sf_app_'.$modulo->getAplicacion().'_url';
                        $cadena_autorizados .= '<tr>
                                                    <td width="24" align="center">
                                                        <div style="position: relative; width:185px;">
                                                        <a href="'.sfConfig::get($app).$modulo->getVinculo().'">
                                                            <div style="position: absolute; width: 24px; text-align: center;"><img src="/images/icon/'.$modulo->getImagen().'"></div>
                                                            <div style="position: absolute; left: 24px;" class="barra_herramientas_fond_text">'.$modulo->getNombre().'</div>
                                                        </a>
                                                        </div><br/>
                                                    </td>
                                                </tr>';
                    }

                    $cadena_modulos = '<div id="menu_sigla" style="visibility:visible; position:relative; left:0px; top:0px; width:185px; height:'.$mayor.'px;">'
                                      .$cadena_modulos
//                                      .$cadena_autorizados
                                      .'</table></div></div>';

                    $cadena_menu = $cadena_script.
                                   '<table align="center">';

                        
                        $cadena_menu.='<tr><td>'.
                             $cadena_perfiles.
                        '</td></tr>'.
                        '<tr><td><br/>'.
                             $cadena_modulos.
                        '</td></tr>'.
                    '</table>';

                    $cadena_menu = str_replace("  ", "", $cadena_menu);
                    $cadena_menu = str_replace("  ", "", $cadena_menu);
                    $cadena_menu = str_replace("  ", "", $cadena_menu);
                    $cadena_menu = str_replace("  ", "", $cadena_menu);
                    
                    $this->getUser()->setAttribute('zmenu', $cadena_menu);

                    // #####################################################
                    // ############ FIN GENERAR MENU DINAMICO ##############
                    // #####################################################


                    // INICIO BORRADO DE CACHE

                    $ultimo_corte = new herramientas();
                    $ultimo_corte->corteCache();

                    // FIN BORRADO DE CACHE
                }
                else
                {
                    // ########### ERROR NO TIENE PERFILES ############
                    $this->getUser()->setFlash('error', 'El usuario que ha ingresado tiene problemas de perfil de acceso, por favor comuniquese a la Dirección de Informática y reporte el error con el número P-'.$usuario_session->getId().'.');
                    $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
                }

                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/session');
            }
        }
        else
        {
            // REVISAR SI TIENE CLAVE TEMPORAL
            $usuario_session = Doctrine::getTable('Acceso_Usuario')->findOneByNombreAndClaveTemporal($usuario,$contrasena);

            if($usuario_session)
            {
                //UN CORREO SOLO SE VALIDA AL INGRESAR CON CLAVE TEMPORAL Y HAYA EMAIL EN DB (SIGNIFICA QUE HIZO LOS TRES PASO DE PRIMER INGRESO)
                $funcionario_usr= Doctrine::getTable('Funcionarios_Funcionario')->findOneById($usuario_session->getId());
                if($funcionario_usr->getEmailPersonal() != '') {
                    $funcionario_usr->setEmailValidado(true);
                    $funcionario_usr->save();
                }
                $this->getUser()->setAttribute('usuario_id', $usuario_session->getId());
                // ########### REINICIO DE CLAVE ############
                $this->getUser()->setFlash('notice', 'La contraseña se ha reiniciado automaticamente. Para comenzar el uso del sistema es necesario crear una contraseña nueva.');
                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario/cambioClave?user='.$usuario_session->getNombre());
            } else {
                // ########### LOGEO MALO ############
                $this->getUser()->setFlash('error', 'La información de nombre de usuario o contraseña introducida no es correcta.');
                $this->redirect(sfConfig::get('sf_app_acceso_url').'usuario');
            }
        }

  }

  public function executeFuncionarioUnidad(sfWebRequest $request)
  {
        $this->funcionario_selected = 0;
        $this->funcionarios = Doctrine::getTable('Funcionarios_FuncionarioCargo')->funcionarioDeUnidades(array($request->getParameter('u_id')));
  }
}