<?php

class RANDOMIZER {

    public function randomHash($size) {
        
        $alphabet = 'ABCDEF1234567890';
        
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $size; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
