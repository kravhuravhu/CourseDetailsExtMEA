<?php

namespace App\Http\Controllers\Api\Personnel;

use App\Http\Controllers\Api\BaseController;
use App\Models\Personnel\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\ApiKey;

class SkillController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->checkApiKey();
    }
    
    /**
     * Check API key authentication.
     */
    private function checkApiKey(): void
    {
        $apiKey = request()->header('X-API-Key') ?: request()->query('api_key');
        
        if (!$apiKey) {
            abort(401, 'API key is required. Please provide X-API-Key header or api_key query parameter.');
        }
        
        $keyRecord = ApiKey::findByKey($apiKey);
        
        if (!$keyRecord) {
            abort(401, 'Invalid API key.');
        }
        
        if (!$keyRecord->isValid()) {
            abort(401, 'API key is not active or has expired.');
        }
        
        // Mark as used
        $keyRecord->markAsUsed();
        
        // Store in request for logging
        request()->merge([
            'api_key_id' => $keyRecord->id,
            'api_key_name' => $keyRecord->name,
        ]);
        
        $this->apiKeyId = $keyRecord->id;
        $this->apiKeyName = $keyRecord->name;
    }
    
    /**
     * Get skills for a personnel.
     */
    public function index(Request $request, $personnelId): JsonResponse
    {
        try {
            $skills = Skill::where('ERP_PERSONNEL_ID', $personnelId)->get();
            
            $this->logAudit('LIST', "Retrieved skills for personnel ID: {$personnelId}");
            
            return $this->sendResponse($skills, 'Skills retrieved successfully.');
            
        } catch (Exception $e) {
            $this->logError('Failed to retrieve skills', $e);
            return $this->sendError('Error retrieving skills.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new skill for personnel.
     */
    public function store(Request $request, $personnelId): JsonResponse
    {
        try {
            $request->validate([
                'NAME' => 'required|string|max:200',
                'CATEGORY' => 'nullable|string|max:100',
                'DESCRIPTION' => 'nullable|string',
                'MRID' => 'nullable|string|max:100|unique:SKILL,MRID',
            ]);

            $skill = Skill::create(array_merge(
                $request->all(),
                ['ERP_PERSONNEL_ID' => $personnelId]
            ));

            $this->logAudit('CREATE', "Created skill for personnel ID: {$personnelId}");

            return $this->sendResponse($skill, 'Skill created successfully.', 201);

        } catch (Exception $e) {
            $this->logError('Failed to create skill', $e);
            return $this->sendError('Error creating skill.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a skill.
     */
    public function destroy($personnelId, $skillId): JsonResponse
    {
        try {
            $skill = Skill::where('ERP_PERSONNEL_ID', $personnelId)
                        ->where('SKILL_ID', $skillId)
                        ->first();

            if (!$skill) {
                return $this->sendError('Skill not found.', [], 404);
            }

            $skill->delete();

            $this->logAudit('DELETE', "Deleted skill ID: {$skillId}");

            return $this->sendResponse([], 'Skill deleted successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to delete skill', $e);
            return $this->sendError('Error deleting skill.', ['error' => $e->getMessage()], 500);
        }
    }
}