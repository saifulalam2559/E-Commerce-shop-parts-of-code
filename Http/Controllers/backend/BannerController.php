<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;
use Image;

class BannerController extends Controller
{
    /*****************************************
   Slider Banner Section here 
  ****************************************/

    public function index()
    {
        $banners = Banner::orderBy("id", "DESC")->get();
        return view("backend.banners.index", compact("banners"));
    }

    public function bannerStatus(Request $request)
    {
        if ($request->mode == "true") {
            Banner::where("id", $request->id)->update(["status" => "active"]);
        } else {
            Banner::where("id", $request->id)->update(["status" => "inactive"]);
        }

        return response()->json([
            "msg" => "Status erfolgreich aktualisiert",
            "status" => true,
        ]);
    }

    public function create()
    {
        return view("backend.banners.create");
    }

    /*****************************************
   Slider Banner Store here 
  ****************************************/

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                "title" => "string|required ",
                "description" => "string|nullable",
                "photo" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",

                "condition" => "nullable|in:banner,promo",
                "status" => "nullable|in:active,inactive",
            ],

            [
                "title.string" => "Bitte geben Sie einen Titel ein",
                "title.required" => "Sie müssen Ihren Namen eingeben",
                "photo.required" => "Ohne Foto ist kein Zutritt möglich",
            ]
        );

        $data = $request->all();
        $slug = Str::slug($request->input("title"));
        $slug_count = Banner::where("slug", $slug)->count();

        if ($slug_count > 0) {
            $slug = time() . "-" . $slug;
        }

        $data["slug"] = $slug;

        $image = $request->photo;

        if ($image) {
            $image_one =
                time() .
                "." .
                request()
                    ->file("photo")
                    ->getClientOriginalExtension();

            Image::make($image)
                ->resize(null, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save("images/banner/" . $image_one);

            $data["photo"] = "images/banner/" . $image_one;

            $status = Banner::create($data);

            if ($status) {
                return redirect()
                    ->route("banner.index")
                    ->with("success", "Banner wurde erfolgreich erstellt !");
            } else {
                return redirect()
                    ->back()
                    ->with("error", "Etwas ist schiefgelaufen");
            }
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $banner = Banner::find($id);

        if ($banner) {
            return view("backend.banners.edit", compact("banner"));
        } else {
            return back()->with("error", "Data not found!!");
        }
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::find($id);

        if ($banner) {
            $this->validate(
                $request,
                [
                    "title" => "string|required ",
                    "description" => "string|nullable",

                    "condition" => "nullable|in:banner,promo",
                    "status" => "nullable|in:active,inactive",
                ],

                [
                    "title.string" => "Bitte geben Sie einen Titel ein",
                    "title.required" => "Sie müssen Ihren Namen eingeben",
                    "photo.required" => "Ohne Foto ist kein Zutritt möglich",
                ]
            );

            $data = $request->all();

            $image = $request->photo;

            if ($image) {
                $image_one =
                    time() .
                    "." .
                    request()
                        ->file("photo")
                        ->getClientOriginalExtension();

                Image::make($image)
                    ->resize(null, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->save("images/banner/" . $image_one);

                $data["photo"] = "images/banner/" . $image_one;

                $status = $banner->fill($data)->save();
                return redirect()
                    ->route("banner.index")
                    ->with(
                        "success",
                        "Banner wurde erfolgreich aktualisiert !"
                    );
            } else {
                $status = $banner->fill($data)->save();
                return redirect()
                    ->route("banner.index")
                    ->with(
                        "success",
                        "Banner wurde erfolgreich aktualisiert !"
                    );
            }
        } else {
            return redirect()
                ->back()
                ->with("error", "Etwas ist schiefgelaufen");
        }
    }

    public function destroy($id)
    {
        $banner = Banner::find($id);

        if ($banner) {
            $status = $banner->delete();

            if ($status) {
                return redirect()
                    ->route("banner.index")
                    ->with("success", "Banner wurde gelöscht!!");
            } else {
                return back()->with("error", "Etwas ist schiefgelaufen!");
            }
        } else {
            return back()->with("error", "Daten nicht gefunden!!");
        }
    }
}
