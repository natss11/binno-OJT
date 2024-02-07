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

$enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");

if (!$enablers) {
    // Handle the case where the API request failed or returned invalid data
    echo "No enablers found.";
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
        <title>Startup Enablers</title>
    </head>

    <body>

        <?php include 'navbar-profiles.php'; ?>

        <div class="container mx-auto p-8">

            <div class="flex items-center mb-5">
                <a href="startup-enabler.php" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <?php
            // Retrieve the parameters from the URL
            $setting_institution = isset($_GET['setting_institution']) ? urldecode($_GET['setting_institution']) : '';

            // Find the specific enabler by institution name
            $selected_enabler = null;
            foreach ($enablers as $enabler) {
                if (isset($enabler['setting_institution']) && $enabler['setting_institution'] === $setting_institution) {
                    $selected_enabler = $enabler;
                    break;
                }
            }

            // Display enabler details if found
            if ($selected_enabler) {
            ?>

                <div class="container mx-auto p-15 px-36">
                    <div class="bg-white rounded-lg overflow-hidden shadow-md mb-5">
                        <!-- Use loadCoverImage for cover pic -->
                        <img id="cover_pic_<?php echo $selected_enabler['member_id']; ?>" src="<?php echo $selected_enabler['setting_coverpic']; ?>" alt="<?php echo htmlspecialchars(str_replace('profile-cover-img/', '', $selected_enabler['setting_coverpic'])); ?>" class="w-full h-64 object-cover" style="background-color: #ffffff;">
                    </div>
                    <div class="flex -mt-20 ml-20">
                        <!-- Use loadProfileImage for profile pic -->
                        <img id="profile_pic_<?php echo $selected_enabler['member_id']; ?>" src="<?php echo $selected_enabler['setting_profilepic']; ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-32 h-32 object-cover rounded-full border-4 border-white" style="background-color: #ffffff;">
                        <div class="px-4 py-2 mt-16 ml-2">
                            <h4 class="text-3xl font-bold mb-2"><?php echo $selected_enabler['setting_institution']; ?></h4>
                            <p class="text-sm text-gray-600 mb-1">Startup Enabler</p>
                        </div>
                    </div>
                </div>

                <!-- Left column for chapters and company description -->
                <div class="flex container mx-auto p-15 px-36">
                    <div class="left-column w-1/3 p-4 bg-gray-200 mt-16">
                        <h7>Company Description</h7>
                        <p class="text-sm text-gray-600 mb-10 mt-3 text-justify"><?php echo $selected_enabler['setting_bio']; ?></p>

                        <h7>Company Address</h7>
                        <p class="text-sm text-gray-600 mb-2 mt-3 text-justify"><?php echo $selected_enabler['setting_address']; ?></p>
                    </div>

                    <!-- Right column for data -->
                    <div class="right-column w-3/4 p-4 flex flex-col gap-4">
                        <!-- Tab buttons -->
                        <div class="flex justify-end gap-10 text-xl">
                            <button class="tab-btn active" onclick="showContent('events', this)">Events</button>
                            <button class="tab-btn" onclick="showContent('blogs', this)">Blogs</button>
                        </div>

                        <!-- Events content -->
                        <div id="eventsContent" class="ml-5">
                            <h10>Events</h10>
                            <?php
                            // Fetch events for the specific member
                            $events_url = "http://217.196.51.115/m/api/events/";
                            $member_id = $selected_enabler['member_id'];
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
                            ?>
                                    <div class="border p-4 mb-4 mt-5">
                                        <div class="flex items-center">
                                            <img id="event_profile_pic_<?php echo $event['event_id']; ?>" src="<?php echo htmlspecialchars($selected_enabler['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full">
                                            <div class="ml-4">
                                                <h4 class="text-xl font-bold"><?php echo $selected_enabler['setting_institution']; ?></h4>
                                                <p class="text-sm text-gray-600"><?php echo $event['event_datecreated']; ?></p>
                                            </div>
                                        </div>

                                        <h2 class="text-sm font-bold mt-3"><?php echo $event['event_title']; ?></h2>
                                        <img id="event_pic_<?php echo $event['event_id']; ?>" alt="<?php echo $event['event_img']; ?>" class="w-full h-64 object-cover mb-2 mt-3" style="background-color: #ffffff;">
                                        <p class="text-sm text-gray-600 mb-2 mt-2"><?php echo $event['event_date']; ?></p>
                                        <p class="text-sm text-black-800 mt-3 text-justify"><?php echo $event['event_description']; ?></p>
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

                        <!-- Blogs content (hidden by default) -->
                        <div id="blogsContent" class="ml-5" style="display: none;">
                            <h10>Blogs</h10>

                            <div id="blogsContent" class="mt-5">
                                <?php
                                // Fetch blogs for the specific member
                                $blogs_url = "http://217.196.51.115/m/api/blogs/";
                                $member_id = $selected_enabler['member_id'];
                                $blogs = fetch_api_data($blogs_url);

                                if ($blogs) {
                                    // Filter and display blogs for the specific member
                                    $filtered_blogs = array_filter($blogs, function ($blog) use ($member_id) {
                                        return $blog['blog_author'] === $member_id;
                                    });

                                    // Sort blogs by date in descending order (newest to oldest)
                                    usort($filtered_blogs, function ($a, $b) {
                                        return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                                    });

                                    foreach ($filtered_blogs as $blog) {
                                ?>
                                        <div class="border p-4 mb-4 mt-5">
                                            <div class="flex items-center">
                                                <img id="blog_profile_pic_<?php echo $blog['blog_id']; ?>" src="<?php echo htmlspecialchars($selected_enabler['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full">
                                                <div class="ml-4">
                                                    <h4 class="text-xl font-bold"><?php echo $selected_enabler['setting_institution']; ?></h4>
                                                    <p class="text-sm text-gray-600"><?php echo $blog['blog_dateadded']; ?></p>
                                                </div>
                                            </div>
                                            <h2 class="text-sm font-bold mt-3"><?php echo $blog['blog_title']; ?></h2>
                                            <img id="blog_pic_<?php echo $blog['blog_id']; ?>" alt="<?php echo $blog['blog_img']; ?>" class="w-full h-64 object-cover mb-2 mt-3" style="background-color: #ffffff;">
                                            <p class="text-sm text-black-800 mt-3 text-justify"><?php echo $blog['blog_content']; ?></p>
                                        </div>
                                <?php
                                        // Call the function to load the image
                                        loadImage('blog_pic_' . $blog['blog_id'], 'blog-pics');

                                        // Call the function to load the profile pic with a unique ID for each blog
                                        loadImage('blog_profile_pic_' . $blog['blog_id'], 'profile-img');
                                    }
                                } else {
                                    // Handle the case where the API request for blogs failed or returned invalid data
                                    echo "Failed to fetch blogs.";
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
                            document.getElementById('blogsContent').style.display = 'none';

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

                </div>

            <?php
                // Call the function to load cover and profile images
                loadImage('cover_pic_' . $selected_enabler['member_id'], 'profile-cover-img');
                loadImage('profile_pic_' . $selected_enabler['member_id'], 'profile-img');
            } else {
                // Handle the case where the selected enabler is not found
                echo "Enabler not found.";
            }
            ?>

        </div>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>