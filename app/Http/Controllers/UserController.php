<?php

namespace App\Http\Controllers;
// Defines this class as part of the App\Http\Controllers namespace,
// so Laravel knows to use it for handling web requests.

use Illuminate\Http\Request;       
// Provides the Request object, which gives access to user input,
// query parameters, and other HTTP request data in controller methods.

use App\Models\User;               
// Imports the User Eloquent model, allowing us to query,
// create, update, and delete records in the `users` database table.

use Illuminate\Support\Facades\Mail;       
// Brings in Laravel’s Mail facade, which offers a simple
// static interface to send emails via configured mail drivers.

use App\Mail\WelcomeMail;          
// Imports the WelcomeMail mailable class, which encapsulates
// the email content, subject, and view template for welcome messages.

use App\Mail\CustomMessageMail;

use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // $users = User::all(); // get all users
        // $users = User::orderBy('name', 'asc')->get(); // alphabetical order
        $users = User::orderBy('created_at', 'desc')->paginate(10); // created_date order descending
        return view('users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Sync role using Spatie
            $user->syncRoles([$request->role]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

     /**
     * Send welcome email to selected users.
     */
    public function sendMail(Request $request)
    {
        // Validate that at least one checkbox was ticked
        $request->validate([
            'recipients'   => 'required|array|min:1',
            'recipients.*' => 'integer|exists:users,id',
        ]);

        // Fetch the selected users
        $users = User::whereIn('id', $request->input('recipients'))->get();

        // Send email to each
        foreach ($users as $user) {
            Mail::to($user->email)->send(new WelcomeMail($user));

             EmailLog::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'subject' => 'Welcome to Email Sender',
                'sent_at' => now(),
            ]);
        }

        // Prepare a comma-separated list of emails for feedback
        $emails = $users->pluck('email')->implode(', ');

        return redirect()
            ->back()
            ->with('emailSuccess', "Terkirim ke: {$emails}");
    }

    /* Fetches all users ordered by name.
    Passes them to the Blade view resources/views/custom-email.blade.php.*/
    public function customEmailForm()
    {
        $users = User::orderBy('name')->get();
        return view('custom-email', compact('users'));
    }

    /*
     * Validates subject, content, and recipient IDs.
     * Sends a rich-text custom email to each selected user using CustomMessageMail.
     * Redirects to the user list with a success message.
     */
    public function sendCustomEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array'
        ]);

        $users = User::whereIn('id', $request->recipients)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new CustomMessageMail(
                $request->subject,
                $request->content
            ));
        }
        $emails = $users->pluck('email')->take(5)->implode(', ');
        $more = $users->count() > 5 ? ' dan lainnya' : '';

        return redirect()
            ->back() //this would redirect to the previous page
            // ->route('email.logs') // Redirects to the email logs route
            ->with('emailSuccess', "Pesan kustom telah diantrekan untuk: {$emails}{$more}");

        // return redirect()->route('email_logs.index')->with('emailSuccess', 'Custom message sent successfully!');
    }

}