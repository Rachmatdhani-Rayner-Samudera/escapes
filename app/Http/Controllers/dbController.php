<?php

namespace App\Http\Controllers;

//import Model "db
use App\Models\db;

use Illuminate\Http\Request;

//return type View
use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

//import Facade "Storage"
use Illuminate\Support\Facades\Storage;

class dbController extends Controller
{
    /**
     * index
     *
     * @return View
     */
    public function index(): View
    {
        //get db
        $db = db::latest()->paginate(5);

        //render view with db
        return view('db.index', compact('db'));
    }

    /**
     * create
     *
     * @return View
     */
    public function create(): View
    {
        return view('db.create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/db', $image->hashName());

        //create post
        db::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        //redirect to index
        return redirect()->route('db.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return View
     */
    public function show(string $id): View
    {
        //get db by ID
        $db = db::findOrFail($id);

        //render view with db
        return view('db.show', compact('db'));
    }

    /**
     * edit
     *
     * @param  mixed $id
     * @return void
     */
    public function edit(string $id): View
    {
        //get db by ID
        $db = db::findOrFail($id);

        //render view with db
        return view('db.edit', compact('db'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image' => 'image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        //get db by ID
        $db = db::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/db', $image->hashName());

            //delete old image
            Storage::delete('public/db/' . $db->image);

            //update db with new image
            $db->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);

        } else {

            //update db without image
            $db->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('db.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * destroy
     *
     * @param  mixed $db
     * @return void
     */
    public function destroy($id): RedirectResponse
    {
        //get db by ID
        $db = db::findOrFail($id);

        //delete image
        Storage::delete('public/db/' . $db->image);

        //delete db
        $db->delete();

        //redirect to index
        return redirect()->route('db.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}