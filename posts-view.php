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

    <style>
        .post {
            display: flex;
            align-items: center;
            /* Align items vertically */
            margin-bottom: 10px;
            /* Adjust margin as needed */
        }

        .post img {
            width: 50px;
            height: 40px;
            margin-right: 10px;
            /* Adjust margin as needed */
        }

        .post .content {
            flex-grow: 1;
            /* Allow content to grow to fill remaining space */
        }

        .blog {
            display: flex;
            align-items: center;
            /* Align items vertically */
            margin-bottom: 10px;
            /* Adjust margin as needed */
        }

        .blog img {
            width: 50px;
            height: 40px;
            margin-right: 10px;
            /* Adjust margin as needed */
        }

        .blog .content {
            flex-grow: 1;
            /* Allow content to grow to fill remaining space */
        }

        .event {
            display: flex;
            align-items: center;
            /* Align items vertically */
            margin-bottom: 10px;
            /* Adjust margin as needed */
        }

        .event img {
            width: 50px;
            height: 40px;
            margin-right: 10px;
            /* Adjust margin as needed */
        }

        .event .content {
            flex-grow: 1;
            /* Allow content to grow to fill remaining space */
        }

        .show-more-button {
            background-color: #FF7A00;
            color: white;
            border: none;
            padding: 5px 100px;
            /* Adjust padding as needed */
            cursor: pointer;
            border-radius: 5px;
            display: inline-flex;
            /* Display button inline with its contents */
            align-items: center;
            /* Align items vertically */
        }

        .show-more-button span {
            margin-right: 5px;
            /* Adjust margin between text and button */
        }

        .show-more-button:hover {
            background-color: darkorange;
        }

        @media only screen and (max-width: 1024px) {

            /* Medium screens */
            .lg\:w-1\/4 {
                display: none;
            }
        }

        @media only screen and (max-width: 640px) {

            /* Small screens */
            .lg\:w-1\/4 {
                display: none;
            }
        }

        .border {
            padding: 10px;
        }
    </style>

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
        $posts = fetch_api_data("https://binnostartup.site/m/api/posts/$post_id");

        if ($posts) {
            $post = $posts[0];

            // Fetch author name and profile picture from members/companies endpoint
            $author_id = $post['post_author'];
            $members = fetch_api_data("https://binnostartup.site/m/api/members/companies");

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

            <div class="container mx-auto p-8 max-w-10xl mx-auto">
                <!-- Back icon with link to 'posts' page -->
                <a href="posts.php" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="flex flex-col lg:flex-row container mx-auto lg:px-8">

                <!-- Left column -->
                <div class="w-full lg:w-1/4 p-4 mt-40 mr-10 mb-5 text-center">
                    <div id="latestPosts" class="border shadow-lg">
                        <h3>Latest Posts</h3>
                    </div>
                    <div id="latestBlogs" class="mt-10 border shadow-lg">
                        <h3>Latest Blogs</h3>
                    </div>
                </div>

                <div class="w-full lg:w-3/4 p-4 flex flex-col gap-4 mb-10">
                    <div class="flex flex-col">
                        <div class="flex items-center mb-3">
                            <!-- Display the author's profile picture -->
                            <img src="<?php echo $author_profilepic; ?>" alt="<?php echo $author_profilepic; ?>" id="author_profile_pic" class="w-16 h-16 object-cover rounded-full border-2">
                            <div class="ml-4">
                                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($author_name); ?></h2>
                                <p class="text-gray-600 text-sm">
                                    <?php
                                    $datetime = strtotime($post['post_dateadded']);
                                    echo date('F j, Y | h:i A', $datetime);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <h2 class="text-2xl font-semibold mt-4"><?php echo htmlspecialchars($post['post_heading']); ?></h2>
                    <img id="post_pic" src="<?php echo htmlspecialchars($post['post_img']); ?>" alt="<?php echo htmlspecialchars($post['post_img']); ?>" class="w-full max-h-full object-cover mb-2 shadow-lg" style="background-color: #888888;">
                    <p class="mb-5"><?php echo htmlspecialchars($post['post_bodytext']); ?></p>
                </div>

                <!-- right column -->
                <div class="w-full lg:w-1/4 p-4 mt-40 ml-10 mb-5 text-center">
                    <div id="latestEvents" class="border shadow-lg">
                        <h3>Upcoming Events</h3>
                    </div>
                </div>

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
        // Fetch and display latest posts
        async function fetchAndDisplayPosts() {
            try {
                const response = await fetch('https://binnostartup.site/m/api/posts');
                const data = await response.json();

                // Sort the data array by post_dateadded
                data.sort((a, b) => new Date(b.post_dateadded) - new Date(a.post_dateadded));

                const latestPostsDiv = document.getElementById('latestPosts');

                // Display only the first 5 posts
                for (let i = 0; i < Math.min(5, data.length); i++) {
                    const post = data[i];
                    const postDiv = document.createElement('div');
                    postDiv.classList.add('post');

                    // Add margin-top to the first post
                    if (i === 0) {
                        postDiv.style.marginTop = '10px'; // Adjust margin-top value as needed
                    }

                    // Create image element and set its attributes
                    const img = document.createElement('img');
                    img.src = `https://binnostartup.site/m/api/images?filePath=post-pics/${encodeURIComponent(post.post_img)}`;
                    img.alt = post.post_heading;
                    postDiv.appendChild(img);

                    const contentDiv = document.createElement('div');

                    const title = document.createElement('p');
                    title.textContent = post.post_heading.length > 20 ? post.post_heading.substring(0, 20) + '...' : post.post_heading;
                    title.style.textAlign = 'left';
                    contentDiv.appendChild(title);

                    const date = document.createElement('p');
                    date.textContent = formatDate(post.post_dateadded);
                    date.style.textAlign = 'left'; // Align left
                    contentDiv.appendChild(date);

                    postDiv.appendChild(contentDiv);

                    latestPostsDiv.appendChild(postDiv);
                }

                // Add a 'Show More' button
                const showMoreButton = document.createElement('button');
                showMoreButton.textContent = 'More';
                showMoreButton.className = 'show-more-button'; // class for styling
                showMoreButton.onclick = function() {
                    window.location.href = 'posts.php'; // Adjust URL as needed
                };
                latestPostsDiv.appendChild(showMoreButton);

                // Fetch and display upcoming events after displaying posts
                fetchAndDisplayEvents();

            } catch (error) {
                console.error('Error fetching and displaying posts:', error);
            }
        }

        // Helper function to format date
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Function to fetch data from the API and display the first 5 blogs
        async function fetchAndDisplayBlogs() {
            try {
                const response = await fetch('https://binnostartup.site/m/api/blogs');
                const data = await response.json();
                data.sort((a, b) => new Date(b.blog_dateadded) - new Date(a.blog_dateadded));

                const latestBlogsDiv = document.getElementById('latestBlogs');

                // Display the first 5 blogs
                for (let i = 0; i < 5; i++) {
                    const blog = data[i];
                    const blogDiv = document.createElement('div');
                    blogDiv.classList.add('blog');

                    // Add margin-top to the first post
                    if (i === 0) {
                        blogDiv.style.marginTop = '10px'; // Adjust margin-top value as needed
                    }

                    // Create image element and set its attributes
                    const img = document.createElement('img');
                    img.src = `https://binnostartup.site/m/api/images?filePath=blog-pics/${encodeURIComponent(blog.blog_img)}`;
                    img.alt = blog.blog_title;
                    blogDiv.appendChild(img);

                    // Create a div to hold title and date
                    const contentDiv = document.createElement('div');

                    // Create paragraph elements for blog title and date
                    const title = document.createElement('p');
                    // Limit the blog title to 15 characters
                    title.textContent = blog.blog_title.length > 20 ? blog.blog_title.substring(0, 20) + '...' : blog.blog_title;
                    title.style.textAlign = 'left'; // Align left
                    contentDiv.appendChild(title);

                    const date = document.createElement('p');
                    date.textContent = formatDate(blog.blog_dateadded);
                    date.style.textAlign = 'left'; // Align left
                    contentDiv.appendChild(date);

                    // Add contentDiv to blogDiv
                    blogDiv.appendChild(contentDiv);

                    latestBlogsDiv.appendChild(blogDiv);
                }

                // Add a 'More' button
                const showMoreButton = document.createElement('button');
                showMoreButton.textContent = 'More';
                showMoreButton.className = 'show-more-button'; // class for styling
                showMoreButton.onclick = function() {
                    window.location.href = 'blogs.php';
                };
                latestBlogsDiv.appendChild(showMoreButton);
            } catch (error) {
                console.error('Error fetching and displaying blogs:', error);
            }
        }

        // Function to format the date
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', options);
        }

        // Fetch and display upcoming events
        async function fetchAndDisplayEvents() {
            try {
                const response = await fetch('https://binnostartup.site/m/api/events');
                const data = await response.json();

                // Sort events by event_date in descending order
                data.sort((a, b) => new Date(b.event_date) - new Date(a.event_date));

                const latestEventsContainer = document.getElementById('latestEvents');
                latestEventsContainer.innerHTML = ''; // Clear previous content

                // Add "Upcoming Events" text at the top
                const upcomingEventsText = document.createElement('h3');
                upcomingEventsText.textContent = 'Upcoming Events';
                latestEventsContainer.appendChild(upcomingEventsText);

                // Display only the first 5 events
                for (let i = 0; i < Math.min(5, data.length); i++) {
                    const event = data[i];
                    const eventDiv = document.createElement('div');
                    eventDiv.classList.add('event');

                    // Add margin-top to the first post
                    if (i === 0) {
                        eventDiv.style.marginTop = '10px'; // Adjust margin-top value as needed
                    }

                    // Create image element and set its attributes
                    const img = document.createElement('img');
                    img.src = `https://binnostartup.site/m/api/images?filePath=event-pics/${encodeURIComponent(event.event_img)}`;
                    img.alt = event.event_title;
                    eventDiv.appendChild(img);

                    const contentDiv = document.createElement('div');

                    const title = document.createElement('p');
                    title.textContent = event.event_title.length > 20 ? event.event_title.substring(0, 20) + '...' : event.event_title;
                    title.style.textAlign = 'left';
                    contentDiv.appendChild(title);

                    const date = document.createElement('p');
                    date.textContent = formatDate(event.event_date);
                    date.style.textAlign = 'left'; // Align left
                    contentDiv.appendChild(date);

                    eventDiv.appendChild(contentDiv);

                    latestEventsContainer.appendChild(eventDiv);
                }

                // Add a 'Show More' button
                const showMoreButton = document.createElement('button');
                showMoreButton.textContent = 'More';
                showMoreButton.className = 'show-more-button'; // class for styling
                showMoreButton.onclick = function() {
                    window.location.href = 'events.php'; // Adjust URL as needed
                };
                latestEventsContainer.appendChild(showMoreButton);

            } catch (error) {
                console.error('Error fetching and displaying events:', error);
            }
        }

        // Helper function to format date
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        // Call functions to fetch and display posts and events
        fetchAndDisplayPosts();
        fetchAndDisplayBlogs();
        fetchAndDisplayEvents();

        // Function to update image src from API
        const updateImageSrc = async (imgElement) => {
            // Get the current src value
            var currentSrc = imgElement.alt;

            // Fetch image data from API
            const res = await fetch('https://binnostartup.site/m/api/images?filePath=profile-img/' + encodeURIComponent(currentSrc))
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

        // Ensure the post picture is displayed after the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const loadImage = async () => {
                const currentSrc = document.getElementById('post_pic').alt;
                const res = await fetch(
                    `https://binnostartup.site/m/api/images?filePath=post-pics/${encodeURIComponent(currentSrc)}`
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