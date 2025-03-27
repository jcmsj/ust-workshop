<!DOCTYPE html>
<html>

<head>
    <title>New Lead Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000000; /* Set text color to black */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header img {
            margin-left: 20px;
        }
    </style>
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="header">
            <h1>New Lead Information</h1>
        </div>
        <table>
            @foreach ($lead->toArray() as $key => $value)
            @if ($value && !in_array($key, App\Http\Mail\NewLead::excludedFields))
            @isset($keyToHeaders[$key])
            <tr>
                <td><strong>{{ $keyToHeaders[$key] }}</strong></td>
                <td>{{ $value }}</td>
            </tr>
            @endisset
            @endif
            @endforeach
            <tr>
                <td><strong>Assigned To</strong></td>
                <td>{{ $leadAssignment->user->name }}</td>
            </tr>
        </table>

        <p style="text-align: center;">
            <a href="{{ route('filament.app.resources.leads.edit', ['record' => $lead->id]) }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #023b73; text-decoration: none; border-radius: 5px; text-align: center;">View lead details in website</a>
        </p>
    </div>
</body>

</html>
