<?php use_helper('jQuery'); ?>

<?php
$tipologias = Doctrine::getTable('Archivo_TipologiaDocumental')->tipologiasDeSerie($archivo_serie_documental->getId());

$cuerpo = null;
foreach ($tipologias as $tipologia) {
    if ($tipologia->getCuerpo() != $cuerpo) {
        echo '<b>' . $tipologia->getCuerpo() . '</b><br/>';
        $cuerpo = $tipologia->getCuerpo();
    }
    if($tipologia->getCuerpoId()!='')
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
    echo $tipologia->getNombre() . '<br/>';
}
?>
