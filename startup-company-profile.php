<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./dist/output.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <title>BINNO | Startup Company</title>
</head>

<body>

    <?php include 'navbar-profiles.php'; ?>

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

    function loadImage($id, $filePath)
    {
    ?>
        <script>
            const loadImage<?php echo $id; ?> = async () => {
                const currentSrc = document.getElementById('<?php echo $id; ?>').alt;
                const res = await fetch(
                    `http://217.196.51.115/m/api/images?filePath=<?php echo $filePath; ?>/${encodeURIComponent(currentSrc)}`
                );

                const blob = await res.blob();
                const imageUrl = URL.createObjectURL(blob);

                document.getElementById('<?php echo $id; ?>').src = imageUrl;
            }

            loadImage<?php echo $id; ?>();
        </script>
    <?php
    }

    $companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

    if (!$companies) {
        // Handle the case where the API request failed or returned invalid data
        echo "No companies found.";
    } else {
    ?>

        <div class="container mx-auto p-8">

            <div class="flex items-center mb-5">
                <a href="startup-company.php" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <?php
            // Retrieve the parameters from the URL
            $setting_institution = isset($_GET['setting_institution']) ? urldecode($_GET['setting_institution']) : '';

            // Find the specific company by institution name
            $selected_company = null;
            foreach ($companies as $company) {
                if (isset($company['setting_institution']) && $company['setting_institution'] === $setting_institution) {
                    $selected_company = $company;
                    break;
                }
            }

            // Display company details if found
            if (isset($selected_company['setting_institution'])) {
            ?>
                <div class="container mx-auto p-15 px-36">
                    <div class="bg-white rounded-lg overflow-hidden shadow-md mb-5">
                        <img id="cover_pic_<?php echo $selected_company['member_id']; ?>" src="<?php echo $selected_company['setting_coverpic']; ?>" alt="<?php echo str_replace('profile-cover-img/', '', $selected_company['setting_coverpic']); ?>" class="w-full h-64 object-cover" style="background-color: #ffffff;">
                    </div>
                    <div class="flex -mt-20 ml-20">
                        <img id="profile_pic_<?php echo $selected_company['member_id']; ?>" src="<?php echo $selected_company['setting_profilepic']; ?>" alt="<?php echo str_replace('profile-img/', '', $selected_company['setting_profilepic']); ?>" class="w-32 h-32 object-cover rounded-full border-4 border-white" style="background-color: #ffffff;">
                        <div class="px-4 py-2 mt-16 ml-2">
                            <h4 class="text-3xl font-bold mb-2"><?php echo $selected_company['setting_institution']; ?></h4>
                            <p class="text-sm text-gray-600 mb-2">Startup Company</p>
                        </div>
                    </div>
                </div>

                <!-- Left column for chapters and company description -->
                <div class="flex container mx-auto p-15 px-36">
                    <div class="w-1/3 p-4 bg-gray-200 mt-16">
                        <h7>About Us</h7>
                        <p class="text-sm text-gray-600 mb-10 mt-3 text-justify"><?php echo $selected_company['setting_bio']; ?></p>

                        <h7>Address</h7>
                        <p class="text-sm text-gray-600 mb-2 mt-3 text-justify"><?php echo $selected_company['setting_address']; ?></p>
                    </div>

                    <!-- Right column for data -->
                    <div class="w-3/4 p-4 flex flex-col gap-4">
                        <!-- Tab buttons -->
                        <div class="flex justify-end gap-10 text-xl">
                            <button class="tab-btn active" onclick="showContent('events', this)">Events</button>
                            <button class="tab-btn" onclick="showContent('posts', this)">Posts</button>
                        </div>

                        <!-- Events content -->
                        <div id="eventsContent" class="ml-5">
                            <h10>Events</h10>
                            <?php
                            // Fetch events for the specific member
                            $events_url = "http://217.196.51.115/m/api/events/";
                            $member_id = $selected_company['member_id'];

                            // Initialize $events before checking its existence
                            $events = fetch_api_data($events_url);

                            if ($events) {
                                // Filter and display events for the specific member
                                $filtered_events = array_filter($events, function ($event) use ($member_id) {
                                    return $event['event_author'] === $member_id;
                                });

                                // Sort events by date in descending order (newest to oldest)
                                usort($filtered_events, function ($a, $b) {
                                    return strtotime($b['event_date']) - strtotime($a['event_date']);
                                });

                                foreach ($filtered_events as $event) {
                                    // Limit description to 50 words
                                    $description_words = explode(' ', $event['event_description']);
                                    $short_description = implode(' ', array_slice($description_words, 0, 50));
                                    $long_description = implode(' ', array_slice($description_words, 50));

                                    // Check if the description has 50 or fewer words
                                    $display_see_more = count($description_words) > 50;

                            ?>
                                    <div class="border p-4 mb-4 mt-5">
                                        <div class="flex items-center">
                                            <img id="event_profile_pic_<?php echo $event['event_id']; ?>" src="<?php echo htmlspecialchars($selected_company['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_company['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full">
                                            <div class="ml-4">
                                                <h4 class="text-xl font-bold"><?php echo $selected_company['setting_institution']; ?></h4>
                                                <p class="text-sm text-gray-600"><?php echo $event['event_datecreated']; ?></p>
                                            </div>
                                        </div>
                                        <h2 class="text-sm font-bold mt-3"><?php echo isset($event['event_title']) ? $event['event_title'] : ''; ?></h2>
                                        <img id="event_pic_<?php echo $event['event_id']; ?>" alt="<?php echo $event['event_img']; ?>" class="w-full h-64 object-cover mb-2 mt-3" style="background-color: #ffffff;">
                                        <p class="text-sm text-gray-600 mb-2 mt-2">
                                            <?php
                                            // Display short description and toggle with JavaScript
                                            echo $short_description;
                                            ?>
                                            <?php if ($display_see_more) : ?>
                                                <span id="toggle_<?php echo $event['event_id']; ?>" class="see-more" style="display: inline;">
                                                    ... <a href="javascript:void(0);" onclick="toggleDescription('<?php echo $event['event_id']; ?>')">See more</a>
                                                </span>
                                            <?php endif; ?>
                                            <span id="expanded_<?php echo $event['event_id']; ?>" style="display: none;">
                                                <?php echo $long_description; ?>
                                                <a href="javascript:void(0);" onclick="toggleDescription('<?php echo $event['event_id']; ?>')" class="see-less">See less</a>
                                            </span>
                                        </p>
                                    </div>
                            <?php
                                    // Call the function to load the image
                                    loadImage('event_pic_' . $event['event_id'], 'event-pics');

                                    // Call the function to load the profile pic with a unique ID for each event
                                    loadImage('event_profile_pic_' . $event['event_id'], 'profile-img');
                                }
                            } else {
                                // Handle the case where the API request for events failed or returned invalid data
                                echo "Failed to fetch events.";
                            }
                            ?>
                        </div>
                        <script>
                            function toggleDescription(eventId) {
                                var toggleSpan = document.getElementById('toggle_' + eventId);
                                var expandedSpan = document.getElementById('expanded_' + eventId);

                                if (toggleSpan.style.display === "inline") {
                                    toggleSpan.style.display = "none";
                                    expandedSpan.style.display = "inline";
                                } else {
                                    toggleSpan.style.display = "inline";
                                    expandedSpan.style.display = "none";
                                }
                            }
                        </script>

                        <div id="postsContent" class="ml-5">
                            <h5>Posts</h5>
                            <?php
                            // Fetch posts for the specific member
                            $posts_url = "http://217.196.51.115/m/api/posts/";
                            $member_id = $selected_company['member_id'];

                            // Initialize $posts before checking its existence
                            $posts = fetch_api_data($posts_url);

                            if ($posts) {
                                // Filter and display posts for the specific member
                                foreach ($posts as $post) {
                                    if ($post['post_author'] === $member_id) {
                                        // Limit post body text to 50 words
                                        $bodytext_words = explode(' ', $post['post_bodytext']);
                                        $short_bodytext = implode(' ', array_slice($bodytext_words, 0, 50));
                                        $long_bodytext = implode(' ', array_slice($bodytext_words, 50));
                                        $display_see_more = count($bodytext_words) > 50;
                            ?>
                                        <div class="border p-4 mb-4 mt-5">
                                            <div class="flex items-center">
                                                <img id="post_profile_pic_<?php echo $post['post_id']; ?>" src="<?php echo htmlspecialchars($selected_company['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_company['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full">
                                                <div class="ml-4">
                                                    <h4 class="text-xl font-bold"><?php echo $selected_company['setting_institution']; ?></h4>
                                                    <p class="text-sm text-gray-600"><?php echo $post['post_dateadded']; ?></p>
                                                </div>
                                            </div>
                                            <h2 class="text-sm font-bold mt-3"><?php echo isset($post['post_heading']) ? $post['post_heading'] : ''; ?></h2>
                                            <img id="post_pic_<?php echo $post['post_id']; ?>" alt="<?php echo $post['post_img']; ?>" class="w-full h-64 object-cover mb-2 mt-3" style="background-color: #ffffff;">
                                            <p class="text-sm text-black-800 text-justify mb-2 mt-2">
                                                <?php echo $short_bodytext; ?>
                                                <?php if ($display_see_more) : ?>
                                                    <span id="toggle_<?php echo $post['post_id']; ?>" class="see-more" style="display: inline;">
                                                        ... <a href="javascript:void(0);" onclick="toggleDescription('<?php echo $post['post_id']; ?>')">See more</a>
                                                    </span>
                                                <?php endif; ?>
                                                <span id="expanded_<?php echo $post['post_id']; ?>" style="display: none;">
                                                    <?php echo $long_bodytext; ?>
                                                    <a href="javascript:void(0);" onclick="toggleDescription('<?php echo $post['post_id']; ?>')" class="see-less">See less</a>
                                                </span>
                                            </p>
                                        </div>
                            <?php
                                        // Call the function to load the image
                                        loadImage('post_pic_' . $post['post_id'], 'post-pics');

                                        // Call the function to load the profile pic with a unique ID for each post
                                        loadImage('post_profile_pic_' . $post['post_id'], 'profile-img');
                                    }
                                }
                            } else {
                                // Handle the case where the API request for posts failed or returned invalid data
                                echo "Failed to fetch posts.";
                            }
                            ?>
                        </div>

                    </div>
                </div>

                <!-- JavaScript to handle tab switching -->
                <script>
                    function showContent(tabName, tabBtn) {
                        // Hide all content
                        document.getElementById('eventsContent').style.display = 'none';
                        document.getElementById('postsContent').style.display = 'none';

                        // Deactivate all tabs
                        document.querySelectorAll('.tab-btn').forEach(function(tabBtn) {
                            tabBtn.classList.remove('active');
                        });

                        // Show the selected content
                        document.getElementById(tabName + 'Content').style.display = 'block';

                        // Activate the selected tab
                        tabBtn.classList.add('active');
                    }
                </script>

            <?php
                // Call the function to load cover and profile images
                loadImage('cover_pic_' . $selected_company['member_id'], 'profile-cover-img');
                loadImage('profile_pic_' . $selected_company['member_id'], 'profile-img');
            } else {
                // Handle the case where the selected company or its institution is not found
                echo "Company not found.";
            }
            ?>

        </div>

        <script>
            const loadImage = async () => {
                const currentSrc = document.getElementById('profile_img').alt
                const res = await fetch(
                    `http://217.196.51.115/m/api/images?filePath=profile-img/${encodeURIComponent(currentSrc)}`
                )

                const blob = await res.blob();
                const imageUrl = URL.createObjectURL(blob);

                document.getElementById('profile_img').src = imageUrl;

            }

            loadImage()
        </script>

    <?php
    }
    ?>

    <?php include 'footer.php'; ?>

</body>

</html>