<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <input type="text" onkeyup="searchkeyword()" placeholder="rechercher" id="mykey">
    <div id="resp"></div>
    <script>
        async function searchkeyword(){
            document.querySelector("#resp").innerHTML = "";
            let keyword = document.querySelector("#mykey").value;
            if (keyword.length > 0) {
                const req = await fetch(`txt.php?keyword=${encodeURIComponent(keyword)}`);
                const json = await req.json();
                console.log(json);
                if (json.length > 0) {
                    json.forEach((post) => {
                        document.querySelector("#resp").innerHTML += 
                            '<a href="../cli/details_prod.php?id=' + post.id_prod + '">' + post.desi_prod + '</a><br>';
                    });


                }  
            }
        }
    </script>
</body>
</html>
