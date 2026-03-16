<?php

namespace App\Livewire\Admin\Media;

use App\Models\MediaFolder;
use App\Models\MediaLibraryAsset;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPicker extends Component
{
    use WithFileUploads;

    /** @var array<TemporaryUploadedFile> */
    public array $pendingUploads = [];

    public string $uploadFolder = '';

    public string $uploadDefaultAlt = '';

    public string $folderName = '';

    public string $folderSlug = '';

    public ?int $folderParentId = null;

    public string $folderDescription = '';

    public function loadMedia(): void
    {
        $items = $this->buildPickerItems();
        $folders = $this->buildPickerFolders();

        $this->dispatch('media:picker-loaded', items: $items, folders: $folders);
    }

    public function doUpload(): void
    {
        if (empty($this->pendingUploads)) {
            $this->dispatch('toast', type: 'error', message: 'No files received by server.');

            return;
        }

        Validator::make(
            ['files' => $this->pendingUploads],
            ['files.*' => ['file', 'max:10240']],
            ['files.*.max' => 'Each file must be under 10 MB.']
        )->validate();

        foreach ($this->pendingUploads as $upload) {
            $asset = MediaLibraryAsset::create([]);

            $customProps = [
                'folder_id' => $this->uploadFolder ?: 'uncategorized',
                'alt' => $this->uploadDefaultAlt,
                'title' => '',
                'caption' => '',
            ];

            if (str_starts_with($upload->getMimeType(), 'image/')) {
                $size = @getimagesize($upload->getRealPath());
                if ($size) {
                    $customProps['width'] = $size[0];
                    $customProps['height'] = $size[1];
                }
            }

            $asset->addMedia($upload->getRealPath())
                ->usingFileName($upload->getClientOriginalName())
                ->withCustomProperties($customProps)
                ->toMediaCollection('default', 'public');
        }

        $this->pendingUploads = [];
        $this->uploadDefaultAlt = '';

        $this->dispatch('toast', type: 'success', message: 'Files uploaded successfully.');
        $this->dispatch('media:picker-loaded', items: $this->buildPickerItems(), folders: $this->buildPickerFolders());
    }

    public function saveFolder(string $name, string $slug, ?string $parentId, string $description): void
    {
        $this->folderName = $name;
        $this->folderSlug = $slug;
        $this->folderDescription = $description;

        if ($parentId && $parentId !== '') {
            $parentFolder = MediaFolder::where('slug', $parentId)->first();
            $this->folderParentId = $parentFolder?->id;
        } else {
            $this->folderParentId = null;
        }

        $this->validate([
            'folderName' => ['required', 'string', 'max:100'],
            'folderSlug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:media_folders,slug'],
        ]);

        MediaFolder::create([
            'name' => $this->folderName,
            'slug' => $this->folderSlug,
            'parent_id' => $this->folderParentId,
            'description' => $this->folderDescription,
        ]);

        $this->folderName = '';
        $this->folderSlug = '';
        $this->folderParentId = null;
        $this->folderDescription = '';

        $this->dispatch('toast', type: 'success', message: 'Folder created.');
        $this->dispatch('media:picker-loaded', items: $this->buildPickerItems(), folders: $this->buildPickerFolders());
    }

    public function render(): View
    {
        return view('livewire.admin.media.media-picker');
    }

    /** @return array<int, array<string, mixed>> */
    private function buildPickerItems(): array
    {
        return Media::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Media $m) => [
                'id' => $m->id,
                'name' => $m->file_name,
                'url' => $m->getUrl(),
                'thumbnail' => $m->hasGeneratedConversion('thumb') ? $m->getUrl('thumb') : $m->getUrl(),
                'folder_id' => $m->getCustomProperty('folder_id', 'uncategorized'),
                'mime_type' => $m->mime_type,
                'size' => $m->size,
                'alt' => $m->getCustomProperty('alt', ''),
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildPickerFolders(): array
    {
        $allMedia = Media::all();
        $countByFolder = $allMedia->groupBy(fn (Media $m) => $m->getCustomProperty('folder_id', 'uncategorized'))
            ->map->count()
            ->toArray();

        $dbFolders = MediaFolder::with('children', 'parent')->orderBy('name')->get();
        $totalCount = $allMedia->count();

        $folders = [
            ['id' => 'all', 'name' => 'All Media', 'count' => $totalCount, 'parent_id' => null, '_expanded' => true],
        ];

        foreach ($dbFolders as $folder) {
            $folders[] = [
                'id' => $folder->slug,
                'name' => $folder->name,
                'count' => $countByFolder[$folder->slug] ?? 0,
                'parent_id' => $folder->parent?->slug,
                '_expanded' => false,
            ];
        }

        $hasBucket = collect($folders)->contains('id', 'uncategorized');
        if (! $hasBucket) {
            $folders[] = ['id' => 'uncategorized', 'name' => 'Uncategorized', 'count' => $countByFolder['uncategorized'] ?? 0, 'parent_id' => null, '_expanded' => false];
        }

        return $folders;
    }
}
