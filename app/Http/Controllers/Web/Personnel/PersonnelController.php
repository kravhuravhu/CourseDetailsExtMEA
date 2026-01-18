<?php

namespace App\Http\Controllers\Web\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonnelController extends Controller
{
    /**
     * Display a listing of personnel.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Personnel::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('erpPersonnel.erpPerson', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('mrid', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->input('status') === 'active') {
                $query->whereHas('erpPersonnel', function ($q) {
                    $q->whereNull('finish_date')
                      ->orWhere('finish_date', '>', now());
                });
            } elseif ($request->input('status') === 'inactive') {
                $query->whereHas('erpPersonnel', function ($q) {
                    $q->whereNotNull('finish_date')
                      ->where('finish_date', '<=', now());
                });
            }
        }

        // Filter by date
        if ($request->has('start_date')) {
            $query->where('created_at', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('created_at', '<=', $request->input('end_date') . ' 23:59:59');
        }

        $personnel = $query->with(['erpPersonnel.erpPerson'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('personnel.index', compact('personnel'));
    }

    /**
     * Show the form for creating new personnel.
     *
     * @return View
     */
    public function create(): View
    {
        return view('personnel.create');
    }

    /**
     * Store a newly created personnel.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'mrid' => 'required|string|max:255|unique:erp_persons,mrid',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date|after:start_date',
        ]);

        try {
            // Create Personnel
            $personnel = Personnel::create();

            // Create ErpPersonnel
            $erpPersonnel = ErpPersonnel::create([
                'personnel_id' => $personnel->id,
                'start_date' => $request->input('start_date'),
                'finish_date' => $request->input('finish_date'),
            ]);

            // Create ErpPerson
            ErpPerson::create([
                'erp_personnel_id' => $erpPersonnel->id,
                'mrid' => $request->input('mrid'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
            ]);

            return redirect()->route('personnel.show', $personnel->id)
                ->with('success', 'Personnel created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating personnel: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified personnel.
     *
     * @param int $id
     * @return View
     */
    public function show($id): View
    {
        $personnel = Personnel::with([
            'erpPersonnel.erpPerson',
            'erpPersonnel.accessCards',
            'erpPersonnel.crafts',
            'erpPersonnel.competencies',
            'erpPersonnel.skills',
            'erpPersonnel.organisations',
            'erpPersonnel.locations'
        ])->findOrFail($id);

        return view('personnel.show', compact('personnel'));
    }

    /**
     * Show the form for editing personnel.
     *
     * @param int $id
     * @return View
     */
    public function edit($id): View
    {
        $personnel = Personnel::with(['erpPersonnel.erpPerson'])->findOrFail($id);
        return view('personnel.edit', compact('personnel'));
    }

    /**
     * Update the specified personnel.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $personnel = Personnel::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'mrid' => 'required|string|max:255|unique:erp_persons,mrid,' . $personnel->erpPersonnel->erpPerson->id,
            'start_date' => 'nullable|date',
            'finish_date' => 'nullable|date|after:start_date',
        ]);

        try {
            // Update ErpPersonnel
            $personnel->erpPersonnel->update([
                'start_date' => $request->input('start_date'),
                'finish_date' => $request->input('finish_date'),
            ]);

            // Update ErpPerson
            $personnel->erpPersonnel->erpPerson->update([
                'mrid' => $request->input('mrid'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
            ]);

            return redirect()->route('personnel.show', $personnel->id)
                ->with('success', 'Personnel updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating personnel: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified personnel.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $personnel = Personnel::findOrFail($id);

        try {
            $personnel->delete();
            return redirect()->route('personnel.index')
                ->with('success', 'Personnel deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting personnel: ' . $e->getMessage());
        }
    }

    /**
     * Export personnel data.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $personnel = Personnel::with(['erpPersonnel.erpPerson'])->get();
        
        $csvData = [];
        $csvData[] = ['ID', 'mRID', 'First Name', 'Last Name', 'Start Date', 'Finish Date', 'Status', 'Created At'];

        foreach ($personnel as $person) {
            $csvData[] = [
                $person->id,
                $person->erpPersonnel->erpPerson->mrid ?? '',
                $person->erpPersonnel->erpPerson->first_name ?? '',
                $person->erpPersonnel->erpPerson->last_name ?? '',
                $person->erpPersonnel->start_date ?? '',
                $person->erpPersonnel->finish_date ?? '',
                $person->erpPersonnel->finish_date && $person->erpPersonnel->finish_date < now() ? 'Inactive' : 'Active',
                $person->created_at,
            ];
        }

        $filename = 'personnel_export_' . date('Y-m-d_H-i-s') . '.csv';
        $file = storage_path('app/exports/' . $filename);
        
        $fp = fopen($file, 'w');
        foreach ($csvData as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        return response()->download($file)->deleteFileAfterSend(true);
    }
}