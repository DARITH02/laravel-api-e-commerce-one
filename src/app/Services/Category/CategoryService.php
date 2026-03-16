<?php
namespace App\Services\Category;

use App\Repositories\Eloquent\CategoryRepository;

class CategoryService{
    public function __construct(private CategoryRepository $repo){}
    public function list(){
        return $this->repo->paginate();
    }
    public function create(array $data){
        return $this->repo->store($data);
    }
}