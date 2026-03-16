<?php
namespace App\Repositories\Eloquent;

use App\Models\Category;

class CategoryRepository 
{
   public function paginate(){
    return Category::latest()->paginate(10);
   }
   public function store(array $data){
    return Category::create($data);
   }
}