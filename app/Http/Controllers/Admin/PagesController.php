<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Dotlogics\Grapesjs\App\Traits\EditorTrait;

class PagesController extends Controller
{
    use EditorTrait;

    public function index()
    {
        return view('admin.pages.show', [
            'pages' => Page::paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store()
    {
        $input = Validator::make(request()->all(), $this->rules())->validate();

        if(!isset($input['slug'])) {
            $input['slug'] = Str::slug($input['title']);
        }
        $input['main'] = 0;

        $page = Page::create($input);

        session()->flash('success', 'Pagina a fost creata cu succes.');
        return redirect()->route('admin.pages.edit', $page->id);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.create', [
            'page' => $page,
        ]);
    }

    public function update(Page $page)
    {
        $input = Validator::make(request()->all(), $this->rules($page->id), [] ,$this->names())->validate();

        if(!isset($input['slug'])) {
            $input['slug'] = Str::slug($input['title']);
        }

        $page->update($input);

        session()->flash('success', 'Pagina a fost editata cu succes.');
        return redirect()->route('admin.pages.edit', $page->id);
    }

    public function editor(Request $request, Page $page)
    {
        return $this->show_gjs_editor($request, $page);
    }

    public function raw(Page $page)
    {
        return view('admin.pages.raw', [
            'page' => $page,
        ]);
    }

    public function destroy(Page $page)
    {
        if($page->main != '1') {
            Page::where('id', $page->id)->delete();

            session()->flash('success', 'Pagina a fost stearsa cu succes.');
        }

        return redirect()->route('admin.pages.show');
    }

    public function rules($id = null)
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:pages,title,'.$id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:pages,slug,'.$id],
        ];
    }

    public function names()
    {
        return [
            'title' => __('titlu'),
            'slug' => __('slug'),
        ];
    }
}