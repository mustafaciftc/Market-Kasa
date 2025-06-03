<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
	 public function index()
	{
		$categories = Category::all();
		return view('kategoriyonetimi', compact('categories'));
	}
	
    public function create()
    {
        return view('kategoriyonetimi');
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:categories,name',
    ]);

    $category = Category::create($request->all());
    return response()->json(['success' => true, 'message' => 'Kategori eklendi.', 'category' => $category], 201);
}

	  public function edit($id)
	{
		$category = Category::findOrFail($id);
		return response()->json([
			'id' => $category->id,
			'name' => $category->name,
		]);
	}
	
	   public function update(Request $request, $id)
	{
		$category = Category::findOrFail($id);
		$request->validate([
			'name' => 'required|string|max:255|unique:categories,name,' . $id,
		]);

		$category->update($request->all());
		return response()->json(['success' => true, 'message' => 'Kategori güncellendi.', 'category' => $category]);
	}
    

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
    
        return redirect()->route('kategoriyonetimi')->with('success', 'Kategori başarıyla silindi.');
    }
    
}
