<?php

namespace App\Livewire\Admin\Test;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProjectDocument;
use Illuminate\Database\Eloquent\Model;

class TestProjectListTable extends Component
{


    use WithPagination;

    // Filters / sort (wire to inputs later if you want)
    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $review = 'all'; // approved|in-review|changes-requested|draft|all

    #[Url]
    public string $sort = 'updated_at_desc'; // updated_at_desc|submitted_at_desc|name_asc

    public int $perPage = 12;

    /**
     * Replace this with your real query. For demonstration, we’ll fabricate
     * a paginator-like structure.
     *
     * Expected Row Fields:
     * - id
     * - project_name
     * - submitter_name
     * - doc_name
     * - type    (PDF|XLSX|DOCX|IMG|MP4...)
     * - size    (already formatted, e.g. "2.4 MB")
     * - status  (approved|in-review|changes-requested|draft)
     * - submitted_at (string)
     * - updated_at   (string)
     * - recent (array of recent docs for collapsible list)
     */
    protected function fetchRows() // : LengthAwarePaginator
    {
        // TODO: swap this for your actual query (ProjectDocument::query() ...)
        // Here’s a tiny in-memory paginator so the example works as-is.
        $items = collect([
            [
                'id' => 421,
                'project_name' => 'Riverbend Redevelopment',
                'submitter_name' => 'Jane Doe',
                'doc_name' => 'EIA_Section4_Mitigation.pdf',
                'type' => 'PDF',
                'size' => '2.4 MB',
                'status' => 'approved',
                'submitted_at' => 'Aug 29, 2025 • 14:18',
                'updated_at' => 'Sep 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'EIA_Section4_Mitigation.pdf', 'meta' => 'PDF • 2.4 MB', 'status' => 'approved'],
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],
            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],
            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],

            [
                'id' => 422,
                'project_name' => 'Harbor Dredging',
                'submitter_name' => 'Mark Lee',
                'doc_name' => 'Sampling_Data_Q1_2024.xlsx',
                'type' => 'XLSX',
                'size' => '1.1 MB',
                'status' => 'changes-requested',
                'submitted_at' => 'Aug 22, 2025 • 11:04',
                'updated_at' => 'Aug 30, 2025 • 16:40',
                'recent' => [
                    ['name' => 'Sampling_Data_Q1_2024.xlsx', 'meta' => 'XLSX • 1.1 MB', 'status' => 'changes-requested'],
                ],
            ],
        ]);

        // Minimal fake paginator:
        $page = (int) request('page', 1);
        $per  = $this->perPage;
        $slice = $items->slice(($page - 1) * $per, $per)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            items: $slice,
            total: $items->count(),
            perPage: $per,
            currentPage: $page,
            options: ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    // Actions (wire:click handlers)

    public function open(int $id): void
    {
        // e.g., redirect to details page
        $this->dispatch('navigateTo', url: route('documents.show', ['id' => $id]));
    }

    public function download(int $id): void
    {
        // Trigger download route (or return a file response via controller)
        $this->dispatch('navigateTo', url: route('documents.download', ['id' => $id]));
    }

    public function history(int $id): void
    {
        $this->dispatch('navigateTo', url: route('documents.history', ['id' => $id]));
    }

    public function delete(int $id): void
    {
        // Confirm + delete logic; replace with your policy checks
        // Model::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', message: "Deleted document #{$id} (demo)");
    }

    

    public function render()
    {
        return view('livewire.admin.test.test-project-list-table',[
            'rows' => $this->fetchRows(),
        ]);
    }
}
