<?php

namespace App\Repository;

use App\Http\Requests\NotificationStoreRequest;
use App\Models\Notification;
use Yajra\DataTables\DataTables;
use App\Interfaces\NotificationInterface;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationRepository implements NotificationInterface
{
    public function index($request)
    {
        if ($request->ajax()) {
            $notifications = Notification::query()->latest()->get();
            return DataTables::of($notifications)
                ->addColumn('action', function ($notifications) {
                    return '
                            <button class="btn btn-pill btn-danger-light" data-toggle="modal" data-target="#delete_modal"
                                    data-id="' . $notifications->id . '" data-title="' . $notifications->title . '">
                                    <i class="fas fa-trash"></i>
                            </button>
                       ';
                })
                ->editColumn('user_id', function ($notifications) {
                    return $notifications->user->name ?? 'للكل';
                })

                ->escapeColumns([])
                ->make(true);
        } else {
            return view('admin.notifications.index');
        }
    }

    public function create()
    {
        $users = User::query()->select('id', 'name')->latest()->get();
        return view('admin.notifications.parts.create', compact('users'));
    }

    public function store(NotificationStoreRequest $request): JsonResponse
    {
        $inputs = $request->all();
        if ($request->choose == 'all') {
                $inputs['user_id'] = null; 
        }

        if (Notification::query()->create($inputs))
            return response()->json(['status' => 200]);
        else
            return response()->json(['status' => 405]);
    }

    public function delete($request)
    {
        $notification = Notification::query()->where('id', $request->id)->first();

        $notification->delete();
        return response(['message' => 'تم الحذف بنجاح', 'status' => 200], 200);
    }
}
