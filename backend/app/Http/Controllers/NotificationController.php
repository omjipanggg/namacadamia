<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\PushNotification;


use Illuminate\Http\Request;

use Faker\Factory as Faker;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NotificationController extends BaseController
{
    public function preview(Request $request)
    {
        $preview = $request->user()->notifications()->latest()->take(5)->get();

        $context = [
            'data' => NotificationResource::collection($preview),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ];
        return $this->sendResponse(true, 'Preview fetched', $context);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(12);
    
        $context = [
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more' => $notifications->hasMorePages(),
            ],
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ];
        
        return $this->sendResponse(true, 'Notifications fetched', $context);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $categories = ['account', 'purchase', 'updates', 'feedback', 'discount', 'subscription'];
        $icons = ['bell-fill', 'exclamation-triangle-fill', 'envelope-x-fill', 'currency-dollar', 'flag-fill', 'people-fill', 'question-diamond-fill', 'tags-fill'];
        $variants = ['danger', 'warning', 'primary', 'success', 'info', 'secondary'];

        $data = [
            'title' => 'Title',
            'description' => 'Description.',
            'icon' => Arr::random($icons),
            'variant' => Arr::random($variants),
            'category' => Arr::random($categories),
            'content' => 'Content.',
            'url' => '#',
        ];

        $users = User::whereIn('id', $request->input('ids'))->get();
        
        $users->each(function ($user) use ($data) {
            $user->notify(new PushNotification($data));
        });

        $notification = null;

        if (in_array($request->user()->id, $request->input('ids', []))) {
            $data = $request->user()->notifications()->latest()->first();
            $notification = new NotificationResource($data);
        }

        return $this->sendResponse(true, 'Notifications sent', $notification);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Notification $notification)
    {
        abort_if($notification->notifiable_id !== $request->user()->id, 403);
        return $this->sendResponse(true, 'Notification showed once', NotificationResource::collection($notification));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return $this->sendResponse(true, 'Deleted', ['unread_count' => $request->user()
        ->unreadNotifications()
        ->count(),]);
    }
        
    public function destroyAll(Request $request) {
        $request->user()->notifications()->delete();
        return $this->sendResponse(true, 'All notifications cleared', ['unread_count' => $request->user()
        ->unreadNotifications()
        ->count(),]);
    }

    public function markAsRead(Request $request, string $id) {
        $notification = $request->user()->notifications()->findOrFail($id);

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return $this->sendResponse(true, 'This notification has been marked as read', ['notification' => $notification, 'unread_count' => $request->user()
        ->unreadNotifications()
        ->count()]);
    }

    public function markAllAsRead(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return $this->sendResponse(true, 'All notifications marked as read', ['unread_count' => $request->user()
        ->unreadNotifications()
        ->count()]);
    }

    public function sendTest(Request $request) {
        $faker = Faker::create();
        $categories = ['account', 'purchase', 'updates', 'feedback', 'discount', 'subscription'];
        $icons = ['bell-fill', 'exclamation-triangle-fill', 'envelope-x-fill', 'currency-dollar', 'flag-fill', 'people-fill', 'question-diamond-fill', 'tags-fill'];
        $variants = ['danger', 'warning', 'primary', 'success', 'info', 'secondary'];

        $data = [
            'title' => Str::headline($faker->sentence),
            'description' => $faker->paragraph,
            'icon' => Arr::random($icons),
            'variant' => Arr::random($variants),
            'category' => Arr::random($categories),
            'content' => $faker->paragraph,
            'url' => '#',
        ];

        User::find(1)->notify(new PushNotification($data));
        $notification = $request->user()->notifications()->latest()->first();

        return $this->sendResponse(true, 'Notifications sent', new NotificationResource($notification));
    }
}