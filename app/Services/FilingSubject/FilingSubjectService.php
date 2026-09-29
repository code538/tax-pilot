<?php

namespace App\Services\FilingSubject;

use App\Models\FilingSubject;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FilingSubjectService
{
    public function all(Organisation $organisation): Collection
    {
        return $organisation->filingSubjects()
            ->latest('id')
            ->get();
    }

    public function find(
        Organisation $organisation,
        int $subjectId
    ): FilingSubject {
        return FilingSubject::query()
            ->where('organisation_id', $organisation->id)
            ->where('id', $subjectId)
            ->firstOrFail();
    }

    public function create(
        Organisation $organisation,
        array $data
    ): FilingSubject {
        return DB::transaction(function () use ($organisation, $data) {

            /*
             * An organisation should have only one
             * organisation-type filing subject.
             */
            if ($data['subject_type'] === 'organisation') {
                $exists = FilingSubject::query()
                    ->where('organisation_id', $organisation->id)
                    ->where('subject_type', 'organisation')
                    ->exists();

                if ($exists) {
                    abort(
                        422,
                        'An organisation filing subject already exists.'
                    );
                }
            }

            $data['organisation_id'] = $organisation->id;

            return FilingSubject::create($data);
        });
    }

    public function update(
        Organisation $organisation,
        int $subjectId,
        array $data
    ): FilingSubject {
        return DB::transaction(function () use (
            $organisation,
            $subjectId,
            $data
        ) {
            $subject = $this->find(
                $organisation,
                $subjectId
            );

            $subject->update($data);

            return $subject->fresh();
        });
    }

    public function delete(
        Organisation $organisation,
        int $subjectId
    ): void {
        DB::transaction(function () use (
            $organisation,
            $subjectId
        ) {
            $subject = $this->find(
                $organisation,
                $subjectId
            );

            /*
             * Do not allow the organisation's own
             * filing subject to be deleted.
             */
            if ($subject->subject_type === 'organisation') {
                abort(
                    422,
                    'The organisation filing subject cannot be deleted.'
                );
            }

            $subject->delete();
        });
    }
}