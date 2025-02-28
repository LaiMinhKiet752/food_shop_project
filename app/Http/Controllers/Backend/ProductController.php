<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\MultiImage;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
    public function AllProduct()
    {
        $products = Product::orderBy('status', 'DESC')->latest()->get();
        return view('backend.product.product_all', compact('products'));
    } //End Method

    public function AddProduct()
    {
        $brands = Brand::latest()->get();
        $categories = Category::latest()->get();
        $suppliers = Supplier::latest()->get();
        return view('backend.product.product_add', compact('suppliers', 'brands', 'categories'));
    } //End Method

    public function StoreProduct(Request $request)
    {
        $product_check = Product::onlyTrashed()->get();
        foreach ($product_check as $product) {
            if ($product['product_name'] == $request->product_name || $product['product_code'] == $request->product_code) {
                $notification = array(
                    'message' => "This Product Has Been Temporarily Removed. Please Check Again In 'Restore Product'",
                    'alert-type' => 'warning',
                );
                return redirect()->back()->with($notification);
            }
        }
        $request->validate([
            'product_code' => 'unique:products',
            'product_thumbnail' => 'image|max:2048',
            'multiple_image.*' => 'image|max:2048',
        ], [
            'product_code.unique' => 'Product code already exists.',
            'product_thumbnail.image' => 'The uploaded file must be an image in one of the following formats: jpg, jpeg, png, bmp, gif, svg, or webp.',
            'product_thumbnail.max' => 'The maximum upload image size is 2MB.',
            'multiple_image.*.image' => 'The uploaded file must be an image in one of the following formats: jpg, jpeg, png, bmp, gif, svg, or webp.',
            'multiple_image.*.max' => 'The maximum upload image size is 2MB.',
        ]);

        //Main Thumbnail
        $file = $request->file('product_thumbnail');
        $filename = hexdec(uniqid()) . '_product_thumbnail' . '.' . $file->getClientOriginalExtension();
        Image::make($file)->resize(1000, 1000)->save('upload/products/thumbnail/' . $filename);
        $save_url = 'upload/products/thumbnail/' . $filename;

        $product_id =  Product::insertGetId([
            'supplier_id' => $request->supplier_id,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,

            'product_name' => $request->product_name,
            'product_code' => $request->product_code,
            'product_thumbnail' => $save_url,
            'product_slug' => strtolower(str_replace(' ', '-', $request->product_name)),
            'product_quantity' => $request->product_quantity,
            'product_tags' => $request->product_tags,
            'product_weight' => $request->product_weight,
            'product_measure' => $request->product_measure,
            'product_dimensions' => $request->product_dimensions,

            'short_description' => $request->short_description,
            'long_description' => $request->long_description,

            'selling_price' => $request->selling_price,
            'discount_price' => $request->discount_price,
            'manufacturing_date' => $request->manufacturing_date,
            'expiry_date' => $request->expiry_date,

            'hot_deals' => $request->hot_deals,
            'featured' => $request->featured,
            'special_offer' => $request->special_offer,
            'special_deals' => $request->special_deals,

            'status' => 1,
            'created_at' => Carbon::now(),
        ]);

        //Multiple Images Upload
        $images = $request->file('multiple_image');
        foreach ($images as $image) {
            $make_name = hexdec(uniqid()) . '_product' . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(1000, 1000)->save('upload/products/multiple_images/' . $make_name);
            $uploadPath = 'upload/products/multiple_images/' . $make_name;

            MultiImage::insert([
                'product_id' => $product_id,
                'photo_name' => $uploadPath,
                'created_at' => Carbon::now(),
            ]);
        }
        $notification = array(
            'message' => 'Product Added Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->route('all.product')->with($notification);
    } //End Method

    public function EditProduct($id)
    {
        $products = Product::findOrFail($id);
        $activeVendor = User::where('status', 'active')->where('role', 'vendor')->latest()->get();
        $suppliers = Supplier::latest()->get();
        $brands = Brand::latest()->get();
        $categories = Category::latest()->get();
        $get_category_id = $products->category_id;
        $subcategory = SubCategory::where('category_id', $get_category_id)->latest()->get();
        $multipleImages = MultiImage::where('product_id', $id)->latest()->get();
        return view('backend.product.product_edit', compact('activeVendor', 'suppliers', 'brands', 'categories', 'subcategory', 'products', 'multipleImages'));
    } //End Method

    public function UpdateProduct(Request $request)
    {
        $product_check = Product::onlyTrashed()->get();
        foreach ($product_check as $product) {
            if ($product['product_name'] == $request->product_name || $product['product_code'] == $request->product_code) {
                $notification = array(
                    'message' => "This Product Has Been Temporarily Removed. Please Check Again In 'Restore Product'",
                    'alert-type' => 'warning',
                );
                return redirect()->back()->with($notification);
            }
        }
        $product_id = $request->id;
        $current_product_code = Product::findOrFail($product_id)->product_code;
        if ($current_product_code != $request->product_code) {
            $request->validate([
                'product_code' => 'unique:products',
            ], [
                'product_code.unique' => 'Product code already exists.',
            ]);
            Product::findOrFail($product_id)->update([
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,

                'product_name' => $request->product_name,
                'product_code' => $request->product_code,
                'product_slug' => strtolower(str_replace(' ', '-', $request->product_name)),
                'product_quantity' => $request->product_quantity,
                'product_tags' => $request->product_tags,
                'product_weight' => $request->product_weight,
                'product_measure' => $request->product_measure,
                'product_dimensions' => $request->product_dimensions,

                'short_description' => $request->short_description,
                'long_description' => $request->long_description,

                'selling_price' => $request->selling_price,
                'discount_price' => $request->discount_price,
                'manufacturing_date' => $request->manufacturing_date,
                'expiry_date' => $request->expiry_date,

                'hot_deals' => $request->hot_deals,
                'featured' => $request->featured,
                'special_offer' => $request->special_offer,
                'special_deals' => $request->special_deals,

                'updated_by' => Auth::user()->id,

                'status' => 1,
            ]);

            $notification = array(
                'message' => 'Product Updated Without Image Successfully!',
                'alert-type' => 'success',
            );
            return redirect()->route('all.product')->with($notification);
        } else {
            Product::findOrFail($product_id)->update([
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,

                'product_name' => $request->product_name,
                'product_code' => $request->product_code,
                'product_slug' => strtolower(str_replace(' ', '-', $request->product_name)),
                'product_quantity' => $request->product_quantity,
                'product_tags' => $request->product_tags,
                'product_weight' => $request->product_weight,
                'product_measure' => $request->product_measure,
                'product_dimensions' => $request->product_dimensions,

                'short_description' => $request->short_description,
                'long_description' => $request->long_description,

                'selling_price' => $request->selling_price,
                'discount_price' => $request->discount_price,
                'manufacturing_date' => $request->manufacturing_date,
                'expiry_date' => $request->expiry_date,

                'hot_deals' => $request->hot_deals,
                'featured' => $request->featured,
                'special_offer' => $request->special_offer,
                'special_deals' => $request->special_deals,

                'updated_by' => Auth::user()->id,

                'status' => 1,
            ]);

            $notification = array(
                'message' => 'Product Updated Without Image Successfully!',
                'alert-type' => 'success',
            );
            return redirect()->route('all.product')->with($notification);
        }
    } //End Method

    public function UpdateProductThumbnail(Request $request)
    {
        $product_id = $request->id;
        $oldImage = $request->old_image;
        $file = $request->file('product_thumbnail');
        $product_thumbnail = Product::where('product_thumbnail', $oldImage)->first();

        if ($oldImage != NULL && $product_thumbnail != NULL && $file == NULL) {
            $notification = array(
                'message' => "Upload Failed Because You Didn't Choose An Image!",
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            $request->validate([
                'product_thumbnail' => 'image|max:2048',
            ], [
                'product_thumbnail.image' => 'The uploaded file must be an image in one of the following formats: jpg, jpeg, png, bmp, gif, svg, or webp.',
                'product_thumbnail.max' => 'The maximum upload image size is 2MB.',
            ]);

            $filename = hexdec(uniqid()) . '_product_thumbnail' . '.' . $file->getClientOriginalExtension();
            Image::make($file)->resize(1000, 1000)->save('upload/products/thumbnail/' . $filename);
            $save_url = 'upload/products/thumbnail/' . $filename;

            if (file_exists($oldImage)) {
                unlink($oldImage);
            }

            Product::findOrFail($product_id)->update([
                'product_thumbnail' => $save_url,
            ]);

            $notification = array(
                'message' => 'Product Thumbnail Updated Successfully!',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    } //End Method

    public function AddNewProductMultipleImages(Request $request)
    {
        $product_id = $request->id;
        $images = $request->add_new_multiple_image;
        if ($images == NULL) {
            $notification = array(
                'message' => "Upload Failed Because You Didn't Choose An Image!",
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            $request->validate([
                'add_new_multiple_image.*' => 'image|max:2048',
            ], [
                'add_new_multiple_image.*.image' => 'The uploaded file must be an image in one of the following formats: jpg, jpeg, png, bmp, gif, svg, or webp.',
                'add_new_multiple_image.*.max' => 'The maximum upload image size is 2MB.',
            ]);
            $images = $request->file('add_new_multiple_image');
            foreach ($images as $image) {
                $make_name = hexdec(uniqid()) . '_product' . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(1000, 1000)->save('upload/products/multiple_images/' . $make_name);
                $uploadPath = 'upload/products/multiple_images/' . $make_name;

                MultiImage::insert([
                    'product_id' => $product_id,
                    'photo_name' => $uploadPath,
                    'created_at' => Carbon::now(),
                ]);
            }
            $notification = array(
                'message' => 'Product Multiple Images Added Successfully!',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    } //End Method

    public function UpdateProductMultipleImages(Request $request)
    {
        $images = $request->multiple_image;
        if ($images == NULL) {
            $notification = array(
                'message' => "Upload Failed Because You Didn't Choose An Image!",
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        } else {
            $request->validate([
                'multiple_image.*' => 'image|max:2048',
            ], [
                'multiple_image.*.image' => 'The uploaded file must be an image in one of the following formats: jpg, jpeg, png, bmp, gif, svg, or webp.',
                'multiple_image.*.max' => 'The maximum upload image size is 2MB.',
            ]);
            foreach ($images as $id => $image) {
                $imageDelete = MultiImage::findOrFail($id);
                unlink($imageDelete->photo_name);
                $make_name = hexdec(uniqid()) . '_product' . '.' . $image->getClientOriginalExtension();
                Image::make($image)->resize(800, 800)->save('upload/products/multiple_images/' . $make_name);
                $uploadPath = 'upload/products/multiple_images/' . $make_name;

                MultiImage::where('id', $id)->update([
                    'photo_name' => $uploadPath,
                ]);
            }
            $notification = array(
                'message' => 'Product Multiple Images Updated Successfully!',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        }
    } //End Method

    public function MultipleImagesDelete($id)
    {
        $old_image = MultiImage::findOrFail($id);
        unlink($old_image->photo_name);

        MultiImage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Product Multiple Images Deleted Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function ProductInActive($id)
    {
        Product::findOrFail($id)->update([
            'status' => 0,
        ]);
        $notification = array(
            'message' => 'Product Inactivated Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function ProductActive($id)
    {
        Product::findOrFail($id)->update([
            'status' => 1,
        ]);
        $notification = array(
            'message' => 'Product Activated Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function DeleteProduct($id)
    {
        $product = Product::findOrFail($id);
        Review::where('product_id', $id)->delete();
        $product->update(['deleted_by' => Auth::user()->id]);
        Product::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Product Deleted Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function RestoreProduct()
    {
        $products = Product::onlyTrashed()->get();
        return view('backend.product.product_restore', compact('products'));
    } //End Method

    public function RestoreProductSubmit($id)
    {
        Product::whereId($id)->restore();
        $notification = array(
            'message' => 'Product Restored Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function ForceDeleteProduct($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        unlink($product->product_thumbnail);
        Product::whereId($id)->forceDelete();
        $images = MultiImage::where('product_id', $id)->get();
        if ($images) {
            foreach ($images as $image) {
                unlink($image->photo_name);
                MultiImage::where('product_id', $id)->delete();
            }
        }
        $notification = array(
            'message' => 'Product Deleted Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function ProductStock()
    {
        $products = DB::table('products')->orderByRaw('CONVERT(product_quantity, SIGNED) asc')->get();
        return view('backend.product.product_stock', compact('products'));
    } //End Method

    public function AddProductFromReturnedOrder()
    {
        $invoice = OrderReturn::with('product', 'order')->select('order_id')->groupBy('order_id')->get();
        return view('backend.product.add_product_from_returned_order', compact('invoice'));
    } //End Method

    public function ViewProductFromReturnedOrder($order_id)
    {
        $invoice = OrderReturn::with('product', 'order')->where('order_id', $order_id)->get();
        // dd($invoice);
        return view('backend.product.view_product_from_returned_order', compact('invoice'));
    } //End Method

    public function ViewAddProductFromReturnedOrder($product_id)
    {
        $products = Product::where('id', $product_id)->first();
        $invoice = OrderReturn::with('product', 'order')->where('product_id', $product_id)->first();
        // dd($products);
        return view('backend.product.view_add_product_from_returned_order', compact('products', 'invoice'));
    } //End Method

    public function StoreProductFromReturnedOrder(Request $request)
    {
        // dd($request->all());
        $product_id = $request->product_id;
        $order_id = $request->order_id;
        Product::findOrFail($product_id)->update([
            'product_quantity' => DB::raw('product_quantity + ' . $request->product_quantity)
        ]);
        $invoice = OrderReturn::where('product_id', $product_id)->where('order_id', $order_id)->first();
        // dd($invoice);
        $invoice->update([
            'quantity' => DB::raw('quantity - ' . $request->product_quantity)
        ]);
        $quantity_check = OrderReturn::where('product_id', $product_id)->where('order_id', $order_id)->first('quantity');
        if ($quantity_check->quantity == 0) {
            OrderReturn::where('product_id', $product_id)->where('order_id', $order_id)->delete();
        }
        $notification = array(
            'message' => 'Added Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->route('view.product.from.returned.order', $order_id)->with($notification);
    } //End Method

    public function DeleteProductFromReturnedOrder($order_id, $product_id)
    {
        OrderReturn::where('product_id', $product_id)->where('order_id', $order_id)->delete();
        $notification = array(
            'message' => 'Deleted Successfully!',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    } //End Method
}
