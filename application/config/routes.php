<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['ganti_password'] = 'auth/ganti_password';
$route['reset_password/(:any)'] = 'auth/reset_password/$1';

// Exam routes
$route['ujian/tambah_sub/(:any)'] = 'ujian/tambah_sub/$1';
$route['ujian/tambah_soal/(:any)'] = 'ujian/tambah_soal/$1';
$route['ujian/tambah_kelas/(:any)'] = 'ujian/tambah_kelas/$1';
$route['ujian/get_soal/(:any)'] = 'ujian/get_soal_by_uuid/$1';
$route['ujian/bulk_hapus_soal'] = 'ujian/bulk_hapus_soal';
$route['ujian/upload_editor_file'] = 'ujian/upload_editor_file';

// Materi routes
$route['mata_pelajaran'] = 'materi';
$route['mata_pelajaran/detail/(:any)'] = 'materi/detail/$1';
$route['mata_pelajaran/hapus/(:any)'] = 'materi/hapus/$1';

// Bab routes
$route['bab/tambah/(:any)'] = 'materi/tambah/$1';
$route['bab/edit/(:any)'] = 'materi/edit/$1';

// Bab/Sub Bab routes
$route['sub_bab/index/(:any)'] = 'bab/index/$1';
$route['sub_bab/tambah/(:any)'] = 'bab/tambah/$1';
$route['sub_bab/tambah_ujian/(:any)'] = 'bab/tambah_ujian/$1';
$route['sub_bab/edit/(:any)'] = 'bab/edit/$1';
$route['sub_bab/hapus/(:any)'] = 'bab/hapus/$1';