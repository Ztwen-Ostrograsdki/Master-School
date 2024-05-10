<!DOCTYPE html>
<html lang="en">
<head>
    {{-- <meta charset="UTF-8"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMPRESSION DOCUMENT</title>
</head>
<style>
    *{
        margin: 0;
        padding: 0;

    }
    body{
        padding: 0;
        margin: 0;
        background-color: rgba(15, 100, 144, 0.7);

    }
    nav{
        width: 100%;
        margin: 0px auto;
        display: flex;
        justify-content: space-between;
        background-color: rgba(15, 100, 164, 1);


    }

    header{

        width: 80%;
        border: thin solid black;
        margin: 0px auto;

    }

    h6{
        padding: 10px 5px;
        cursor: pointer;


    }

    .nav-left{

        border-right: solid thin;

    }

    .nav-right{

        border-left: solid thin;

    }

    .nav-right:hover{
        background-color: white;

    }

    div.container{
        margin: 0px auto;

        padding: 5px;

        margin-top: 25px;

        width: 80%;
        border: thin solid black;
        


    }

</style>

<body>
    <header>
        <nav>
            <div class="nav-left">
                <h6>PAGE IMPRESSION DOCUMENT</h6>
            </div>

            <div class="nav-right" >
                <h6 wire:click="to_print">Imprimer maintenant</h6>
            </div>
        </nav>
    </header>

    <div class="container">

        <h5>LE CONTENU</h5>

        <div>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor voluptatum reiciendis pariatur placeat, debitis molestias in odio obcaecati voluptatem vel, iusto itaque eius sequi asperiores, deleniti suscipit dolorum aut dicta?</p>

        </div>

    </div>

</body>
</html>