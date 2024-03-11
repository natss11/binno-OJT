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

$blogs = fetch_api_data("http://217.196.51.115/m/api/blogs/class/company");

if (!$blogs) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch blogs.";
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
        <title>BINNO | STARTUP BLOGS</title>
    </head>

    <body class="bg-gray-100">

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
                    <h4 class="font-bold text-3xl md:text-5xl">Startup Blog Articles</h4>
                </div>

                <!-- Search Bar -->
                <div class="my-4 flex justify-center">
                    <div class="relative" style="width: 700px;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m4-6a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                            </svg>
                        </span>
                        <input type="text" placeholder="Search for a topic or organizer" class="pl-10 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500" style="width: calc(100% - 60px);"> <!-- Subtracting 40px for the icon -->
                        <button type="submit" id="searchButton">Search</button>
                    </div>
                </div>

                <div class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php
                        // Sort the blogs array by the blog_dateadded field in descending order
                        usort($blogs, function ($a, $b) {
                            return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                        });

                        $i = 0;
                        foreach ($blogs as $blog) :
                            $i++;
                        ?>

                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg">
                                <a href="blogs-view.php?blog_id=<?php echo $blog['blog_id']; ?>" class="link">
                                    <img src="<?php echo htmlspecialchars($blog['blog_img']); ?>" alt="<?php echo htmlspecialchars($blog['blog_img']); ?>" id="dynamicImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <div>
                                                <h2 class="text-2xl font-semibold"><?php echo strlen($blog['blog_title']) > 20 ? htmlspecialchars(substr($blog['blog_title'], 0, 20)) . '...' : htmlspecialchars($blog['blog_title']); ?></h2>
                                                <p class="text-gray-600 text-sm mb-2">
                                                    <?php
                                                    $formatted_date = date('F j, Y | h:i A', strtotime($blog['blog_dateadded']));
                                                    echo $formatted_date;
                                                    ?>
                                                </p>
                                                <p class="mb-2 mt-2">
                                                    <?php
                                                    $words = str_word_count($blog['blog_content'], 1);
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
            </div>
        </main>

        <script>
            // Function to update image src from API
            const updateImageSrc = async (imgElement) => {
                // Get the current src value
                var currentSrc = imgElement.alt;

                // Fetch image data from API
                const res = await fetch('http://217.196.51.115/m/api/images?filePath=blog-pics/' + encodeURIComponent(currentSrc))
                    .then(response => response.blob())
                    .then(data => {
                        // Create a blob from the response data
                        var blob = new Blob([data], {
                            type: 'image/png'
                        }); // Adjust type if needed

                        console.log(blob)
                        // Set the new src value using a blob URL
                        imgElement.src = URL.createObjectURL(blob);
                    })
                    .catch(error => console.error('Error fetching image data:', error));
            }

            // Loop through images with IDs containing "dynamicImg"
            var i = 1;
            while (true) {
                var imgElement = document.getElementById("dynamicImg-" + i);
                if (imgElement) {
                    // Update each image's src from the API
                    updateImageSrc(imgElement);
                    i++;
                } else {
                    break; // Break the loop if no more images are found
                }
            }
        </script>

    </body>

    </html>
<?php
}
?>