<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <form method="POST">
            @csrf

            @if ($errors->has('pincode'))
                <div class="alert alert-danger">
                    $errors->first('pincode');
                </div>
            @endif

            <div class="form-group">
                <input type="number" class="form-control" name="pincode" />
            </div>

            <button type="submit">Verify</button>
        </form>
    </body>
</html>
