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
$events = fetch_api_data("http://217.196.51.115/m/api/events/");
$blogs = fetch_api_data("http://217.196.51.115/m/api/blogs/");

if (!$posts || !$events || !$blogs) {
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
        <title>BINNO</title>
    </head>

    <body>

        <?php include 'navbar.php'; ?>

        <main class="flex justify-center">
            <div class="container mx-16">

                <!-- Display Startup Posts -->
                <div class="text-center">
                    <h3 class="font-bold text-3xl md:text-4xl">Latest Posts</h3>
                </div>
                
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="cardContainer">
                        <?php
                        // Sort the posts array by post date in descending order
                        usort($posts, function ($a, $b) {
                            return strtotime($b['post_dateadded']) - strtotime($a['post_dateadded']);
                        });

                        // Display only the first 3 posts
                        for ($i = 0; $i < min(3, count($posts)); $i++) {
                            $post = $posts[$i];
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <a href="posts-view.php?post_id=<?= $post['post_id']; ?>" class="link">
                                    <img src="<?= htmlspecialchars($post['post_img']); ?>" alt="<?= htmlspecialchars($post['post_img']); ?>" id="dynamicPostImg-<?= $i + 1 ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <div>
                                                <h2 class="text-2xl font-semibold"><?php echo strlen($post['post_heading']) > 20 ? htmlspecialchars(substr($post['post_heading'], 0, 20)) . '...' : htmlspecialchars($post['post_heading']); ?></h2>
                                                <p class="text-gray-600 text-sm"><?= date('F j, Y', strtotime($post['post_dateadded'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                    <script>
                        const cards = <?php echo json_encode($posts); ?>;

                        // Function to fetch image data from API
                        async function updateImageSrc(imgSrc) {
                            imgSrc.src = `http://217.196.51.115/m/api/images?filePath=post-pics/${imgSrc.alt}`;
                            console.log(imgSrc);
                        }

                        // Loop through images with IDs containing "dynamicPostImg"
                        document.querySelectorAll('[id^="dynamicPostImg-"]').forEach((imgElement, index) => {
                            // Update each image's src from the API
                            updateImageSrc(imgElement);
                        });
                    </script>
                </div>

                <!-- Display Events -->
                <div class="text-center">
                    <h3 class="font-bold text-3xl md:text-4xl">Latest Events</h3>
                </div>
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="eventCardContainer">

                        <?php
                        // Sort events array by date in descending order
                        usort($events, function ($a, $b) {
                            return strtotime($b['event_datecreated']) - strtotime($a['event_datecreated']);
                        });

                        $i = 0;
                        foreach (array_slice($events, 0, 3) as $event) :
                            $i++;
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <a href="events-view.php?event_id=<?= $event['event_id']; ?>" class="link">
                                    <img src="<?= htmlspecialchars($event['event_img']); ?>" alt="<?= htmlspecialchars($event['event_img']); ?>" id="dynamicEventImg-<?= $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <div>
                                                <h2 class="text-2xl font-semibold"><?php echo strlen($event['event_title']) > 20 ? htmlspecialchars(substr($event['event_title'], 0, 20)) . '...' : htmlspecialchars($event['event_title']); ?></h2>
                                                <p class="text-gray-600 text-sm"><?= date('F j, Y', strtotime($event['event_datecreated'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        const eventCards = <?php echo json_encode($events); ?>;

                        // Function to fetch image data from API
                        async function updateImageSrc(imgSrc) {
                            imgSrc.src = `http://217.196.51.115/m/api/images?filePath=event-pics/${imgSrc.alt}`
                            console.log(imgSrc)
                        }

                        // Loop through images with IDs containing "dynamicEventImg"
                        document.querySelectorAll('[id^="dynamicEventImg-"]').forEach((imgElement, index) => {
                            // Update each image's src from the API
                            updateImageSrc(imgElement);
                        });
                    </script>
                </div>

                <!-- Display Blogs -->
                <div class="text-center">
                    <h3 class="font-bold text-3xl md:text-4xl">Latest Blogs</h3>
                </div>
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="blogCardContainer">

                        <?php
                        // Sort blogs by 'blog_dateadded' in descending order
                        usort($blogs, function ($a, $b) {
                            return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                        });

                        $i = 0;
                        foreach (array_slice($blogs, 0, 3) as $blog) :
                            $i++;
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <a href="blogs-view.php?blog_id=<?= $blog['blog_id']; ?>" class="link">
                                    <img src="<?= htmlspecialchars($blog['blog_img']); ?>" alt="<?= htmlspecialchars($blog['blog_img']); ?>" id="dynamicBlogImg-<?= $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                    <div class="p-4">
                                        <div class="flex items-center mb-2">
                                            <div>
                                                <h2 class="text-2xl font-semibold"><?php echo strlen($blog['blog_title']) > 20 ? htmlspecialchars(substr($blog['blog_title'], 0, 20)) . '...' : htmlspecialchars($blog['blog_title']); ?></h2>
                                                <p class="text-gray-600 text-sm"><?= date('F j, Y', strtotime($blog['blog_dateadded'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        const blogCards = <?php echo json_encode($blogs); ?>;

                        // Function to fetch image data from API
                        async function updateBlogImageSrc(imgSrc) {
                            imgSrc.src = `http://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`;
                            console.log(imgSrc);
                        }

                        // Loop through images with IDs containing "dynamicBlogImg"
                        document.querySelectorAll('[id^="dynamicBlogImg-"]').forEach((imgElement, index) => {
                            // Update each image's src from the API
                            updateBlogImageSrc(imgElement);
                        });
                    </script>
                </div>
            </div>
        </main>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>