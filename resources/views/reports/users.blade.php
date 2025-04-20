<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Report</title>
    <style>
        /* PDF Page Setup */
        @page {
            size: A4;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 100px;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        .summary {
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .summary p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #dee2e6;
        }

        th {
            background: #007bff;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        td {
            padding: 8px;
            text-align: center;
            font-size: 12px;
            word-wrap: break-word;
            max-width: 150px;
            white-space: normal;
        }

        /* Align long text left for readability */
        td.name,
        td.email,
        td.ban-reason {
            text-align: left;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .no-data {
            text-align: center;
            color: #dc3545;
            font-size: 18px;
            margin-top: 20px;
        }

        /* PDF Page Breaks */
        .page-break {
            page-break-before: always;
        }

        @media print {
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            .summary {
                background: #007bff;
                color: white;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                text-align: center;
                display: flex;
                justify-content: space-around;
                flex-wrap: wrap;
            }

            .summary p {
                margin: 10px;
                font-size: 18px;
                font-weight: bold;
            }

            .summary p strong {
                display: block;
                font-size: 16px;
                color: #f8f9fa;
            }
        }
        .icon {
            width: 50px;
            height: 50px;
            fill: black;
        }
        body.dark-mode .icon {
            fill: white;
        }
    </style>
</head>
<body class="dark-mod">
    <header class="header">
        <h2>Infinity Blog</h2>
        <img src="{{ asset('uploads/logo.png') }}" alt="Post Image" width="80" style="border-radius: 100%; max-width: 50%;"> Infinity Blog
        <h1>Users Details</h1>
    </header>

    <div class="summary">
        <p><strong>Filtered Users:</strong> {{ count($data) }}</p>

        {{-- <p><strong>Total Users:</strong> {{ $totalUsers }}</p> --}}
        <p><strong>Total Comments:</strong> {{ $totalComments }}</p>
        <p><strong>Total Likes:</strong> {{ $totalLikes }}</p>
    </div>


    @if (!empty($data))

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
                <th>Comments</th>
                <th>Likes</th>
                <th>Banned</th>
                <th>Ban Reason</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td class="name">{{ $user->name }}</td>
                <td class="email">{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>{{ $user->posts_count }}</td>
                <td>{{ $user->comments_count }}</td>
                <td>{{ $user->likes_count }}</td>
                <td>{{ $user->banned ? 'Yes' : 'No' }}</td>
                <td class="ban-reason">{{ $user->ban_reason ?? 'N/A' }}</td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-data">No Details available. Please choose a valid date range.</p>
    @endif
</body>

</html>