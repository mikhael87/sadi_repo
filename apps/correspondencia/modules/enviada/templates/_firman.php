<?php $c_firman=Doctrine::getTable('Correspondencia_FuncionarioEmisor')->filtrarPorCorrespondencia($correspondencia_correspondencia->getId()); ?>

<table width="150">
  <?php foreach ($c_firman as $list_firman): ?>
    <tr>
        <td title="<?php echo $list_firman->getCtnombre(); ?>">
            <font>
                <?php echo ucwords(strtolower($list_firman->getpn())); ?>
                <?php echo ucwords(strtolower($list_firman->getpa())); ?>
                <?php if($list_firman->getFirma()=='S') echo image_tag('icon/tick.png'); ?>
            </font>
      </td>
    </tr>
   <?php endforeach; ?>
</table>




		<script type="text/javascript" charset="utf-8">
			$(function() {
				$('#userAgent').html(navigator.userAgent);
				$('#test1-flex').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'horizontal'
				});
				$('#test2-flex').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'vertical'
				});
				$('#test1').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'horizontal'
				});
				$('#test2').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'vertical'
				});
				$('#test3').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'horizontal',
					length:     75
				});
				$('#test4').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'horizontal',
					length:    75,
					position:  'bottom'
				});
				$('#test5').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'vertical',
					length:    75
				});
				$('#test6').gradient({
					from:      '003366',
					to:        '333333',
					direction: 'vertical',
					length:    75,
					position:  'right'
				});
				$('#test7').gradient({
					from: '003366',
					to:   '333333'
				});
			});
		</script>




		<div id="wrapper">
			<div id="test1-flex" class="box flexible"></div>
			<div id="test2-flex" class="box flexible"></div>
			<div id="test1" class="box"></div>
			<div id="test2" class="box"></div>
			<div id="test3" class="box"></div>
			<div id="test4" class="box bottom"></div>
			<div id="test5" class="box"></div>
			<div id="test6" class="box right"></div>
			<span id="test7">&nbsp;&nbsp;&nbsp;Testing with an inline element.&nbsp;&nbsp;&nbsp;</span>
		</div>