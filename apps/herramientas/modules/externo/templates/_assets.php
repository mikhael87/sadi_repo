<style>
    label.error{display: none !important}
    .ui-progressbar { width: 762px; height: 21px }
    #progressbar { width: 762px; height: 21px }
</style>

<script language="JavaScript" type="text/javascript">  
    $(document).ready(function(){
        $("#form_masive").validate({
            rules: {
                'telf[unico][tlf]' : { required: { depends: function(element) { return ( $("#archivo").val() == '') } }, maxlength: 7, minlength: 7, digits: true },
                'public_mensajes[contenido]' : 'required'
            },
            messages: {
                'telf[unico][tlf]' : { required: 'Requerido', maxlength: 'Por favor ingrese no mas de 7 digitos', minlength: 'Por favor ingrese no menos de 7 digitos', digits: 'Por favor ingrese solo números' },
                'public_mensajes[contenido]' : 'Este campo es requerido.'
            },
            errorElement: "span"
        });
        
        $('.sf_admin_action_save input').click(function(){
            $('#indicator').css('display', 'block')
        });
    });
    
    var interval= setInterval('indicator_out()', 1000);
    
    function indicator_out(){
        if($('.error').length){
            $('#indicator').css('display', 'none');
        }
    }
</script>
