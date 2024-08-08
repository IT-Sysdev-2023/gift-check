<?php

// get user department
if (!function_exists('userDepartment')) {
    function userDepartment($user) {
        $dept = $user->usertype;
        return match ($dept) {
            '2' => 1, // treasury department
            '6' => 2, //marketing department
            default => $dept,
        };
    }
}
