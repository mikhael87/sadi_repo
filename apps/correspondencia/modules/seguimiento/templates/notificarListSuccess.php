<style>
    .list_ul {
        list-style: none;
    }
    
    .list_ul li {
        list-style: none;
        padding-bottom: 3px
    }
</style>

<ul class="list_ul">
    <?php
    foreach($funcionarios_corresp_datos as $funcionario) {
        echo '<li><input style="vertical-align: middle" checked type="checkbox" name="noti_funcio" value="' . $funcionario->getId() . '" />&nbsp;' . $funcionario->getPrimerNombre().' '.$funcionario->getPrimerApellido() . '</li>';
    } ?>
</ul>
