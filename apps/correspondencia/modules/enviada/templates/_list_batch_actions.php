<li class="sf_admin_batch_actions_choice">
  <select name="batch_action">
    <option value=""><?php echo __('Choose an action', array(), 'sf_admin') ?></option>
    <option value="batchAnular"><?php echo __('Anular', array(), 'sf_admin') ?></option>
    <option value="batchFirmarEnviar"><?php echo __('Firmar y Enviar', array(), 'sf_admin') ?></option>
    <option value="batchFirmarEnviarCertificado"><?php echo __('Firmar y Enviar (Firma Certificada)', array(), 'sf_admin') ?></option>
  </select>
  <?php $form = new BaseForm(); if ($form->isCSRFProtected()): ?>
    <input type="hidden" name="<?php echo $form->getCSRFFieldName() ?>" value="<?php echo $form->getCSRFToken() ?>" />
  <?php endif; ?>
    <input type="button" onclick="prepare_signature_multiples('formatos/prepararFirmaMultiple','enviada/batchFirmarEnviar'); return false;" value="ok"/>
</li>
