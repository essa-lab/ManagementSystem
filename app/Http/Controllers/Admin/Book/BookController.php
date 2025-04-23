<?php

namespace App\Http\Controllers\Admin\Book;

use App\Helper\ApiResponse;
use App\Helper\Authorize;
use App\Helper\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Books\Book\BookStoreRequest;
use App\Http\Requests\Books\Book\PoetryCollectionStoreRequest;
use App\Http\Requests\Books\Book\PrintInformationRequest;
use App\Http\Requests\Books\Book\SpecificSubjectStoreRequest;
use App\Http\Requests\Books\Book\TranslatorTypeRequest;
use App\Http\Requests\Resource\ResourceRequest;
use App\Http\Resources\PaginatingResource;
use App\Http\Resources\Resource\ResourceResource;
use App\Models\Book\Book;
use App\Models\Book\BookSpecificSubject;
use App\Models\Book\BookTranslator;
use App\Models\Book\PoetryCollectionName;
use App\Models\Resource\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ResourceRequest $request)
    {
        //
        $request->validated();
        $book = Resource::query();
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user)) {
            $book->where('library_id', $user->library_id);
        }
        $book->where('resourceable_type', Book::class);

        if ($request->has('title')) {
            $searchTerm = $request->get('title');
            $book->where(function ($query) use ($searchTerm) {
                $query->where('title_ar', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title_en', 'like', '%' . $searchTerm . '%')
                    ->orWhere('title_ku', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('language_id')) {
            $book->where('language_id', $request->get('language_id'));
        }
        if ($request->filled('registeration_number')) {
            $registrationNumber = $request->get('registeration_number');
            $book->whereHasMorph('resourceable', [Book::class], function ($query) use ($registrationNumber) {
                $query->where('registeration_number', $registrationNumber);
            });
        }
        $book->with(['library', 'language', 'resourceSource.source', 'subjects', 'curators']);
        $book->orderBy($request->get('sortBy', 'id'), $request->get('sortOrder', 'asc'));
        $book = $book->paginate($request->get('limit', 10));
        return ApiResponse::sendPaginatedResponse( new PaginatingResource($book, ResourceResource::class));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookStoreRequest $request)
    {
        $data = $request->validated();
        try {
            unset($data['book_id']);
            $book = Book::find($request->book_id);
            if(!$book){
                return ApiResponse::sendError(__('messages.book_create_error'));
            }
            $book->update($data);
            return ApiResponse::sendResponse(__('messages.book_create'), Book::find($request->book_id));
        } catch (\Exception $e) {
            Logger::log('Error Creating new book : ' . $e->getMessage());
            return ApiResponse::sendError(__('messages.book_create_error'));
        }
    }
    public function storeTranslatorType(TranslatorTypeRequest $request, string $bookId)
    {
        $data = $request->validated();
        try {
            $book = Book::find($bookId);
            if (!$book) {
                return ApiResponse::sendError(__('messages.book_not_found'));
            }

            DB::beginTransaction();

            $existingTranslatorType = $book->bookTranslator()->get()->keyBy('id');

            $typesToInsert = [];

            foreach ($data as $typeData) {
                $typeId = $typeData['id'] ?? null; 

                $typeFeilds = [
                    'name_ar' => $typeData['name_ar'] ?? null,
                    'name_ku' => $typeData['name_ku'] ?? null,
                    'name_en' => $typeData['name_en'] ?? null,
                    'translate_type_id' => $typeData['translate_type_id'],
                    'book_id' => $book->id,
                ];
                if ($typeId && isset($existingTranslatorType[$typeId])) {
                    $existingTranslatorType[$typeId]->update($typeFeilds);
                } else {
                    $typesToInsert[] = $typeFeilds;
                }
            }

            if (!empty($typesToInsert)) {
                $book->bookTranslator()->insert($typesToInsert);
            }

            

            DB::commit();

            return ApiResponse::sendResponse(__('messages.book'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new book : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.book_error'));

        }
    }
    public function deleteTranslator($id){
        
        $book = BookTranslator::find($id);
         if(!$book){
            return ApiResponse::sendError(__('messages.book_not_found'));
         }
         $book->delete();
         return ApiResponse::sendResponse(__('messages.book_delete'));
        
    }
    public function storePoetryCollection(PoetryCollectionStoreRequest $request, string $bookId)
    {
        $data = $request->validated();
        try {
            $book = Book::find($bookId);
            if (!$book) {
                return ApiResponse::sendError(__('messages.book_not_found'));
            }
            // $book->poetryCollectionName()?->delete();
            $book->poetryCollectionName()->updateOrCreate(['book_id'=>$bookId],$data);
            
            return ApiResponse::sendResponse(__('messages.book'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new book poetry : ' . $e->getMessage());
            return ApiResponse::sendError(__('messages.book_error'));
        }
    }

    public function deletePoetryCollection($id){
        
        $book = PoetryCollectionName::find($id);
         if(!$book){
            return ApiResponse::sendError(__('messages.book_not_found'));
         }
         $book->delete();
         return ApiResponse::sendResponse(__('messages.book_delete'));
        
    }

    public function storeSpecificSubject(SpecificSubjectStoreRequest $request, string $bookId)
    {
        $data = $request->validated();
        try {
            $book = Book::find($bookId);
            if (!$book) {
                return ApiResponse::sendError(__('messages.book_not_found'));
            }

            DB::beginTransaction();

            

            $existingSubjects = $book->specificSubjects()->get()->keyBy('id');

            $subjectsToInsert = [];

            foreach ($data as $subjectData) {
                $subjectId = $subjectData['id'] ?? null; 

                $subjectFeilds = [
                    'title_ar' => $subjectData['title_ar'] ?? null,
                    'title_ku' => $subjectData['title_ku'] ?? null,
                    'title_en' => $subjectData['title_en'] ?? null,
                    'book_id' => $book->id,
                ];
                if ($subjectId && isset($existingSubjects[$subjectId])) {
                    $existingSubjects[$subjectId]->update($subjectFeilds);
                } else {
                    $subjectsToInsert[] = $subjectFeilds;
                }
            }

            if (!empty($subjectsToInsert)) {
                $book->specificSubjects()->insert($subjectsToInsert);
            }

            

            DB::commit();


            return ApiResponse::sendResponse(__('messages.book'), $data);
        } catch (\Exception $e) {
            Logger::log('Error Creating new book specific subject : ' . $e->getMessage());
            return ApiResponse::sendError(__('messages.book_error'));
        }
    }
    public function deleteSpecificSubject($id){
        
        $book = BookSpecificSubject::find($id);
         if(!$book){
            return ApiResponse::sendError(__('messages.book_not_found'));
         }
         $book->delete();
         return ApiResponse::sendResponse(__('messages.book_delete'));
        
    }


    public function storePrintInformation(PrintInformationRequest $request, string $bookId)
    {
        $data = $request->validated();
        try {
            $book = Book::find($bookId);
            if (!$book) {
                return ApiResponse::sendError(__('messages.book_not_found'));
            }
            if (empty($data)) {
                $book->printInformation()?->delete();
            } else {
                $book->addPrintInformation($data);
            }
            return ApiResponse::sendResponse(__('messages.book'), $data);

        } catch (\Exception $e) {
            Logger::log('Error Creating new book specific subject : ' . $e->getMessage());

            return ApiResponse::sendError(__('messages.book_error'));

        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
//
        $book = Resource::with(['createdBy','library', 'language', 'curators', 'editors', 'resourceSource.source', 'subjects', 'medias'])->find($id);


        if (!$book) {
            return ApiResponse::sendError(__('messages.book_not_found'));
        }
        if ($book->resourceable_type != Book::class) {
            return ApiResponse::sendError(__('messages.uncompatible_resourceable_type'));

        }
        $user = Auth::user();
        if (!Authorize::isSuperAdmin($user) && $user->library_id != $book->library_id) {
            return ApiResponse::sendError(__('messages.not_authorized'));
        }
        $book->load([
            'resourceable.specificSubjects',

            'resourceable.bookTranslator.type',
            'resourceable.poetryCollectionName.poetryCollection',
            'resourceable.printInformation',
            'resourceable.printInformation.conditions',
            'resourceable.printInformation.type',
        ]);

        return ApiResponse::sendResponse(__('messages.get_book'), new ResourceResource($book));
    }
}
