<?php

namespace App\Http\Controllers;

use App\Models\ProductMasterList;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Input;

class ProductMasterListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        # Prepare columns
        $categories = ProductMasterList::all(['product_id','types','brand','model','capacity','quantity']);
        return response()->json($categories);
        $columns = ['product_id','types','brand','model','capacity','quantity'];
        $length = $request->input('length');
        $column = $request->input('column');
        $search_input = $request->input('search');
        # Get product master list
        $query = ProductMasterList::select('product_id','types','brand','model','capacity','quantity')
                                    ->orderBy($columns[$column]);
        # Filter list
        if ($search_input) {
            $query->where(function($query) use ($search_input) {
                $query->where('product_id', 'like', '%' . $search_input . '%')
                      ->orWhere('model', 'like', '%' . $search_input . '%')
                      ->orWhere('quantity', 'like', '%' . $search_input . '%');
            });
        }
        # Paginate list
        $products = $query->paginate($length);
        return ['data' => $products];
    }

    /**
     * Update quantity of products through uploading excel file
     */
    public function formSubmit(Request $request)
    {   
        # File validation
        $file = $request->file->getClientOriginalExtension();
        $ext = strtolower($file);
        if ($ext != "xlsx") {
            $res = [
                "success" => false,
                "message" => 'Unsupported upload type'
            ];

            return response()->json($res);
        }

        # Import and read excel file
        $import = new ProductImport;
        Excel::import($import, $request->file);
        $data = $import->getArray();
        $resultant = [];
        
        # Loop through products and calculate quantity
        array_walk_recursive($data,function($value,$key) use (&$resultant){
            if(is_numeric($value)) {
                $resultant['product'] = $value;
                if(!isset($resultant[$value])){
                    $resultant[$value] = 0;
                }
            }
            else {
                $product = $resultant['product'];
                if($value == 'Buy'){
                    $resultant[$product] += 1;
                }
                else if($value == 'Sold') {
                    $resultant[$product] -= 1;
                }
            }
        },$resultant);
        unset($resultant['product']);

        $products = ProductMasterList::whereIn('product_id', array_keys($resultant))->get();
        # Update quantity of products
        foreach($products as $product){
            foreach($resultant as $key => $value){
                if($product->product_id == $key){
                    $product->quantity += $value;
                    $product->save();
                }
            }
        }
 
        return response()->json([
            'success' => true,
            'message' => 'You have successfully upload file.'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductMasterList $productMasterList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductMasterList $productMasterList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductMasterList $productMasterList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductMasterList $productMasterList)
    {
        //
    }
}
