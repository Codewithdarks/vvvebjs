<?php

namespace App\Http\Controllers;

define('MAX_FILE_LIMIT', 1024 * 1024 * 2); //2 Megabytes max html file size
use App\Models\PageBuilder;
use Illuminate\Http\Request;

class VvvebController extends Controller
{

    public function Editor($active=null) {
        $pages = PageBuilder::all();
        $list = array();
        foreach ($pages as $page) {
            $list[] = array(
                'name' => $page->url_slug,
                'title' => $page->name,
                'url' => route('get.content', encrypt($page->id)),
                'file' => encrypt($page->id)
            );
        }
        $all = json_encode($list);
        if ($active !== null) {
            $active = $active;
        } else {
            $active = $list[0]['name'];
        }
        return view('vvvebjs.editor', compact('all', 'active'));
    }

    public function SaveContent(Request $request) {
        $data = $request->all();
        if (!empty($data['startTemplateUrl'])) {
            $html = file_get_contents(resource_path('views/vvvebjs/blank.blade.php'));
        } elseif ($data['html'] !== null) {
            $html = substr($data['html'], 0, MAX_FILE_LIMIT);
        }
        $file = $data['file'];
        $data = PageBuilder::find(decrypt($file));
        $status = $data->update(['html' => $html]);
        if ($status) {
            return response('Saved Successfully.', 200);
        } else {
            return response('Something Went Wrong.', 500);
        }
    }

    /**
     * Sanitizing file for non-acceptable contents.
     *
     * @param $data
     * @return string
     */
    private function Sanitization($data) {
        return __DIR__ . '/' . preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $data)));
    }

    public function GetContent($id) {
        $data = PageBuilder::find(decrypt($id));
        return $data['html'];
    }
}
