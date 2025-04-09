<?php

namespace App\Http\Services;

use App\Models\Patron;

class PatronService{

    public function index($data)
    {
        $patron = Patron::query();
        if(isset($data['search'])){
            $searchTerm = $data['search'];
            $patron->where('name','like','%'.$searchTerm.'%');
        }
        if(isset($data['role'])){
            $patron->where('role',$data['role']);
        }
        if(isset($data['status'])){
            $patron->where('status',$data['status']);
        }
        $patron->orderBy($data['sortBy'],$data['sortOrder']);
        return $patron->paginate($data['limit']);
    }


    public function show(string $id)
    {
        $patron = Patron::find($id);
        if(!$patron){
            return null;
        }

        return $patron;
    }
}