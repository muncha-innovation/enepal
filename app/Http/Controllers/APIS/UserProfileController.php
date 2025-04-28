<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserProfileController extends Controller
{
    /**
     * Get the authenticated user's work experiences
     */
    public function getWorkExperiences()
    {
        $experiences = auth()->user()->workExperience;
        return response()->json([
            'success' => true,
            'data' => $experiences
        ]);
    }

    /**
     * Get specific work experience item
     */
    public function getWorkExperienceItem($id)
    {
        try {
            $experience = auth()->user()->workExperience()->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $experience
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Work experience not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Add or update work experience
     */
    public function updateWorkExperience(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'nullable|exists:user_experiences,id',
                'job_title' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'description' => 'nullable|string',
            ]);

            $user = auth()->user();
            
            if (isset($data['id']) && !empty($data['id'])) {
                $experience = $user->workExperience()->findOrFail($data['id']);
                $experience->update($data);
                $message = 'Work experience updated successfully';
            } else {
                $experience = $user->workExperience()->create($data);
                $message = 'Work experience added successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $experience
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update work experience: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete work experience
     */
    public function deleteWorkExperience($id)
    {
        try {
            $experience = auth()->user()->workExperience()->findOrFail($id);
            $experience->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Work experience deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete work experience'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get the authenticated user's education records
     */
    public function getEducations()
    {
        $education = auth()->user()->education;
        return response()->json([
            'success' => true,
            'data' => $education
        ]);
    }

    /**
     * Get specific education item
     */
    public function getEducationItem($id)
    {
        try {
            $education = auth()->user()->education()->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $education
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Add or update education record
     */
    public function updateEducation(Request $request)
    {
        try {
            $data = $request->validate([
                'id' => 'nullable|exists:user_education,id',
                'degree' => 'nullable|string|max:255',
                'institution' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'type' => 'required|in:under_slc,slc,plus_two,bachelors,masters,phd,training',
            ]);

            $user = auth()->user();
            
            if (isset($data['id']) && !empty($data['id'])) {
                $education = $user->education()->findOrFail($data['id']);
                $education->update($data);
                $message = 'Education updated successfully';
            } else {
                $education = $user->education()->create($data);
                $message = 'Education added successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $education
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update education: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete education record
     */
    public function deleteEducation($id)
    {
        try {
            $education = auth()->user()->education()->findOrFail($id);
            $education->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Education deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete education record'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    
}