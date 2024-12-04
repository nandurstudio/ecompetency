<?php

namespace App\Models;

use CodeIgniter\Model;

class UserJobTitleModel extends Model
{
    protected $table            = 'trUser_JobTitle'; // Tabel asli di database
    protected $primaryKey       = 'intTrUserJobTitleID';
    protected $allowedFields    = [
        'intUserID',
        'intJobTitleID',
        'bitAchieved',
        'bitActive',
        'txtInsertedBy',
        'dtmInsertedDate',
        'txtUpdatedBy',
        'dtmUpdatedDate',
        'txtGUID'
    ];

    public function getUserJobTitles($start, $length, $searchValue, $orderBy, $orderDirection)
    {
        $builder = $this->db->table('trUser_JobTitle')
        ->select('trUser_JobTitle.*, mUser.txtUserName, mUser.txtFullName, mJobTitle.txtJobTitle')
        ->join('mUser', 'mUser.intUserID = trUser_JobTitle.intUserID', 'left')
        ->join('mJobTitle', 'mJobTitle.intJobTitleID = trUser_JobTitle.intJobTitleID', 'left');

        // Tambahkan pencarian jika diperlukan
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('mUser.txtUserName', $searchValue)
                ->orLike('mUser.txtFullName', $searchValue)
                ->orLike('mJobTitle.txtJobTitle', $searchValue)
                ->orLike('trUser_JobTitle.intUserID', $searchValue)
                ->orLike('trUser_JobTitle.bitActive', $searchValue) // Menambahkan pencarian untuk bitActive
                ->groupEnd();
        }

        // Tambahkan sorting
        $builder->orderBy($orderBy, $orderDirection);

        // Batasi jumlah data yang dikembalikan
        $builder->limit($length, $start);
        
        // Eksekusi query dan kembalikan hasilnya
        return $builder->get()->getResultArray();
    }

    public function countAllUserJobTitles($searchValue = null)
    {
        $builder = $this->table($this->table);

        if ($searchValue) {
            $builder->like('intUserID', $searchValue)
                ->orLike('intJobTitleID', $searchValue); // Ganti dengan kolom yang ingin dihitung
        }

        return $builder->countAllResults();
    }

    public function getAllUserJobTitles()
    {
        return $this->findAll(); // Mengambil semua data dari mCompetencies
    }
}
