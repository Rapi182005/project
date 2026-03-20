<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index() {
        if (!session('admin_logged_in')) return redirect()->route('login');
        $products = Product::all();
        return view('admin.dashboard', compact('products'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();
        return back()->with('success', 'Product added successfully!');
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);
        
        // Delete the image file if it exists
        if($product->image && File::exists(public_path('products/' . $product->image))) {
            File::delete(public_path('products/' . $product->image));
        }
        
        $product->delete();
        return back()->with('success', 'Product deleted!');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->price = $request->price;

        if ($request->hasFile('image')) {
            // Delete the old file from the folder if it exists
            if($product->image && File::exists(public_path('products/'.$product->image))) {
                File::delete(public_path('products/'.$product->image));
            }
            
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('products'), $imageName);
            $product->image = $imageName;
        }

        $product->save();
        return back()->with('success', 'Product updated successfully!');
    }
}