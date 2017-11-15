<?php

    $user = $msg['userData'];
    echo json_encode(array(
        'id' => $user->id,
        'name' => $user->name,
        'username' => $user->username,
        'privilege' => $user->privilege
    ))
 ?>
