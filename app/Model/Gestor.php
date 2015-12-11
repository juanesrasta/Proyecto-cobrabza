<?php
App::uses('AppModel', 'Model');
class Gestor extends AppModel {
	public $name = 'Gestor';
	
	public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        ),
    );
	
	function crearClave($nombre,$user_id) {
		$nombre = explode(' ',$nombre);
		if (!empty($nombre[1])){
			$clave = $nombre[0].$nombre[1];
		} else {
			$clave = $nombre[0];
		}
		$existe_clave = $this->find('first',array('conditions'=>array('Clave' => $clave,'user_id <>' => $user_id)));
		$i=1;
		while (!empty($existe_clave)) {
			$clave = $clave.$i;
			$existe_clave = $this->find('first',array('conditions'=>array('Clave' => $clave)));
		}
		return $clave;
	}

    function buscarGestores(){
        $gestores_b = $this->find('all',array( // operadores¿?
            'fields' => array('Clave','User.nombre_completo','User.supervisor_id'),
            'conditions' => array('User.supervisor_id <>' => '0')
        ));
        foreach ($gestores_b as $g) {
            $gestors[$g['Gestor']['Clave']] = $g['User']['nombre_completo'];
        }
        return $gestors;
    }

    function comisiones($arreglo_comisiones){
        $i = 0;
        foreach ($arreglo_comisiones as $comision) {
            $diasMora = $comision['ClienPago']['diasMora'];
            $arreglo_comisiones[$i]['pagoGestor'] = 0;
            if($comision['User']['tipo'] == 'interno'){
                if ($diasMora > 2500){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['aIntPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['aIntPorcen'];
                }
                if ($diasMora > 750 && $diasMora <= 2500){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['bIntPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['bIntPorcen'];
                }
                if ($diasMora > 360 && $diasMora <= 750){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['cIntPorcen'] / 100;
                    $arreglo_comisiones[$i]['comision'] = $comision['GruposProducto']['cIntPorcen'];
                }
                if ($diasMora <= 360){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['dIntPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['dIntPorcen'];
                }
            }

            if($comision['User']['tipo'] == 'externo'){
                if ($diasMora > 2500){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['aExtPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['aExtPorcen'];
                }
                if ($diasMora > 750 && $diasMora <= 2500){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['bExtPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['bExtPorcen'];
                }
                if ($diasMora > 360 && $diasMora <= 750){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['cExtPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['cExtPorcen'];
                }
                if ($diasMora <= 360){
                    $arreglo_comisiones[$i]['ClienPago']['pagoGestor']  = $comision['ClienPago']['TOTAL_PAGO'] * $comision['GruposProducto']['dExtPorcen'] / 100;
                    $arreglo_comisiones[$i]['ClienPago']['comision'] = $comision['GruposProducto']['dExtPorcen'];
                }

            }

            $i++;
        }

        return ($arreglo_comisiones);
    }

    function buscarGestoresPorSupervisor($supervisor_id){
        $gestores_b = $this->find('all',array( // operadores¿?
            'fields' => array('Clave','User.nombre_completo','User.supervisor_id'),
            'conditions' => array('User.supervisor_id ' => $supervisor_id)
        ));
        foreach ($gestores_b as $g) {
            $gestors[$g['Gestor']['Clave']] = $g['User']['nombre_completo'];
        }
        return $gestors;
    }
}

?>