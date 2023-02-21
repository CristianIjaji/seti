<?php
function setActive($routeName) {
    return request()->routeIs($routeName) ? 'active' : '';   
}

/**
 * Retorna los valores para las consultas del grid
 * 
 * @param string $value valor consultado por el usuario
 * @return array
 */
function getValoresConsulta($value) {
    $operator = [];

    foreach (['>=', '<=', '!=', '=', '>', '<'] as $item) {
        $operator = explode($item, trim($value));

        if(count($operator) > 1){
            $operator[0] = $item;
            break;
        }
    }

    return [
        'operator' => count($operator) > 1 ? $operator[0] : 'like',
        'value' => (count($operator) > 1 ? $operator[1] : strtolower("%$value%"))
    ];
}

?>