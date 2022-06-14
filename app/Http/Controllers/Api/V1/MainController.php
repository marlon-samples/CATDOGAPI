<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Response;
use Illuminate\Support\Facades\Http;

class MainController extends Controller
{
    public function breeds(Request $request)
    {
        try {
            $cats = [];
            $dogs = [];

            if($request->all() != NULL){
                $limit = ($request->limit <= 1) ? $request->limit : $request->limit / 2;
                $page = (!empty($request->page) && !empty($request->page)) ? '?page='.$request->page.'&limit='.$limit : '';    
            }else{
                $page = '';
            }
    
            $response1 = Http::get('https://api.thecatapi.com/v1/breeds'.$page);
            $result1 = json_decode($response1->getBody(), true);

            foreach($result1 as $key => $value){
                $cats[] = [
                    "id" => (isset($value['id'])) ? $value['id'] : '', 
                    "name" => (isset($value['name'])) ? $value['name'] : '',
                    "temperament" => (isset($value['temperament'])) ? $value['temperament'] : '',
                    "origin" => (isset($value['origin'])) ? $value['origin'] : '',
                    "country_code" => (isset($value['country_code'])) ? $value['country_code'] : '',
                    "description" => (isset($value['description'])) ? $value['description'] : '',
                    "image" => (isset($value['image'])) ? $value['image'] : '',
                ];    
            }
            
            $response2 = Http::get('https://api.TheDogAPI.com/v1/breeds'.$page);
            $result2 = json_decode($response2->getBody(), true);
            
            foreach($result2 as $key => $value){
                $dogs[] = [
                    "id" => (isset($value['id'])) ? $value['id'] : '', 
                    "name" => (isset($value['name'])) ? $value['name'] : '',
                    "temperament" => (isset($value['temperament'])) ? $value['temperament'] : '',
                    "origin" => (isset($value['origin'])) ? $value['origin'] : '',
                    "country_code" => 'N/A',
                    "description" => 'N/A',
                    "image" => (isset($value['image'])) ? $value['image'] : '',
                ];    
            }
            
            $data = array_merge($cats, $dogs);

            return ['page' => $request->page, 'limit' => $request->limit, 'results' => $data];  

        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('error_msg', $exception); 
        }         
    }

    public function breed(Request $request)
    {
        try {
            $cats = [];
            $dogs = [];

            if($request->all() != NULL){
                $breed_ids = $request->breed_ids;
            }else{
                $breed_ids = null;
            }
    
            $response1 = Http::get('https://api.thecatapi.com/v1/images/search?breed_ids='.$breed_ids);
            $result1 = json_decode($response1->getBody(), true);
            foreach($result1 as $key => $value){
                foreach($value['breeds'] as $item){
                    $cats[] = [
                        "image" => $this->getImageCat($item['reference_image_id']),
                    ];        
                }
            }
            
            $response2 = Http::get('https://api.TheDogAPI.com/v1/images/search?breed_ids='.$breed_ids);
            $result2 = json_decode($response2->getBody(), true);
            
            foreach($result2 as $key => $value){
                foreach($value['breeds'] as $item){
                    $dogs[] = [
                        "image" =>  $this->getImageDog($item['reference_image_id']),
                    ];        
                }
            }
            
            $data = array_merge($cats, $dogs);

            return ['page' => $request->page, 'limit' => $request->limit, 'results' => $data];  

        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('error_msg', $exception); 
        }         
    }

    public function list(Request $request)
    {
        try {
            $cats = [];
            $dogs = [];

            if($request->all() != NULL){
                $limit = ($request->limit <= 1) ? $request->limit : $request->limit / 2;
                $page = (!empty($request->page) && !empty($request->page)) ? '?page='.$request->page.'&limit='.$limit : '';    
            }else{
                $page = '';
            }
    
            $response1 = Http::get('https://api.thecatapi.com/v1/breeds'.$page);
            $result1 = json_decode($response1->getBody(), true);

            foreach($result1 as $key => $value){
                $cats[] = [
                    "id" => (isset($value['image'])) ? $value['image']['id'] : '', 
                    "url" => (isset($value['image'])) ? $value['image']['url'] : '', 
                    "width" => (isset($value['image'])) ? $value['image']['width'] : '', 
                    "height" => (isset($value['image'])) ? $value['image']['height'] : '', 
                ];    
            }
            
            $response2 = $response1 = Http::get('https://api.TheDogAPI.com/v1/breeds'.$page);
            $result2 = json_decode($response2->getBody(), true);
            
            foreach($result2 as $key => $value){
                $dogs[] = [
                    "id" => (isset($value['image'])) ? $value['image']['id'] : '', 
                    "url" => (isset($value['image'])) ? $value['image']['url'] : '', 
                    "width" => (isset($value['image'])) ? $value['image']['width'] : '', 
                    "height" => (isset($value['image'])) ? $value['image']['height'] : '', 
                ];    
            }
            
            $data = array_merge($cats, $dogs);

            return ['page' => $request->page, 'limit' => $request->limit, 'results' => $data];  

        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('error_msg', $exception); 
        }         
    }

    public function images(Request $request)
    {   
        $ctemp = [];
        $dtemp = [];

        $cats = $this->getImageCat($request->imgid);
        $dogs = $this->getImageDog($request->imgid);

        if(isset($cats['status']) && $cats['status'] == 400) {
            //
        }else{    
            $ctemp = [
                'id' => (isset($cats)) ? $cats['id'] : '',
                'url' => (isset($cats)) ? $cats['url'] : '',
                'width' => (isset($cats)) ? $cats['width'] : '',
                'height' => (isset($cats)) ? $cats['height'] : '',
            ];
        }

        if(isset($dogs['status']) &&  $dogs['status'] == 400) {
            //
        }else{    
            $dtemp = [
                'id' => (isset($dogs)) ? $dogs['id'] : '',
                'url' => (isset($dogs)) ? $dogs['url'] : '',
                'width' => (isset($dogs)) ? $dogs['width'] : '',
                'height' => (isset($dogs)) ? $dogs['height'] : ''
            ];
        }

        $data = array_merge($ctemp, $dtemp);

        return $data;
    }

    private function getImageCat($imgID)
    {
        try{
            $response = Http::get('https://api.thecatapi.com/v1/images/'.$imgID);
            $result = json_decode($response->getBody(), true);
            return $result;    
        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('error_msg', $exception); 
        }         

    }
        
    private function getImageDog($imgID)
    {
        try{
            $response = Http::get('https://api.TheDogAPI.com/v1/images/'.$imgID);
            $result = json_decode($response->getBody(), true);
            return $result;    
        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('error_msg', $exception); 
        }         

    }

}
