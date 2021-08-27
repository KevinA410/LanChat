<?php
function layout_begin(){
    echo('
        <!DOCTYPE html>
        <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>LanChat</title>

                <!--Custom CSS -->
                <link rel="stylesheet" href="css/styles.css">

                <!-- Bootstrap -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"
                    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"
                    integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous">
                </script>

                <!--Jquery-->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

                <!-- Script to manage sockets -->
                <script src="js/socket_connect.js"></script>
            </head>

            <body>

                <!-- Navbar -->
                <nav class="navbar navbar-expand navbar-light bg-light">
                    <div class="container-fluid">
                        <!-- Slice connected panel -->
                        <button class="btn d-inline-block d-lg-none" id="btn_slide">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#191919" class="bi bi-list"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                            </svg>
                        </button>

                        <!-- Brand -->
                        <a class="navbar-brand" href="index.html">LanChat</a>

                        <!-- Navbar items -->
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <!-- Settings -->
                                <a class="nav-link" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#191919"
                                        class="bi bi-gear-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div id="main_container" class="container-fluid">
    ');
}

function layout_end(){
    echo('
                </div>

                <!-- Footer -->
                <div class="container-fluid bg-light">
                    <p class="text-center mb-0 p-1">
                        <small>&copy;LanChat 2021. Todos los derechos reservados.</small>
                    </p>
                </div>

            </body>
        </html>
    ');
}

?>