<?php

namespace App\Models\Resource;

use App\Http\Resources\DigitalResource\DigitalResource;
use App\Http\Resources\LibraryResource;
use App\Http\Resources\Meilisearch\MeilisearchResource;
use App\Http\Resources\Resource\MediaResource;
use App\Models\Article\Article;
use App\Models\BaseModel;
use App\Models\Book\Book;
use App\Models\Library;
use App\Models\Research\Research;
use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Searchable;


class Resource extends BaseModel
{
    use HasFactory, SoftDeletes,Searchable;

    protected $with = ['resourceable'];
    protected $fillable = [
        'created_by',
        'library_id',
        'language_id',
        'uuid',
        'link',
        'registry_date',
        'arrival_date',
        'number_of_copies',
        'title_en',
        'title_ku',
        'title_ar',
        'resourceable_id',
        'resourceable_type'
    ];

    //Relations
    public function resourceable()
    {
        return $this->morphTo();
    }

    public function resourceSetting(){
        return $this->hasOne(ResourceSetting::class,'resource_id');
    }
    public function copies(){
        return $this->hasMany(ResourceCopy::class,'resource_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'resource_subjects');
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
    public function library()
    {
        return $this->belongsTo(Library::class, 'library_id');
    }
    public function medias()
    {
        return $this->hasMany(Media::class);
    }
    public function curators()
    {
        return $this->hasMany(Curator::class);
    }
    public function editors()
    {
        return $this->hasMany(Editor::class);
    }
    public function resourceSource()
    {
        return $this->hasOne(ResourceSource::class,'resource_id');
    }
    // public function source()
    // {
    //     return $this->hasOneThrough(
    //         Source::class,
    //         ResourceSource::class,
    //     );
    // }
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }

    //attribute
    public function getGroupedCuratorsAttribute()
    {
        return $this->curators->groupBy('type');
    }
    public function getGroupedMediasAttribute()
    {
        return $this->medias->groupBy('type');
    }
    public function getGroupedEditorsAttribute()
    {
        return $this->editors->groupBy('type');
    }

    public function searchableAs(): string
    {
        return 'resources';
    }
    public function toSearchableArray(): array
    {
                
        $resource = $this->toArray();
        $resource['uuid'] = $this->uuid;
        $resource['link'] = $this->link;
        $resource['registry_date'] = $this->registry_date;
        $resource['arrival_date'] = $this->arrival_date;
        $resource['number_of_copies'] = $this->number_of_copies;
        $resource['title_en'] = $this->title_en;
        $resource['title_ku'] = $this->title_ku;
        $resource['title_ar'] = $this->title_ar;
        $resource['curators'] = $this->curators;
        if(!empty($this->medias)){
            $resource['medias'] = MediaResource::collection($this->medias);
        }
        $resource['editors'] = $this->editors;
        $resource['subjects'] = $this->subjects;

        $resource['library'] = new LibraryResource($this->library);
        $resource['language'] = $this->language;

        $resource['resourceable_type'] = class_basename($this->resourceable_type);

        $resource['resourceable'] = [];

    if ($this->resourceable) {
        $resource['resourceable'] = $this->resourceable->toArray(); // Load base resourceable data

        if ($this->resourceable_type === Book::class) {
            $resource['resourceable'] += [
                'specificSubjects' => $this->resourceable->specificSubjects,
                'bookTranslateType' => $this->resourceable->bookTranslateType,
                'registeration_number' => $this->resourceable->registeration_number,
                'order_number' => $this->resourceable->order_number,
                'isbn' => $this->resourceable->isbn,
                'barcode' => $this->resourceable->barcode,
            ];
        } elseif ($this->resourceable_type === Article::class) {
            $resource['resourceable'] += [
                'registeration_number' => $this->resourceable->registeration_number,
                'order_number' => $this->resourceable->order_number,
                'article_scientific_classification_id' => $this->resourceable->article_scientific_classification_id,
                'article_type_id' => $this->resourceable->article_type_id,
                'article_specification_id' => $this->resourceable->article_specification_id,
                'articleType'=>$this->resourceable->articleType,
                'articleSpecification'=>$this->resourceable->articleSpecification,
                'articleScientificClassification'=>$this->resourceable->articleScientificClassification,
                'articleKeyword'=>$this->resourceable->articleKeyword,

            ];
        } elseif ($this->resourceable_type === Research::class) {
            $resource['resourceable'] += [
                'registeration_number' => $this->resourceable->registeration_number,
                'order_number' => $this->resourceable->order_number,
                'education_level_id' => $this->resourceable->education_level_id,
                'research_type_id' => $this->resourceable->research_type_id,
                'research_format_id' => $this->resourceable->research_format_id,
                'researchKeywords'=>$this->resourceable->researchKeywords,
                'researchEducationLevel'=>$this->resourceable->researchEducationLevel,
                'researchType'=>$this->resourceable->researchType,
                'researchFormat'=>$this->resourceable->researchFormat,

            ];
        } elseif ($this->resourceable_type === DigitalResource::class) {
            $resource['resourceable'] += [
                'digital_format_id' => $this->resourceable->digital_format_id,
                'digital_resource_type_id' => $this->resourceable->digital_resource_type_id,
                'identifier' => $this->resourceable->identifier,
                'research_type_id' => $this->resourceable->research_type_id,
                'research_format_id' => $this->resourceable->research_format_id,
                'specificSubject'=>$this->resourceable->specificSubject,
                'digitalFormat'=>$this->resourceable->digitalFormat,
                'digitalType'=>$this->resourceable->digitalType,

            ];
        }
    }

        return $resource;
    }
    public function relatedBySubject()
    {
        $res= self::whereHas('subjects', function ($q) {
            $q->whereIn('subjects.id', function ($subQuery) {
                $subQuery->select('subject_id')
                    ->from('resource_subjects')
                    ->where('resource_id', $this->id);
            });
        })->where('id', '!=', $this->id)->with('medias')->get();

        return $res;
    }

    //boot
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($resource) {
            if ($resource->resourceable) {
                $resource->resourceable->delete();
            }
        });
        static::deleted(function ($resource) {
            $resource->unsearchable();
        
            // info("Resource deleted: {$resource->id}, syncing with Meilisearch");

            // $host = env('MEILISEARCH_HOST');
            // $apiKey = env('MEILISEARCH_KEY');

            // try {
            //     $response = Http::withHeaders([
            //         'Authorization' => "Bearer $apiKey",
            //         'Content-Type' => 'application/json',
            //     ])->delete("$host/indexes/resource/documents/$resource->id");

            // } catch (ConnectException $e) {
            //     info($e->getMessage());
            // }


            // if ($response->failed()) {
            //     info("Failed to delete resource from Meilisearch", [
            //         'resource_id' => $resource->id,
            //         'response' => $response->json(),
            //     ]);
            // } else {
            //     info("resource deleted from Meilisearch successfully", [
            //         'resource_id' => $resource->id,
            //     ]);
            // }
        });

        static::created(function ($resource) {
            $resource->searchable();
        });
        static::updated(function ($resource) {
            $resource->searchable();

        });
    }
    // public static function syncResourceToMeiliSearch($resource)
    // {
    //     $host = env('MEILISEARCH_HOST');
    //     $apiKey = env('MEILISEARCH_KEY');

    //     try {

    //         $resource = new MeilisearchResource($resource->load(['library', 'language', 'subjects', 'medias', 'editors', 'curators']));

    //         $response = Http::withHeaders([
    //             'Authorization' => "Bearer $apiKey",
    //             'Content-Type' => 'application/json',
    //         ])->post("$host/indexes/resources/documents", $resource->toArray(request()));

    //         if ($response->failed()) {
    //             info("Failed to create/update resource to Meilisearch", [
    //                 'resource_id' => $resource->id,
    //                 'response' => $response->json(),
    //             ]);
    //         }

    //         info("create/update resource to Meilisearch", [
    //             'resource_id' => $resource->id,
    //             'response' => $response->json(),
    //         ]);
    //     } catch (\Exception $e) {
    //         info($e->getMessage());
    //         info($e->getTrace());
    //         // info("Failed to create/update resource to Meilisearch", [
    //         //     'resource_id' => $resource->id,
    //         //     'response' => $response->json(),
    //         // ]);  
    //     }
    // }

}
