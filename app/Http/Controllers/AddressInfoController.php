<?php

namespace App\Http\Controllers;

use App\Models\AddressInfo;
use Auth;
use Illuminate\Http\Request;
use Validator;

class AddressInfoController extends Controller
{
    private const ADDRESS_NOT_FOUND_ERROR = 'AddressInfo not found or access denied';

    public function index()
    {
        $info = AddressInfo::all();

        return response()->json([
            'message' => 'All Address Infos retrieved',
            'data' => $info,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_1' => 'required|string|max:255',
            'address_2' => 'string|max:255',
            'city' => 'required|string|max:50',
            'state_province' => 'required|string|max:50',
            'country' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the user ID from the authenticated user
        $userId = Auth::id();

        // Check if the address already exists for the user
        $addressInfo = AddressInfo::where('user_id', $userId)->first();

        if ($addressInfo) {
            // Update the existing address
            $addressInfo->update($validator->validated());

            return response()->json($addressInfo, 200);
        } else {
            // Create a new address if not existing
            $addressInfo = AddressInfo::create(array_merge($validator->validated(), ['user_id' => $userId]));

            return response()->json($addressInfo, 201);
        }
    }

    public function show($id)
    {
        $addressInfo = AddressInfo::find($id);

        if (! $addressInfo || $addressInfo->user_id !== Auth::id()) {
            return response()->json(['message' => self::ADDRESS_NOT_FOUND_ERROR], 404);
        }

        return response()->json($addressInfo);
    }

    public function update(Request $request, $id)
    {
        $addressInfo = AddressInfo::find($id);

        if (! $addressInfo || $addressInfo->user_id !== Auth::id()) {
            return response()->json(['message' => self::ADDRESS_NOT_FOUND_ERROR], 404);
        }

        $validator = Validator::make($request->all(), [
            'address_1' => 'required|string|max:255',
            'address_2' => 'string|max:255',
            'city' => 'required|string|max:50',
            'state_province' => 'required|string|max:50',
            'country' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $addressInfo->update($validator->validated());

        return response()->json($addressInfo);
    }

    public function destroy($id)
    {
        $addressInfo = AddressInfo::find($id);

        if (! $addressInfo || $addressInfo->user_id !== Auth::id()) {
            return response()->json(['message' => self::ADDRESS_NOT_FOUND_ERROR], 404);
        }

        $addressInfo->delete();

        return response()->json(null, 204);
    }
}
