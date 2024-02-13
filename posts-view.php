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

    <?php

    // Function definitions should go at the top

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

    function loadProfilePic($authorProfilePic)
    {
    ?>
        <script>
            fetch("<?php echo $authorProfilePic; ?>")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.blob();
                })
                .then(blob => {
                    const imageUrl = URL.createObjectURL(blob);
                    console.log('Profile picture loaded successfully');
                    document.getElementById('author_profile_pic').src = imageUrl;
                })
                .catch(error => {
                    console.error('Error fetching profile picture:', error);
                });
        </script>
    <?php
    }

    ?>

    <?php

    // Get the post ID from the query parameter
    $post_id = isset($_GET['post_id']) ? ($_GET['post_id']) : 0;

    // Check if a valid post ID is provided
    if ($post_id > 0) {
        $posts = fetch_api_data("http://217.196.51.115/m/api/posts/$post_id");

        if ($posts) {
            $post = $posts[0];

            // Fetch author name and profile picture from members/companies endpoint
            $author_id = $post['post_author'];
            $members = fetch_api_data("http://217.196.51.115/m/api/members/companies");

            if ($members) {
                // Find the author's name and profile picture based on member_id
                $author_name = '';
                $author_profilepic = ''; // Variable to hold the profile picture URL
                foreach ($members as $member) {
                    if ($member['member_id'] == $author_id) {
                        $author_name = $member['setting_institution'];
                        $author_profilepic = $member['setting_profilepic']; // Get the author's profile picture
                        break;
                    }
                }
            }

            include 'navbar-posts.php';
    ?>

            <div class="container mx-auto p-8 max-w-5xl mx-auto">
                <!-- Back icon with link to 'posts' page -->
                <a href="posts.php" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="flex flex-row mb-4 mt-5">
                    <div class="flex items-center">
                        <!-- Display the author's profile picture -->
                        <img src="<?php echo $author_profilepic; ?>" alt="<?php echo $author_profilepic; ?>" id="author_profile_pic" class="w-16 h-16 object-cover rounded-full border-2 border-white shadow-lg">
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($author_name); ?></h2>
                            <p class="text-gray-600"><?php echo date('F j, Y', strtotime($post['post_dateadded'])); ?></p>
                        </div>
                    </div>
                </div>
                <img id="post_pic" src="<?php echo htmlspecialchars($post['post_img']); ?>" alt="<?php echo htmlspecialchars($post['post_img']); ?>" class="w-full h-full object-cover mb-2" style="background-color: #888888;">
                <h2 class="text-2xl font-semibold mt-5 mb-2"><?php echo htmlspecialchars($post['post_heading']); ?></h2>
                <p class="mb-5"><?php echo htmlspecialchars($post['post_bodytext']); ?></p>
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
        // Function to update image src from API
        const updateImageSrc = async (imgElement) => {
            // Get the current src value
            var currentSrc = imgElement.alt;

            // Fetch image data from API
            const res = await fetch('http://217.196.51.115/m/api/images?filePath=profile-img/' + encodeURIComponent(currentSrc))
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

        // Update author's profile picture
        updateImageSrc(document.getElementById("author_profile_pic"));

        // Update post picture
        updateImageSrc(document.getElementById('post_pic'));

        // Ensure the post picture is displayed after the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>

    <?php include 'footer.php'; ?>

</body>

</html>