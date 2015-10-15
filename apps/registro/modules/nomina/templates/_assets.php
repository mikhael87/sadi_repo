 
<?php use_stylesheet('../css/global.css', 'first') ?> 
<?php use_stylesheet('../css/default.css', 'first') ?> 

<script>
    $(document).ready(function (){
        $('.input_recaudo').click(function() {
            var id= this.id;
            
            $.ajax({
                url:'<?php echo sfConfig::get('sf_app_registro_url'); ?>nomina/changeRecaudo',
                type:'POST',
                dataType:'html',
                data: 'id='+this.id+'&status='+$(this).is(":checked"),
                beforeSend: function(Obj){
                    $('#'+id).attr('disabled', 'disabled');
                },
                success:function(data, textStatus){
                    if(data === 'error') {
                        alert('Ha ocurrido algun inconveniente, si persiste por favor reportelo.')
                    }else {
                        if(data === 'C') {
                            $('#'+id).attr('checked', true);
                        }else {
                            $('#'+id).attr('checked', false);
                        }
                    }
                    $('#'+id).attr('disabled', false);
                }});
        });
    });
</script>