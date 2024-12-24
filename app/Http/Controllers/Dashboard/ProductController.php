<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Attribute;
use App\Models\BranchCompany;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\ProductImage;
use App\Models\Size;
use App\Models\StandardSpecification;
use App\Models\SubCategory;

use App\Traits\Dashboard\ProductTrait;
use Illuminate\Http\Request;
use App\Traits\ImageProcessing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ImageProcessing;
    use ProductTrait;
    // 'المنتجات الغير مفعله'
    // php artisan make:model Popular -mcr
    function __construct()
    {
        $this->middleware('permission:جميع المنتجات', ['only' => ['index']]);
        $this->middleware('permission:المنتجات الغير مفعله', ['only' => ['productsInactive']]);
        $this->middleware('permission:اضافة منتج', ['only' => ['store', 'create']]);
        $this->middleware('permission:تعديل منتج', ['only' => ['update', 'edit']]);
        $this->middleware('permission:حذف منتج', ['only' => ['destroy']]);
        $this->middleware('permission:حالة منتج', ['only' => ['updateStatusProduct']]);
        $this->middleware('permission:نسخ المنتج', ['only' => ['editFork']]);
    }



    public function index()
    {
        $products = Product::all();
        $branches = BranchCompany::all();
        return view('dashboard.product.index', compact('products', 'branches'));
    }
    public function productSpacial()
    {
        $products = Product::where('user_id', Auth::user()->id)->get();

        return view('dashboard.product.products-special', compact('products'));
    }

    public function productsInactive()
    {
        $products = Product::where('status', 0)->get();

        return view('dashboard.product.products-inactive', compact('products'));
    }

    public function create()
    {
        $colors = Color::all();
        $sizes = Size::all();
        $categories = Category::all();
        return view('dashboard.product.store', compact('categories', 'colors', 'sizes'));
    }


    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        try {
            // return $request;
            $user = Auth::User();
            $product = $this->createProduct($request, $user);
            $this->handleProductStatus($product, $user);
            $this->saveProductAttributes($request, $product);
            $titals_en = $this->arrayWithoutNull($request->input('titals_en', []));
            $titals_ar = $this->arrayWithoutNull($request->input('titals_ar', []));
            $values_ar = $this->arrayWithoutNull($request->input('values_ar', []));
            $values_en = $this->arrayWithoutNull($request->input('values_en', []));
            $productId = $product->id;
            // return $productId;
            $standards = [];
            if (count($titals_en) > 0) {
                $standards = array_map(function ($i) use ($titals_en, $titals_ar, $values_ar, $values_en, $productId) {
                    return [
                        'title_en' => $titals_en[$i],
                        'title_ar' => $titals_ar[$i],
                        'value_ar' => $values_ar[$i],
                        'value_en' => $values_en[$i],
                        'product_id' => $productId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, range(0, count($request->titals_ar) - 1));

                $product->standardSpecification()->createMany($standards);
            }
            // return $product;
            $this->saveProductImages($request, $product);
            DB::commit();
            session()->flash('Add', 'تم اضافة المنتج بنجاح ');
            return redirect()->route('products.special')->with('success', 'Category created successfully');
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            session()->flash('delete', 'لم يتم  اضافة المنتج ');
            return back();
        }
    }

    public function prductDetails(Request $request)
    {
        $productId = $request->input('product_id'); // استخدام الـ ID هنا
        $product = Product::with('branches')->find($productId);

        return response()->json($product);
    }
    public function assignBranchCompany(Request $request)
    {
        foreach ($request->branches as $branchId) {
            ProductBranch::updateOrCreate(
                [
                    'product_id' => $request->id,
                    'branch_company_id' => $branchId,
                ],
                ['stock_quantity' => 0] // Default stock_quantity value
            );
        }

        session()->flash('Add', 'تم تعيين الفروع بنجاح');
        return redirect()->back();
    }
    public function affiliateProduct(Request $request)
    {
        $id = $request->input('id');
        $locale = $request->input('locale');
        $product = Product::with('images', 'attribute.size', 'attribute.color')
            ->select('id', 'name_' . $locale . ' AS name', 'price', 'image', 'discount_start', 'discount_end', 'discount', 'description_' . $locale . ' AS description', 'quantity', 'sub_category_id', 'views')
            ->findOrFail($id);

        $product->each(function ($product) {
            $product->final_price;
        });

        $relatedProducts = Product::where('sub_category_id', $product->sub_category_id)->where('id', '!=', $product->id)->Sorted()->take(5)->get();

        $relatedProducts->each(function ($product) {
            $product->final_price;
        });

        return view('dashboard.product.product-detalis', compact('product', 'locale'));
    }

    public function edit(int $id)
    {
        $product = Product::with(['images'])->findOrFail($id);
        $category = Category::findOrFail($product->subCategory->category_id);
        $subcategories = SubCategory::where('category_id', $category->id)->get();
        $categories = Category::all();
        $attributes = Attribute::where('product_id', $product->id)->get();
        $standers = StandardSpecification::where('product_id', $product->id)->get();
        // return $standers;
        $colors = Color::all();
        $sizes = Size::all();
        $attributeSizes = [];
        $attributeColors = [];
        $sizes_colors = [];
        foreach ($attributes as $index => $value) {
            if ($value->size_id == null) {
                $attributeColors[] = $value;
            } else if ($value->color_id == null) {
                $attributeSizes[] = $value;
            } else {
                $sizes_colors[] = $value;
            }
        }
        return view('dashboard.product.update', compact('product', 'category', 'subcategories', 'categories', 'attributeSizes', 'attributeColors', 'sizes_colors', 'sizes', 'colors', 'standers'));
    }
    public function editFork(int $id)
    {
        DB::beginTransaction();
        try {
            $originalProduct = Product::findOrFail($id);

            // Create a clone of the original product with some modifications
            $clonedProduct = $originalProduct->replicate();
            $clonedProduct->name_ar = $originalProduct->name_ar . ' (Clone)';
            $clonedProduct->name_en = $originalProduct->name_en . ' (Clone)';
            $clonedProduct->user_id = Auth::user()->id;
            $clonedProduct->created_at = now();
            $clonedProduct->save();

            // Clone attributes
            if ($originalProduct->attribute) {
                foreach ($originalProduct->attribute as $originalAttribute) {
                    $clonedAttribute = $originalAttribute->replicate();
                    $clonedAttribute->product_id = $clonedProduct->id;
                    $clonedAttribute->save();
                }
            }

            // Clone images if they exist
            if ($originalProduct->images) {
                foreach ($originalProduct->images as $originalImage) {
                    $clonedImage = $originalImage->replicate();
                    $clonedImage->product_id = $clonedProduct->id;
                    $clonedImage->save();
                }
            }

            DB::commit();
            session()->flash('Add', 'تم نسخ المنتج بنجاح');
            return redirect()->route('products.special', ['product' => $clonedProduct->id]);
        } catch (\Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function update(ProductUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            // return $request;
            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);
            $this->updateBasicDetails($product, $request);
            $this->updateDiscountDetails($product, $request);
            $this->updateStatusAndNotifications($product, $user, $request);
            $this->handleImages($product, $request);
            $this->updateAttributes($product, $request);
            $product->save();
            DB::commit();
            session()->flash('Add', 'تم تعديل المنتج بنجاح');
            return redirect()->back()->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('delete', 'لم يتم التعديل علي المنتج');
            return $e;
            back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            // Retrieve the product
            $product = Product::findOrFail($request->id);
            $this->deleteImage($product->image);

            // Delete the product images and remove the images from storage
            $product->images()->each(function ($image) {
                $this->deleteImage($image->image);
            });

            $product->delete();

            DB::commit();
            session()->flash('delete', 'تم الحذف  ');
            return redirect()->back()->withSuccess('Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteImage($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
        }
        $productImagesCount = ProductImage::where('product_id', $image->product_id)->count();

        // Check if it's the last image
        if ($productImagesCount <= 1) {
            return response()->json(['success' => false, 'message' => 'You cannot delete the last image.'], 400);
        }
        // $this->deleteImage($image->image);
        $image->delete();
        return response()->json(['success' => true, 'message' => 'Image deleted successfully.'], 200);
    }



    public function destroyAttr(Request $request)
    {
        DB::beginTransaction();
        try {
            // Retrieve the product
            $attribute = Attribute::findOrFail($request->id);
            // Delete the product
            $attribute->delete();

            DB::commit();
            return redirect()->back()->withSuccess('attribute deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function updateStatusProduct(Request $request)
    {
        $isToggleOnString = (string) $request->isToggleOn;
        $status = true;
        $productId = $request->input('productId');
        if ($isToggleOnString == "true") {
            $status = true;
        } else {
            $status = false;
        }



        $product = Product::find($productId);

        if ($product) {
            // Update the status field
            $product->status = $status;
            $product->save();

            return response()->json(['success' => true, 'message' => 'product status  updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'product not found']);
    }

    public function getSubsections(Request $request)
    {
        $categoryId = $request->input('category');

        // Retrieve subsections based on the selected category ID
        $subcategory = SubCategory::where('category_id', $categoryId)->pluck('name_ar', 'id');

        return response()->json($subcategory, 200);
    }
}
