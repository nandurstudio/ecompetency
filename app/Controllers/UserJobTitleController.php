<?php

namespace App\Controllers;

use App\Models\UserJobTitleModel;
use App\Models\JobTitleModel;
use CodeIgniter\Controller;
use App\Models\MenuModel;

class UserJobTitleController extends Controller
{
    protected $userJobTitleModel;

    public function __construct()
    {
        $this->userJobTitleModel = new UserJobTitleModel();
    }

    // Index (List Data)
    public function index()
    {
        // Redirect jika belum login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        $roleID = session()->get('roleID');

        // Ambil menu berdasarkan role
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        // Ambil data user job titles
        $userJobTitles = $this->userJobTitleModel
            ->select('trUser_JobTitle.*, mUser.txtFullName as userName, mJobTitle.txtJobTitle')
            ->join('mUser', 'mUser.intUserID = trUser_JobTitle.intUserID', 'left')
            ->join('mJobTitle', 'mJobTitle.intJobTitleID = trUser_JobTitle.intJobTitleID', 'left')
            ->findAll();

        // Group by intUserID
        $groupedUserJobTitles = [];
        foreach ($userJobTitles as $row) {
            $groupedUserJobTitles[$row['intUserID']][] = $row;
        }

        // Kirim data ke view
        $data = [
            'menus' => $menus,
            'groupedUserJobTitles' => $groupedUserJobTitles, // Pass grouped data
            'pageTitle' => 'Daftar User',
            'pageSubTitle' => 'Menampilkan daftar user dan employee',
            'cardTitle' => 'Users',
            'icon' => 'users',
            'scripts' => 'assets/js/pages/transactions/user_jobtitle.js' // Kirim nama file script
        ];

        // Render view
        return view('transactions/user_jobtitle/index', $data);
    }

    // Create (Form)
    public function create()
    {
        return view('transactions/user_jobtitle/create');
    }

    // Store Data
    public function store()
    {
        $this->userJobTitleModel->save([
            'intUserID'      => $this->request->getPost('intUserID'),
            'intJobTitleID'  => $this->request->getPost('intJobTitleID'),
            'bitAchieved'    => $this->request->getPost('bitAchieved'),
            'txtInsertedBy'  => session()->get('userName'),
            'txtGUID'        => uniqid(),
        ]);
        return redirect()->to('/transactions/user_jobtitle')->with('success', 'Data berhasil ditambahkan.');
    }

    // Details (Read)
    public function details($userJobTitleID)
    {
        if (!$userJobTitleID || !$this->userJobTitleModel->find($userJobTitleID)) {
            return redirect()->to('/transactions/user_jobtitle')->with('error', 'Invalid User ID.');
        }

        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        $roleID = session()->get('roleID');

        // Ambil menu berdasarkan role
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        $jobTitles = $this->userJobTitleModel
            ->select('trUser_JobTitle.*, mJobTitle.txtJobTitle, trUser_JobTitle.bitAchieved')
            ->join('mJobTitle', 'mJobTitle.intJobTitleID = trUser_JobTitle.intJobTitleID', 'left')
            ->where('trUser_JobTitle.intTrUserJobTitleID', $userJobTitleID)
            ->findAll();
        $achieved = array_filter($jobTitles, fn($item) => $item['bitAchieved']);
        $notAchieved = array_filter($jobTitles, fn($item) => !$item['bitAchieved']);

        // Kirim data ke view
        $data = [
            'menus' => $menus,
            'achieved' => $achieved,
            'notAchieved' => $notAchieved,
            'pageTitle' => 'Daftar User',
            'pageSubTitle' => 'Menampilkan daftar user dan employee',
            'cardTitle' => 'Users',
            'icon' => 'users'
        ];

        return view('transactions/user_jobtitle/details', $data);
    }

    public function edit($id)
    {
        // Ambil data User Job Title berdasarkan ID
        // $userJobTitle = $this->userJobTitleModel
        //     ->select('trUser_JobTitle.*, mUser.txtFullName as userName, mJobTitle.txtJobTitle, trUser_JobTitle.bitActive') // Tambahkan bitActive di sini
        //     ->join('mUser', 'mUser.intUserID = trUser_JobTitle.intUserID', 'left')
        //     ->join('mJobTitle', 'mJobTitle.intJobTitleID = trUser_JobTitle.intJobTitleID', 'left')
        //     ->where('trUser_JobTitle.intTrUserJobTitleID', $id)
        //     ->first();

        // Ambil data User Job Title berdasarkan ID, hanya kolom yang dibutuhkan
        $userJobTitle = $this->userJobTitleModel
            ->select('trUser_JobTitle.intTrUserJobTitleID, mUser.intUserID, trUser_JobTitle.bitActive, trUser_JobTitle.bitAchieved, mJobTitle.txtJobTitle, mUser.txtFullName as userName') // Pilih hanya kolom yang diperlukan
            ->join('mUser', 'mUser.intUserID = trUser_JobTitle.intUserID', 'left')
            ->join('mJobTitle', 'mJobTitle.intJobTitleID = trUser_JobTitle.intJobTitleID', 'left')
            ->where('trUser_JobTitle.intTrUserJobTitleID', $id)
            ->first();

        // Cek apakah data ditemukan
        if (!$userJobTitle) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Invalid User Job Title ID.']);
            }
            return redirect()->to('/transactions/user_jobtitle')->with('error', 'Invalid User Job Title ID.');
        }

        // Jika permintaan adalah AJAX, kembalikan data JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($userJobTitle);
        }

        // Jika bukan AJAX, proses untuk render view
        $roleID = session()->get('roleID');
        $menusModel = new MenuModel();
        $menus = $menusModel->getMenusByRole($roleID);

        $jobTitleModel = new JobTitleModel();
        $jobTitles = $jobTitleModel->findAll();

        $data = [
            'userJobTitle' => $userJobTitle,
            'jobTitles' => $jobTitles,
            'menus' => $menus,
            'pageTitle' => 'Edit User Job Title',
            'pageSubTitle' => 'Form untuk mengubah Job Title user',
            'cardTitle' => 'Edit Job Title',
            'icon' => 'edit'
        ];

        return view('transactions/user_jobtitle/edit', $data);
    }

    // Update Data
    public function update()
    {
        try {
            $request = $this->request->getJSON();

            // Validasi input
            if (!isset($request->intTrUserJobTitleID)) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request.']);
            }

            // Ambil ID dan data yang akan diupdate
            $id = $request->intTrUserJobTitleID;
            $data = [
                'bitAchieved' => $request->bitAchieved,
                'bitActive' => $request->bitActive,
                'txtUpdatedBy' => session()->get('userName'),
                'dtmUpdatedDate' => date('Y-m-d H:i:s'),
            ];

            // Update data di database
            $model = new UserJobTitleModel();
            if (!$model->update($id, $data)) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to update data.']);
            }

            // Return success response
            return $this->response->setJSON(['message' => 'Data successfully updated.']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function getUserJobTitles()
    {
        try {
            $model = new UserJobTitleModel();

            // Ambil parameter dari request
            $draw = (int)$this->request->getVar('draw');
            $start = (int)$this->request->getVar('start');
            $length = (int)$this->request->getVar('length');
            $searchValue = $this->request->getVar('search')['value'];

            // Ambil parameter order untuk sorting
            $order = $this->request->getVar('order');
            $orderColumnIndex = isset($order[0]['column']) ? (int)$order[0]['column'] : 0; // Default ke kolom pertama jika null
            $orderDirection = isset($order[0]['dir']) ? $order[0]['dir'] : 'asc'; // Default ke 'asc' jika null

            // Daftar kolom yang bisa diurutkan
            $columns = ['intTrUserJobTitleID', 'bitAchieved', 'bitActive', 'txtInsertedBy', 'dtmInsertedDate', 'txtUpdatedBy', 'dtmUpdatedDate'];

            // Tentukan kolom untuk pengurutan
            $orderBy = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'intTrUserJobTitleID';

            // Ambil data dari model
            $jobtitles = $model->getUserJobTitles(0, PHP_INT_MAX, $searchValue, $orderBy, $orderDirection); // Ambil semua data terlebih dahulu

            // Kelompokkan data berdasarkan intUserID
            foreach ($jobtitles as $row) {
                $userID = $row['intUserID'];
                if (!isset($userJobTitles[$userID])) {
                    $userJobTitles[$userID] = [
                        'intUserID' => $row['intUserID'],
                        'txtFullName' => $row['txtFullName'] ?? 'N/A', // Gunakan nilai default
                        'jobTitles' => [],
                        'txtInsertedBy' => $row['txtInsertedBy'] ?? 'Unknown',
                    ];
                }
                $userJobTitles[$userID]['jobTitles'][] = [
                    'title' => $row['txtJobTitle'],
                    'achieved' => $row['bitAchieved'] ? 'Yes' : 'No',
                    'jobTitleID' => $row['intTrUserJobTitleID'], // Tambahkan ID unik
                ];
            }

            // Format ulang data untuk DataTables
            $dataForTable = [];
            foreach ($userJobTitles as $user) {
                $dataForTable[] = [
                    'intUserID' => $user['intUserID'],
                    'txtFullName' => $user['txtFullName'],
                    'jobTitles' => $user['jobTitles'], // Kirim array jobTitles
                    'txtInsertedBy' => $user['txtInsertedBy'],
                ];
            }

            // Pagination setelah grouping
            $pagedData = array_slice($dataForTable, $start, $length);

            // Membuat respons
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => count($dataForTable),
                'recordsFiltered' => count($dataForTable),
                'data' => $pagedData, // Data sesuai pagination
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}
