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

if (!function_exists('admin_visible_creator_uuids')) {
    /** Return creator UUIDs visible to the current admin. */
    function admin_visible_creator_uuids() {
        $CI =& get_instance();
        $admin_uuid = $CI->session->userdata('uuid');
        $visible_uuids = array($admin_uuid);

        if (user_role() !== 'admin' || empty($admin_uuid)) {
            return $visible_uuids;
        }

        $CI->db->select('uuid');
        $CI->db->where('role_id', 3);
        $CI->db->where('created_by', $admin_uuid);
        $teachers = $CI->db->get('users')->result();
        foreach ($teachers as $teacher) {
            $visible_uuids[] = $teacher->uuid;
        }

        return array_values(array_unique($visible_uuids));
    }
}

if (!function_exists('admin_can_access_creator')) {
    /** Check whether the current admin owns a record or manages its creator. */
    function admin_can_access_creator($creator_uuid) {
        $role = user_role();
        if ($role === 'superadmin') {
            return true;
        }

        if ($role !== 'admin') {
            return $creator_uuid === get_instance()->session->userdata('uuid');
        }

        return in_array($creator_uuid, admin_visible_creator_uuids(), true);
    }
}

if (!function_exists('apply_admin_creator_scope')) {
    /** Apply creator ownership filtering to the current query builder. */
    function apply_admin_creator_scope($column) {
        if (user_role() === 'admin') {
            $CI =& get_instance();
            $admin_uuid = $CI->db->escape($CI->session->userdata('uuid'));
            $condition = '(' . $column . ' = ' . $admin_uuid
                . ' OR ' . $column . ' IN (SELECT uuid FROM users WHERE role_id = 3 AND created_by = ' . $admin_uuid . '))';
            $CI->db->where($condition, NULL, FALSE);
        }
    }
}