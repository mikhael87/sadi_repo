<script>
    $(document).ready(function()
    {
       jQuery.extend(jQuery.validator.messages, {
            required: "Este campo es requerido."
        });

       $('#form_grupo').validate({
           rules: {
                        'val_input' : { validator_vistobueno_repetido : true }
           },
           messages: {},
            errorElement: "span"
        });

        jQuery.validator.addMethod("validator_vistobueno_repetido", function(value, element) {
            if($('#table_vistobuenos >tbody >tr').length) {
                  var repetido= 0;
                  $('#table_vistobuenos input').each(function() {
                      var dato= $(this).val();

                      $('#table_vistobuenos input').each(function() {
                          if($(this).val()== dato){
                              repetido++;
                          }
                      });
                  });
                  if(repetido > $('#table_vistobuenos >tbody >tr').length)
                      return false;
                  else
                      return true;
            }else {
                  return true;
            }
      }, "Por favor, no repita los visto buenos.");
    });
    
    function sondeo_vistobueno(){
        $('#content_window_right').html('<?php echo image_tag('icon/cargando.gif', array('size'=>'25x25')); ?> Preparando Visto buenos...');

        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_correspondencia_url'); ?>grupos/sondeoVistoBueno',
            type:'POST',
            dataType:'html',
            success:function(data, textStatus){
                $("#content_window_right").html(data);
            }});
    }
</script>
