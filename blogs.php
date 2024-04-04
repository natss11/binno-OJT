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




            .slideshow-container {
                transition: transform 0.3s ease-in-out;
                /* Add transition effect */
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
        </style>

    </head>

    <body class="bg-gray-100">

        <div class="bg-white">
            <?php include 'navbar-blogs.php'; ?>
        </div>

        <main class="flex justify-center">
            <div class="container mx-16">
                <div>
                    <h4 class="mt-5 font-bold text-3xl md:text-5xl">Blog Articles</h4>
                </div>

                <div class="my-4 flex flex-col items-center">
                    <!-- Search Bar -->
                    <div class="relative" style="width: 700px;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m4-6a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                            </svg>
                        </span>
                        <input type="text" id="searchInput" placeholder="Search for a topic or organizer" class="pl-10 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500" style="width: calc(100% - 60px); border-radius: 15px;"> <!-- Subtracting 40px for the icon -->
                        <button id="searchButton" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md" style="border-top-right-radius: 16px; border-bottom-right-radius: 16px;">Search</button>
                    </div>
                    <!-- Recent Words -->
                    <div id="recentWords" class="recent" style="width: 700px;"></div>
                </div>

                <div id="searchResults" class="grid grid-cols-4 gap-6"></div>

                <script>
                    document.getElementById('searchButton').addEventListener('click', function() {
                        performSearch();
                    });

                    document.getElementById('searchInput').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            performSearch();
                        }
                    });

                    // Initialize array to store recent words
                    var recentWords = [];

                    // Maximum number of recent words to display
                    var maxRecentWords = 5;

                    // Event listener for keystrokes in the search input field
                    document.getElementById('searchInput').addEventListener('keyup', function() {
                        updateRecentWords(this.value);
                    });

                    // Function to update the list of recent words
                    function updateRecentWords(inputText) {
                        // Split input text into words
                        var words = inputText.trim().split(/\s+/);

                        // Update recentWords array with unique words from the input
                        recentWords = [];
                        for (var i = words.length - 1; i >= 0 && recentWords.length < maxRecentWords; i--) {
                            if (words[i] && !recentWords.includes(words[i])) {
                                recentWords.unshift(words[i]);
                            }
                        }

                        // Display recent words
                        displayRecentWords();
                    }

                    // Function to display recent words
                    function displayRecentWords() {
                        var recentWordsContainer = document.getElementById('recentWords');
                        recentWordsContainer.innerHTML = '';
                        recentWordsContainer.style.width = '600px'; // Fixed width set here

                        recentWords.forEach(function(word) {
                            var wordElement = document.createElement('span');
                            wordElement.textContent = word;
                            wordElement.classList.add('recent-word', 'px-4', 'py-1', 'text-black', 'rounded');
                            wordElement.style.fontSize = '18px';
                            wordElement.style.textAlign = 'left';
                            recentWordsContainer.appendChild(wordElement);
                        });
                    }

                    // Function to perform live search
                    function performLiveSearch() {
                        var searchTerm = document.getElementById('searchInput').value.trim();
                        if (searchTerm !== '') {
                            fetch('http://217.196.51.115/m/api/search/blog', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        search_term: searchTerm
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    displaySearchResults(data);
                                })
                                .catch(error => {
                                    console.error('Error fetching search results:', error);
                                });
                        } else {
                            // If search term is empty, restore original display
                            restoreOriginalDisplay();
                        }
                    }

                    // Event listener for input changes in the search input field
                    document.getElementById('searchInput').addEventListener('input', function() {
                        performLiveSearch();
                    });

                    function restoreOriginalDisplay() {
                        // Clear search results
                        document.getElementById('searchResults').innerHTML = '';

                        // Restore original display elements
                        document.getElementById('startupCompanySection').style.display = 'block';
                        document.getElementById('startupEnablerSection').style.display = 'block';
                        document.getElementById('startupCompanyCards').style.display = 'block';
                        document.getElementById('startupEnablerCards').style.display = 'block';
                    }

                    function performSearch() {
                        var searchTerm = document.getElementById('searchInput').value.trim();
                        if (searchTerm !== '') {
                            fetch('http://217.196.51.115/m/api/search/blog', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        search_term: searchTerm
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    displaySearchResults(data);
                                })
                                .catch(error => {
                                    console.error('Error fetching search results:', error);
                                });
                        }
                    }

                    function displaySearchResults(results) {
                        var searchResultsContainer = document.getElementById('searchResults');
                        var startupCompanySection = document.getElementById('startupCompanySection');
                        var startupEnablerSection = document.getElementById('startupEnablerSection');
                        var startupCompanyCards = document.getElementById('startupCompanyCards');
                        var startupEnablerCards = document.getElementById('startupEnablerCards');

                        // Clear previous results
                        searchResultsContainer.innerHTML = '';

                        // Hide startup company and enabler sections
                        startupCompanySection.style.display = 'none';
                        startupEnablerSection.style.display = 'none';
                        startupCompanyCards.style.display = 'none';
                        startupEnablerCards.style.display = 'none';

                        // Sort results by blog_dateadded in descending order
                        results.sort(function(a, b) {
                            return new Date(b.blog_dateadded) - new Date(a.blog_dateadded);
                        });

                        if (results && results.length > 0) {
                            results.forEach(function(blog) {
                                // Display search results
                                var formattedDate = new Date(blog.blog_dateadded);
                                formattedDate = formattedDate.toLocaleString('en-US', {
                                    timeZone: 'UTC',
                                    hour12: true,
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    year: 'numeric'
                                });
                                formattedDate = formattedDate.replace("at", "|");

                                var card = `
                    <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg mt-10">
                        <a href="blogs-view.php?blog_id=${blog.blog_id}" class="link">
                            <img src="${blog.blog_img}" alt="${blog.blog_img}" class="w-full h-40 object-cover" style="background-color: #888888;">
                            <div class="p-4">
                                <div class="flex items-center mb-2">
                                    <div>
                                        <h2 class="text-2xl font-semibold">${blog.blog_title.length > 20 ? blog.blog_title.substring(0, 20) + '...' : blog.blog_title}</h2>
                                        <p class="text-gray-600 text-sm">${formattedDate}</p>
                                        <p class="text-m text-gray-600 mb-3" id="author-${blog.blog_id}">Loading...</p> <!-- Placeholder for author name -->
                                        <p class="mb-2 mt-2">${blog.blog_content.split(' ').slice(0, 30).join(' ')}${blog.blog_content.split(' ').length > 30 ? '...' : ''}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                                searchResultsContainer.insertAdjacentHTML('beforeend', card);

                                // Update image source
                                var imgElement = searchResultsContainer.querySelector(`img[src="${blog.blog_img}"]`);
                                updateImageSrc(imgElement);

                                // Fetch and display author name
                                fetchAuthorName(blog.blog_author, function(authorName) {
                                    var authorElement = searchResultsContainer.querySelector(`#author-${blog.blog_id}`);
                                    if (authorElement) {
                                        authorElement.textContent = "" + authorName;
                                    }
                                });
                            });
                        } else {
                            // If no search results found, show startup company and enabler sections
                            startupCompanySection.style.display = 'block';
                            startupEnablerSection.style.display = 'block';
                            startupCompanyCards.style.display = 'block';
                            startupEnablerCards.style.display = 'block';
                            searchResultsContainer.innerHTML = '<p>No results found.</p>';
                        }
                    }

                    function updateImageSrc(imgElement) {
                        if (imgElement) {
                            imgElement.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgElement.alt}`;
                        }
                    }

                    function fetchAuthorName(authorId, callback) {
                        fetch('http://217.196.51.115/m/api/members/enablers', {
                                method: 'GET',
                            })
                            .then(response => response.json())
                            .then(data => {
                                const enablers = data;
                                const enabler = enablers.find(member => member.member_id === authorId);
                                if (enabler) {
                                    callback(enabler.setting_institution);
                                } else {
                                    // If not found in enablers, search in companies
                                    fetch('http://217.196.51.115/m/api/members/companies', {
                                            method: 'GET',
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            const companies = data;
                                            const company = companies.find(member => member.member_id === authorId);
                                            if (company) {
                                                callback(company.setting_institution);
                                            } else {
                                                callback("Author Not Found");
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error fetching author name:', error);
                                        });
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching author name:', error);
                            });
                    }
                </script>

                <?php
                // If search results are not displayed, show Startup Enabler section
                if (!$searchResultsDisplayed) {
                    echo '<div id="startupCompanySection" class="text-center">';
                    echo '<h3 class="font-bold text-3xl md:text-4xl mb-5">Startup Company</h3>';
                    echo '<div class="flex justify-end">';
                    echo '<a href="startups-blogs.php?type=company" class="view-all">View All</a>';
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

                <div class="flex flex-row bg-white">

                    <div class="flex justify-center mt-4">
                        <div class="text-center" style="margin-top: 220px;">
                            <a id="company-left" href="#" class="mr-4"><i class="fas fa-chevron-left"></i></a>
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
                                <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg">
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
                                                        echo htmlspecialchars(implode(' ', array_slice($words, 0, 30)));
                                                        if (count($words) > 30) {
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
                    <div style="display: flex; justify-content: center;" class="bg-white">
                        <div class="flex justify-center mt-4">
                            <div class="text-center" style="margin-top: 220px;">
                                <a id="company-right" href="#" class="ml-4"><i class="fas fa-chevron-right"></i></a>
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
                    echo '<h3 class="font-bold text-3xl md:text-4xl mb-5 mt-10">Startup Enabler</h3>';
                    echo '<div class="flex justify-end">';
                    echo '<a href="enablers-blogs.php?type=enabler" class="view-all">View All</a>';
                    echo '</div>';
                    echo '</div>';
                }

                $totalBlogs = count($enabler);
                $pages = ceil($totalBlogs / $perPage);

                $currentPage = isset($_GET['enabler_page']) ? $_GET['enabler_page'] : 1;
                $enablerOffset = ($currentPage - 1) * $perPage;
                ?>

                <div class="flex flex-row bg-white">

                    <div class="flex justify-center mt-4">
                        <div class="text-center" style="margin-top: 220px;">
                            <a id="enabler-left" href="#" class="mr-4"><i class="fas fa-chevron-left"></i></a>
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
                                <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg">
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
                                                        echo htmlspecialchars(implode(' ', array_slice($words, 0, 30)));
                                                        if (count($words) > 30) {
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
                                <a id="enabler-right" href="#" class="ml-4"><i class="fas fa-chevron-right"></i></a>
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
                            fetch(`?enabler_page=${pageNumber}`)
                                .then(response => response.text())
                                .then(html => {
                                    const div = document.createElement('div');
                                    div.innerHTML = html;
                                    const content = div.querySelector('#startupEnablerCards').innerHTML;
                                    enablerSlideshowContainer.innerHTML = content;
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
            </div>
        </main>

    </body>

    </html>
<?php
}
?>