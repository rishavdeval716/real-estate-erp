<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompanyModel;
use App\Models\BranchModel;
use App\Models\AuditLogModel;

class CompanyController extends BaseController
{
    protected $companyModel;
    protected $branchModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
        $this->branchModel  = new BranchModel();
    }

    /**
     * View Corporate Company Profile
     */
    public function index()
    {
        $company  = $this->companyModel->getCompanyProfile();
        $branches = $this->branchModel->where('company_id', $company['id'])->findAll();

        $data = [
            'title'    => 'Corporate Profile - Real Estate ERP',
            'company'  => $company,
            'branches' => $branches,
        ];

        return view('company/index', $data);
    }

    /**
     * Show Edit Company Form
     */
    public function edit()
    {
        $company = $this->companyModel->getCompanyProfile();

        $data = [
            'title'   => 'Edit Corporate Profile - Real Estate ERP',
            'company' => $company,
        ];

        return view('company/edit', $data);
    }

    /**
     * Update Company details and optional Logo Upload
     */
    public function update()
    {
        $company = $this->companyModel->getCompanyProfile();
        $id      = (int) $company['id'];

        $rules = [
            'name'            => 'required|min_length[3]|max_length[150]',
            'email'           => 'permit_empty|valid_email|max_length[150]',
            'phone'           => 'permit_empty|max_length[30]',
            'alternate_phone' => 'permit_empty|max_length[30]',
            'website'         => 'permit_empty|valid_url_strict|max_length[255]',
            'tax_number'      => 'permit_empty|max_length[50]',
            'city'            => 'permit_empty|max_length[100]',
            'state'           => 'permit_empty|max_length[100]',
            'country'         => 'permit_empty|max_length[100]',
            'pincode'         => 'permit_empty|max_length[20]',
            'status'          => 'required|in_list[active,inactive]',
            'logo'            => 'permit_empty|is_image[logo]|mime_in[logo,image/jpg,image/jpeg,image/png,image/webp]|max_size[logo,2048]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'name'            => trim($this->request->getPost('name')),
            'email'           => trim($this->request->getPost('email')),
            'phone'           => trim($this->request->getPost('phone')),
            'alternate_phone' => trim($this->request->getPost('alternate_phone')),
            'website'         => trim($this->request->getPost('website')),
            'tax_number'      => trim($this->request->getPost('tax_number')),
            'address'         => trim($this->request->getPost('address')),
            'city'            => trim($this->request->getPost('city')),
            'state'           => trim($this->request->getPost('state')),
            'country'         => trim($this->request->getPost('country')),
            'pincode'         => trim($this->request->getPost('pincode')),
            'description'     => trim($this->request->getPost('description')),
            'status'          => $this->request->getPost('status'),
        ];

        // Process Logo File Upload
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/company/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newName = $logoFile->getRandomName();
            $logoFile->move($uploadDir, $newName);
            $updateData['logo'] = '/uploads/company/' . $newName;

            // Remove old logo if exists
            if (!empty($company['logo']) && file_exists(FCPATH . ltrim($company['logo'], '/'))) {
                @unlink(FCPATH . ltrim($company['logo'], '/'));
            }
        }

        $this->companyModel->update($id, $updateData);

        AuditLogModel::record(
            'COMPANY_UPDATED',
            'Company',
            $id,
            "Updated corporate profile for '{$updateData['name']}'"
        );

        return redirect()->to('/company')->with('success', 'Corporate profile updated successfully.');
    }
}
