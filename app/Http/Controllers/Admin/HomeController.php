<?php
namespace App\Http\Controllers\Admin;


use App\Helper\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Home\AboutContentRequest;
use App\Http\Requests\Home\AboutRequest;
use App\Http\Requests\Home\HomeRequest;
use App\Http\Resources\Home\AboutContentResource;
use App\Http\Resources\Home\AboutResource;
use App\Http\Resources\Home\HomeResource;
use App\Models\Home\About;
use App\Models\Home\AboutContent;
use App\Models\Home\Home;


class HomeController extends Controller
{
    

    public function getHomeConetnt(){

        return ApiResponse::sendResponse('success',new HomeResource(Home::first()));   
    }

    public function getAbout(){

        return ApiResponse::sendResponse('success',new AboutResource(About::first()));   
    }

    public function getAboutContent(){

        return ApiResponse::sendResponse('success',new AboutContentResource(AboutContent::first()));   
    }


    public function updateHomeConetnt(HomeRequest $request){

        $data = $request->validated();
        $home = Home::first();
        $home->update($data);
        return ApiResponse::sendResponse('success',new HomeResource($home));   
    }

    public function updateAbout(AboutRequest $request){

        $data = $request->validated();
        $about = About::first();
        $about->update($data);
        return ApiResponse::sendResponse('success',new AboutResource($about));  

    }

    public function updateAboutContent(AboutContentRequest $request){

        $data = $request->validated();
        $AboutContent = AboutContent::first();
        $AboutContent->update($data);

        return ApiResponse::sendResponse(new AboutContentResource($AboutContent));   
    }


   
}
