<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyMediaModel;
use App\Models\PropertyModel;
use App\Models\AuditLogModel;

class PropertyMediaController extends BaseController
{
    protected $mediaModel;
    protected $propertyModel;

    public function __construct()
    {
        $this->mediaModel    = new PropertyMediaModel();
        $this->propertyModel = new PropertyModel();
    }

    /**
     * Upload media file for a property
     */
    public function upload()
    {
        $propertyId = (int)$this->request->getPost('property_id');
        $property   = $this->propertyModel->find($propertyId);

        if (!$property) {
            return redirect()->back()->with('error', 'Property not found.');
        }

        $mediaType = $this->request->getPost('media_type') ?: 'photo';
        $title     = trim($this->request->getPost('title') ?? '');

        $file = $this->request->getFile('media_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Please provide a valid file to upload.');
        }

        // Validate MIME & Extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4', 'doc', 'docx'];
        $ext = strtolower($file->getExtension());

        if (!in_array($ext, $allowedExtensions, true)) {
            return redirect()->back()->with('error', "Invalid file format. Allowed extensions: " . implode(', ', $allowedExtensions));
        }

        // Max 20MB
        if ($file->getSizeByUnit('mb') > 20) {
            return redirect()->back()->with('error', 'File size exceeds 20MB limit.');
        }

        // Generate safe random name
        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH . 'uploads/properties';

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file->move($uploadPath, $newName);

        $mediaData = [
            'property_id' => $propertyId,
            'media_type'  => $mediaType,
            'file_name'   => $file->getClientName(),
            'file_path'   => $newName,
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getClientMimeType(),
            'title'       => $title ?: $file->getClientName(),
            'description' => trim($this->request->getPost('description') ?? ''),
            'is_primary'  => $this->request->getPost('is_primary') ? 1 : 0,
        ];

        // If marked as primary, reset others
        if ($mediaData['is_primary'] === 1) {
            $this->mediaModel->where('property_id', $propertyId)->set(['is_primary' => 0])->update();
        }

        $mediaId = $this->mediaModel->insert($mediaData);

        AuditLogModel::record(
            'MEDIA_UPLOADED',
            'Media',
            (int)$mediaId,
            "Uploaded {$mediaType} '{$mediaData['title']}' for property ID {$propertyId}"
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', 'Media file uploaded successfully.');
    }

    /**
     * Set a media item as primary photo
     */
    public function setPrimary($id)
    {
        $id = (int)$id;
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return redirect()->back()->with('error', 'Media item not found.');
        }

        $this->mediaModel->setPrimary($media['property_id'], $id);

        AuditLogModel::record(
            'MEDIA_SET_PRIMARY',
            'Media',
            $id,
            "Set media ID {$id} as primary image for property ID {$media['property_id']}"
        );

        return redirect()->to("/properties/view/{$media['property_id']}")->with('success', 'Primary photo updated.');
    }

    /**
     * Delete media item and its physical file
     */
    public function delete($id)
    {
        $id = (int)$id;
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return redirect()->back()->with('error', 'Media item not found.');
        }

        $propertyId = $media['property_id'];
        $filePath   = WRITEPATH . 'uploads/properties/' . basename($media['file_path']);

        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        $this->mediaModel->delete($id);

        AuditLogModel::record(
            'MEDIA_DELETED',
            'Media',
            $id,
            "Deleted media file '{$media['title']}' from property ID {$propertyId}"
        );

        return redirect()->to("/properties/view/{$propertyId}")->with('success', 'Media file removed successfully.');
    }

    /**
     * Safely serve stored media file
     */
    public function viewFile($id)
    {
        $id = (int)$id;
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return $this->response->setStatusCode(404, 'File not found');
        }

        // Prevent path traversal
        $safeFilename = basename($media['file_path']);
        $fullPath = WRITEPATH . 'uploads/properties/' . $safeFilename;

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return $this->response->setStatusCode(404, 'File on disk not found');
        }

        $mimeType = $media['mime_type'] ?: 'application/octet-stream';
        $content  = file_get_contents($fullPath);

        return $this->response
                    ->setContentType($mimeType)
                    ->setBody($content);
    }
}
