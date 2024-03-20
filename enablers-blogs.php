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

// Fetch data from member APIs
$enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");
$companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

if (!$company || !$enabler || !$enablers || !$companies) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch data.";
} else {
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
        <title>BINNO | BLOGS</title>

        <style>
            .recent {
                background-color: lightgray;
                margin-right: 55px;
            }
        </style>

    </head>

    <body class="bg-gray-50">

        <div class="bg-white">
            <?php include 'navbar-blogs.php'; ?>
        </div>

        <main class="flex justify-center">
            <div class="container mx-16">
                <div>
                    <div class="mt-5 mb-5">
                        <!-- Back icon with link to 'blogs' page -->
                        <a href="<?php echo htmlspecialchars("blogs.php"); ?>" class="blue-back text-lg">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <h4 class="mt-5 font-bold text-3xl md:text-5xl mb-10">Enabler Blog Articles</h4>
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

                <div id="searchResults" class="grid grid-cols-4 gap-4"></div>

                <script>
                    document.getElementById('searchButton').addEventListener('click', performSearch);

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
                        if (this.value.trim() === '') {
                            restoreOriginalDisplay();
                        }
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
                            fetch('http://217.196.51.115/m/api/search/blog/enabler', {
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
                    document.getElementById('searchInput').addEventListener('input', performLiveSearch);

                    function restoreOriginalDisplay() {
                        // Clear search results
                        document.getElementById('searchResults').innerHTML = '';

                        // Restore original display elements
                        document.getElementById('startupEnablerSection').style.display = 'block';
                    }

                    function performSearch() {
                        var searchTerm = document.getElementById('searchInput').value.trim();
                        if (searchTerm !== '') {
                            fetch('http://217.196.51.115/m/api/search/blog/enabler', {
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
                        var startupEnablerSection = document.getElementById('startupEnablerSection');

                        // Clear previous results
                        searchResultsContainer.innerHTML = '';

                        startupEnablerSection.style.display = 'none';

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
                                    <p class="text-m text-gray-600 mb-3" id="author-${blog.blog_id}">Loading...</p>
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
                            // If no search results found, enabler section
                            startupEnablerSection.style.display = 'block';
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
                                    callback("Author Not Found");
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching author name:', error);
                            });
                    }
                </script>

                <div id="startupEnablerSection" class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php
                        // Sort the blogs array by the blog_dateadded field in descending order
                        usort($enabler, function ($a, $b) {
                            return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                        });

                        foreach ($enabler as $enablerBlog) :
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
                                    <img src="<?php echo htmlspecialchars($enablerBlog['blog_img']); ?>" alt="<?php echo htmlspecialchars($enablerBlog['blog_img']); ?>" id="dynamicEnablerImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
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
                    <script>
                        const enablerCards = <?php echo json_encode($enabler); ?>;

                        // Function to fetch image data from API
                        async function updateEnablerImageSrc(imgSrc) {
                            imgSrc.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`
                            console.log(imgSrc)
                        }

                        // Loop through images with IDs containing "dynamicEnablerImg"
                        document.querySelectorAll('[id^="dynamicEnablerImg-"]').forEach((imgElement, index) => {
                            // Update each image's src from the API
                            updateEnablerImageSrc(imgElement);
                        });
                    </script>
                </div>
            </div>
        </main>

    </body>

    </html>
<?php
}
?>