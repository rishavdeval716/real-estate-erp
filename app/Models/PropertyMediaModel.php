<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyMediaModel extends Model
{
    protected $table            = 'property_media';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'media_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'title',
        'description',
        'is_primary',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_not_unique[properties.id]',
        'media_type'  => 'required|in_list[photo,gallery,video,floor_plan,brochure,virtual_tour,document]',
        'file_name'   => 'required|max_length[255]',
        'file_path'   => 'required|max_length[500]',
        'mime_type'   => 'required|max_length[100]',
    ];

    /**
     * Set a media item as the primary photo for a property
     */
    public function setPrimary(int $propertyId, int $mediaId): bool
    {
        $this->where('property_id', $propertyId)
             ->set(['is_primary' => 0])
             ->update();

        return (bool)$this->update($mediaId, ['is_primary' => 1]);
    }

    /**
     * Get media items grouped or filtered by type
     */
    public function getMediaForProperty(int $propertyId, ?string $mediaType = null): array
    {
        $builder = $this->where('property_id', $propertyId);

        if (!empty($mediaType)) {
            $builder->where('media_type', $mediaType);
        }

        return $builder->orderBy('is_primary', 'DESC')
                       ->orderBy('id', 'ASC')
                       ->findAll();
    }
}
