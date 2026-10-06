<?php

namespace App\Http\Controllers\Api\V1\Filing;

use App\Http\Controllers\Controller;
use App\Models\Filing;
use App\Models\FilingSubject;
use App\Models\FilingType;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\FilingVersionService;

class FilingController extends Controller
{
    public function __construct(
        protected ApiResponseService $apiResponse,
        protected FilingVersionService $filingVersionService
    ) {}

    /**
     * List filings
     */
    public function index(Request $request, $organisationId)
    {
        $query = Filing::with([
            'filingSubject',
            'filingType',
            'preparer',
        ])
            ->where('organisation_id', $organisationId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('filing_type_id')) {
            $query->where(
                'filing_type_id',
                $request->filing_type_id
            );
        }

        if ($request->filled('filing_subject_id')) {
            $query->where(
                'filing_subject_id',
                $request->filing_subject_id
            );
        }

        $filings = $query
            ->latest()
            ->paginate(
                $request->integer('per_page', 15)
            );

        return $this->apiResponse->success(
            $filings,
            'Filings retrieved successfully.'
        );
    }

    /**
     * Create filing
     */
    public function store(Request $request, $organisationId)
    {
        $validated = $request->validate([
            'filing_subject_id' => [
                'required',
                'integer',
                'exists:filing_subjects,id',
            ],

            'filing_type_id' => [
                'required',
                'integer',
                'exists:filing_types,id',
            ],
        ]);

        /*
        * Make sure the filing subject belongs
        * to the current organisation.
        */
        $subject = FilingSubject::where(
            'id',
            $validated['filing_subject_id']
        )
            ->where(
                'organisation_id',
                $organisationId
            )
            ->first();

        if (!$subject) {
            return $this->apiResponse->error(
                'Filing subject does not belong to this organisation.',
                422
            );
        }

        /*
        * Make sure filing type is active.
        */
        $filingType = FilingType::where(
            'id',
            $validated['filing_type_id']
        )
            ->where('is_active', true)
            ->first();

        if (!$filingType) {
            return $this->apiResponse->error(
                'Invalid or inactive filing type.',
                422
            );
        }

        $filing = DB::transaction(function () use (
            $organisationId,
            $validated,
            $request
        ) {

            /*
            * 1. Create Filing
            */
            $filing = Filing::create([
                'organisation_id' => $organisationId,
                'filing_subject_id' => $validated['filing_subject_id'],
                'filing_type_id' => $validated['filing_type_id'],
                'reference' => $this->generateReference(),
                'status' => 'draft',
                'prepared_by' => $request->user()->id,
            ]);

            /*
            * 2. Automatically create Filing Version 1
            *
            * This will also attach the currently
            * published Form Version.
            */
            $this->filingVersionService->create(
                $filing->id,
                $request->user()->id
            );

            return $filing;
        });

        /*
        * Load everything needed by the API response.
        */
        $filing->load([
            'filingSubject',
            'filingType',
            'preparer',
            'versions.formVersion.formDefinition',
        ]);

        return $this->apiResponse->success(
            'Filing created successfully.',
            $filing,
            201
        );
    }

    /**
     * Show filing
     */
    public function show($organisationId, $filingId)
    {
        $filing = Filing::with([
            'filingSubject',
            'filingType',
            'preparer',
        ])
            ->where('organisation_id', $organisationId)
            ->find($filingId);

        if (!$filing) {
            return $this->apiResponse->error(
                'Filing not found.',
                404
            );
        }

        return $this->apiResponse->success(
            $filing,
            'Filing retrieved successfully.'
        );
    }

    /**
     * Update filing
     */
    public function update(
        Request $request,
        $organisationId,
        $filingId
    ) {
        $filing = Filing::where(
            'organisation_id',
            $organisationId
        )->find($filingId);

        if (!$filing) {
            return $this->apiResponse->error(
                'Filing not found.',
                404
            );
        }

        /*
         * Only draft / in-review filings
         * can currently be edited.
         */
        if (!in_array(
            $filing->status,
            ['draft', 'in_review']
        )) {
            return $this->apiResponse->error(
                'Only draft or in-review filings can be updated.',
                422
            );
        }

        $validated = $request->validate([
            'filing_subject_id' => [
                'sometimes',
                'integer',
                'exists:filing_subjects,id',
            ],

            'filing_type_id' => [
                'sometimes',
                'integer',
                'exists:filing_types,id',
            ],
        ]);

        /*
         * Validate subject belongs
         * to organisation.
         */
        if (isset($validated['filing_subject_id'])) {

            $subjectExists = FilingSubject::where(
                'id',
                $validated['filing_subject_id']
            )
                ->where(
                    'organisation_id',
                    $organisationId
                )
                ->exists();

            if (!$subjectExists) {
                return $this->apiResponse->error(
                    'Filing subject does not belong to this organisation.',
                    422
                );
            }
        }

        /*
         * Validate filing type.
         */
        if (isset($validated['filing_type_id'])) {

            $typeExists = FilingType::where(
                'id',
                $validated['filing_type_id']
            )
                ->where('is_active', true)
                ->exists();

            if (!$typeExists) {
                return $this->apiResponse->error(
                    'Invalid or inactive filing type.',
                    422
                );
            }
        }

        $filing->update($validated);

        $filing->load([
            'filingSubject',
            'filingType',
            'preparer',
        ]);

        return $this->apiResponse->success(
            $filing,
            'Filing updated successfully.'
        );
    }

    /**
     * Delete filing
     */
    public function destroy($organisationId, $filingId)
    {
        $filing = Filing::where(
            'organisation_id',
            $organisationId
        )->find($filingId);

        if (!$filing) {
            return $this->apiResponse->error(
                'Filing not found.',
                404
            );
        }

        /*
         * Only draft filings can be deleted.
         */
        if ($filing->status !== 'draft') {
            return $this->apiResponse->error(
                'Only draft filings can be deleted.',
                422
            );
        }

        $filing->delete();

        return $this->apiResponse->noContent();
    }

    /**
     * Generate unique filing reference.
     */
    private function generateReference(): string
    {
        do {
            $reference = 'FIL-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(Str::random(6));

        } while (
            Filing::where(
                'reference',
                $reference
            )->exists()
        );

        return $reference;
    }
}