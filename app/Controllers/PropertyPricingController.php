<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyPricingModel;
use App\Models\PropertyModel;
use App\Models\PropertyUnitModel;
use App\Models\AuditLogModel;

class PropertyPricingController extends BaseController
{
    protected $pricingModel;
    protected $propertyModel;
    protected $unitModel;

    public function __construct()
    {
        $this->pricingModel  = new PropertyPricingModel();
        $this->propertyModel = new PropertyModel();
        $this->unitModel     = new PropertyUnitModel();
    }

    /**
     * View pricing overview and history across properties
     */
    public function index()
    {
        $perPage = 15;
        $pricings = $this->pricingModel
                         ->select('
                             property_pricing.*,
                             properties.title as property_title,
                             properties.property_code,
                             properties.area as property_area,
                             property_units.unit_number
                         ')
                         ->join('properties', 'properties.id = property_pricing.property_id', 'left')
                         ->join('property_units', 'property_units.id = property_pricing.unit_id', 'left')
                         ->orderBy('property_pricing.id', 'DESC')
                         ->paginate($perPage);

        $pager = $this->pricingModel->pager;

        $data = [
            'title'    => 'Property Pricing & Valuation - Real Estate ERP',
            'pricings' => $pricings,
            'pager'    => $pager,
        ];

        return view('pricing/index', $data);
    }

    /**
     * Show form to add new pricing / valuation revision
     */
    public function create()
    {
        $propertyId = (int)($this->request->getGet('property_id') ?? 0) ?: null;
        $unitId     = (int)($this->request->getGet('unit_id') ?? 0) ?: null;

        $properties = $this->propertyModel->orderBy('title', 'ASC')->findAll();
        $units      = $propertyId ? $this->unitModel->where('property_id', $propertyId)->findAll() : [];

        $selectedProperty = $propertyId ? $this->propertyModel->find($propertyId) : null;
        $selectedUnit     = $unitId ? $this->unitModel->find($unitId) : null;

        $data = [
            'title'            => 'Record Pricing & Valuation - Real Estate ERP',
            'properties'       => $properties,
            'units'            => $units,
            'selectedProperty' => $selectedProperty,
            'selectedUnit'     => $selectedUnit,
            'propertyId'       => $propertyId,
            'unitId'           => $unitId,
        ];

        return view('pricing/create', $data);
    }

    /**
     * Store new pricing record
     */
    public function store()
    {
        $rules = [
            'property_id'      => 'required|is_not_unique[properties.id]',
            'unit_id'          => 'permit_empty|is_not_unique[property_units.id]',
            'base_price'       => 'required|numeric|greater_than_equal_to[0]',
            'price_per_sqft'   => 'permit_empty|numeric|greater_than_equal_to[0]',
            'market_price'     => 'permit_empty|numeric|greater_than_equal_to[0]',
            'negotiated_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'discount'         => 'permit_empty|numeric|greater_than_equal_to[0]',
            'effective_from'   => 'permit_empty|valid_date',
            'effective_to'     => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $propertyId = (int)$this->request->getPost('property_id');
        $unitId     = $this->request->getPost('unit_id') ? (int)$this->request->getPost('unit_id') : null;
        $basePrice  = (float)$this->request->getPost('base_price');

        $pricePerSqft = $this->request->getPost('price_per_sqft') !== null && $this->request->getPost('price_per_sqft') !== ''
            ? (float)$this->request->getPost('price_per_sqft')
            : null;

        // Auto calculate price per sq ft if empty
        if ($pricePerSqft === null || $pricePerSqft == 0) {
            if ($unitId) {
                $unit = $this->unitModel->find($unitId);
                if ($unit && (float)$unit['built_up_area'] > 0) {
                    $pricePerSqft = round($basePrice / (float)$unit['built_up_area'], 2);
                }
            } else {
                $prop = $this->propertyModel->find($propertyId);
                if ($prop && (float)$prop['area'] > 0) {
                    $pricePerSqft = round($basePrice / (float)$prop['area'], 2);
                }
            }
        }

        $pricingData = [
            'property_id'      => $propertyId,
            'unit_id'          => $unitId,
            'base_price'       => $basePrice,
            'price_per_sqft'   => $pricePerSqft,
            'market_price'     => $this->request->getPost('market_price') ? (float)$this->request->getPost('market_price') : null,
            'negotiated_price' => $this->request->getPost('negotiated_price') ? (float)$this->request->getPost('negotiated_price') : null,
            'discount'         => (float)($this->request->getPost('discount') ?? 0),
            'effective_from'   => $this->request->getPost('effective_from') ?: date('Y-m-d'),
            'effective_to'     => $this->request->getPost('effective_to') ?: null,
            'remarks'          => trim($this->request->getPost('remarks') ?? ''),
        ];

        $pricingId = $this->pricingModel->insert($pricingData);

        if (!$pricingId) {
            return redirect()->back()->withInput()->with('error', 'Failed to record pricing.');
        }

        // Update the current property or unit price if requested
        if ($this->request->getPost('update_primary_price')) {
            if ($unitId) {
                $this->unitModel->update($unitId, ['unit_price' => $basePrice]);
            } else {
                $this->propertyModel->update($propertyId, ['price' => $basePrice]);
            }
        }

        AuditLogModel::record(
            'PRICING_CREATED',
            'Pricing',
            (int)$pricingId,
            "Recorded pricing for property ID {$propertyId}: Base Price " . number_format($basePrice, 2)
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', 'Pricing valuation schedule saved successfully.');
    }

    /**
     * Delete a pricing record
     */
    public function delete($id)
    {
        $id = (int)$id;
        $pricing = $this->pricingModel->find($id);

        if (!$pricing) {
            return redirect()->back()->with('error', 'Pricing record not found.');
        }

        $propertyId = $pricing['property_id'];
        $this->pricingModel->delete($id);

        AuditLogModel::record(
            'PRICING_DELETED',
            'Pricing',
            $id,
            "Deleted historical pricing record ID {$id} for property ID {$propertyId}"
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', 'Pricing record removed.');
    }
}
