<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailLog;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // Step 1: Validate input
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Validate email using MailboxLayer or similar (skip gracefully if unavailable)
        if (env('MAILBOXLAYER_API_KEY')) {
            $response = Http::get('https://apilayer.net/api/check', [
                'access_key' => env('MAILBOXLAYER_API_KEY'),
                'email' => $request->email,
                'smtp' => 1,
                'format' => 1,
            ]);

            if ($response->successful() && array_key_exists('smtp_check', $response->json()) && !$response['smtp_check']) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Email ini tampaknya tidak valid atau tidak dapat dikirimi.',
                ]);
            }
        }

        // Continue with registration only if email is valid

        // Step 2: Use transaction to safely rollback if email fails
        DB::beginTransaction();

        try {
            // Step 3: Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // Step 4: Assign default role
            $user->assignRole('learner');

            // Step 5: Try to send email verification and welcome email
            $user->sendEmailVerificationNotification(); // Laravel email verify
            // Mail::to($user->email)->send(new WelcomeMail($user)); // Custom email

            // Step 6: Log the sent email
            EmailLog::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'subject' => 'Welcome Message',
                'sent_at' => now(),
            ]);

            // Step 7: Commit to DB if no errors
            DB::commit();

            // Step 8: Auto-login and redirect
            Auth::login($user);
            return redirect()->route('verification.notice');

        } catch (\Exception $e) {
            // Step 9: Rollback if any mail failed
            DB::rollBack();
            \Log::error('Registration error: '.$e->getMessage());


            return redirect()->back()->withInput()->withErrors([
                'email' => 'Registrasi gagal. Tidak bisa mengirim email verifikasi. Periksa kembali alamat email Anda.',
            ]);
        }
    }


    // public function register(Request $request)
    // {
    //     // Single validation block with confirmation rule
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6|confirmed',
    //     ]);

    //     // Create the user
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);

    //      // Assign the default role 'learner'
    //     $user->assignRole('learner');

    //      // Send Laravel's email verification
    //     $user->sendEmailVerificationNotification();

    //     // Auto-login the user to trigger redirection to /verify-email
    //     Auth::login($user);

    //     // Send the welcome email
    //     Mail::to($user->email)->send(new WelcomeMail($user));

    //     // Log that we sent the email
    //     EmailLog::create([
    //         'user_id' => $user->id,
    //         'email'   => $user->email,
    //         'subject' => 'Welcome Message',  //mailable’s subject
    //         'sent_at' => now(),
    //     ]);
        
    //     // Redirect to the Breeze verification notice page
    //      return redirect()->route('verification.notice');

    //     // Redirect back to the users form with success message
    //     // return redirect()->route('users.index')->with('success', 'Registration successful! Check your email.');
    //     // return redirect()->route('register.form')->with('success', 'Registration successful! Check your email.');
    // }


    public function showAdminRegisterForm()
    {
        return view('admin.register-user');
    }

    public function registerByAdmin(Request $request)
    {
        // dd('Form submitted'); // Add this temporarily for debugging

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,guru',
        ]);

        // Check email deliverability using MailboxLayer (skip gracefully if unavailable)
        if (env('MAILBOXLAYER_API_KEY')) {
            $response = Http::get('https://apilayer.net/api/check', [
                'access_key' => env('MAILBOXLAYER_API_KEY'),
                'email' => $request->email,
                'smtp' => 1,
                'format' => 1,
            ]);

            if ($response->successful() && array_key_exists('smtp_check', $response->json()) && !$response['smtp_check']) {
                return redirect()->back()->withErrors([
                    'email' => 'Email ini tampaknya tidak valid atau tidak dapat dikirimi.',
                ])->withInput();
            }
        }

        // Create the user directly (no OTP verification)
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($request->role);

            // Send the welcome email
            Mail::to($user->email)->send(new WelcomeMail($user));

            // Log the email
            EmailLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'subject' => 'Welcome Message',
                'sent_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.register.form')->with('emailSuccess', 'Pengguna berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin registration error: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors([
                'email' => 'Registrasi gagal. Silakan coba lagi.',
            ]);
        }
    }

}
