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

class MediaManager extends Component
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

    public function doUpload(): void
    {
        \Log::info('doUpload called', [
            'count' => count($this->pendingUploads),
            'uploads' => $this->pendingUploads,
        ]);

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
                ->toMediaCollection('default', 'public');  // ← explicitly pass 'public' disk
        }

        $this->pendingUploads = [];
        $this->uploadDefaultAlt = '';

        $this->dispatch('toast', type: 'success', message: 'Files uploaded successfully.');
        $this->dispatch('media:items-updated', items: $this->buildMediaItems());
    }

    public function saveFolder(string $name, string $slug, ?string $parentId, string $description): void
    {
        $this->folderName = $name;
        $this->folderSlug = $slug;
        $this->folderDescription = $description;

        // Resolve slug to integer ID, or null if empty/invalid
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
        $this->dispatch('media:folders-updated', folders: $this->buildFolders());
        $this->dispatch('folder:created');
    }

    public function deleteFolder(string $folderId): void
    {
        $folder = MediaFolder::where('slug', $folderId)->firstOrFail();

        // Move any media in this folder to uncategorized
        Media::all()->each(function (Media $m) use ($folderId) {
            if ($m->getCustomProperty('folder_id') === $folderId) {
                $m->setCustomProperty('folder_id', 'uncategorized');
                $m->save();
            }
        });

        $folder->delete();

        $this->dispatch('toast', type: 'success', message: 'Folder deleted.');
        $this->dispatch('media:folders-updated', folders: $this->buildFolders());
        $this->dispatch('media:items-updated', items: $this->buildMediaItems());
    }

    public function saveMediaMeta(int $mediaId, string $alt, string $title, string $caption): void
    {
        $media = Media::findOrFail($mediaId);
        $media->setCustomProperty('alt', $alt);
        $media->setCustomProperty('title', $title);
        $media->setCustomProperty('caption', $caption);
        $media->save();

        $this->dispatch('toast', type: 'success', message: 'Changes saved.');
    }

    public function deleteMedia(int $mediaId): void
    {
        Media::findOrFail($mediaId)->delete();
        $this->dispatch('toast', type: 'success', message: 'File deleted.');
        $this->dispatch('media:items-updated', items: $this->buildMediaItems());
    }

    public function bulkDeleteMedia(array $ids): void
    {
        Media::whereIn('id', $ids)->each(fn (Media $m) => $m->delete());
        $this->dispatch('toast', type: 'success', message: count($ids).' files deleted.');
        $this->dispatch('media:items-updated', items: $this->buildMediaItems());
    }

    public function bulkMoveMedia(array $ids, string $folderId): void
    {
        Media::whereIn('id', $ids)->each(function (Media $m) use ($folderId) {
            $m->setCustomProperty('folder_id', $folderId);
            $m->save();
        });
        $this->dispatch('toast', type: 'success', message: 'Files moved.');
        $this->dispatch('media:items-updated', items: $this->buildMediaItems());
    }

    public function render(): View
    {
        $mediaItems = $this->buildMediaItems();
        $folders = $this->buildFolders();

        return view('livewire.admin.media.media-manager', [
            'mediaItemsJson' => json_encode($mediaItems),
            'foldersJson' => json_encode($folders),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function buildMediaItems(): array
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
                'width' => $m->getCustomProperty('width'),
                'height' => $m->getCustomProperty('height'),
                'created_at' => $m->created_at?->format('Y-m-d') ?? '',
                'alt' => $m->getCustomProperty('alt', ''),
                'title' => $m->getCustomProperty('title', ''),
                'caption' => $m->getCustomProperty('caption', ''),
            ])
            ->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    private function buildFolders(): array
    {
        $dbFolders = MediaFolder::orderBy('name')->get();

        // Count media per folder using PHP instead of raw SQL
        $allMedia = Media::all();
        $countByFolder = $allMedia->groupBy(fn (Media $m) => $m->getCustomProperty('folder_id', 'uncategorized'))
            ->map(fn ($group) => $group->count())
            ->toArray();
        $totalCount = $allMedia->count();

        $folders = [
            ['id' => 'all', 'name' => 'All Media', 'count' => $totalCount, 'parent_id' => null, 'depth' => 0, '_expanded' => true],
        ];

        $children = $dbFolders->whereNotNull('parent_id')->groupBy('parent_id');
        $roots = $dbFolders->whereNull('parent_id');

        $addFolder = function ($folder, int $depth) use (&$addFolder, &$folders, $children, $countByFolder) {
            $folders[] = [
                'id' => $folder->slug,
                'name' => $folder->name,
                'count' => $countByFolder[$folder->slug] ?? 0,
                'parent_id' => $folder->parent?->slug,
                'depth' => $depth,
                '_expanded' => false,
            ];
            foreach ($children->get($folder->id, collect()) as $child) {
                $addFolder($child, $depth + 1);
            }
        };

        foreach ($roots as $root) {
            $addFolder($root, 0);
        }

        $hasBucket = collect($folders)->contains('id', 'uncategorized');
        if (! $hasBucket) {
            $folders[] = [
                'id' => 'uncategorized',
                'name' => 'Uncategorized',
                'count' => $countByFolder['uncategorized'] ?? 0,
                'parent_id' => null,
                'depth' => 0,
                '_expanded' => false,
            ];
        }

        return $folders;
    }
}
