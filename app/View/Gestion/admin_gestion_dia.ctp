<div id="bloquea" style="width: 20%;height: 80px;overflow: hidden;z-index: 10000;position: relative;text-align: center;background: #FFFFFF;margin-left: auto;margin-right: auto;display:none">
	<?php echo $this->Html->image('enviando.gif',array('style' => 'margin-top:20px')) ?>
</div>
<div style="float:left; margin-top:8px" class="filtro_gestiones_dia">
<?php
	echo $this->Form->input('supervisor_id',array('options' => $supervisores,'class'=>'filtro_supervisor','value'=>0)). '<br><br>';
?>
</div>
<div style="float:right;font-weight: bold;font-size: 15px;margin-top: 13px;margin-right: 33px;">
	<?php echo '<span style="font-weight:normal">Fecha:</span> '.$hoy ?>
</div>
<div class="float_left gestiones_x_dia">
	<span style="font-size:15px; font-weight:bold">Descripcion por Gestor</span>
	<table class="table_gestores" style="font-size:13px">
		<tr>
			<th>Nombre</th>
			<th>Nuevas</th>
			<th>Agenda</th>
			<th>Atraso</th>
			<th>Realizadas</th>
			<th>Supervisor</th>
		</tr>
		<?php
		$i = 0;
		foreach ($gestores as $d) {
			if ($i == 0) {
				$class = "seleccionado";
			} else {
				$class = "";
			}
		?>
			<tr class="gestores_dia <?php echo $class ?>" name="<?php echo $d['Gestor']['Clave']?>">
				<td><?php echo $d['Gestor']['Nombre']?></td>
				<td><?php 
					if (!empty($gestiones['Nuevas'][$d['Gestor']['id']])) {
						$gest = $gestiones['Nuevas'][$d['Gestor']['id']];
					} else {
						$gest = 0;
					}
					echo $gest ?>
				</td>
				<td><?php 
					if (!empty($gestiones['Agenda'][$d['Gestor']['id']])) {
						$gest = $gestiones['Agenda'][$d['Gestor']['id']];
					} else {
						$gest = 0;
					}
					echo $gest ?>
				</td>
				<td><?php 
					if (!empty($gestiones['Atraso'][$d['Gestor']['id']])) {
						$gest = $gestiones['Atraso'][$d['Gestor']['id']];
					} else {
						$gest = 0;
					}
					echo $gest ?>
				</td>
				<td><?php 
					if (!empty($gestiones['Realizadas'][$d['Gestor']['id']])) {
						$gest = $gestiones['Realizadas'][$d['Gestor']['id']];
					} else {
						$gest = 0;
					}
					echo $gest ?>
				</td>
				<td><?php echo $gestiones['Supervisor'][$d['Gestor']['id']];?></td>
			</tr>
		<?php
			$i++;
		}
		?>
	</table>
</div>
<div class="gestiones_realizadas">
	<span style="font-size:15px; font-weight:bold">Gestiones Realizadas en el día</span>
	<table class="table_gestiones_realizadas" style="font-size:14px;">
		<tr>
			<th>Fecha</th>
			<th>Tipo Gestión</th>
			<th>Obervación</th>
		</tr>
		<?php 
		 if (!empty($gest_realizadas)) {
			$i = 0;
			foreach($gest_realizadas as $r) {
				if ($i == 0) {
					$clase = "seleccionado";
				} else {
					$clase = "";
				}
				?>
				<tr class="tr_gestiones_realizadas <?php echo $clase?>">
					<td><?php echo $r['ClienGest']['fecha'] ?></td>
					<td><?php echo $r['ClienGest']['cond_deud'] ?></td>
					<td><?php echo $r['ClienGest']['observac'] ?></td>
				</tr>
				<?php
			}
		 }
		?>
	</table>
</div>
<script>
	$('.table_gestores').delegate('.gestores_dia','click',function(){
		//Selecciono el gestor
		$('.gestores_dia').removeClass('seleccionado');
		$(this).addClass('seleccionado');
		
		//Busco sus gestiones realizadas
		gestor = $(this).attr('name');
		$.ajax({
			type:'POST',
			dataType:'JSON',
			data:{gestor:gestor},
			url:"<?php echo Router::url(array('action'=>'buscar_gestiones_realizadas')); ?>",
			success:function(data){
				//Coloco las gestiones realizadas
				$('.tr_gestiones_realizadas ').remove();
				i = 0;
				tabla_gestiones_realizadas = '';
				$.each(data.gest_realizadas,function(a,b){
					if (i == 0) {
						clase="seleccionado";
					} else {
						clase = '';
					}
					
					tabla_gestiones_realizadas += '<tr class="tr_gestiones_realizadas '+clase+'">';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.fecha+'</td>';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.cond_deud+'</td>';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.observac+'</td>';
					tabla_gestiones_realizadas += '</tr>';
					i++;
				});
				$(".table_gestiones_realizadas").append(tabla_gestiones_realizadas);
			},error: function() {
				alert("error_filtro");
			}
		});
	});
	$('.table_gestiones_realizadas').delegate('.tr_gestiones_realizadas','click',function(){
		$('.tr_gestiones_realizadas').removeClass('seleccionado');
		$(this).addClass('seleccionado');
	});
	
	$('.filtro_supervisor').change(function(){
		document.getElementById('total_wrap').style.display='block';
		document.getElementById('bloquea').style.display='block';
		supervisor = $(this).val();
		$.ajax({
			type:'POST',
			dataType:'JSON',
			data:{supervisor:supervisor},
			url:"<?php echo Router::url(array('action'=>'buscar_gestores')); ?>",
			success:function(data){
				$('.gestores_dia').remove();
				i = 0;
				tabla_gestores = '';
				$.each(data.gestores,function(a,b){
					if (i == 0) {
						clase="seleccionado";
					} else {
						clase = '';
					}
					indice = b.Gestor.id ;
					tabla_gestores += '<tr class="gestores_dia '+clase+'" name="'+b.Gestor.Clave+'">';
					tabla_gestores += '<td>'+b.Gestor.Nombre+'</td>';
					tabla_gestores += '<td>'+data.gestiones.Nuevas[indice]+'</td>';
					tabla_gestores += '<td>'+data.gestiones.Agenda[b.Gestor.id]+'</td>';
					tabla_gestores += '<td>'+data.gestiones.Atraso[b.Gestor.id]+'</td>';
					tabla_gestores += '<td>'+data.gestiones.Realizadas[b.Gestor.id]+'</td>';
					tabla_gestores += '<td>'+data.gestiones.Supervisor[b.Gestor.id]+'</td>';
					tabla_gestores += '</tr>';
					i++;
				});
				$(".table_gestores").append(tabla_gestores);
				
				//Coloco las gestiones realizadas del primero
				$('.tr_gestiones_realizadas ').remove();
				i = 0;
				tabla_gestiones_realizadas = '';
				$.each(data.gest_realizadas,function(a,b){
					if (i == 0) {
						clase="seleccionado";
					} else {
						clase = '';
					}
					
					tabla_gestiones_realizadas += '<tr class="tr_gestiones_realizadas '+clase+'">';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.fecha+'</td>';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.cond_deud+'</td>';
					tabla_gestiones_realizadas += '<td>'+b.ClienGest.observac+'</td>';
					tabla_gestiones_realizadas += '</tr>';
					i++;
				});
				$(".table_gestiones_realizadas").append(tabla_gestiones_realizadas);
				document.getElementById('total_wrap').style.display='none';
				document.getElementById('bloquea').style.display='none';
			},error: function() {
				alert("error_filtro");
			}
		});
	});
</script>