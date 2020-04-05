<?php

function formatCurrency($value){
    if($value > 0)
        return number_format($value, 2, ",",".");
    
    return 0;
}

?>