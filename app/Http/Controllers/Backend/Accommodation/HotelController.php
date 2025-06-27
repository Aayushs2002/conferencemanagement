<?php

namespace App\Http\Controllers\Backend\Accommodation;

use App\Http\Controllers\Controller;
use App\Models\Accomodation\Hotel;
use App\Services\File\FileService;
use Exception;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected FileService $file_service) {}

    public function index($society, $conference)
    {
        // $conferenceDetail = conference_detail();

        // if (empty($conferenceDetail)) {
        //     return redirect()->route('dashboard');
        // }

        $hotels = Hotel::where(['conference_id' => $conference->id, 'status' => 1])->latest()->get();
        return view('backend.accomodation.hotel.index', compact('hotels', 'society', 'conference'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($society, $conference)
    {
        return view('backend.accomodation.hotel.create', compact('society', 'conference'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $society, $conference)
    {
        try {
            $rules = [
                'name' => 'required|unique:hotels,name',
                'address' => 'required',
                'contact_person' => 'required',
                'phone' => 'required',
                'email' => 'required|email',
                'rating' => 'nullable',
                'featured_image' => 'nullable|mimes:jpg,png',
                'cover_image' => 'required|mimes:jpg,png',
                'pics' => 'nullable',
                'pics.*' => 'mimes:jpg,png',
                'room_type' => 'nullable',
                'description' => 'nullable',
                'google_map' => 'nullable',
                'price' => 'nullable',
                'website' => 'nullable|url',
                'facility' => 'nullable',
                'promo_code' => 'nullable',
            ];

            $validated = $request->validate($rules);

            if (!empty($validated['pics']) && count($validated['pics']) > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }

            $validated['slug'] = slugify($validated['name']);

            if (!empty($validated['featured_image'])) {
                $validated['featured_image'] = $this->file_service->fileUpload($validated['featured_image'], 'hotel-featured_image', 'hotel/featured-image/');
            }
            if (!empty($validated['cover_image'])) {
                $validated['cover_image'] = $this->file_service->fileUpload($validated['cover_image'], 'hotel-cover_image', 'hotel/cover-image/');
            }

            if (!empty($validated['pics'])) {
                foreach ($validated['pics'] as $key => $pic) {
                    $validated['images'][] = [
                        'fileName' => $this->file_service->fileUpload($pic, 'hotel-images', 'hotel/images/'),
                        'room_type' => $validated['room_type'][$key]
                    ];
                }
            }

            $validated['conference_id'] = $conference->id;

            Hotel::create($validated);

            return redirect()->route('hotel.index', [$society, $conference])->with('status', 'Hotel Added Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($society, $conference, Hotel $hotel)
    {
        return view('backend.accomodation.hotel.create', compact('hotel', 'society', 'conference'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $society, $conference, Hotel $hotel)
    {
        try {
            $rules = [
                'name' => 'required|unique:hotels,name,' . $hotel->id,
                'address' => 'required',
                'contact_person' => 'nullable',
                'phone' => 'nullable',
                'email' => 'nullable|email',
                'rating' => 'nullable',
                'featured_image' => 'nullable|mimes:jpg,png',
                'cover_image' => 'nullable|mimes:jpg,png',
                'pics' => 'nullable',
                'pics.*' => 'mimes:jpg,png',
                'room_type' => 'nullable',
                'room_type_old' => 'nullable',
                'description' => 'nullable',
                'google_map' => 'nullable',
                'price' => 'nullable',
                'website' => 'nullable|url',
                'facility' => 'nullable',
                'promo_code' => 'nullable',
            ];

            $validated = $request->validate($rules);

            $countOldImages = 0;
            if (!empty($hotel->images)) {
                $countOldImages = count($hotel->images);
            }
            if (!empty($validated['pics']) && count($validated['pics']) + $countOldImages > 5) {
                return redirect()->back()->withInput()->with('delete', 'Images Limitation Crossed.');
            }

            $validated['slug'] = slugify($validated['name']);

            if (!empty($validated['featured_image'])) {
                $this->file_service->deleteFile($hotel->featured_image, 'hotel/featured-image');

                $validated['featured_image'] = $this->file_service->fileUpload($validated['featured_image'], 'hotel-featured_image', 'hotel/featured-image/');
            }
            if (!empty($validated['cover_image'])) {
                $this->file_service->deleteFile($hotel->cover_image, 'hotel/featured-image');

                $validated['cover_image'] = $this->file_service->fileUpload($validated['cover_image'], 'hotel-cover_image', 'hotel/cover-image/');
            }

            $newImages = [];
            $oldImages = [];
            if (!empty($hotel->images)) {
                $oldImages = $hotel->images;
                foreach ($oldImages as $k => $v) {
                    $oldImages[$k]['room_type'] = $validated['room_type_old'][$k];
                }
            }
            if (!empty($validated['pics'])) {
                foreach ($validated['pics'] as $key => $pic) {
                    $newImage  = [
                        'fileName' => $this->file_service->fileUpload($pic, 'hotel-images', 'hotel/images/'),
                        'room_type' => $validated['room_type'][$key]
                    ];

                    $newImages[] = $newImage;
                }
            }

            $validated['images'] = array_merge($oldImages, $newImages);

            $hotel->update($validated);

            return redirect()->route('hotel.index', [$society, $conference])->with('status', 'Hotel Updated Successfully');
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($society, $conference, Hotel $hotel)
    {
        $hotel->update(['status' => 0]);

        return redirect()->route('hotel.index', [$society, $conference])->with('delete', 'Hotel Deleted Successfully');
    }

    public function deleteImage(Hotel $hotel, string $img)
    {
        $this->file_service->deleteFile($img, 'hotel/images');

        $images = $hotel->images;

        if (count($hotel->images) == 1) {
            $hotel->update(['images' => null]);
        } else {
            foreach ($images as $key => $value) {
                if ($value['fileName'] == $img) {
                    unset($images[$key]);
                    break;
                }
            }
            $images = array_values($images);
            $hotel->update(['images' => $images]);
        }

        return redirect()->back()->with('delete', 'Image Deleted Successfully');
    }

    public function changeStatus(Hotel $hotel)
    {
        if ($hotel->visible_status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        $hotel->update(['visible_status' => $status]);

        return redirect()->back()->with('status', 'Hotel featured status changed successfully.');
    }
}
