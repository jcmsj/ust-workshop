<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>{{ $title }}</h2>
        
        <p>Hi,</p>

        <div style="text-align: justify">
            @if(is_array($content))
                @foreach($content as $msg)
                    <p style="text-align: justify">
                        {!! $msg !!}
                    </p>
                @endforeach
            @else
                <p style="text-align: justify">
                    {!! $content !!}
                </p>
            @endif
        </div>

        @if(isset($url) && !empty($url))
            <p>
                <a href="{{ $url }}" style="display: inline-block; background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                    {{ (isset($url_text) && !empty($url_text)) ? $url_text : __('Click here') }}
                </a>
            </p>
        @endif

        <p>
            Thanks,<br>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
