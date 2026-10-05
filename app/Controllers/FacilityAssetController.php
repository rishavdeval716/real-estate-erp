<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FacilityAssetModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class FacilityAssetController extends BaseController
{
    protected $assetModel;
    protected $propertyModel;
    protected $auditModel;

    public function __construct()
    {
        $this->assetModel    = new FacilityAssetModel();
        $this->propertyModel = new PropertyModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $category   = trim($this->request->getGet('category') ?? '');
        $status     = trim($this->request->getGet('status') ?? '');
        $propertyId = (int)$this->request->getGet('property_id');
        $search     = trim($this->request->getGet('search') ?? '');

        $builder = $this->assetModel->select('facility_assets.*, p.title as property_title, p.property_code')
            ->join('properties p', 'p.id = facility_assets.property_id');

        if ($category !== '') {
            $builder->where('facility_assets.category', $category);
        }
        if ($status !== '') {
            $builder->where('facility_assets.status', $status);
        }
        if ($propertyId > 0) {
            $builder->where('facility_assets.property_id', $propertyId);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('facility_assets.name', $search)
                ->orLike('facility_assets.asset_code', $search)
                ->orLike('facility_assets.location_details', $search)
                ->orLike('p.title', $search)
                ->groupEnd();
        }

        $assets = $builder->orderBy('facility_assets.id', 'DESC')->paginate(15);
        $pager  = $this->assetModel->pager;

        $db = \Config\Database::connect();
        $kpi = [
            'total'             => $db->table('facility_assets')->countAllResults(),
            'operational'       => $db->table('facility_assets')->where('status', 'operational')->countAllResults(),
            'under_maintenance' => $db->table('facility_assets')->where('status', 'under_maintenance')->countAllResults(),
            'decommissioned'    => $db->table('facility_assets')->where('status', 'decommissioned')->countAllResults(),
        ];

        $properties = $this->propertyModel->findAll();

        return view('maintenance/asset_index', [
            'title'      => 'Facility Assets Directory - Real Estate ERP',
            'assets'     => $assets,
            'pager'      => $pager,
            'filters'    => ['category' => $category, 'status' => $status, 'property_id' => $propertyId, 'search' => $search],
            'kpi'        => $kpi,
            'properties' => $properties,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'category'    => 'required|in_list[elevators,generators,fire_safety,water_treatment,electrical,hvac,other]',
            'property_id' => 'required|is_natural_no_zero',
            'status'      => 'required|in_list[operational,under_maintenance,decommissioned]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $code   = $this->assetModel->generateAssetCode();
        $userId = session()->get('user_id');

        $data = [
            'asset_code'        => $code,
            'name'              => trim($this->request->getPost('name')),
            'category'          => $this->request->getPost('category'),
            'property_id'       => (int)$this->request->getPost('property_id'),
            'location_details'  => trim($this->request->getPost('location_details') ?? '') ?: null,
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expiry'   => $this->request->getPost('warranty_expiry') ?: null,
            'status'            => $this->request->getPost('status') ?? 'operational',
        ];

        $assetId = $this->assetModel->insert($data);

        $this->auditModel->record(
            $userId,
            'ASSET_CREATED',
            'FacilityAssets',
            $assetId,
            "Registered facility asset {$code} - {$data['name']}"
        );

        return redirect()->to(base_url('facility-assets'))->with('success', "Asset {$code} created successfully.");
    }

    public function update(int $id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to(base_url('facility-assets'))->with('error', 'Asset not found.');
        }

        $rules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'category' => 'required|in_list[elevators,generators,fire_safety,water_treatment,electrical,hvac,other]',
            'status'   => 'required|in_list[operational,under_maintenance,decommissioned]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'              => trim($this->request->getPost('name')),
            'category'          => $this->request->getPost('category'),
            'property_id'       => (int)$this->request->getPost('property_id') ?: $asset['property_id'],
            'location_details'  => trim($this->request->getPost('location_details') ?? '') ?: null,
            'installation_date' => $this->request->getPost('installation_date') ?: null,
            'warranty_expiry'   => $this->request->getPost('warranty_expiry') ?: null,
            'status'            => $this->request->getPost('status'),
        ];

        $this->assetModel->update($id, $data);

        $this->auditModel->record(
            session()->get('user_id'),
            'ASSET_UPDATED',
            'FacilityAssets',
            $id,
            "Updated facility asset {$asset['asset_code']}"
        );

        return redirect()->to(base_url('facility-assets'))->with('success', "Asset {$asset['asset_code']} updated.");
    }

    public function delete(int $id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to(base_url('facility-assets'))->with('error', 'Asset not found.');
        }

        $this->assetModel->delete($id);

        $this->auditModel->record(
            session()->get('user_id'),
            'ASSET_DELETED',
            'FacilityAssets',
            $id,
            "Deleted facility asset {$asset['asset_code']}"
        );

        return redirect()->to(base_url('facility-assets'))->with('success', "Asset {$asset['asset_code']} deleted.");
    }
}
