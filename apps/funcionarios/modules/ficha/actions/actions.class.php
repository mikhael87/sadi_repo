<?php

/**
 * ficha actions.
 *
 * @package    siglas
 * @subpackage ficha
 * @author     Mikhael Portela
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class fichaActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {          
      // Datos contacto      
      $this->contacto = Doctrine::getTable('Funcionarios_Contacto')->datosContatoFuncionario($this->getUser()->getAttribute('funcionario_id'));      
      
      // Datos corporal
      $corporal = Doctrine::getTable('Funcionarios_InformacionCorporal')->datosCorporalFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->corporal = $corporal[0];

      // Datos basicos
      $basica = Doctrine::getTable('Funcionarios_InformacionBasica')->datosBasicosFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->basica  = $basica[0];
      
      $eduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->datosFuncionarioEducacionUniversitaria($this->getUser()->getAttribute('funcionario_id'));
      $this->eduuniversitaria  = $eduuniversitaria[0];
      $edumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->datosFuncionarioEducacionMedia($this->getUser()->getAttribute('funcionario_id'));
      $this->edumedia  = $edumedia[0];
      $eduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->datosFuncionarioEducacionAdicional($this->getUser()->getAttribute('funcionario_id'));
      $this->eduadicional  = $eduadicional[0];
      
      $familiar = Doctrine::getTable('Funcionarios_Familiar')->findByFuncionarioIdAndStatus($this->getUser()->getAttribute('funcionario_id'),'A');
      $this->familiar  = $familiar[0];     
      
      // Datos residencia         
      $residencia = Doctrine::getTable('Funcionarios_Residencia')->datosFuncionarioResidencia($this->getUser()->getAttribute('funcionario_id'));
      $this->residencia  = $residencia[0];
      
      $idioma = Doctrine::getTable('Funcionarios_IdiomaManejado')->datosIdiomaFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->idioma  = $idioma[0];
      
      $grupo = Doctrine::getTable('Funcionarios_GrupoSocial')->datosGrupoFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->grupo  = $grupo[0];
      
      
  }
  
  public function executeInfoBasica(sfWebRequest $request)
  {
      $basica = Doctrine::getTable('Funcionarios_InformacionBasica')->datosBasicosFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->basica = $basica[0];
      
      $this->form = new Funcionarios_InformacionBasicaForm();
  }
  
  public function executeEliminarResidencia(sfWebRequest $request){
    
    $residencia = Doctrine::getTable('Funcionarios_Residencia')->findById($request->getParameter('res_id'));
    $residencia->delete();
    $this->redirect('ficha/index');
 
  }
  
  public function executeEliminarFamiliar(sfWebRequest $request){
        
    $familiar = Doctrine::getTable('Funcionarios_Familiar')->findOneById($request->getParameter('familiar_id'));
    if ($familiar->getProteccion() == ''){
        $familiarcorporal = Doctrine::getTable('Funcionarios_InformacionCorporalFamiliar')->findOneByFamiliarId($request->getParameter('familiar_id'));
        if ($familiarcorporal!='') {
            $familiarcorporal->setStatus('I');
            $familiarcorporal->save();
        }
        $familiardiscapacidad = Doctrine::getTable('Funcionarios_FamiliarDiscapacidad')->findOneByFamiliarId($request->getParameter('familiar_id'));
        if ($familiardiscapacidad!='') {
            $familiardiscapacidad->setStatus('I');
            $familiardiscapacidad->save();
        }
        $familiar->setStatus('I');
        $familiar->save();
    }else{
        $this->getUser()->setFlash('error_familiar', 'La información fue procesada por RRHH. No puede eliminarla.');
    }
    $this->redirect('ficha/index'); 
  }
  
   public function executeEliminarEduuniversitaria(sfWebRequest $request){    
    $eduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->findOneById($request->getParameter('eduuniversitaria_id'));   

    if ($eduuniversitaria->getProteccion() == ''){
        $eduuniversitaria->setStatus('I');
        $eduuniversitaria->save();
    }else{
        $this->getUser()->setFlash('error', 'La información fue procesada por RRHH. No puede eliminarla.');
    }
    
    $this->redirect('ficha/index'); 
  }  
   
  public function executeEliminarEdumedia(sfWebRequest $request){    
    $edumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->findOneById($request->getParameter('edumedia_id'));
     if ($edumedia->getProteccion() == ''){
        $edumedia->setStatus('I');
        $edumedia->save();
    }else{
        $this->getUser()->setFlash('error', 'La información fue procesada por RRHH. No puede eliminarla.');
    }
    
    $this->redirect('ficha/index'); 
  }  
  
  public function executeEliminarEduadicional(sfWebRequest $request){    
    $eduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->findOneById($request->getParameter('eduadicional_id'));
    if ($eduadicional->getProteccion() == ''){
        $eduadicional->setStatus('I');
        $eduadicional->save();
    }else{
        $this->getUser()->setFlash('error', 'La información fue procesada por RRHH. No puede eliminarla.');
    }
    $this->redirect('ficha/index'); 
  }
  
   public function executeEliminarContacto(sfWebRequest $request){
    
    $contacto = Doctrine::getTable('Funcionarios_Contacto')->findById($request->getParameter('contacto_id'));
    $contacto->delete();
    $this->redirect('ficha/index');
 
  }  
   
   public function executeInfoEduuniversitaria(sfWebRequest $request){      
      $this->getUser()->setAttribute('eduuniversitaria_accion',$request->getParameter('eduuniversitaria_accion'));
      $this->getUser()->setAttribute('eduuniversitaria_id',$request->getParameter('eduuniversitaria_id'));
      
      $eduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->findById($request->getParameter('eduuniversitaria_id'));
      $this->eduuniversitaria = $eduuniversitaria[0];
      
      $this->form = new Funcionarios_EducacionUniversitariaForm();        
  }
     
   public function executeInfoEduadicional(sfWebRequest $request){      
      $this->getUser()->setAttribute('eduadicional_accion',$request->getParameter('eduadicional_accion'));
      $this->getUser()->setAttribute('eduadicional_id',$request->getParameter('eduadicional_id'));
      
      $eduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->findById($request->getParameter('eduadicional_id'));
      $this->eduadicional = $eduadicional[0];
      
      $this->form = new Funcionarios_EducacionAdicionalForm();        
  }
     
   public function executeInfoEdumedia(sfWebRequest $request){      
      $this->getUser()->setAttribute('edumedia_accion',$request->getParameter('edumedia_accion'));
      $this->getUser()->setAttribute('edumedia_id',$request->getParameter('edumedia_id'));
      
      $edumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->findById($request->getParameter('edumedia_id'));
      $this->edumedia = $edumedia[0];
      
      $this->form = new Funcionarios_EducacionMediaForm();        
  }
  
   public function executeInfoFamiliar(sfWebRequest $request){
      
      $this->getUser()->setAttribute('familiar_accion',$request->getParameter('familiar_accion'));
      $this->getUser()->setAttribute('familiar_id',$request->getParameter('familiar_id'));
      
      $familiar = Doctrine::getTable('Funcionarios_Familiar')->findByIdAndStatus($request->getParameter('familiar_id'),'A');
      $this->familiar = $familiar[0];
      
      $this->form = new Funcionarios_FamiliarForm();  
      
  }
  
  public function executeInfoContacto(sfWebRequest $request){
      
      $this->getUser()->setAttribute('contacto_accion',$request->getParameter('contacto_accion'));
      $this->cantcontactos = Doctrine::getTable('Funcionarios_Contacto')->datosContatoFuncionario($this->getUser()->getAttribute('funcionario_id'));      
      $this->form = new Funcionarios_ContactoForm();  
      
  }
  
  public function executeInfoGruposocial(sfWebRequest $request){
      
      $this->cantgrupos = Doctrine::getTable('Funcionarios_GrupoSocial')->datosGrupoFuncionario($this->getUser()->getAttribute('funcionario_id'));      
      $this->form = new Funcionarios_GrupoSocialForm();  
      
  }
  
   public function executeInfoIdioma(sfWebRequest $request){
      
      $this->cantidiomas = Doctrine::getTable('Funcionarios_IdiomaManejado')->datosIdiomaFuncionario($this->getUser()->getAttribute('funcionario_id'));              
      
      $this->form = new Funcionarios_IdiomaManejadoForm();  
      
  }
  
  public function executeInfoResidencial(sfWebRequest $request)
  {
     
      $this->getUser()->setAttribute('residencia_accion',$request->getParameter('residencia_accion'));
      $this->getUser()->setAttribute('res_id',$request->getParameter('res_id'));
            
      //$residencia = Doctrine::getTable('Funcionarios_Residencia')->datosFuncionarioResidencia($this->getUser()->getAttribute('funcionario_id'));
      $residencia = Doctrine::getTable('Funcionarios_Residencia')->findById($request->getParameter('res_id'));
      $this->residencia  = $residencia[0];

      $this->form = new Funcionarios_ResidenciaForm();
  }
  
  public function executeInfoCorporal(sfWebRequest $request)
  {
      $corporal = Doctrine::getTable('Funcionarios_InformacionCorporal')->datosCorporalFuncionario($this->getUser()->getAttribute('funcionario_id'));
      $this->corporal = $corporal[0];
      
      
      $this->form = new Funcionarios_InformacionCorporalForm(); 
  }
  
  public function executeInfoFamiliarcorporal(sfWebRequest $request)
  {
      $this->getUser()->setAttribute('familiarcorporal_id',$request->getParameter('familiarcorporal_id'));
      $familiarcorporal = Doctrine::getTable('Funcionarios_InformacionCorporalFamiliar')->datosCorporalFamiliar($this->getUser()->getAttribute('familiarcorporal_id'));
      $this->familiarcorporal = $familiarcorporal[0];
      
      $this->form = new Funcionarios_InformacionCorporalFamiliarForm(); 
  }  
  
  public function executeInfoFamiliardiscapacidad(sfWebRequest $request)
  {
      $this->getUser()->setAttribute('familiardiscapacidad_id',$request->getParameter('familiardiscapacidad_id'));
      $this->cantdiscapacidad = Doctrine::getTable('Funcionarios_FamiliarDiscapacidad')->datosDiscapacidadFamiliar($this->getUser()->getAttribute('familiardiscapacidad_id'));
      
      $this->form = new Funcionarios_FamiliarDiscapacidadForm(); 
  }
  
   public function executeInfoFuncionariodiscapacidad(sfWebRequest $request)
  {    
      $this->cantdiscapacidad = Doctrine::getTable('Funcionarios_FuncionarioDiscapacidad')->datosDiscapacidadFuncionario($this->getUser()->getAttribute('funcionario_id'));      
      $this->form = new Funcionarios_FuncionarioDiscapacidadForm(); 
  }
  
  public function executeSaveInfoBasica(sfWebRequest $request)
  {
      
    $dia = $request->getParameter('dia');
    $mes = $request->getParameter('mes');
    $ano = $request->getParameter('ano');
    $f_nacimiento = $ano.'-'.$mes.'-'.$dia;
    $estado_nacimiento_id = $request->getParameter('estado_nacimiento_id');
    $sexo = $request->getParameter('sexo');
    $edo_civil = $request->getParameter('edo_civil');
    $licencia_conducir_uno_grado = $request->getParameter('licencia_conducir_uno_grado');
    $licencia_conducir_dos_grado = $request->getParameter('licencia_conducir_dos_grado');    
    
    $basica = Doctrine::getTable('Funcionarios_InformacionBasica')->datosBasicosFuncionario($this->getUser()->getAttribute('funcionario_id'));
    if(count($basica)== 0) {
        $data_basica = new Funcionarios_InformacionBasica();
        $data_basica->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_basica->setFNacimiento($f_nacimiento);
        $data_basica->setEstadoNacimientoId($estado_nacimiento_id);
        $data_basica->setSexo($sexo);
        $data_basica->setEdoCivil($edo_civil);
        $data_basica->setLicenciaConducirUnoGrado($licencia_conducir_uno_grado);
        $data_basica->setLicenciaConducirDosGrado($licencia_conducir_dos_grado);
        $data_basica->setStatus('A');
        $data_basica->save();
    }else {        
        $data_update= Doctrine::getTable('Funcionarios_InformacionBasica')->findOneByFuncionarioIdAndStatus($this->getUser()->getAttribute('funcionario_id'),'A');        
        if ($data_update->getProteccion()!=''){
            $data_update->setStatus('I');
            $data_update->save();
            $data_update = new Funcionarios_InformacionBasica();
            $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));            
        }
        $data_update->setFNacimiento($f_nacimiento);
        $data_update->setEstadoNacimientoId($estado_nacimiento_id);
        $data_update->setSexo($sexo);
        $data_update->setEdoCivil($edo_civil);
        $data_update->setLicenciaConducirUnoGrado($licencia_conducir_uno_grado);
        $data_update->setLicenciaConducirDosGrado($licencia_conducir_dos_grado);
        $data_update->setStatus('A');
        $data_update->save();
    }
    $basica = Doctrine::getTable('Funcionarios_InformacionBasica')->datosBasicosFuncionario($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_basica', array('basica' => $basica[0]));
 
    exit();
    
  }
  
  public function executeSaveInfoCorporal(sfWebRequest $request)
  {
    $peso = str_replace(',', '.', $request->getParameter('peso'));
    $altura = str_replace(',', '.', $request->getParameter('altura'));
    $ojos = $request->getParameter('ojos');
    $cabello = $request->getParameter('cabello');
    $piel = $request->getParameter('piel');
    $sangre = $request->getParameter('sangre');
    $lentes = $request->getParameter('lentes');
    $camisa = $request->getParameter('camisa');
    $pantalon = $request->getParameter('pantalon');
    $calzado = $request->getParameter('calzado');
    $gorra = $request->getParameter('gorra');
     
    $corporal = Doctrine::getTable('Funcionarios_InformacionCorporal')->datosCorporalFuncionario($this->getUser()->getAttribute('funcionario_id'));

    if(count($corporal)== 0) {
       
        $data_corporal = new Funcionarios_InformacionCorporal();
        $data_corporal->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_corporal->setPeso($peso);
        $data_corporal->setAltura($altura);
        $data_corporal->setColorOjos($ojos);
        $data_corporal->setColorCabello($cabello);
        $data_corporal->setColorPiel($piel);
        $data_corporal->setTipoSangre($sangre);
        $data_corporal->setLentesFormula($lentes);
        $data_corporal->setTallaCamisa($camisa);
        $data_corporal->setTallaPantalon($pantalon);
        $data_corporal->setTallaCalzado($calzado);
        $data_corporal->setTallaGorra($gorra);
        $data_corporal->setStatus('A');
        $data_corporal->save();
    }else {
        $data_update= Doctrine::getTable('Funcionarios_InformacionCorporal')->findOneByFuncionarioIdAndStatus($this->getUser()->getAttribute('funcionario_id'),'A');
        $data_update->setStatus('I');
        $data_update->save();
        $data_update = new Funcionarios_InformacionCorporal();
        $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_update->setPeso($peso);
        $data_update->setAltura($altura);
        $data_update->setColorOjos($ojos);
        $data_update->setColorCabello($cabello);
        $data_update->setColorPiel($piel);
        $data_update->setTipoSangre($sangre);
        $data_update->setLentesFormula($lentes);
        $data_update->setTallaCamisa($camisa);
        $data_update->setTallaPantalon($pantalon);
        $data_update->setTallaCalzado($calzado);
        $data_update->setTallaGorra($gorra);
        $data_update->setStatus('A');
        $data_update->save();
    }
    $corporal = Doctrine::getTable('Funcionarios_InformacionCorporal')->datosCorporalFuncionario($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_corporal', array('corporal' => $corporal[0]));
    exit();
  }   
  
   public function executeSaveInfoFamiliarcorporal(sfWebRequest $request)
  {
    
    $peso = str_replace(',', '.', $request->getParameter('peso'));
    $altura = str_replace(',', '.', $request->getParameter('altura'));
    $ojos = $request->getParameter('ojos');
    $cabello = $request->getParameter('cabello');
    $piel = $request->getParameter('piel');
    $camisa = $request->getParameter('camisa');
    $pantalon = $request->getParameter('pantalon');
    $calzado = $request->getParameter('calzado');
    $gorra = $request->getParameter('gorra');
     
    $familiarcorporal = Doctrine::getTable('Funcionarios_InformacionCorporalFamiliar')->datosCorporalFamiliar($this->getUser()->getAttribute('familiarcorporal_id'));

    if(count($familiarcorporal)== 0) {
       
        $data_corporal = new Funcionarios_InformacionCorporalFamiliar();
        $data_corporal->setFamiliarId($this->getUser()->getAttribute('familiarcorporal_id'));
        $data_corporal->setColorOjos($ojos);
        $data_corporal->setColorCabello($cabello);
        $data_corporal->setColorPiel($piel);
        $data_corporal->setPeso($peso);
        $data_corporal->setAltura($altura);
        $data_corporal->setTallaCamisa($camisa);
        $data_corporal->setTallaPantalon($pantalon);
        $data_corporal->setTallaCalzado($calzado);
        $data_corporal->setTallaGorra($gorra);
        $data_corporal->setStatus('A');
        $data_corporal->save();
    }else {
        $data_update= Doctrine::getTable('Funcionarios_InformacionCorporalFamiliar')->findOneByFamiliarId($this->getUser()->getAttribute('familiarcorporal_id'));        
        $data_update->setColorOjos($ojos);
        $data_update->setColorCabello($cabello);
        $data_update->setColorPiel($piel);
        $data_update->setPeso($peso);
        $data_update->setAltura($altura);
        $data_update->setTallaCamisa($camisa);
        $data_update->setTallaPantalon($pantalon);
        $data_update->setTallaCalzado($calzado);
        $data_update->setTallaGorra($gorra);
        $data_update->setStatus('A');
        $data_update->save();
    }
    $familiarcorporal = Doctrine::getTable('Funcionarios_InformacionCorporalFamiliar')->datosCorporalFamiliar($this->getUser()->getAttribute('familiarcorporal_id'));

    echo $this->getPartial('ficha/info_corporal', array('familiarcorporal' => $familiarcorporal[0]));
    exit();
  }   

   public function executeSaveInfoFamiliardiscapacidad(sfWebRequest $request)
  {
    $discapacidades = Doctrine::getTable('Funcionarios_FamiliarDiscapacidad')->datosDiscapacidadFamiliar($this->getUser()->getAttribute('familiardiscapacidad_id')); 
    
    foreach($discapacidades as $discapacidad){
        $discapacidad->setStatus('I');
        $discapacidad->save();
    }    
    $recibido = $request->getParameter('grupoId');
    $primero = explode("," , $recibido);    
      
        foreach($primero as $primer){
            $tipodiscapacidad = $primer[0];
       
            $datos_discapacidad = new Funcionarios_FamiliarDiscapacidad();        
            $datos_discapacidad->setFamiliarId($this->getUser()->getAttribute('familiardiscapacidad_id'));
            $datos_discapacidad->setDiscapacidadId($tipodiscapacidad);   
            $datos_discapacidad->setStatus('A');
            $datos_discapacidad->save();
        }
    $familiardiscapacidad = Doctrine::getTable('Funcionarios_FamiliarDiscapacidad')->datosDiscapacidadFamiliar($this->getUser()->getAttribute('familiardiscapacidad_id'));

    echo $this->getPartial('ficha/info_familiar', array('familiardiscapacidad' => $familiardiscapacidad[0]));
    exit();
  }   
  
  
   public function executeSaveInfoFuncionariodiscapacidad(sfWebRequest $request)
  {
    $discapacidades = Doctrine::getTable('Funcionarios_FuncionarioDiscapacidad')->datosDiscapacidadFuncionario($this->getUser()->getAttribute('funcionario_id')); 
    
    foreach($discapacidades as $discapacidad){
        $discapacidad->setStatus('I');
        $discapacidad->save();
    }    
    $recibido = $request->getParameter('grupoId');
    $primero = explode("," , $recibido);    
      
        foreach($primero as $primer){
            $tipodiscapacidad = $primer[0];
       
            $datos_discapacidad = new Funcionarios_FuncionarioDiscapacidad();        
            $datos_discapacidad->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $datos_discapacidad->setDiscapacidadId($tipodiscapacidad);   
            $datos_discapacidad->setStatus('A');
            $datos_discapacidad->save();
        }
    $funcionariodiscapacidad = Doctrine::getTable('Funcionarios_FuncionarioDiscapacidad')->datosDiscapacidadFuncionario($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_familiar', array('funcionariodiscapacidad' => $funcionariodiscapacidad[0]));
    exit();
  }   
   public function executeSaveInfoContacto(sfWebRequest $request)
  {   
       
    $contactos = Doctrine::getTable('Funcionarios_Contacto')->datosContatoFuncionario($this->getUser()->getAttribute('funcionario_id')); 
    
    foreach($contactos as $contacto){
        $contacto->setStatus('I');
        $contacto->save();
    }
    
    $recibido = $request->getParameter('tipo');
    $primero = explode("," , $recibido);    
      
        foreach($primero as $primer){
            $segundo = explode("#" , $primer);
            $tipo = $segundo[0];
            $valor = $segundo[1];
       
            $datos_contacto = new Funcionarios_Contacto();        
            $datos_contacto->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $datos_contacto->setTipo($tipo);
            $datos_contacto->setValor($valor);          
            $datos_contacto->setStatus('A');
            $datos_contacto->save();
        }
        
    $contacto = Doctrine::getTable('Funcionarios_Contacto')->findByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_contacto', array('contacto' => $contacto[0]));
 
    exit();
    
  }  
  
   public function executeSaveInfoIdioma(sfWebRequest $request)
  {   
       
    $idiomas = Doctrine::getTable('Funcionarios_IdiomaManejado')->datosIdiomaFuncionario($this->getUser()->getAttribute('funcionario_id')); 
    
    foreach($idiomas as $idioma){
        $idioma->setStatus('I');
        $idioma->save();
    }
    
    $recibido = $request->getParameter('idiomaId');
    $primero = explode("," , $recibido);    
      
        foreach($primero as $primer){
            $segundo = explode("#" , $primer);
            $idioma_id = $segundo[0];
            $principal = $segundo[1];
            $habla     = $segundo[2];
            $lee       = $segundo[3];
            $escribe   = $segundo[4];
       
            $datos_idioma = new Funcionarios_IdiomaManejado();        
            $datos_idioma->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $datos_idioma->setIdiomaId($idioma_id);      
            $datos_idioma->setPrincipal($principal);
            $datos_idioma->setHabla($habla);
            $datos_idioma->setLee($lee);
            $datos_idioma->setEscribe($escribe);
            $datos_idioma->setStatus('A');
            $datos_idioma->save();
        }
        
    $idioma = Doctrine::getTable('Funcionarios_IdiomaManejado')->findByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_idioma', array('idioma' => $idioma[0]));
 
    exit();
    
  }  
  
   public function executeSaveInfoGrupo(sfWebRequest $request)
  {   
       
    $grupos = Doctrine::getTable('Funcionarios_GrupoSocial')->datosGrupoFuncionario($this->getUser()->getAttribute('funcionario_id')); 
    
    foreach($grupos as $grupo){
        $grupo->setStatus('I');
        $grupo->save();
    }
    
    $recibido = $request->getParameter('grupoId');
    $primero = explode("," , $recibido);    
      
        foreach($primero as $primer){
            $segundo = explode("#" , $primer);
            $tipogrupo_id = $segundo[0];
            $nombre = $segundo[1];
            $descripcion     = $segundo[2];
       
            $datos_grupo = new Funcionarios_GrupoSocial();        
            $datos_grupo->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $datos_grupo->setTipoGrupoSocialId($tipogrupo_id);      
            $datos_grupo->setNombre($nombre);
            $datos_grupo->setDescripcion($descripcion);    
            $datos_grupo->setStatus('A');
            $datos_grupo->save();
        }
        
    $grupo = Doctrine::getTable('Funcionarios_GrupoSocial')->findByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_gruposocial', array('grupo' => $grupo[0]));
 
    exit();
    
  }  
  
  public function executeSaveInfoResidencial(sfWebRequest $request)
  {      
    $estado = $request->getParameter('estado');
    $municipio = $request->getParameter('municipio');
    $parroquia= $request->getParameter('parroquia');    
    $avenida = $request->getParameter('avenida');
    $edificio = $request->getParameter('edificio');
    $piso = $request->getParameter('piso');
    $apartamento = $request->getParameter('apartamento');
    $urbanizacion = $request->getParameter('urbanizacion');  
    $ciudad = $request->getParameter('ciudad');
    $punto = $request->getParameter('punto');
    $telefono1 = $request->getParameter('telefono1');
    $telefono2 = $request->getParameter('telefono2');
    
    
    $residencia = Doctrine::getTable('Funcionarios_Residencia')->datosFuncionarioResidencia($this->getUser()->getAttribute('funcionario_id'));
     
    if((count($residencia)== 0) or ($this->getUser()->getAttribute('residencia_accion')=='nuevo')) {
        $datos_residencia = new Funcionarios_Residencia();        
        $datos_residencia->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $datos_residencia->setEstadoId($estado);
        $datos_residencia->setMunicipioId($municipio);
        $datos_residencia->setParroquiaId($parroquia);
        $datos_residencia->setDirAvCalleEsq($avenida);
        $datos_residencia->setDirEdfCasa($edificio);
        $datos_residencia->setDirPiso($piso);
        $datos_residencia->setDirAptNombre($apartamento);
        $datos_residencia->setDirUrbanizacion($urbanizacion);
        $datos_residencia->setDirCiudad($ciudad);
        $datos_residencia->setDirPuntoReferencia($punto);
        $datos_residencia->setTelfUno($telefono1);
        $datos_residencia->setTelfDos($telefono2);        
        $datos_residencia->setStatus('A');
        $datos_residencia->save();
    }else {
        $data_update= Doctrine::getTable('Funcionarios_Residencia')->findOneByFuncionarioId($this->getUser()->getAttribute('funcionario_id'));        
        $data_update->setEstadoId($estado);
        $data_update->setMunicipioId($municipio);
        $data_update->setParroquiaId($parroquia);
        $data_update->setDirAvCalleEsq($avenida);
        $data_update->setDirEdfCasa($edificio);
        $data_update->setDirPiso($piso);
        $data_update->setDirAptNombre($apartamento);
        $data_update->setDirUrbanizacion($urbanizacion);
        $data_update->setDirCiudad($ciudad);
        $data_update->setDirPuntoReferencia($punto);
        $data_update->setTelfUno($telefono1);
        $data_update->setTelfDos($telefono2);        
        $data_update->setStatus('A');
        $data_update->save();
    }
    $residencia = Doctrine::getTable('Funcionarios_Residencia')->datosFuncionarioResidencia($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_residencial', array('residencia' => $residencia[0]));
 
    exit();
    
  } 
  
public function executeSaveInfoFamiliar(sfWebRequest $request)
  {          
    $cedula         = $request->getParameter('cedula');
    $parentesco     = $request->getParameter('parentesco');
    $primernombre   = $request->getParameter('primernombre');
    $segundonombre  = $request->getParameter('segundonombre');
    $primerapellido = $request->getParameter('primerapellido');
    $segundoapellido= $request->getParameter('segundoapellido');
    $dia = $request->getParameter('dia');
    $mes = $request->getParameter('mes');
    $ano = $request->getParameter('ano');
    $f_nacimiento = $ano.'-'.$mes.'-'.$dia;
    $nacionalidad   = $request->getParameter('nacionalidad');
    $sexo           = $request->getParameter('sexo');
    $nivel          = $request->getParameter('nivel');
    $estudia        = $request->getParameter('estudia');
    $trabaja        = $request->getParameter('trabaja');
    $dependencia    = $request->getParameter('dependencia');    
    
    $familiar = Doctrine::getTable('Funcionarios_Familiar')->datosFuncionarioFamiliar($this->getUser()->getAttribute('funcionario_id'));
     
    if((count($familiar)== 0) or ($this->getUser()->getAttribute('familiar_accion')=='nuevo')) {
   
        $datos_familiar = new Funcionarios_Familiar();        
        $datos_familiar->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $datos_familiar->setCi($cedula);
        $datos_familiar->setParentescoId($parentesco);
        $datos_familiar->setPrimerNombre($primernombre);
        $datos_familiar->setSegundoNombre($segundonombre);
        $datos_familiar->setPrimerApellido($primerapellido);
        $datos_familiar->setSegundoApellido($segundoapellido);
        $datos_familiar->setFNacimiento($f_nacimiento);
        $datos_familiar->setNacionalidad($nacionalidad);
        $datos_familiar->setSexo($sexo);
        $datos_familiar->setNivelAcademicoId($nivel);
        $datos_familiar->setEstudia($estudia);
        $datos_familiar->setTrabaja($trabaja);
        $datos_familiar->setDependencia($dependencia);
        $datos_familiar->setStatus('A');
        $datos_familiar->save();
    }else {     
        $data_update= Doctrine::getTable('Funcionarios_Familiar')->findOneById($this->getUser()->getAttribute('familiar_id'));                    
        $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_update->setCi($cedula);
        $data_update->setParentescoId($parentesco);
        $data_update->setPrimerNombre($primernombre);
        $data_update->setSegundoNombre($segundonombre);
        $data_update->setPrimerApellido($primerapellido);
        $data_update->setSegundoApellido($segundoapellido);
        $data_update->setFNacimiento($f_nacimiento);
        $data_update->setNacionalidad($nacionalidad);
        $data_update->setSexo($sexo);
        $data_update->setNivelAcademicoId($nivel);
        $data_update->setEstudia($estudia);
        $data_update->setTrabaja($trabaja);
        $data_update->setDependencia($dependencia);
        $data_update->setStatus('A');
        $data_update->save();
    }
    $familiar = Doctrine::getTable('Funcionarios_Familiar')->datosFuncionarioFamiliar($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_familiar', array('familiar' => $familiar[0]));
 
    exit();
    
  }    
  
public function executeSaveInfoEduuniversitaria(sfWebRequest $request)
  {          
    $pais       = $request->getParameter('pais');
    $organismo  = $request->getParameter('organismo');
    $carrera    = $request->getParameter('carrera');
    $nivel      = $request->getParameter('nivel');   
    $diai       = $request->getParameter('diai');
    $mesi       = $request->getParameter('mesi');
    $anoi       = $request->getParameter('anoi');
    $diag       = $request->getParameter('diag');
    $mesg       = $request->getParameter('mesg');
    $anog       = $request->getParameter('anog');
    $fingreso   = $anoi.'-'.$mesi.'-'.$diai;
    $fgraduado  = $anog.'-'.$mesg.'-'.$diag;
    $estudia    = $request->getParameter('estudia');
    $segmento   = $request->getParameter('segmento');   
    
    $eduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->datosFuncionarioEducacionUniversitaria($this->getUser()->getAttribute('funcionario_id'));
     
    if((count($eduuniversitaria)== 0) or ($this->getUser()->getAttribute('eduuniversitaria_accion')=='nuevo')) {
   
        $datos_eduuniversitaria = new Funcionarios_EducacionUniversitaria();        
        $datos_eduuniversitaria->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $datos_eduuniversitaria->setPaisId($pais);
        $datos_eduuniversitaria->setOrganismoEducativoId($organismo);
        $datos_eduuniversitaria->setCarreraId($carrera);
        $datos_eduuniversitaria->setNivelAcademicoId($nivel);
        $datos_eduuniversitaria->setFIngreso($fingreso);
        $datos_eduuniversitaria->setFGraduado($fgraduado);
        $datos_eduuniversitaria->setEstudiandoActualmente($estudia);
        $datos_eduuniversitaria->setSegmento($segmento);       
        $datos_eduuniversitaria->setStatus('A');
        $datos_eduuniversitaria->save();
    }else {     
        $data_update= Doctrine::getTable('Funcionarios_EducacionUniversitaria')->findOneById($this->getUser()->getAttribute('eduuniversitaria_id'));                    
        $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_update->setPaisId($pais);
        $data_update->setOrganismoEducativoId($organismo);
        $data_update->setCarreraId($carrera);
        $data_update->setNivelAcademicoId($nivel);
        $data_update->setFIngreso($fingreso);
        $data_update->setFGraduado($fgraduado);
        $data_update->setEstudiandoActualmente($estudia);
        $data_update->setSegmento($segmento);       
        $data_update->setStatus('A');
        $data_update->save();
    }
    $eduuniversitaria = Doctrine::getTable('Funcionarios_EducacionUniversitaria')->datosFuncionarioEducacionUniversitaria($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_academica', array('eduuniversitaria' => $eduuniversitaria[0]));
 
    exit();
    
  }    
  
public function executeSaveInfoEdumedia(sfWebRequest $request)
  {          
    $pais       = $request->getParameter('pais');
    $organismo  = $request->getParameter('organismo');
    $especialidad    = $request->getParameter('especialidad');
    $nivel      = $request->getParameter('nivel');   
    $diai       = $request->getParameter('diai');
    $mesi       = $request->getParameter('mesi');
    $anoi       = $request->getParameter('anoi');
    $diag       = $request->getParameter('diag');
    $mesg       = $request->getParameter('mesg');
    $anog       = $request->getParameter('anog');
    $fingreso   = $anoi.'-'.$mesi.'-'.$diai;
    $fgraduado  = $anog.'-'.$mesg.'-'.$diag;
    $estudia    = $request->getParameter('estudia');   
    
    $edumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->datosFuncionarioEducacionMedia($this->getUser()->getAttribute('funcionario_id'));
     
    if((count($edumedia)== 0) or ($this->getUser()->getAttribute('edumedia_accion')=='nuevo')) {
   
        $datos_edumedia = new Funcionarios_EducacionMedia();        
        $datos_edumedia->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $datos_edumedia->setPaisId($pais);
        $datos_edumedia->setOrganismoEducativoId($organismo);
        $datos_edumedia->setEspecialidadId($especialidad);
        $datos_edumedia->setNivelAcademicoId($nivel);
        $datos_edumedia->setFIngreso($fingreso);
        $datos_edumedia->setFGraduado($fgraduado);
        $datos_edumedia->setEstudiandoActualmente($estudia);      
        $datos_edumedia->setStatus('A');
        $datos_edumedia->save();
    }else {     
        $data_update= Doctrine::getTable('Funcionarios_EducacionMedia')->findOneById($this->getUser()->getAttribute('edumedia_id'));                    
        $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $data_update->setPaisId($pais);
        $data_update->setOrganismoEducativoId($organismo);
        $data_update->setEspecialidad($especialidad);
        $data_update->setNivelAcademicoId($nivel);
        $data_update->setFIngreso($fingreso);
        $data_update->setFGraduado($fgraduado);
        $data_update->setEstudiandoActualmente($estudia);  
        $data_update->setStatus('A');
        $data_update->save();
    }
    $edumedia = Doctrine::getTable('Funcionarios_EducacionMedia')->datosFuncionarioEducacionMedia($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_academica', array('edumedia' => $edumedia[0]));
 
    exit();
    
  }       
  
public function executeSaveInfoEduadicional(sfWebRequest $request)
  {          
    $pais       = $request->getParameter('pais');
    $organismo  = $request->getParameter('organismo');
    $nombre     = $request->getParameter('nombre');
    $tipo      = $request->getParameter('tipo');   
    $diai       = $request->getParameter('diai');
    $mesi       = $request->getParameter('mesi');
    $anoi       = $request->getParameter('anoi');   
    $fingreso   = $anoi.'-'.$mesi.'-'.$diai;
    $horas    = $request->getParameter('horas');   
    
    $eduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->datosFuncionarioEducacionAdicional($this->getUser()->getAttribute('funcionario_id'));
     
    if((count($eduadicional)== 0) or ($this->getUser()->getAttribute('eduadicional_accion')=='nuevo')) {
   
        $datos_eduadicional = new Funcionarios_EducacionAdicional();        
        $datos_eduadicional->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
        $datos_eduadicional->setPaisId($pais);
        $datos_eduadicional->setOrganismoEducativoId($organismo);
        $datos_eduadicional->setNombre($nombre);
        $datos_eduadicional->setTipoEducacionAdicionalId($tipo);
        $datos_eduadicional->setFIngreso($fingreso);
        $datos_eduadicional->setHoras($horas);      
        $datos_eduadicional->setStatus('A');
        $datos_eduadicional->save();
    }else {
        $data_update= Doctrine::getTable('Funcionarios_EducacionAdicional')->findOneById($this->getUser()->getAttribute('eduadicional_id'));                    
        //if ($data_update->getProteccion()!=""){
            $data_update->setFuncionarioId($this->getUser()->getAttribute('funcionario_id'));
            $data_update->setPaisId($pais);
            $data_update->setOrganismoEducativoId($organismo);
            $data_update->setNombre($nombre);
            $data_update->setTipoEducacionAdicionalId($tipo);
            $data_update->setFIngreso($fingreso);
            $data_update->setHoras($horas);  
            $data_update->setStatus('A');
            $data_update->save();
        //}
     }
    $eduadicional = Doctrine::getTable('Funcionarios_EducacionAdicional')->datosFuncionarioEducacionAdicional($this->getUser()->getAttribute('funcionario_id'));

    echo $this->getPartial('ficha/info_academica', array('eduadicional' => $eduadicional[0]));
 
    exit();
    
  }       
}
