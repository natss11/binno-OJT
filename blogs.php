<?php

function fetch_api_data($api_url)
{
    // Make the request
    $response = file_get_contents($api_url);

    // Check for errors
    if ($response === false) {
        return false;
    }

    // Decode JSON response
    $data = json_decode($response, true);

    set_time_limit(60); // Set to a value greater than 30 seconds

    // Check if the decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Handle JSON decoding error
        return false;
    }

    return $data;
}

// Fetch data from blogs API
$company = fetch_api_data("http://217.196.51.115/m/api/blogs/class/company/");
$enabler = fetch_api_data("http://217.196.51.115/m/api/blogs/class/enabler/");

// Check if there is no available data yet
if (empty($company) && empty($enabler)) {
    echo "No blogs yet.";
    exit;
}

// Fetch data from member APIs
$enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");
$companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

if (!$enablers || !$companies) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch data.";
} else {
    // Combine company and enabler arrays
    $allBlogs = array_merge($company ?? [], $enabler ?? []);

    // Determine if search results are being displayed
    $searchResultsDisplayed = isset($_POST['search_term']) && !empty($_POST['search_term']);
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="./dist/output.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <title>BINNO | BLOGS</title>

        <style>
            .recent {
                background-color: #599EF3;
                margin-right: 55px;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
            }

            .fa-chevron-left,
            .fa-chevron-right {
                color: black;
                /* Default color */
                font-size: 24px;
                /* Default font size */
                transition: color 0.3s ease, font-size 0.3s ease;
                /* Smooth transition */
            }

            .fa-chevron-left:hover,
            .fa-chevron-right:hover {
                color: blue;
                /* Hover color */
                font-size: 28px;
                /* Increased font size on hover */
            }

            /* Add CSS for box effect transition */
            #startupCompanyCards {
                transition: transform 0.5s ease;
                transform-origin: center;
            }

            .box-transition {
                transform: scale(0.8);
                /* Initial scale */
                opacity: 0;
                /* Initially hidden */
            }

            #company-left {
                width: 80px;
                height: 80px;
            }

            #company-right {
                width: 80px;
                height: 80px;
            }

            #enabler-left {
                width: 80px;
                height: 80px;
            }

            #enabler-right {
                width: 80px;
                height: 80px;
            }
        </style>

    </head>

    <body class="bg-gray-100">

        <div class="bg-white">
            <?php include 'navbar-blogs.php'; ?>
        </div>

        <main class="justify-center">
            <div>
                <div>
                    <h4 class="mt-5 font-bold text-8xl md:text-5xl ml-20">Blog Articles</h4>
                </div>

                <div class="my-4 flex flex-col items-center">
                    <!-- Search Bar -->
                    <div style="height: 30px;"></div>

                    <div class="relative" style="width: 700px;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m4-6a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                            </svg>
                        </span>
                        <input type="text" id="blogSearchInput" placeholder="Search for a topic or organizer" class="pl-10 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500" style="width: calc(100% - 50px); border-radius: 16px;"> <!-- Subtracting 40px for the icon -->
                        <button id="searchButton" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md" style="border-top-right-radius: 16px; border-bottom-right-radius: 16px;">Search</button>
                    </div>
                </div>

                <?php
                // If search results are not displayed, show Startup Company section
                if (!$searchResultsDisplayed) {
                    echo '<div id="startupCompanySection" class="text-center">';
                    echo '<h3 class="font-bold text-3xl md:text-4xl mb-10">Startup Company</h3>';
                    echo '<div class="flex justify-end">';
                    echo '<a href="startups-blogs.php?type=company" class="view-all mr-10">View All</a>';
                    echo '</div>';
                    echo '</div>';
                }

                $totalBlogs = count($company);
                $perPage = 4;
                $pages = ceil($totalBlogs / $perPage);

                $currentPage = isset($_GET['company_page']) ? $_GET['company_page'] : 1;
                $companyOffset = ($currentPage - 1) * $perPage;

                // Sort the blogs array by the blog_dateadded field in descending order
                usort($company, function ($a, $b) {
                    return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                });

                $displayedBlogs = array_slice($company, $companyOffset, $perPage);
                ?>

                <div class="bg-white">

                    <div class="flex flex-row">

                        <div class="flex justify-center mt-4">
                            <div class="text-center" style="margin-top: 220px;">
                                <a id="company-left" href="#"><i class="fas fa-chevron-left text-3l"></i></a>
                            </div>
                        </div>

                        <div id="startupCompanyCards" class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <?php foreach ($displayedBlogs as $companyBlog) :
                                    $authorName = '';
                                    foreach ($companies as $companyMember) {
                                        if ($companyMember['member_id'] == $companyBlog['blog_author']) {
                                            $authorName = $companyMember['setting_institution'];
                                            break;
                                        }
                                    }
                                ?>
                                    <div class="card-container rounded-lg overflow-hidden bg-white">
                                        <a href="blogs-view.php?blog_id=<?php echo $companyBlog['blog_id']; ?>" class="link">
                                            <img src="http://217.196.51.115/m/api/images?filePath=blog-pics/<?php echo htmlspecialchars($companyBlog['blog_img']); ?>" alt="<?php echo htmlspecialchars($companyBlog['blog_img']); ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                            <div class="p-4">
                                                <div class="flex items-center mb-2">
                                                    <div>
                                                        <h2 class="text-2xl font-semibold"><?php echo strlen($companyBlog['blog_title']) > 20 ? htmlspecialchars(substr($companyBlog['blog_title'], 0, 20)) . '...' : htmlspecialchars($companyBlog['blog_title']); ?></h2>
                                                        <p class="text-gray-600 text-sm">
                                                            <?php
                                                            $formatted_date = date('F j, Y | h:i A', strtotime($companyBlog['blog_dateadded']));
                                                            echo $formatted_date;
                                                            ?>
                                                        </p>
                                                        <p class="text-m text-gray-600 mb-3"><?php echo $authorName; ?></p>
                                                        <p class="mb-2 mt-2">
                                                            <?php
                                                            $words = str_word_count($companyBlog['blog_content'], 1);
                                                            echo htmlspecialchars(implode(' ', array_slice($words, 0, 25)));
                                                            if (count($words) > 25) {
                                                                echo '...';
                                                            }
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center;">
                            <div class="flex justify-center mt-4">
                                <div class="text-center" style="margin-top: 220px;">
                                    <a id="company-right" href="#"><i class="fas fa-chevron-right text-3l"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // JavaScript code for slideshow functionality for startup company section
                    document.addEventListener('DOMContentLoaded', function() {
                        const company_left = document.getElementById('company-left');
                        const company_right = document.getElementById('company-right');
                        const companySlideshowContainer = document.getElementById('startupCompanyCards');
                        let currentPage = <?php echo $currentPage; ?>;
                        const totalPages = <?php echo $pages; ?>;

                        company_left.style.display = <?php echo $pages <= 1 ? "'none'" : "'block'"; ?>;
                        company_right.style.display = <?php echo $pages <= 1 ? "'none'" : "'block'"; ?>;

                        company_left.addEventListener('click', function(event) {
                            event.preventDefault();
                            const prevPage = currentPage - 1;
                            if (prevPage >= 1) {
                                loadPage(prevPage);
                                currentPage = prevPage;
                            } else {
                                const lastPage = totalPages;
                                loadPage(lastPage);
                                currentPage = lastPage;
                            }
                        });

                        company_right.addEventListener('click', function(event) {
                            event.preventDefault();
                            const nextPage = currentPage + 1;
                            if (nextPage <= totalPages) {
                                loadPage(nextPage);
                                currentPage = nextPage;
                            } else {
                                loadPage(1); // Go back to the first page
                                currentPage = 1;
                            }
                        });

                        function loadPage(pageNumber) {
                            fetch(`?company_page=${pageNumber}`)
                                .then(response => response.text())
                                .then(html => {
                                    const div = document.createElement('div');
                                    div.innerHTML = html;
                                    const content = div.querySelector('#startupCompanyCards').innerHTML;
                                    companySlideshowContainer.innerHTML = content;
                                })
                                .catch(error => console.error('Error fetching page:', error));
                        }
                    });

                    // Function to fetch image data from API for startup company section
                    async function updateCompanyImageSrc(imgSrc) {
                        imgSrc.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`
                    }

                    // Loop through images with IDs containing "dynamicCompanyImg"
                    document.querySelectorAll('[id^="dynamicCompanyImg-"]').forEach((imgElement, index) => {
                        // Update each image's src from the API
                        updateCompanyImageSrc(imgElement);
                    });
                </script>

                <?php
                // If search results are not displayed, show Startup Enabler section
                if (!$searchResultsDisplayed) {
                    echo '<div id="startupEnablerSection" class="text-center">';
                    echo '<h3 class="font-bold text-3xl md:text-4xl mb-10 mt-10">Startup Enabler</h3>';
                    echo '<div class="flex justify-end">';
                    echo '<a href="enablers-blogs.php?type=enabler" class="view-all mr-10">View All</a>';
                    echo '</div>';
                    echo '</div>';
                }

                $totalBlogs = count($enabler);
                $pages = ceil($totalBlogs / $perPage);

                $currentPage = isset($_GET['enabler_page']) ? $_GET['enabler_page'] : 1;
                $enablerOffset = ($currentPage - 1) * $perPage;
                ?>

                <div class="bg-white">
                    <div class="flex flex-row mb-10">

                        <div class="flex justify-center mt-4">
                            <div class="text-center" style="margin-top: 220px;">
                                <a id="enabler-left" href="#"><i class="fas fa-chevron-left text-3l"></i></a>
                            </div>
                        </div>


                        <div id="startupEnablerCards" class="slideshow-container container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <?php
                                // Sort the blogs array by the blog_dateadded field in descending order
                                usort($enabler, function ($a, $b) {
                                    return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                                });

                                $displayedBlogs = array_slice($enabler, $enablerOffset, $perPage);

                                foreach ($displayedBlogs as $enablerBlog) :
                                    $authorName = '';
                                    foreach ($enablers as $enablerMember) {
                                        if ($enablerMember['member_id'] == $enablerBlog['blog_author']) {
                                            $authorName = $enablerMember['setting_institution'];
                                            break;
                                        }
                                    }
                                ?>
                                    <div class="card-container overflow-hidden bg-white">
                                        <a href="blogs-view.php?blog_id=<?php echo $enablerBlog['blog_id']; ?>" class="link">
                                            <img src="http://217.196.51.115/m/api/images?filePath=blog-pics/<?php echo htmlspecialchars($enablerBlog['blog_img']); ?>" alt="<?php echo htmlspecialchars($enablerBlog['blog_img']); ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                            <div class="p-4">
                                                <div class="flex items-center mb-2">
                                                    <div>
                                                        <h2 class="text-2xl font-semibold"><?php echo strlen($enablerBlog['blog_title']) > 20 ? htmlspecialchars(substr($enablerBlog['blog_title'], 0, 20)) . '...' : htmlspecialchars($enablerBlog['blog_title']); ?></h2>
                                                        <p class="text-gray-600 text-sm">
                                                            <?php
                                                            $formatted_date = date('F j, Y | h:i A', strtotime($enablerBlog['blog_dateadded']));
                                                            echo $formatted_date;
                                                            ?>
                                                        </p>
                                                        <p class="text-m text-gray-600 mb-3"><?php echo $authorName; ?></p>
                                                        <p class="mb-2 mt-2">
                                                            <?php
                                                            $words = str_word_count($enablerBlog['blog_content'], 1);
                                                            echo htmlspecialchars(implode(' ', array_slice($words, 0, 25)));
                                                            if (count($words) > 25) {
                                                                echo '...';
                                                            }
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center;">
                            <div class="flex justify-center mt-4">
                                <div class="text-center" style="margin-top: 220px;">
                                    <a id="enabler-right" href="#"><i class="fas fa-chevron-right text-3l"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // JavaScript code for slideshow functionality for startup enabler section
                    document.addEventListener('DOMContentLoaded', function() {
                        const enabler_left = document.getElementById('enabler-left');
                        const enabler_right = document.getElementById('enabler-right');
                        const enablerSlideshowContainer = document.getElementById('startupEnablerCards');
                        let currentPage = <?php echo $currentPage; ?>;
                        const totalPages = <?php echo $pages; ?>;

                        enabler_left.style.display = <?php echo $pages <= 1 ? "'none'" : "'block'"; ?>;
                        enabler_right.style.display = <?php echo $pages <= 1 ? "'none'" : "'block'"; ?>;

                        enabler_left.addEventListener('click', function(event) {
                            event.preventDefault();
                            const prevPage = currentPage - 1;
                            if (prevPage >= 1) {
                                loadPage(prevPage);
                                currentPage = prevPage;
                            } else {
                                const lastPage = totalPages;
                                loadPage(lastPage);
                                currentPage = lastPage;
                            }
                        });

                        enabler_right.addEventListener('click', function(event) {
                            event.preventDefault();
                            const nextPage = currentPage + 1;
                            if (nextPage <= totalPages) {
                                loadPage(nextPage);
                                currentPage = nextPage;
                            } else {
                                loadPage(1); // Go back to the first page
                                currentPage = 1;
                            }
                        });

                        function loadPage(pageNumber) {
                            const enablerSlideshowContainer = document.getElementById('startupEnablerCards');
                            enablerSlideshowContainer.style.opacity = 0; // Fade out content

                            fetch(`?enabler_page=${pageNumber}`)
                                .then(response => response.text())
                                .then(html => {
                                    const div = document.createElement('div');
                                    div.innerHTML = html;
                                    const content = div.querySelector('#startupEnablerCards').innerHTML;

                                    // Wait for a short delay to allow the fade-out effect to complete
                                    setTimeout(() => {
                                        enablerSlideshowContainer.innerHTML = content;
                                        enablerSlideshowContainer.style.opacity = 1; // Fade in new content
                                    }, 300); // Adjust the delay time as needed
                                })
                                .catch(error => console.error('Error fetching page:', error));
                        }
                    });


                    // Function to fetch image data from API for startup enabler section
                    async function updateEnablerImageSrc(imgSrc) {
                        imgSrc.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`
                    }

                    // Loop through images with IDs containing "dynamicEnablerImg"
                    document.querySelectorAll('[id^="dynamicEnablerImg-"]').forEach((imgElement, index) => {
                        // Update each image's src from the API
                        updateEnablerImageSrc(imgElement);
                    });
                </script>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const blogSearchInput = document.getElementById('blogSearchInput');
                        const cards = document.querySelectorAll('.card-container');

                        blogSearchInput.addEventListener('input', function(event) {
                            const searchTerm = event.target.value.toLowerCase().trim();

                            cards.forEach(card => {
                                const title = card.querySelector('h2').textContent.toLowerCase();
                                const author = card.querySelector('.text-m').textContent.toLowerCase(); // Update class selector to target author name

                                if (title.includes(searchTerm) || author.includes(searchTerm)) { // Check if title or author name includes the search term
                                    card.style.display = 'block';

                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        });
                    });
                </script>

            </div>
        </main>

    </body>

    </html>
<?php
}
?>