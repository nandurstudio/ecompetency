<?php

namespace App\Controllers;

use App\Models\MenuModel;


class Home extends BaseController
{
    public function index()
    {
        $menuModel = new MenuModel();

        // Ambil role dari session, misal session role ID tersimpan sebagai 'role_id'
        $intRoleID = session()->get('roleID');

        // Ambil menu berdasarkan role
        $menus = $menuModel->getMenusByRole($intRoleID);

        // Debugging: Cek data menu
        // print_r($menus); // Tambahkan ini
        // exit(); // Berhenti sementara untuk melihat hasil output

        return view('dashboard', [
            'menus' => $menus,
            'title' => 'Dashboard'
        ]);
    }
}
