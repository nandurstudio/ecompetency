<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\JobTitleModel;
use App\Models\LineModel;
use App\Models\DepartmentModel;
use App\Models\MenuModel;
use App\Helpers\Encrypt;
use CodeIgniter\Controller;

class UserController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        $roleID = session()->get('roleID');

        // Ambil menu berdasarkan role
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        // Set pagination parameters
        $perPage = 10; // Number of users per page
        $currentPage = $this->request->getVar('page') ?? 1; // Get current page, default to 1 if not set

        // Fetch users with pagination
        $users = $this->userModel->paginate($perPage, 'default', $currentPage);

        // Get pager object
        $pager = $this->userModel->pager;

        // Fetch Role names for each user
        $roleModel = new RoleModel();
        foreach ($users as &$user) {
            $user['txtRoleName'] = $roleModel->find($user['intRoleID'])['txtRoleName'];
        }

        return view('user/index', [
            'menus' => $menus,
            'users' => $users,
            'pager' => $pager, // Pass pager object to the view
            'pageTitle' => 'Daftar User',
            'pageSubTitle' => 'Menampilkan daftar user dan employee',
            'cardTitle' => 'Users',
            'icon' => 'users'
        ]);
    }

    // CREATE - Menampilkan form tambah user
    public function create()
    {
        return view('user/create');
    }

    // STORE - Proses penyimpanan user baru
    public function store()
    {
        $data = [
            'intRoleID'       => $this->request->getPost('intRoleID'),
            'intJobTitleID'   => $this->request->getPost('intJobTitleID'),
            'intSupervisorID' => $this->request->getPost('intSupervisorID'),
            'intLineID'       => $this->request->getPost('intLineID'),
            'intDepartmentID' => $this->request->getPost('intDepartmentID'),
            'txtUserName'     => $this->request->getPost('txtUserName'),
            'txtFullName'     => $this->request->getPost('txtFullName'),
            'txtNick'         => $this->request->getPost('txtNick', FILTER_SANITIZE_STRING) ?: 'DUM',  // Default value
            'txtEmpID'        => $this->request->getPost('txtEmpID'),
            'txtEmail'        => $this->request->getPost('txtEmail', FILTER_SANITIZE_EMAIL) ?: 'dummy@email.com', // Default value
            'txtPassword'     => Encrypt::encryptPassword($this->request->getPost('txtPassword')),
            'bitActive'       => $this->request->getPost('bitActive', FILTER_VALIDATE_BOOLEAN) ?: 1, // Default 1
            'txtInsertedBy'   => session()->get('userID'),
            'txtGUID'         => uniqid(),
            'txtPhoto'        => $this->request->getPost('txtPhoto') ?? 'default.jpg',
            'dtmJoinDate'     => $this->request->getPost('dtmJoinDate') ?: date('Y-m-d H:i:s'), // Default current date
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/login')->with('success', 'User created successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }

    // SHOW - Menampilkan detail user berdasarkan ID
    public function view($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        $roleID = session()->get('roleID');
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        $user = $this->userModel->find($id);

        if ($user) {
            // Ambil role dan supervisor berdasarkan ID
            $roleModel = new RoleModel(); // Misalnya ada model untuk role
            $role = $roleModel->find($user['intRoleID']); // Ambil nama role berdasarkan ID

            $supervisorName = $this->userModel->find($user['intSupervisorID']); // Ambil supervisor jika ada

            return view('user/view', [
                'menus' => $menus,
                'user' => $user,
                'role' => $role, // Pastikan role di-pass ke view
                'supervisorName' => $supervisorName ? $supervisorName['txtFullName'] : 'No Supervisor', // Jika supervisor ada
                'pageTitle' => 'Daftar User',
                'pageSubTitle' => 'Menampilkan daftar user dan employee',
                'cardTitle' => 'Users',
                'icon' => 'users'
            ]);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User with ID $id not found");
        }
    }

    // EDIT - Menampilkan form edit user
    public function edit($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        $roleID = session()->get('roleID');
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        // Fetch the required data for dropdowns
        $roleModel = new RoleModel();
        $roles = $roleModel->findAll();

        $jobTitleModel = new JobTitleModel();
        $jobTitles = $jobTitleModel->findAll();

        $userModel = new UserModel();
        $supervisors = $userModel->findAll();  // Get all users as supervisors

        $lineModel = new LineModel();
        $lines = $lineModel->findAll();

        $departmentModel = new DepartmentModel();
        $departments = $departmentModel->findAll();

        $user = $userModel->find($id);

        if ($user) {
            return view('user/update', [
                'menus' => $menus,
                'user' => $user,
                'roles' => $roles,
                'jobTitles' => $jobTitles,
                'supervisors' => $supervisors,
                'lines' => $lines,
                'departments' => $departments,
                'pageTitle' => 'Edit User',
                'pageSubTitle' => 'Edit data user dan informasi',
                'cardTitle' => 'Edit User',
                'icon' => 'edit'
            ]);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User with ID $id not found");
        }
    }

    // UPDATE - Proses update data user
    public function update($id = null)
    {
        $user = $this->userModel->find($id); // Ambil data user lama
        $photo = $this->request->getFile('txtPhoto');
        $newName = $user['txtPhoto']; // Default tetap pakai foto lama

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            // Path foto lama
            $oldPhotoPath = FCPATH . 'uploads/photos/' . $user['txtPhoto'];

            // Ambil NIK (txtEmpID) dan generate nama file baru
            $nik = $user['txtEmpID'];
            $datePrefix = date('dmy_His'); // Format tanggal dan waktu: ddmmyy_hhmmss
            $newName = $nik . '_' . $datePrefix . '.' . $photo->getExtension(); // Nama file baru

            // Pindahkan foto baru ke folder uploads
            $photo->move(FCPATH . 'uploads/photos', $newName);

            // Hapus foto lama jika ada dan bukan default
            if (!empty($user['txtPhoto']) && file_exists($oldPhotoPath) && $user['txtPhoto'] !== 'default.jpg') {
                unlink($oldPhotoPath); // Hapus file lama
            }
        }

        $data = [
            'intRoleID'       => $this->request->getVar('intRoleID'),
            'intJobTitleID'   => $this->request->getVar('intJobTitleID'),
            'intSupervisorID' => $this->request->getVar('intSupervisorID'),
            'intLineID'       => $this->request->getVar('intLineID'),
            'intDepartmentID' => $this->request->getVar('intDepartmentID'),
            'txtUserName'     => $this->request->getVar('txtUserName'),
            'txtFullName'     => $this->request->getVar('txtFullName'),
            'txtNick'         => $this->request->getVar('txtNick'),
            'txtEmpID'        => $this->request->getVar('txtEmpID'),
            'txtEmail'        => $this->request->getVar('txtEmail'),
            'txtUpdatedBy'    => session()->get('userID'),
            'bitActive'       => $this->request->getVar('bitActive') ? 1 : 0, // Handle bitActive checkbox
            'txtPhoto'        => $newName, // Simpan nama file baru
        ];

        // Handle join date (ensure correct format)
        $joinDate = $this->request->getVar('dtmJoinDate');
        if ($joinDate) {
            $data['dtmJoinDate'] = date('Y-m-d H:i:s', strtotime($joinDate)); // Format to 'YYYY-MM-DD HH:MM:SS'
        }

        // Handle password update jika ada
        $password = $this->request->getVar('txtPassword');
        if (!empty($password)) {
            $data['txtPassword'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update user data
        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/user')->with('success', 'User updated successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
    }
}
