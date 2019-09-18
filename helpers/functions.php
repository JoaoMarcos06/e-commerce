<?php

function formatCurrency(float $value){
    return number_format($value, 2, ",",".");
}

?>