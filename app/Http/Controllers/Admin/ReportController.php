<?php
namespace App\Http\Controllers\Admin;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\TopTenRequest;
use App\Http\Resources\DigitalResource\DigitalResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\Resource\LanguageResource;
use App\Http\Resources\Resource\SubjectResource;
use App\Models\Article\Article;
use App\Models\Book\Book;
use App\Models\Library;
use App\Models\Research\Research;
use App\Models\Resource\Circulation;
use App\Models\Resource\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    private $mapResources;
    public function __construct()
    {
        $this->mapResources = [
            'Book' => Book::class,
            'Research' => Research::class,
            'Article' => Article::class,
            'DigitalResource' => DigitalResource::class
        ];
    }
    public function topTen(TopTenRequest $request)
    {

        $data = $request->validated();

        $circulations = Circulation::select(
            'resources.title_en',
            'resources.title_ku',
            'resources.title_ar',
            'resources.resourceable_type',
            DB::raw('COUNT(circulations.id) as circulation_count')
        )
            ->join('resource_copies', 'circulations.resource_copy_id', '=', 'resource_copies.id')
            ->join('resources', 'resource_copies.resource_id', '=', 'resources.id')
            ->where('resources.resourceable_type', $this->mapResources[$data['type']])
            ->groupBy('resources.id', 'resources.title_en', 'resources.title_ku', 'resources.title_ar', 'resources.resourceable_type')
            ->orderByDesc('circulation_count')
            ->limit(10)
            ->get();

        return ApiResponse::sendResponse(__('messages.resource_update'), $circulations);
    }

    public function resourceCountPerLibraryAndLanguage()
    {
        $user = Auth::user();
        $libraryId = null;
        if (!Authorize::isSuperAdmin($user)) {
            $libraryId = $user->library_id;
        }

        $resourceCounts = Resource::query();

        if (isset($libraryId)) {
            $resourceCounts->where('library_id', $libraryId);
        }

        $resourceCounts = $resourceCounts->select('library_id', 'language_id')
            ->with(['library', 'language'])
            ->selectRaw("
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as book_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as article_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as research_count,
            SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as digital_count,
            SUM(
                CASE WHEN resourceable_type IN (?, ?, ?, ?) THEN 1 ELSE 0 END
            ) as total_count
        ", [
                'App\Models\Book\Book',
                'App\Models\Article\Article',
                'App\Models\Research\Research',
                'App\Models\DigitalResource\DigitalResource',
                'App\Models\Book\Book',
                'App\Models\Article\Article',
                'App\Models\Research\Research',
                'App\Models\DigitalResource\DigitalResource'
            ])
            ->groupBy('library_id', 'language_id')
            ->get()
            ->groupBy('library_id')
            ->map(function ($items, $libraryId) {
                return [
                    'library_id' => $libraryId,
                    'library' => new LibraryResource($items->first()->library),
                    'details' => $items->map(function ($item) {
                        return [
                            'language_id' => $item->language_id,
                            'language' => new LanguageResource($item->language),
                            'book_count' => (int) $item->book_count,
                            'article_count' => (int) $item->article_count,
                            'research_count' => (int) $item->research_count,
                            'digital_count' => (int) $item->digital_count,
                            'total_count' => (int) $item->total_count,
                        ];
                    })->values()
                ];
            })->values();



        return ApiResponse::sendResponse('success', $resourceCounts);
    }

    public function resourceCountPerLibrary()
    {
        $user = Auth::user();
        $libraryId = null;
        if (!Authorize::isSuperAdmin($user)) {
            $libraryId = $user->library_id;
        }

        $resourceCounts = Resource::query();

        if (isset($libraryId)) {
            $resourceCounts->where('library_id', $libraryId);
        }

        $resourceCounts = $resourceCounts->select('library_id')
            ->with(['library'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as book_count", ['App\Models\Book\Book'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as article_count", ['App\Models\Article\Article'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as research_count", ['App\Models\Research\Research'])
            ->selectRaw("SUM(CASE WHEN resourceable_type = ? THEN 1 ELSE 0 END) as digital_count", ['App\Models\DigitalResource\DigitalResource'])
            ->groupBy('library_id')
            ->get();

        return ApiResponse::sendResponse('success', $resourceCounts);
    }

    public function resourceCountPerSubject()
    {
        $user = Auth::user();




        $libraries = Cache::remember('library_subjects_' . $user->id, now()->addHour(), function () use ($user) {
            return Library::query()
                ->when(!Authorize::isSuperAdmin($user), function ($query) use ($user) {
                    $query->where('id', $user->library_id);
                })
                ->with(['resources.subjects'])
                ->get()
                ->map(function ($library) {
                    return [
                        'library' => new LibraryResource($library),
                        'subjects' => $library->resources->flatMap->subjects
                            ->groupBy('id')
                            ->map(fn($subjects) => [
                                'subject' => new SubjectResource($subjects->first()),
                                'total_resources' => count($subjects)
                            ])
                            ->values()
                    ];
                });
        });

        return ApiResponse::sendResponse('success', $libraries);
    }
    
}