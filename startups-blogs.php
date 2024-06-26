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
                background-color: #599EF3;
                margin-right: 55px;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
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
                    <h4 class="mt-5 font-bold text-3xl md:text-5xl mb-10">Startup Blog Articles</h4>
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

                <div id="startupCompanySection" class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php
                        // Sort the blogs array by the blog_dateadded field in descending order
                        usort($company, function ($a, $b) {
                            return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                        });

                        foreach ($company as $companyBlog) :
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
                                    <img src="<?php echo htmlspecialchars($companyBlog['blog_img']); ?>" alt="<?php echo htmlspecialchars($companyBlog['blog_img']); ?>" id="dynamicCompanyImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
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
                    <script>
                        const companyCards = <?php echo json_encode($company); ?>;

                        // Function to fetch image data from API
                        async function updateCompanyImageSrc(imgSrc) {
                            imgSrc.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`
                            console.log(imgSrc)
                        }

                        // Loop through images with IDs containing "dynamicCompanyImg"
                        document.querySelectorAll('[id^="dynamicCompanyImg-"]').forEach((imgElement, index) => {
                            // Update each image's src from the API
                            updateCompanyImageSrc(imgElement);
                        });
                    </script>
                </div>

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