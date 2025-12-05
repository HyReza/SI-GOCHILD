<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Menangani pencarian artikel
        $query = Article::query();

        // Cek jika ada parameter pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Ambil artikel dengan pagination
        $articles = $query->with('category')
            ->latest()
            ->paginate(10)
            ->appends(['search' => $request->search]); // Menambahkan parameter search di pagination

        return view('admin.articles.articles-index.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.articles.articles-create.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi form input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string', // Konten Quill
            'category_id' => 'required|exists:categories,id', // Pastikan kategori ada
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg', // Validasi gambar
        ]);

        // Jika validasi gagal, kembalikan ke form dengan pesan error
        if ($validator->fails()) {
            return redirect()->route('articles.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Generate slug dari judul artikel
        $slug = Str::slug($request->title);

        // Cek apakah slug sudah ada, jika ada tambahkan angka
        $slug = $this->generateUniqueSlug($slug);

        // Proses upload gambar
        $imagePath = $request->file('image')->store('articles', 'public');

        // Simpan artikel ke database
        $article = Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content, // Konten Quill
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'user_id' => auth('web')->id(), // Jika menggunakan auth
        ]);

        // Redirect ke halaman artikel setelah berhasil disimpan
        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dibuat!');
    }




    // Fungsi untuk memastikan slug unik
    private function generateUniqueSlug($slug)
    {
        // Cek apakah slug sudah ada di database
        $count = Article::where('slug', $slug)->count();

        // Jika slug sudah ada, tambahkan angka untuk memastikan slug unik
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        return $slug;
    }


    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // Mengambil artikel bersama dengan penulis dan kategori
        $article->load('user', 'category');

        // Mengambil artikel terkait berdasarkan kategori
        $relatedArticles = Article::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(3)
            ->get();

        return view('admin.articles.articles-show.index', compact('article', 'relatedArticles'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $categories = Category::all();
        return view('admin.articles.articles-edit.index', compact('article', 'categories'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string', // Konten Quill
            'category_id' => 'required|exists:categories,id', // Pastikan kategori ada
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // Validasi gambar
        ]);

        // Jika validasi gagal, kembalikan ke form dengan pesan error
        if ($validator->fails()) {
            return redirect()->route('articles.edit', $article->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Cek jika judul artikel berubah, maka generate slug baru
        if ($request->title !== $article->title) {
            $slug = Str::slug($request->title); // Generate slug dari title baru

            // Pastikan slug unik
            $slug = $this->generateUniqueSlug($slug);

            $article->slug = $slug; // Update slug artikel
        }

        // Update artikel
        $article->title = $request->title;
        $article->content = $request->content; // Konten Quill
        $article->category_id = $request->category_id;

        // Cek apakah ada gambar baru yang di-upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if (file_exists(storage_path('app/public/' . $article->image))) {
                unlink(storage_path('app/public/' . $article->image));
            }

            // Simpan gambar baru
            $imagePath = $request->file('image')->store('articles', 'public');
            $article->image = $imagePath;
        }

        // Simpan perubahan ke database
        $article->save();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Menghapus gambar dari storage
        if (file_exists(storage_path('app/public/' . $article->image))) {
            unlink(storage_path('app/public/' . $article->image));
        }

        // Menghapus artikel
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dihapus!');
    }
}
