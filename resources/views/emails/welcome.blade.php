<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Absensi Kelas Al-Barokah | Email Welcome</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e3f2fd, #e8f5e9);
            padding: 30px;
            color: #333;
        }

        .email-container {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            border: 4px solid transparent;
            background-image: linear-gradient(#fff, #fff),
                              linear-gradient(90deg, #2563eb, #6610f2, #20c997, #fd7e14, #dc3545, #2563eb);
            background-origin: padding-box, border-box;
            background-clip: padding-box, border-box;
        }

        h2 {
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 15px;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }

        .footer strong {
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Welcome, {{ $user->name }}!</h2>

        <p>
            We are thrilled to welcome you to the <strong>Sistem Absensi Kelas Al-Barokah</strong> — a platform designed to help streamline recordkeeping, communication, and collaboration within our school community.
        </p>

        <p>
            Your registered email is: <strong>{{ $user->email }}</strong>.
        </p>

        <p>
            You may now log in and start exploring the features available to your assigned role. If you encounter any issues or have questions, our support team is here to assist you.
        </p>

        <p>
            Thank you for being part of this innovative step toward digital transformation in education.
        </p>

        <div class="footer">
            Respectfully,<br>
            <strong>Al-Barokah Project Team</strong>
            Bachelor of Science in Information Technology
        </div>
    </div>
</body>
</html>
