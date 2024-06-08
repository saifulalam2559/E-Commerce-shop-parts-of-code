<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy("id", "DESC")->Paginate(10);
        return view("backend.users.index", compact("users"));
    }

    public function userStatus(Request $request)
    {
        if ($request->mode == "true") {
            User::where("id", $request->id)->update(["status" => "active"]);
        } else {
            User::where("id", $request->id)->update(["status" => "inactive"]);
        }

        return response()->json([
            "msg" => "Successfully Status Updated",
            "status" => true,
        ]);
    }

    public function create()
    {
        return view("backend.users.create");
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "first_name" => "string|required ",
            "last_name" => "string|required ",
            "username" => "string|nullable",
            "photo" => "required",
            "email" => "email|required|unique:users,email",
            "password" => "min:4|required",
            "phone" => "string|nullable",
            "address" => "string|nullable",
            "role" => "required|in:admin,customer,vendor",
            "status" => "nullable|in:active,inactive",
        ]);

        $data = $request->all();
        $data["password"] = Hash::make($request->password);

        $status = User::create($data);

        if ($status) {
            return redirect()
                ->route("user.index")
                ->with("success", "User has been created successfully !");
        } else {
            return redirect()
                ->back()
                ->with("error", "Something went wrong");
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::find($id);

        if ($user) {
            return view("backend.users.edit", compact("user"));
        } else {
            return back()->with("error", "Data not found!!");
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $this->validate($request, [
                "first_name" => "string|required ",
                "last_name" => "string|required ",
                "username" => "string|nullable",
                "photo" => "required",
                "email" => "email|required|exists:users,email",

                "phone" => "string|nullable",
                "address" => "string|nullable",
                "role" => "required|in:admin,customer,vendor",
                "status" => "nullable|in:active,inactive",
            ]);

            $data = $request->all();

            $status = $user->fill($data)->save();

            if ($status) {
                return redirect()
                    ->route("user.index")
                    ->with("success", "User has been updated successfully !");
            } else {
                return redirect()
                    ->back()
                    ->with("error", "Something went wrong");
            }
        } else {
            return back()->with("error", "Data not found!!");
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $status = $user->delete();

            if ($status) {
                return redirect()
                    ->route("user.index")
                    ->with("success", "User has been deleted!!");
            } else {
                return back()->with("error", "Something went wrong!!");
            }
        } else {
            return back()->with("error", "Data not found!!");
        }
    }
}
