<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <form method="POST">
            @csrf
            <input type="number" name="{{ config('magictoken.http.input_keys.pincode') }}" />
            <button type="submit">Verify</button>
        </form>
    </body>
</html>
