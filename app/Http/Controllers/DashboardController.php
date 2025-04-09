<?php
namespace App\Http\Controllers;

use App\Http\Resources\Dashboard\ContactResource;
use App\Http\Resources\Dashboard\NavigationResource;
use App\Http\Resources\Dashboard\QuickLinkResource;
use App\Http\Resources\Dashboard\SoicalResource;
use App\Http\Resources\Home\AboutContentResource;
use App\Http\Resources\Home\AboutResource;
use App\Http\Resources\Home\HomeResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\Dashboard\Contact;
use App\Models\Dashboard\Navigation;
use App\Models\Dashboard\QuickLinks;
use App\Models\Dashboard\SocialMedia;
use App\Models\Home\About;
use App\Models\Home\AboutContent;
use App\Models\Home\Home;
use App\Models\Library;
use App\Models\Resource\Resource;

class DashboardController extends Controller
{

    public function getAboutPage(){
        $about = About::first();
        $aboutContent = AboutContent::first();
        
        return response()->json([
            'about' => new AboutResource($about),
            'about_content' => new AboutContentResource($aboutContent),
        ]);
       
    }
    public function getLibraryData()
    {
        $libraries = Library::all();

        $resourceTypes = [
            'book' => 'App\Models\Book\Book',
            'article' => 'App\Models\Article\Article',
            'research' => 'App\Models\Research\Research',
            'digital' => 'App\Models\DigitalResource\DigitalResource',
        ];

        $resourceCounts = Resource::selectRaw('resourceable_type, COUNT(*) as total')
            ->groupBy('resourceable_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [class_basename($item->resourceable_type) => $item->total];
            });

        $latestResources = [];
        foreach ($resourceTypes as $key => $model) {
            $latestResources[$key] = ResourceResource::collection(Resource::with(['curators','editors','medias'])->where('resourceable_type', $model)
                ->latest()
                ->limit(6)
                ->get());
        }

        $home_content = new HomeResource(Home::first());

        return response()->json([
            'libraries' => LibraryResource::collection( $libraries),
            'resource_types' => array_keys($resourceTypes),
            'resource_counts' => $resourceCounts,
            'latest_resources' => $latestResources,
            'home_content' => $home_content,
        ]);
    }

    public function getLibraryNavigation(){
    {
        return response()->json([
            'navigation' => NavigationResource::collection(Navigation::all()),
            'quick_links' =>QuickLinkResource::collection( QuickLinks::all()),
            'socials' => SoicalResource::collection( SocialMedia::all()),
            'contacts' =>ContactResource::collection(Contact::all()),
        ]);
    }
    
    }
}