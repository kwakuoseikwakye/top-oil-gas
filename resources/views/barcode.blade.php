<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Testing barcode</title>
</head>

<body>
    <form id="barcode-form">
        <input type="text" id="barcode" name="barcode" autofocus>
    </form>
</body>

<script>
    let barcode = document.getElementById("barcode")
    let barcodeForm = document.getElementById("barcode-form")

    barcode.addEventListener("focus", function (e) {
        let formdata = new FormData(barcodeForm)
        console.log(barcode.value);

        return;
        // fetch(`${APP_URL}/api/cylinder`, {
        //     method: "POST",
        //     body: formdata,
        // }).then().then()

    })

</script>

</html>
