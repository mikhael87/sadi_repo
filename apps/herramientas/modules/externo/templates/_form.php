<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php include_partial('externo/assets'); ?>
<?php use_helper('jQuery'); ?>

<?php 
//echo $sf_user->getAttribute('sms_pendientes');
?>

<div id="indicator" style="display: none; position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; background-color: black; opacity: 0.4; filter:alpha(opacity=40); z-index: 200;">
    <?php echo image_tag('icon/spinner.gif', array('id'=> 'charge_image', 'style'=> 'position: absolute; top:50%; left:50%; opacity: 1; filter:alpha(opacity=100); z-index: 500')); ?>
</div>


<script>

//creo el plugin cuentaCaracteres
jQuery.fn.cuentaCaracteres = function() {
   //para cada uno de los elementos del objeto jQuery
   this.each(function(){
      //creo una variable elem con el elemento actual, suponemos un textarea
      elem = $(this);
      //creo un elemento DIV sobre la marcha
      var help = $('<div class="f11n rojo">La cantidad mensajes podria aumentar en uno ya que el sistema no corta las palabras.</div>');
      var contador = $('<div>Mensajes: 0, Caracteres: ' + elem.attr("value").length + '</div>');
      //inserto el DIV después del elemento textarea
      elem.after(contador);
      elem.after(help);
      //guardo una referencia al elemento DIV en los datos del objeto jQuery
      elem.data("campocontador", contador);
      
      //creo un evento keyup para este elemento actual
      elem.keyup(function(){
         //creo una variable elem con el elemento actual, suponemos un textarea
         var elem = $(this);
         //recupero el objeto que tiene el elemento DIV contador asociado al textarea
         var campocontador = elem.data("campocontador");
         
         var mensajes = Math.ceil(elem.attr("value").length / 160);
         //modifico el texto del contador, para actualizarlo con el número de caracteres escritos
         campocontador.text('Mensajes: ' + mensajes + ', Caracteres: ' + elem.attr("value").length);
      });
   });
   //siempre tengo que devolver this
   return this;
};
$(document).ready(function(){
   $("textarea").cuentaCaracteres();
})
</script>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@public_mensajes_externo', array('id' => 'form_masive', 'enctype' => 'multipart/form-data')) ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('externo/form_fieldset', array('public_mensajes' => $public_mensajes, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('externo/form_actions', array('public_mensajes' => $public_mensajes, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </form>
</div>
