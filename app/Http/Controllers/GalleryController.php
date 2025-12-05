<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = Gallery::orderBy('gallery_date', 'desc')->paginate(10);
        return view('teacher.gallery.gallery-index.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teacher.gallery.gallery-create.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all()); // This will show all data received by the store method
        // Ensure that the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a gallery.');
        }

        // Validate the incoming request
        $request->validate([
            'gallery_title' => 'required|string|max:255',
            'gallery_description' => 'nullable|string',
            'gallery_date' => 'required|date',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Save the gallery information
            $gallery = Gallery::create([
                'user_id' => Auth::id(), // Now using Auth::id() instead of auth()->id()
                'gallery_title' => $request->gallery_title,
                'gallery_description' => $request->gallery_description,
                'gallery_date' => $request->gallery_date,
            ]);

            // Initialize an array to hold the image URLs
            $imageUrls = [];

            // Process each uploaded image
            foreach ($request->file('images') as $image) {
                // Store the image in the 'gallery_images' directory
                $path = $image->store('gallery_images', 'public');

                // Add the image path to the imageUrls array
                $imageUrls[] = $path;
            }

            // If there are any images, save them to the database
            foreach ($imageUrls as $url) {
                GalleryImage::create([
                    'galleries_id' => $gallery->id,
                    'image_url' => $url,
                ]);
            }

            // Commit the transaction if everything was successful
            DB::commit();

            // Return a success message
            return redirect()->route('gallery-activity.index')->with('success', 'Gallery created successfully!');
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();

            // Return an error message
            return back()->withErrors(['error' => 'Something went wrong, please try again!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the gallery by ID or throw a 404 exception
        $gallery = Gallery::with('galleryImages')->findOrFail($id);

        // Log the gallery details for debugging purposes
        Log::info("Gallery details: " . $gallery->gallery_title);

        // Return the gallery details view
        return view('teacher.gallery.gallery-show.index', compact('gallery'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Mengambil data gallery dan gambar terkait berdasarkan id
        $gallery = Gallery::with('galleryImages')->findOrFail($id);

        return view('teacher.gallery.gallery-edit.index', compact('gallery'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        // Validasi input
        $request->validate([
            'gallery_title' => 'required|string|max:255',
            'gallery_description' => 'nullable|string',
            'gallery_date' => 'required|date',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:gallery_images,id',
        ]);

        // Perbarui data galeri
        $gallery->update([
            'gallery_title' => $request->gallery_title,
            'gallery_description' => $request->gallery_description,
            'gallery_date' => $request->gallery_date,
        ]);

        // Hapus gambar jika diminta
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = GalleryImage::find($imageId);
                if ($image) {
                    // Hapus gambar dari penyimpanan
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }
            }
        }

        // Tambahkan gambar baru jika ada
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $imageFile) {
                $path = $imageFile->store('gallery_images', 'public');
                $gallery->galleryImages()->create([
                    'image_url' => $path,
                ]);
            }
        }

        return redirect()->route('gallery-activity.index')->with('success', 'Gallery updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);

            // Log Gallery found
            Log::info("Gallery found: " . $gallery->gallery_title);

            // Check if the gallery has associated images and delete them
            $galleryImages = $gallery->galleryImages; // Get gallery images using the correct relationship

            if ($galleryImages->isNotEmpty()) {
                foreach ($galleryImages as $image) {
                    $imagePath = 'public/' . $image->image_url;

                    // Log the image path being deleted
                    Log::info("Deleting image: " . $imagePath);

                    // Check if the file exists before trying to delete
                    if (Storage::exists($imagePath)) {
                        Storage::delete($imagePath);  // Delete image file from storage
                        $image->delete();  // Delete the image record from the database
                    } else {
                        Log::warning("Image not found: " . $imagePath);
                    }
                }
            } else {
                Log::warning("No images found for gallery: " . $gallery->gallery_title);
            }

            // Delete the gallery record itself
            $gallery->delete();

            // Log successful deletion
            Log::info("Gallery deleted: " . $gallery->gallery_title);

            return redirect()->route('gallery-activity.index')->with('success', 'Gallery deleted successfully!');
        } catch (\Exception $e) {
            // Log error details
            Log::error("Error deleting gallery: " . $e->getMessage());

            return redirect()->route('gallery-activity.index')->with('error', 'Failed to delete gallery!');
        }
    }
}
