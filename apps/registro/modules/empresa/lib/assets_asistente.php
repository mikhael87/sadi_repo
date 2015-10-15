<script>
    function rastrear(paso, regid) {
        if(paso === '1') {
            empresa_asis();
        } else {
            if(paso === '2') {
                encargado_asis(regid);
            }else {
                if(paso === '3') {
                    delegado_asis(regid);
                } else {
                    if(paso === '4') {
                        disciplina_asis(regid);
                    }
                }
            }
        }
    }
    
    function empresa_asis(){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>empresa/asistenteEmpresa',
            type:'POST',
            dataType:'html',
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> Cargando...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(1);
            }});
    }
    
    function encargado_asis(regid){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>encargado/asistenteEncargado',
            type:'POST',
            dataType:'html',
            data: {regid: regid},
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> Cargando...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(2);
            }});
    }
    
    function delegado_asis(regid){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>delegado/asistenteDelegado',
            type:'POST',
            dataType:'html',
            data: {regid: regid},
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> Cargando...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(3);
            }});
    }
    
    function disciplina_asis(regid){
        $.ajax({
            url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>disciplina/asistenteDisciplina',
            type:'POST',
            dataType:'html',
            data: {regid: regid},
            beforeSend: function(Obj){
                $('#div_button_upload').html('<?php echo image_tag('icon/cargando.gif'); ?> Cargando...');
            },
            success:function(data, textStatus){
                $('#div_prosesar').html(data);
                reiniciar_pasos(4);
            }});
    }
    
    function remover_tr(tr_id){
        $('#tr_'+tr_id).hide("slow", function() {
            $(this).remove();
        });
    }
    
    function reiniciar_pasos(paso){
        $('.vinculo').hide();
        
        for (i = 1; i < paso; i++) {
            $('#div_vinculo_'+i).show();
        }
        
        $('.pasos').css('background-color','');
        $('#div_paso_'+paso).css('background-color','#CCCCFF');
        
        $('.pasos').css('font-weight','normal');
        $('#div_paso_'+paso).css('font-weight','bold');
        
        $('.pasos').css('color','#aaa');
        $('#div_paso_'+paso).css('color','#000');
    }
</script>