<div class="filtro_gestion">
<div class = "inner_left">
	<?php
		echo $this->Form->input('supervisor_id',array(
			'class' => 'form-control',
			'empty' => 'TODOS',
			'id' => 'select_supervisor'
		));
		
		echo $this->Form->input('cliente_id',array(
			'label' => 'Empresa',
			'class' => 'form-control',
			'empty' => 'TODOS',
			'id' => 'select_cliente'
		));
		
		echo $this->Form->input('producto_id',array(
			'class' => 'form-control',
			'empty' => 'TODOS',
			'id' => 'select_producto'
		));
		
		echo $this->Form->input('fecha', array('label' => 'Fecha de Asignación:  ', 'class' => 'fecha_asignacion','empty' => 'Todos','id' => 'pickDate','type' => 'text','readonly' => 'true'));
	?>
	<div id="preload">

</div>
</div>
<div class = "inner_right">
	<?php 
		echo $this->Form->input('gestore_id',array( // acomodar nombre del campo de la bd
			'class' => 'form-control',
			'label' => 'Gestor',
			'empty' => 'TODOS',
			'id' => 'select_gestor'
		));
	
		echo $this->Form->input('statu_id',array(
			'class' => 'form-control',
			'id' => 'select_statu',
			'empty' => 'TODOS',
			'label' => 'Status',
		));
	    echo $this->Form->input('cedula', array('label' => 'Busqueda general: ','id' => 'select_general'));
	?>
</div>
    <div style="clear:left">
        <?php
        echo $this->Form->radio('cartera',array('' => 'Todos','atraso' => 'Atraso', 'agenda' => 'Agenda', 'nuevas' => 'Nuevas', 'gesthoy' => 'Gest.Hoy', 'cartera' => 'Cartera'),
            array( // nombre inventado
            'class' => 'form-control',
            'id' => 'select_tipo'
        ));
        ?>
    </div>

</div>
<script>
//Buscar status y productos cuando selecciono un cliente
$(".filtro_gestion").change(function(){
	buscar_deudores();
});
$("#select_cliente").change(function(){
	if(!isNaN($(this).val())){
		$.ajax({
			type:'POST',
			dataType:'JSON',
			data:{cliente_id:$("#select_cliente").val()},
			url:"<?php echo Router::url(array('action'=>'actualizar_status_productos')); ?>",
			success:function(data){
				options_p = "<option value = '0'>TODOS</option>";
				options_s = "<option value = '0'>TODOS</option>";
				$.each(data.productos,function(i,v){
					options_p +="<option value='"+i+"'>"+v+"</option>";
				});
				$.each(data.status,function(i,v){
					options_s +="<option value='"+i+"'>"+v+"</option>";
				});
				$("#select_producto").html(options_p);
				$("#select_statu").html(options_s);
			},
			beforeSend:function(){
				$("#select_cliente").attr('disabled',true);
				//loader();
			},
			complete:function(){
				$("#select_cliente").removeAttr('disabled');
				//$('#preload').empty().append('');
			}

		});
	}
});

$("#select_supervisor").change(function(){
	if(!isNaN($(this).val())){ //Carga los gestores
		$.ajax({
			type:'POST',
			dataType:'JSON',
			data:{supervisor_id:$("#select_supervisor").val()},
			url:"<?php echo Router::url(array('action'=>'actualizar_gestores')); ?>",
			success:function(data){
				options_p = "<option value = ''>TODOS</option>";
				$.each(data.gestores,function(i,v){
					options_p +="<option value='"+i+"'>"+v+"</option>";
				});
				$("#select_gestor").html(options_p);
			},
			beforeSend:function(){
				$("#select_supervisor").attr('disabled',true);
				//loader();
			},
			complete:function(){
				$("#select_supervisor").removeAttr('disabled');
				//buscar_deudores();
				//$('#preload').empty().append('');
			}

		});
	}
});

function buscar_deudores() {
	//Busca los deudores, tomando en cuenta todos los parametros de busqueda
		$.ajax({
			type:'POST',
			dataType:'JSON',
			data:{supervisor_id:$("#select_supervisor").val(),gestor_clave:$("#select_gestor").val(),cliente:$("#select_cliente").val(),producto:$("#select_producto").val(),statu:$("#select_statu").val(),tipo:$("#select_tipo").val(),fecha:$(".fecha_asignacion").val(),general:$("#select_general").val()},
			url:"<?php echo Router::url(array('action'=>'actualizar_deudores')); ?>",
			success:function(data){
				$('.tr_deudores').remove(); //Elimino los deudores que estaban
				info_deudores ='<div class = "inner_table"><table class="table_info_deudores" style="width:886px"><tr><th> Nombre	</th><th> Cédula	</th><th> Teléfono	</th><th> FECHA_AS 	</th><th> Fecha	</th><th> Gestor	</th><th> Saldo	</th></tr>'; //Creo de nuevo la tabla con la nueva info
				var i = 0; //Para saber si es la primera fila y ponerla en amarillo
				$.each(data,function(i,v){ // La data es un arreglo igual al que viene en principio sin usar las busquedas
					if (i==0) {
						clase = 'seleccionado';
					} else {
						clase = '';
					}
					info_deudores += '<tr class="tabla_deudores tr_deudores '+clase+'" name="'+v.Cobranza.CEDULAORIF+'">';
					info_deudores += '<td>'+v.Cobranza.NOMBRE+'</td>';
					info_deudores += '<td>'+v.Cobranza.CEDULAORIF+'</td>';
					info_deudores += '<td>'+v.ClienGest.telefono+'</td>';
					var fecha_asig = v.Cobranza.FECH_ASIG.split(" ");
					info_deudores += '<td>'+fecha_asig[0]+'</td>';
					var proxima_g = v.ClienGest.proximag.split(" ");
					info_deudores += '<td>'+proxima_g[0]+'</td>';
					info_deudores += '<td>'+v.Cobranza.GESTOR+'</td>';
					info_deudores += '<td>Saldo</td>';
					info_deudores += '</tr>';
					i++;
				});
				info_deudores += '</table></div>';
				$('#table_info_deudores').html(info_deudores);
				cedula = data[0].Cobranza.CEDULAORIF;
				cargar_info(cedula);
				//cargar_info_datos_deudor(cedula);
				//gestion_id = data[0].ClienGest.id;
				//cargar_info_comentario(gestion_id);
			},
			beforeSend:function(){
				$("#select_supervisor").attr('disabled',true);
				loader();
			},
			complete:function(){
				$("#select_supervisor").removeAttr('disabled');
				//buscar_deudores();
				$('#preload').empty().append('');
			}
		});
	//}
}


function loader(){
	$('#preload').append('<div><img width="40" src="/cobranza/app/webroot/img/status.gif"/></div>');
	$('#preload').css({'width':'200px','position':'absolute','z-index':'1','margin-top':'-35px','margin-left':'250px'});
}

</script>