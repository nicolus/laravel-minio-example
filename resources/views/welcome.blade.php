<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel + Minio example</title>
    </head>
    <body>
        <h1>Laravel + Minio example</h1>

        @if($temporaryUrl)
            <img src="{{$temporaryUrl}}" alt="Uploaded image">
        @endif

        <form id="minio-uploader" data-url="{{$uploadUrl}}" action="#" method="get" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file">
            <button type="submit">Upload</button>
        </form>
    <script>
        document.getElementById('minio-uploader').addEventListener('submit', function(e) {
            e.preventDefault();

            let file = this.querySelector('input[type="file"]').files[0];
            let url = this.dataset.url;

            fetch(url, {
                method: 'PUT',
                body: file
            }).then(() => window.location.reload());
            return false;
        });
    </script>
    </body>
</html>
