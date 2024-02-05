<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./dist/output.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <title>POSTS</title>
</head>

<body>

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

    // Get the post ID from the query parameter
    $post_id = isset($_GET['post_id']) ? ($_GET['post_id']) : 0;

    // Check if a valid post ID is provided
    if ($post_id > 0) {
        $posts = fetch_api_data("http://217.196.51.115/m/api/posts/$post_id");

        if ($posts) {
            $post = $posts[0];
    ?>

            <?php include 'navbar-posts.php'; ?>

            <div class="container mx-auto p-8 max-w-5xl mx-auto">
                <!-- Back icon with link to 'posts' page -->
                <a href="<?php echo 'posts.php'; ?>" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="flex flex-row mb-4 mt-5">
                    <div>
                        <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($post['post_author']); ?></h2>
                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($post['post_dateadded']); ?></p>
                    </div>
                </div>
                <img id="post_pic" src="<?php echo htmlspecialchars($post['post_img']); ?>" alt="<?php echo htmlspecialchars($post['post_img']); ?>" class="w-full h-full object-cover mb-2" style="background-color: #888888;">
                <h2 class="text-2xl font-semibold mt-5 mb-2"><?php echo htmlspecialchars($post['post_heading']); ?></h2>
                <p class="text-gray-600 mb-5" style="text-align: justify;"><?php echo htmlspecialchars($post['post_bodytext']); ?></p>
            </div>
    <?php
        } else {
            echo '<p>No post found.</p>';
        }
    } else {
        echo '<p>Invalid post ID.</p>';
    }
    ?>
    <script>
        const loadImage = async () => {
            const currentSrc = document.getElementById('post_pic').alt;
            const res = await fetch(
                `http://217.196.51.115/m/api/images?filePath=post-pics/${encodeURIComponent(currentSrc)}`
            );

            const blob = await res.blob();
            const imageUrl = URL.createObjectURL(blob);

            document.getElementById('post_pic').src = imageUrl;
        }

        loadImage();
    </script>

    <?php include 'footer.php'; ?>

</body>

</html>