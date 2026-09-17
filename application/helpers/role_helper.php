<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Role & Permission Helper
 * Memudahkan pengecekan role dan permission di views dan controllers
 */

if (!function_exists('is_superadmin')) {
    /**
     * Check if current user is superadmin
     */
    function is_superadmin() {
        $CI =& get_instance();
        return $CI->auth_model->is_superadmin();
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if current user has specific role
     * @param string|array $role Role name(s) to check
     */
    function has_role($role) {
        $CI =& get_instance();
        
        if (!is_array($role)) {
            $role = [$role];
        }
        
        $user_role = $CI->session->userdata('role');
        
        return in_array($user_role, $role);
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if current user has specific permission
     * Superadmin otomatis memiliki semua permission
     * @param string $permission Permission name
     */
    function has_permission($permission) {
        $CI =& get_instance();
        
        // Superadmin always has all permissions
        if (is_superadmin()) {
            return true;
        }
        
        return $CI->auth_model->has_permission($permission);
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current user data
     */
    function current_user() {
        $CI =& get_instance();
        return $CI->auth_model->current_user();
    }
}

if (!function_exists('user_role')) {
    /**
     * Get current user role
     */
    function user_role() {
        $CI =& get_instance();
        return $CI->session->userdata('role');
    }
}

if (!function_exists('user_role_id')) {
    /**
     * Get current user role ID
     */
    function user_role_id() {
        $CI =& get_instance();
        return $CI->session->userdata('role_id');
    }
}

if (!function_exists('is_admin_or_superadmin')) {
    /**
     * Check if current user is admin or superadmin
     */
    function is_admin_or_superadmin() {
        return has_role(['admin', 'superadmin']);
    }
}

if (!function_exists('require_role_access')) {
    /**
     * Check if user has access based on role (superadmin has access to all)
     * @param string|array $roles Role name(s) that have access
     */
    function require_role_access($roles) {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        $user_role = user_role();
        
        // Superadmin always has access
        if ($user_role === 'superadmin') {
            return true;
        }
        
        return in_array($user_role, $roles);
    }
}