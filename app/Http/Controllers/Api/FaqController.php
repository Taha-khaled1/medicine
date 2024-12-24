<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{

    public function index()
    {
        try {
            $countries = Faq::paginate(50);
            return successResponse(['faqs' => buildPaginatedResponse($countries)]);
        } catch (\Throwable $e) {
            return errorResponse(__('custom.failed_to_retrieve_data'));
        }
    }
}
//    public function index()
//     {
//         try {
//             $brands = Brand::where('category_id', 1)->where('id', '>', 152)->get();
//             $iterationCount = 0; // Initialize the iteration count
//             foreach ($brands as $brand) {
//                 $brandName = str_replace(' ', '-', $brand->name_en);
//                 $url = "https://dubai.dubizzle.com/motors/svc/buyer/api/v1/options/?category=motors/used-cars/{$brandName}&city=0&filter_name=category";
//                 $response = Http::get($url);
//                 $data = $response->json();
//                 if (isset($data['options'])) {
//                     foreach ($data['options'] as $option) {
//                         ModelBrand::create([
//                             'name_en' => $option['label']['en'],
//                             'name_ar' => $option['label']['ar'],
//                             'brand_id' => $brand->id // Assuming a static brand_id for simplicity; adjust as necessary.
//                         ]);
//                     }
//                 }

//                 $iterationCount++; // Increment the iteration count
//                 if ($iterationCount % 10 === 0) {
//                     // Sleep for 60 seconds after every 10 iterations
//                     // sleep(60);
//                     echo "iterationCount : $iterationCount" . " \n ";
//                 }
//             }
//             return response()->json(['message' => 'Data fetched and stored successfully.'], 200);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }
