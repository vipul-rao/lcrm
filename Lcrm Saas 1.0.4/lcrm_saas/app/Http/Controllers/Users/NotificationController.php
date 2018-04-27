<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllData()
    {
        $this->user = $this->getUser();
        $total = $this->user->notifications()->whereStatus(false)->count();
        $notifications = $this->user->notifications()->latest()->take(5)->whereStatus(false)->get();

        return response()->json(compact('total', 'notifications'), 200);
    }

    public function postRead(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $model = Notification::find($request->get('id'));
        $model->status = true;
        $model->save();

        return response()->json(['message' => 'Notification updated successfully'], 200);
    }
}
