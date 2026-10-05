<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PropertyDocumentModel;
use App\Models\PropertyModel;
use App\Models\ProjectModel;
use App\Models\PropertyOwnerModel;
use App\Models\AuditLogModel;

class PropertyDocumentController extends BaseController
{
    protected $documentModel;
    protected $propertyModel;
    protected $projectModel;
    protected $ownerModel;
    protected $auditModel;

    public function __construct()
    {
        $this->documentModel = new PropertyDocumentModel();
        $this->propertyModel = new PropertyModel();
        $this->projectModel  = new ProjectModel();
        $this->ownerModel    = new PropertyOwnerModel();
        $this->auditModel    = new AuditLogModel();
    }

    public function index()
    {
        $category = $this->request->getGet('category');
        $status   = $this->request->getGet('status');
        $search   = trim($this->request->getGet('search') ?? '');

        $builder = $this->documentModel->select('property_documents.*, properties.title as property_title, projects.name as project_name')
            ->join('properties', 'properties.id = property_documents.property_id', 'left')
            ->join('projects', 'projects.id = property_documents.project_id', 'left');

        if ($category) {
            $builder->where('property_documents.document_category', $category);
        }
        if ($status) {
            $builder->where('property_documents.verification_status', $status);
        }
        if ($search) {
            $builder->groupStart()
                ->like('property_documents.title', $search)
                ->orLike('property_documents.document_code', $search)
                ->orLike('property_documents.file_name', $search)
                ->groupEnd();
        }

        $documents = $builder->where('property_documents.deleted_at IS NULL')
            ->orderBy('property_documents.id', 'DESC')
            ->get()->getResultArray();

        $totalDocs    = $this->documentModel->countAllResults();
        $verifiedDocs = $this->documentModel->where('verification_status', 'Verified')->countAllResults();
        $pendingDocs  = $this->documentModel->where('verification_status', 'Pending')->countAllResults();

        return view('documents/index', [
            'title'        => 'Legal & Property Document Management',
            'documents'    => $documents,
            'category'     => $category,
            'status'       => $status,
            'search'       => $search,
            'totalDocs'    => $totalDocs,
            'verifiedDocs' => $verifiedDocs,
            'pendingDocs'  => $pendingDocs,
        ]);
    }

    public function create()
    {
        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();
        $projects   = $this->projectModel->findAll();
        $owners     = $this->ownerModel->where('deleted_at IS NULL')->findAll();

        return view('documents/create', [
            'title'      => 'Upload Compliance Document',
            'properties' => $properties,
            'projects'   => $projects,
            'owners'     => $owners,
        ]);
    }

    public function store()
    {
        $rules = [
            'title'             => 'required|min_length[3]|max_length[200]',
            'document_category' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $filePath = 'uploads/documents/sample_document.pdf';
        $fileName = 'document.pdf';
        $fileSize = 102400;
        $mimeType = 'application/pdf';

        $file = $this->request->getFile('document_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = WRITEPATH . 'uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = $file->getClientName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            $newName  = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $filePath = 'writable/uploads/documents/' . $newName;
        }

        $code = $this->documentModel->generateDocumentCode();
        $userId = session()->get('user_id') ?? 1;

        $data = [
            'document_code'       => $code,
            'title'               => trim($this->request->getPost('title')),
            'document_category'   => $this->request->getPost('document_category'),
            'property_id'         => $this->request->getPost('property_id') ?: null,
            'project_id'          => $this->request->getPost('project_id') ?: null,
            'owner_id'            => $this->request->getPost('owner_id') ?: null,
            'file_path'           => $filePath,
            'file_name'           => $fileName,
            'file_size'           => $fileSize,
            'mime_type'           => $mimeType,
            'issue_date'          => $this->request->getPost('issue_date') ?: null,
            'expiry_date'         => $this->request->getPost('expiry_date') ?: null,
            'verification_status' => $this->request->getPost('verification_status') ?? 'Pending',
            'notes'               => trim($this->request->getPost('notes') ?? ''),
            'uploaded_by'         => $userId,
        ];

        $docId = $this->documentModel->insert($data);

        $this->auditModel->log(
            'DOCUMENT_UPLOADED',
            "Property Document {$code} ({$data['title']}) uploaded under {$data['document_category']}",
            'property_documents',
            $docId
        );

        return redirect()->to('/documents')->with('success', "Document {$code} uploaded successfully.");
    }

    public function show($id)
    {
        $document = $this->documentModel->select('property_documents.*, properties.title as property_title, projects.name as project_name, property_owners.first_name as owner_fname, property_owners.last_name as owner_lname')
            ->join('properties', 'properties.id = property_documents.property_id', 'left')
            ->join('projects', 'projects.id = property_documents.project_id', 'left')
            ->join('property_owners', 'property_owners.id = property_documents.owner_id', 'left')
            ->where('property_documents.id', $id)
            ->first();

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        return view('documents/view', [
            'title'    => "Document: {$document['title']} ({$document['document_code']})",
            'document' => $document,
        ]);
    }

    public function edit($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $properties = $this->propertyModel->where('deleted_at IS NULL')->findAll();
        $projects   = $this->projectModel->findAll();
        $owners     = $this->ownerModel->where('deleted_at IS NULL')->findAll();

        return view('documents/edit', [
            'title'      => "Edit Document: {$document['document_code']}",
            'document'   => $document,
            'properties' => $properties,
            'projects'   => $projects,
            'owners'     => $owners,
        ]);
    }

    public function update($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $rules = [
            'title'             => 'required|min_length[3]|max_length[200]',
            'document_category' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'             => trim($this->request->getPost('title')),
            'document_category' => $this->request->getPost('document_category'),
            'property_id'       => $this->request->getPost('property_id') ?: null,
            'project_id'        => $this->request->getPost('project_id') ?: null,
            'owner_id'          => $this->request->getPost('owner_id') ?: null,
            'issue_date'        => $this->request->getPost('issue_date') ?: null,
            'expiry_date'       => $this->request->getPost('expiry_date') ?: null,
            'notes'             => trim($this->request->getPost('notes') ?? ''),
        ];

        $this->documentModel->update($id, $data);

        $this->auditModel->log(
            'DOCUMENT_UPDATED',
            "Property Document {$document['document_code']} updated",
            'property_documents',
            $id
        );

        return redirect()->to('/documents/view/' . $id)->with('success', 'Document updated successfully.');
    }

    public function verify($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $status = $this->request->getPost('verification_status');
        $reason = trim($this->request->getPost('rejection_reason') ?? '');
        $userId = session()->get('user_id') ?? 1;

        $this->documentModel->update($id, [
            'verification_status' => $status,
            'verified_by'         => $userId,
            'verified_at'         => date('Y-m-d H:i:s'),
            'rejection_reason'    => $reason ?: null,
        ]);

        $this->auditModel->log(
            'DOCUMENT_VERIFICATION',
            "Property Document {$document['document_code']} status updated to {$status}",
            'property_documents',
            $id
        );

        return redirect()->to('/documents/view/' . $id)->with('success', "Document status updated to {$status}.");
    }

    public function delete($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found.');
        }

        $this->documentModel->delete($id);

        $this->auditModel->log(
            'DOCUMENT_DELETED',
            "Property Document {$document['document_code']} deleted",
            'property_documents',
            $id
        );

        return redirect()->to('/documents')->with('success', "Document {$document['document_code']} deleted successfully.");
    }
}
