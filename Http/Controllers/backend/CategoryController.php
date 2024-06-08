<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /*****************************************
   E-commerce shop products Catagory
  ****************************************/

    public function index()
    {
        $categories = Category::orderBy("id", "DESC")->get();
        return view("backend.categories.index", compact("categories"));
    }

    public function categoryStatus(Request $request)
    {
        if ($request->mode == "true") {
            Category::where("id", $request->id)->update(["status" => "active"]);
        } else {
            Category::where("id", $request->id)->update([
                "status" => "inactive",
            ]);
        }

        return response()->json([
            "msg" => "Status erfolgreich aktualisiert",
            "status" => true,
        ]);
    }

    public function create()
    {
        $parent_cats = Category::where("is_parent", 1)
            ->orderBy("title", "ASC")
            ->get();
        return view("backend.categories.create", compact("parent_cats"));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => "string|required ",
            "summary" => "string|nullable",
            "is_parent" => "sometimes|in:1",
            "parent_id" => "nullable",
            "status" => "nullable|in:active,inactive",
        ]);

        $data = $request->all();

        $slug = Str::slug($request->input("title"));
        $slug_count = Category::where("slug", $slug)->count();

        if ($slug_count > 0) {
            $slug = time() . "-" . $slug;
        }

        $data["slug"] = $slug;
        $data["is_parent"] = $request->input("is_parent", 0);
        if ($request->is_parent == 1) {
            $data["parent_id"] = null;
        }

        $status = Category::create($data);

        if ($status) {
            return redirect()
                ->route("category.index")
                ->with("success", "Kategorie wurde erfolgreich erstellt !");
        } else {
            return redirect()
                ->back()
                ->with("error", "Etwas ist schiefgelaufen");
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $parent_cats = Category::where("is_parent", 1)
            ->orderBy("title", "ASC")
            ->get();

        if ($category) {
            return view(
                "backend.categories.edit",
                compact(["category", "parent_cats"])
            );
        } else {
            return back()->with("error", "Daten nicht gefunden!!");
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if ($category) {
            $this->validate($request, [
                "title" => "string|required ",
                "summary" => "string|nullable",
                "is_parent" => "sometimes|in:1",
                "parent_id" => "nullable",
                "status" => "nullable|in:active,inactive",
            ]);

            $data = $request->all();

            $data["is_parent"] = $request->input("is_parent", 0);
            if ($request->is_parent == 1) {
                $data["parent_id"] = null;
            }

            $status = $category->fill($data)->save();

            if ($status) {
                return redirect()
                    ->route("category.index")
                    ->with(
                        "success",
                        "Kategorie wurde erfolgreich aktualisiert!"
                    );
            } else {
                return redirect()
                    ->back()
                    ->with("error", "Etwas ist schiefgelaufen");
            }
        } else {
            return back()->with("error", "Daten nicht gefunden!!!");
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        $child_cat_id = Category::where("parent_id", $id)->pluck("id");

        if ($category) {
            $status = $category->delete();

            if ($status) {
                if (count($child_cat_id) > 0) {
                    Category::shiftChild($child_cat_id);
                }

                return redirect()
                    ->route("category.index")
                    ->with("success", "Kategorie wurde gelöscht!!");
            } else {
                return back()->with("error", "Etwas ist schiefgelaufen!!");
            }
        } else {
            return back()->with("error", "Daten nicht gefunden!!");
        }
    }

    /*****************************************
   Getting sub Catagory based on parent Catagory
  ****************************************/

    public function getChildByParentId(Request $request, $id)
    {
        $category = Category::find($id);

        if ($category) {
            $child_id = Category::getChildByParentID($id);

            if (count($child_id) <= 0) {
                return response()->json([
                    "newDivOpen" => false,
                    "data" => null,
                    "msg" => "",
                ]);
            } else {
                return response()->json([
                    "newDivOpen" => true,
                    "data" => $child_id,
                    "msg" => "",
                ]);
            }
        } else {
            return response()->json([
                "newDivOpen" => false,
                "data" => null,
                "msg" => "",
            ]);
        }
    }
}
