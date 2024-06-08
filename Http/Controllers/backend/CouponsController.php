<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupons;

class CouponsController extends Controller
{

     /*****************************************
   E-commerce shop Coupon section
  ****************************************/
    public function index()
    {
        $coupons = Coupons::orderBy("id", "DESC")->get();
        return view("backend.coupons.index", compact("coupons"));
    }

    public function couponStatus(Request $request)
    {
        if ($request->mode == "true") {
            Coupons::where("id", $request->id)->update(["status" => "active"]);
        } else {
            Coupons::where("id", $request->id)->update([
                "status" => "inactive",
            ]);
        }

        return response()->json([
            "msg" => "Gutscheinstatus erfolgreich aktualisiert",
            "status" => true,
        ]);
    }

    public function create()
    {
        return view("backend.coupons.create");
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "code" => "required|min:2",
            "type" => "required|in:fixed,percent",
            "status" => "required|in:active,inactive",
            "value" => "required|numeric",
        ]);

        $data = $request->all();

        $status = Coupons::create($data);

        if ($status) {
            return redirect()
                ->route("coupon.index")
                ->with("success", "Gutschein wurde erfolgreich erstellt !");
        } else {
            return redirect()
                ->back()
                ->with("error", "Etwas ist schiefgelaufen");
        }
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $coupon = Coupons::find($id);

        if ($coupon) {
            return view("backend.coupons.edit", compact("coupon"));
        } else {
            return back()->with("error", "Gutschein nicht gefunden!");
        }
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupons::find($id);

        if ($coupon) {
            $this->validate($request, [
                "code" => "required|min:2",
                "type" => "required|in:fixed,percent",
                "status" => "required|in:active,inactive",
                "value" => "required|numeric",
            ]);

            $data = $request->all();

            $status = $coupon->fill($data)->save();

            if ($status) {
                return redirect()
                    ->route("coupon.index")
                    ->with("success", "Coupon has been updated !");
            } else {
                return redirect()
                    ->back()
                    ->with("error", "Etwas ist schiefgelaufen");
            }
        } else {
            return back()->with("error", "Gutschein nicht gefunden!");
        }
    }

    public function destroy($id)
    {
        $coupon = Coupons::find($id);

        if ($coupon) {
            $status = $coupon->delete();

            if ($status) {
                return redirect()
                    ->route("coupon.index")
                    ->with("success", "Coupon has been deleted!!");
            } else {
                return back()->with("error", "Etwas ist schiefgelaufen!!");
            }
        } else {
            return back()->with("error", "Gutschein nicht gefunden!!");
        }
    }
}
