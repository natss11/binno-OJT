<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./dist/output.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <title>BINNO</title>

    <style>
        .menu-icon {
            display: none;
        }

        @media (max-width: 768px) {
            .menu-icon {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
                cursor: pointer;
            }

            .menu-icon i {
                font-size: 24px;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 80px;
                /* Adjust as per your design */
                right: 0;
                background-color: #fff;
                border: 1px solid #ccc;
                padding: 10px;
                border-radius: 5px;
                z-index: 1000;
            }

            .nav-menu.show {
                display: block;
            }

            .nav-menu li {
                list-style: none;
                margin: 0;
                padding: 0;
            }

            .nav-menu li a {
                display: block;
                padding: 10px;
                text-decoration: none;
            }

            /* Blue hover for menu icon */
            .menu-icon:hover .nav-menu li a {
                text-decoration: none;
            }
        }

        span {
            color: rgba(0, 0, 0, 0.6); /* Black color with 80% opacity */
        }
    </style>

    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevents the click event from propagating to the document body
                $(this).next('ul').toggleClass('hidden');
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown-toggle').length) {
                    $('.dropdown-toggle').next('ul').addClass('hidden');
                }
            });
        });
    </script>

</head>

<body class="font-sans">

    <header>
        <div>
            <nav class="h-16 px-5 py-4 md:py-0 md:px-10 flex flex-col md:flex-row justify-between items-center">
                <div class="logo mb-4 md:mb-0">
                    <a href="welcome.php">
                        <img class="w-52 h-52 md:w-52 md:h-52" src="logo/logo.png" alt="Logo">
                    </a>
                </div>

                <!-- Menu Icon for smaller screens -->
                <div class="menu-icon md:hidden">
                    <i class="fas fa-bars" id="menuToggle"></i>
                </div>

                <!-- Navigation menu -->
                <ul class="md:flex md:justify-end nav-menu" id="navMenu">

                    <li style="margin-right: 75px;" class="mr-4">
                        <a href="blogs.php" class="blue-underline">
                            <span class="btn-navmenu">Blog Articles</span>
                        </a>
                    </li>
                    <li style="margin-right: 75px;" class="mr-4">
                        <a href="events.php" class="blue-underline">
                            <span class="btn-navmenu">Events</span>
                        </a>
                    </li>
                    <li style="margin-right: 75px;" class="mr-4">
                        <a href="guides.php" class="blue-underline">
                            <span class="btn-navmenu2">Guides</span>
                        </a>
                    </li>
                    <li style="margin-right: 75px;" class="mr-4">
                        <a href="startup-company.php" class="blue-underline">
                            <span class="btn-navmenu">Startup Companies</span>
                        </a>
                    </li>
                    <li style="margin-right: 171px;" class="mr-4">
                        <a href="startup-enabler.php" class="blue-underline">
                            <span class="btn-navmenu">Startup Enablers</span>
                        </a>
                    </li>
                    <li style="margin-right: 75px;" class="mr-4 relative group">
                        <a href="https://member.binnostartup.site" class="btn-navmenu blue-underline">
                            <span class="btn-navmenu fas fa-user-circle"></span>
                            <span class="btn-navmenu btn-text">Sign In</span>
                        </a>
                    </li>

                </ul>

                <!-- End of navigation menu -->

            </nav>
        </div>
    </header>

    <script>
        $(document).ready(function() {
            $('#menuToggle').click(function() {
                $('#navMenu').toggleClass('show');
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown-toggle').length && !$(e.target).closest('#menuToggle').length) {
                    $('.dropdown-toggle').next('ul').addClass('hidden');
                    $('#navMenu').removeClass('show');
                }
            });
        });
    </script>

</body>

</html>