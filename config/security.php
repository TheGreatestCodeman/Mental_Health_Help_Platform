<?php

//inputs
function inputs($data){
    return htmlspecialchars(strip_tags(trim($data)));
}

//pasword hashing
function hash_password($password){
    return password_hash($password,PASSWORD_BCRYPT);
}

//password verification
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

//session protection
function secure_session_start(){
    session_start();
    session_regenerate_id(true);
}

//auth check
function require_login(){
    if (!isset($_SESSION["user_id"])){
        http_response_code(401);
        exit('unauthorized');
    }
}

//role acces
function require_role($role) {
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('Forbidden');
    }
} 

?>