<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Posts Report | Infinity Blog</title>
    <style>
        /* A4 Page Setup for PDF */
        @page {
            size: A4;
            margin: 20px;
        }

        /* Global Styling */
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
            background: #f4f6f9;
            line-height: 1.6;
        }

        /* Header */
        .header {
           
            margin-bottom: 20px;
            text-align: center;
        }
        .header h1, .header h2 {
            margin: 0;
            text-align: center;
        }
        .header h2 {
            color: #007bff;
            margin-bottom: 5px;
            text-align: center;
        }
        .header p {
            color: #555;
            margin-top: 5px;
            text-align: center;
        }

        /* Summary Section */
        .summary {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
        }
        .summary p {
            margin: 0;
            font-size: 14px;
        }
        .summary strong {
            color: #007bff;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        th {
            background: #007bff;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            text-align: center;
            font-size: 12px;
            color: #555;
            word-break: break-word;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        tr:hover {
            background: #e9ecef;
            transition: background 0.1s ease-in-out;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #888;
            font-size: 12px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        
        <img src="{{ asset('uploads/logo.png') }}" alt="Post Image" width="80" style="border-radius: 100%; max-width: 50%;">
<h2>Infinity Blog</h2>
      </header>

    <p>Your Gateway to Infinite Stories</p>
    <h1>Posts Report</h1>

    <!-- Summary Section -->
    <div class="summary">
        <p><strong>Total Posts:</strong> {{ $data->count() }}</p>
        <p><strong>Total Likes:</strong> {{ $totalLikes }}</p>
        <p><strong>Total Comments:</strong> {{ $totalComments }}</p>
    </div>

    <!-- Posts Table -->
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                {{-- <th>Content</th> --}}
                <th>Author</th>
                <th>Likes</th>
                <th>Comments</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $post)
                <tr>
                   
                    <td>{{ $post['id'] }}</td>
                    <td>{{ $post->title }}</td>
                    {{-- <td>{!! Str::limit($post->description, 1) !!}</td> --}}
                    <td>{{ $post->author }}</td>
                    <td>{{ $post->likes_count }}</td>
                    <td>{{ $post->comments_count }}</td>
                    <td>{{ $post->category }}</td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>{{ $post->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <footer class="footer">
        <p>Infinity Blog &copy; {{ date('Y') }} — All Rights Reserved.</p>
        @dd($data)

    </footer>

</body>
</html>
