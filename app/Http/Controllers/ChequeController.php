<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChequeRequest;
use App\Models\ActivityLog;
use App\Models\Cheque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ChequeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cheques = Cheque::all();
        return view('cheques.index', compact('cheques'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cheques.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChequeRequest $request)
    {
        try {
            DB::beginTransaction();

            $cheque = Cheque::create($request->validated());
            // Log the activity
            $user = auth()->user();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $action = 'Cheque Created Successfully';
            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'action' => $action,
                'details' => 'Cheque ' . $cheque->check_number . ' Created Successfully by ' . $user->name
            ]);

            DB::commit();

            toast('Cheque ' . $cheque->check_number . ' added successfully', 'success');
            return redirect()->route('cheque.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            toast('Whoops!!! Something went wrong ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $cheque)
    {
        return view('cheques.show', [
            'cheque' => Cheque::findOrFail($cheque)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $cheque)
    {
        return view('cheques.edit', [
            'cheque' => Cheque::findOrFail($cheque)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChequeRequest $request, Cheque $cheque)
    {
        try {
            DB::beginTransaction();
            $cheque->update($request->validated());
            // Log the activity
            $user = auth()->user();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $action = 'Cheque Updated Successfully';
            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'action' => $action,
                'details' => 'Cheque ' . $cheque->check_number . ' Updated Successfully by ' . $user->name
            ]);
            DB::commit();

            toast('Cheque ' . $cheque->check_number . ' updated successfully', 'success');
            return redirect()->route('cheque.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toast('Whoops!!! Something went wrong ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cheque $cheque, Request $request)
    {
        try {
            $check_password = Hash::check($request->password, auth()->user()->password);
            if (!$check_password) {
                toast('These credentials do not match our records', 'error');
                
                return redirect()->back();
            }
            DB::beginTransaction();
            $chequeNumber = $cheque->check_number;
            $cheque->delete();

            // Log the activity
            $user = auth()->user();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $action = 'Cheque Deleted Successfully';
            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'action' => $action,
                'details' => 'Cheque ' . $chequeNumber . ' Deleted Successfully by ' . $user->name
            ]);
            DB::commit();

            toast('Cheque deleted successfully', 'success');
            return redirect()->route('cheque.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toast('Whoops!!! Something went wrong ' .  $e->getMessage(), 'error');
            return redirect()->back();
        }
    }
}
