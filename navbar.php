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

        @media (min-width: 768px) and (max-width: 1024px) {
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
            <nav class="h-40 px-5 py-4 md:py-0 md:px-10 flex flex-col md:flex-row justify-between items-center">
                <div class="logo mb-4 md:mb-0">
                    <a href="welcome.php">
                        <img class="w-52 h-52 md:w-64 md:h-64 mb-4" src="logo/logo.png" alt="Logo">
                    </a>
                </div>

                <!-- Menu Icon for smaller screens -->
                <div class="menu-icon md:hidden sm:hidden">
                    <i class="fas fa-bars" id="menuToggle"></i>
                </div>

                <!-- Navigation menu -->
                <ul class="md:flex md:justify-end nav-menu" id="navMenu">

                    <li style="margin-right: 65px;" class="mr-4">
                        <a href="welcome.php" class="blue-underline">
                            <span class="btn-navmenu2">Discover</span>
                        </a>
                    </li>

                    <li style="margin-right: 65px;" class="mr-4">
                        <a href="posts.php" class="blue-underline">
                            <span class="btn-navmenu">Posts</span>
                        </a>
                    </li>

                    <li style="margin-right: 65px;" class="mr-4">
                        <a href="events.php" class="blue-underline">
                            <span class="btn-navmenu">Events</span>
                        </a>
                    </li>

                    <li style="margin-right: 65px;" class="mr-4">
                        <a href="blogs.php" class="blue-underline">
                            <span class="btn-navmenu">Blogs</span>
                        </a>
                    </li>

                    <li style="margin-right: 65px;" class="mr-4">
                        <a href="guides.php" class="blue-underline">
                            <span class="btn-navmenu">Guides</span>
                        </a>
                    </li>

                    <li style="margin-right: 65px;" class="mr-4 relative group">
                        <a href="#" class="btn-navmenu blue-underline dropdown-toggle">
                            <span class="btn-text">Profiles</span>
                            <!-- dropdown icon -->
                            <span class="dropdown-icon">▼</span>
                        </a>
                        <ul class="mt-5 absolute hidden space-y-2 bg-white border border-gray-200 rounded-md shadow-lg custom-dropdown">
                            <li><a href="startup-enabler.php" class="orange-text block py-3 px-4">Startup Enablers</a></li>
                            <li><a href="startup-company.php" class="orange-text block py-3 px-4">Startup Companies</a></li>
                        </ul>
                    </li>

                    <li class="mr-4">
                        <a href="https://member.binnostartup.site" class="btn-blue">
                            <span>Become a member</span>
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