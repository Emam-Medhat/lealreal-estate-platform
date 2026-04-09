<?php

/**
 * Laravel IDE Helper
 * 
 * This file helps IDEs recognize Laravel facades and classes
 */

// Add this at the top of your files if IDE doesn't recognize Laravel
if (!function_exists('route')) {
    function route($name, $params = []) {
        return app('router')->get($name, $params);
    }
}

if (!function_exists('view')) {
    function view($name, $data = [], $mergeData = []) {
        return view($name, $data, $mergeData);
    }
}

if (!function_exists('auth')) {
    function auth($guard = null) {
        return auth()->guard($guard);
    }
}

if (!function_exists('request')) {
    function request($key = null, $default = null) {
        return request()->input($key, $default);
    }
}

if (!function_exists('back')) {
    function back($status = 302, $default = '/') {
        return redirect()->back($status, $default);
    }
}

if (!function_exists('redirect')) {
    function redirect($to = null, $status = 302, $headers = []) {
        return redirect($to, $status, $headers);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        return csrf_token();
    }
}

if (!function_exists('old')) {
    function old($key = null, $default = null) {
        return old($key, $default);
    }
}

if (!function_exists('now')) {
    function now() {
        return \Carbon\Carbon::now();
    }
}
  
