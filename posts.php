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

$posts = fetch_api_data("http://217.196.51.115/m/api/posts/");

if (!$posts) {
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
        <title>BINNO | POSTS</title>
    </head>

    <body>

        <?php include 'navbar-posts.php'; ?>

        <main class="flex justify-center">
            <div class="container mx-16">
                <div class="text-center">
                    <h3 class="font-bold text-3xl md:text-4xl">Posts</h3>
                </div>

                <div class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php
                        //'post_dateadded' property
                        usort($posts, function ($a, $b) {
                            return strtotime($b['post_dateadded']) - strtotime($a['post_dateadded']);
                        });

                        $i = 0;
                        foreach ($posts as $post) {
                            $i++;
                            // Check if the required properties exist in the current post
                            if (isset($post['post_dateadded']) && isset($post['post_img']) && isset($post['post_heading'])) {
                                // Limit the display of post_heading to 20 characters and append '...'
                        ?>
                                <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg">
                                    <a href="posts-view.php?post_id=<?php echo $post['post_id']; ?>" class="link">
                                        <img src="<?php echo htmlspecialchars($post['post_img']); ?>" alt="<?php echo htmlspecialchars(($post['post_img'])); ?>" id="dynamicImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                        <div class="p-4 object-cover ml-5">
                                            <h2 class="text-2xl font-semibold"><?php echo strlen($post['post_heading']) > 20 ? htmlspecialchars(substr($post['post_heading'], 0, 20)) . '...' : htmlspecialchars($post['post_heading']); ?></h2>
                                            <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars(date('F j, Y', strtotime($post['post_dateadded']))); ?></p>
                                        </div>
                                    </a>
                                </div>
                        <?php
                            }
                        } ?>
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
                const res = await fetch('http://217.196.51.115/m/api/images?filePath=post-pics/' + encodeURIComponent(currentSrc))
                    .then(response => response.blob())
                    .then(data => {
                        // Create a blob from the response data
                        var blob = new Blob([data], {
                            type: 'image/png'
                        }); // Adjust type if needed

                        console.log(blob);
                        // Set the new src value using a blob URL
                        imgElement.src = URL.createObjectURL(blob);
                    })
                    .catch(error => console.error('Error fetching image data:', error));
            }

            // Loop through all images with IDs starting with "dynamicImg"
            document.querySelectorAll('[id^="dynamicImg-"]').forEach(imgElement => {
                // Update each image's src from the API
                updateImageSrc(imgElement);
            });
        </script>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>