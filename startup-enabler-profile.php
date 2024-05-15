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
        <title>BINNO | Startup Enabler</title>

        <style>
            .contact-info-container {
                display: flex;
                justify-content: space-between;
            }

            .contact-info-item {
                width: 48%;
                /* Adjust as needed */
            }

            .rec {
                border-radius: 8px;
                border-color: #d9d9d9;
                padding: 5px;
            }

            .datetime {
                float: right;
            }
        </style>

    </head>

    <body>

        <div class="container mx-auto p-8">

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

                <div class="container mx-auto px-4 sm:px-8 md:px-16 lg:px-20 xl:px-64">
                    <div class="flex items-center mb-10">
                        <a href="startup-enabler.php" class="blue-back text-lg">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="bg-white rounded-lg overflow-hidden mb-5">
                        <!-- Use loadCoverImage for cover pic -->
                        <img id="cover_pic_<?php echo $selected_enabler['member_id']; ?>" src="<?php echo $selected_enabler['setting_coverpic']; ?>" alt="<?php echo htmlspecialchars(str_replace('profile-cover-img/', '', $selected_enabler['setting_coverpic'])); ?>" class="w-full h-64 object-cover" style="background-color: #ffffff;">
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start -mt-20 ml-0 sm:ml-20">
                        <!-- Use loadProfileImage for profile pic -->
                        <img id="profile_pic_<?php echo $selected_enabler['member_id']; ?>" src="<?php echo $selected_enabler['setting_profilepic']; ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-32 h-32 object-cover rounded-full border-4 border-white" style="background-color: #ffffff;">
                        <div class="px-4 py-2 sm:mt-16">
                            <h2 class="text-3xl font-bold mb-2"><?php echo $selected_enabler['setting_institution']; ?></h2>
                            <p class="text-sm text-gray-600 mb-1"><?php echo ($selected_enabler['enabler_class']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="container mx-auto px-4 sm:px-8 md:px-8 lg:px-20 xl:px-64">

                    <div class="p-4 flex flex-col gap-4">
                        <!-- Tab buttons -->
                        <div class="flex justify-center sm:justify-end gap-10 text-xl">
                            <button class="tab-btn active" onclick="showContent('events', this)">Events</button>
                            <button class="tab-btn" onclick="showContent('blogs', this)">Blogs</button>
                            <button class="tab-btn" onclick="showContent('mentors', this)">Mentors</button>
                            <button class="tab-btn" onclick="showContent('about', this)">About</button>
                        </div>


                        <!-- Events content -->
                        <div id="eventsContent">
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
                                    $description_words = explode(" ", $event['event_description']);
                                    $short_description = implode(" ", array_slice($description_words, 0, 50));
                                    $remaining_description = implode(" ", array_slice($description_words, 50));
                            ?>
                                    <div class="bg-white border p-4 mb-4 mt-5" style="border-radius: 10px;">
                                        <div class="flex items-center">
                                            <img id="event_profile_pic_<?php echo $event['event_id']; ?>" src="<?php echo htmlspecialchars($selected_enabler['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full border">
                                            <div class="ml-4">
                                                <h2 class="text-xl font-bold"><?php echo $selected_enabler['setting_institution']; ?></h2>
                                                <p class="text-sm text-gray-600">
                                                    <?php
                                                    $event_datetime = strtotime($event['event_datecreated']);
                                                    echo date('F j, Y | h:i A', $event_datetime);
                                                    ?>
                                                </p>
                                            </div>
                                        </div>

                                        <p class="font-semibold text-sm mt-5 mb-2">
                                            <i class="fas fa-map-marker-alt mr-1"></i><?php echo ($event['event_address']); ?>
                                        </p>
                                        <p class="text-sm font-semibold text-black-600 mb-2 mt-2">
                                            <i class="fas fa-calendar-alt mr-1"></i><?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                        </p>
                                        <p class="text-sm font-semibold text-black-600 mb-2 mt-2">
                                            <i class="far fa-clock mr-1"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                                        </p>
                                        <h2 class="text-sm font-bold mt-3"><?php echo $event['event_title']; ?></h2>
                                        <img id="event_pic_<?php echo $event['event_id']; ?>" alt="<?php echo $event['event_img']; ?>" class="w-full h-full object-cover mb-2 mt-3 border" style="background-color: #ffffff;">
                                        <p class="text-sm text-black-800 mt-3">
                                            <?php echo $short_description; ?>
                                            <?php if (count($description_words) > 50) : ?>
                                                <span id="full_description_<?php echo $event['event_id']; ?>" class="full-content hidden"><?php echo $remaining_description; ?></span>
                                                <span id="toggle_description_<?php echo $event['event_id']; ?>" class="see-more-link cursor-pointer text-blue-500">See more</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <script>
                                        document.getElementById('toggle_description_<?php echo $event['event_id']; ?>').addEventListener('click', function() {
                                            var fullDescription = document.getElementById('full_description_<?php echo $event['event_id']; ?>');
                                            fullDescription.classList.toggle('hidden');
                                            this.textContent = fullDescription.classList.contains('hidden') ? 'See more' : 'See less';
                                        });
                                    </script>
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

                            <script>
                                // Add event listener for "See more" link
                                document.querySelectorAll('.see-more-link').forEach(link => {
                                    link.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const parent = e.target.closest('.border');
                                        parent.querySelector('.full-content').classList.toggle('hidden');
                                        e.target.textContent = e.target.textContent === 'See more' ? 'See less' : 'See more';
                                    });
                                });
                            </script>
                        </div>

                        <!-- Blogs content (hidden by default) -->
                        <div id="blogsContent" style="display: none;">
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
                                        // Check if content length is more than 50 words
                                        $content_words = str_word_count($blog['blog_content']);
                                        $display_see_more = $content_words > 50;

                                        // Limit content display to 50 words
                                        $display_content = implode(' ', array_slice(explode(' ', $blog['blog_content']), 0, 50));

                                        // Output blog content with "See more" link if applicable
                                ?>
                                        <div class="bg-white border p-4 mb-4 mt-5 position-relative" style="border-radius: 10px;">
                                            <div class="flex items-center">
                                                <img id="blog_profile_pic_<?php echo $blog['blog_id']; ?>" src="<?php echo htmlspecialchars($selected_enabler['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_enabler['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full border">
                                                <div class="ml-4">
                                                    <h2 class="text-xl font-bold"><?php echo $selected_enabler['setting_institution']; ?></h2>
                                                    <p class="text-sm text-gray-600"><?php echo date('F j, Y | g:i A', strtotime($blog['blog_dateadded'])); ?></p>
                                                </div>
                                            </div>
                                            <h2 class="text-sm font-bold mt-3"><?php echo $blog['blog_title']; ?></h2>
                                            <img id="blog_pic_<?php echo $blog['blog_id']; ?>" alt="<?php echo $blog['blog_img']; ?>" class="w-full h-full object-cover mb-2 mt-3 border" style="background-color: #ffffff;">
                                            <p class="text-sm text-black-800 mt-3">
                                                <?php echo $display_content; ?>
                                                <?php if ($display_see_more) { ?>
                                                    <span class="full-content hidden"><?php echo $blog['blog_content']; ?></span>
                                                    <span class="see-more position-absolute bottom-0 end-0 mb-2 me-2"> <a href="#" class="see-more-link">See more</a></span>
                                                <?php } ?>
                                            </p>
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
                            <script>
                                // Add event listener for "See more" link
                                document.querySelectorAll('.see-more-link').forEach(link => {
                                    link.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const parent = e.target.closest('.border');
                                        parent.querySelector('.full-content').classList.toggle('hidden');
                                        e.target.textContent = e.target.textContent === 'See more' ? 'See less' : 'See more';
                                    });
                                });
                            </script>
                        </div>

                        <div id="mentorsContent" style="display: none;">
                            <h10>Mentors</h10>
                            <?php
                            // Fetch mentors for the specific enabler
                            $mentor_url = "http://217.196.51.115/m/api/mentor/list/enabler/";
                            $enabler_id = $selected_enabler['member_id'];
                            $mentors = fetch_api_data($mentor_url . $enabler_id);

                            if ($mentors && !empty($mentors)) {
                                // Display mentors
                                foreach ($mentors as $mentor) {
                            ?>
                                    <div class="flex items-center mt-10 mb-6 mr-5">
                                        <!-- Load mentor profile picture -->
                                        <div class="relative flex items-start">
                                            <!-- Mentor name -->
                                            <p class="absolute top-16 left-0 ml-10 mt-0 text-lg border border-blue-500 p-16" style="border-radius: 16px; border-color: #599EF3; z-index: 0;"><?php echo $mentor['mentor_name']; ?></p>
                                            <!-- Profile picture -->
                                            <img id="mentor_profile_pic_<?php echo $mentor['mentor_id']; ?>" alt="<?php echo $mentor['mentor_profile_pic']; ?>" class="w-32 h-32 rounded-full object-cover border-8 border-blue-500 relative z-10">
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                // Display message if no mentors found
                                echo "<p>No mentors yet.</p>";
                            }
                            ?>
                        </div>

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

                                        // Set the new src value using a blob URL
                                        imgElement.src = URL.createObjectURL(blob);
                                    })
                                    .catch(error => console.error('Error fetching image data:', error));
                            }

                            // Update each mentor's profile picture
                            const mentorImages = document.querySelectorAll('[id^="mentor_profile_pic_"]');
                            mentorImages.forEach(img => updateImageSrc(img));
                        </script>

                        <div id="aboutContent" style="display: none;">
                            <h10>About</h10>
                            <div id="aboutContent" class="mt-5">
                                <div class="contact-info-container mb-10">
                                    <div class="contact-info-item">
                                        <h8 class="text-sm font-bold">Contact Number</h8>
                                        <?php if (!empty($selected_enabler['contact_number'])) : ?>
                                            <p class="text-sm mb-3 mt-3 border rec">
                                                <i class="fas fa-phone-alt mr-2"></i><?php echo $selected_enabler['contact_number']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="contact-info-item">
                                        <h8 class="text-sm font-bold">Email Address</h8>
                                        <?php if (!empty($selected_enabler['email_address'])) : ?>
                                            <p class="text-sm mb-3 mt-3 border rec">
                                                <i class="far fa-envelope mr-2"></i><?php echo $selected_enabler['email_address']; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h8 class="text-sm font-bold">Company Address</h8>
                                <p class="text-sm mb-10 mt-3 border rec"><?php echo $selected_enabler['setting_address']; ?></p>

                                <h8 class="text-sm font-bold">Company Links</h8>
                                <p class="text-sm mb-3">
                                    <?php
                                    if (isset($selected_enabler['links']) && is_array($selected_enabler['links'])) {
                                        foreach ($selected_enabler['links'] as $link) {
                                            if (isset($link['url']) && !empty($link['url'])) {
                                                // Add the website icon before each URL
                                                echo '<div class="border rec mb-3"><i class="fas fa-globe"></i> ' . $link['url'] . "</div>";
                                            }
                                        }
                                    }
                                    ?>
                                </p>

                                <br>

                                <h8 class="text-sm font-bold">Company Description</h8>
                                <p class="text-sm mb-10 mt-3 border rec"><?php echo $selected_enabler['setting_bio']; ?></p>

                                <div id="historyWrapper"></div>
                            </div>
                        </div>

                        <script>
                            // Function to fetch and display history
                            function fetchAndDisplayHistory(memberId) {
                                $.ajax({
                                    url: 'http://217.196.51.115/m/api/history/fetch',
                                    type: 'POST',
                                    contentType: 'application/json',
                                    data: JSON.stringify({
                                        member_id: memberId
                                    }),
                                    success: function(response) {
                                        if (response.length > 0) {
                                            var historyContent = '<h12>History</h12><div class="mt-5" id="historyContent"><ul>';
                                            response.forEach(function(historyItem) {
                                                // Convert date string to JavaScript Date object
                                                var date = new Date(historyItem.history_datecreated);
                                                // Format date
                                                var formattedDate = date.toLocaleString('en-US', {
                                                    month: 'long',
                                                    day: 'numeric',
                                                    year: 'numeric'
                                                });
                                                // Format time
                                                var hours = date.getHours();
                                                var minutes = ('0' + date.getMinutes()).slice(-2);
                                                var ampm = hours >= 12 ? 'PM' : 'AM';
                                                hours = hours % 12;
                                                hours = hours ? hours : 12; // 12-hour clock, so 0 should be 12
                                                var formattedTime = hours + ':' + minutes + ' ' + ampm;

                                                // Concatenate formatted date and time
                                                var formattedDateTime = formattedDate + ' | ' + formattedTime;

                                                // Append to historyContent
                                                historyContent += '<li><div class="history-item"><div class="border rec mb-3">' + historyItem.history_text + '<span class="datetime">' + formattedDateTime + '</span></div></div></li>';
                                            });
                                            historyContent += '</ul></div>';
                                            $('#historyWrapper').html(historyContent);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching history:', error);
                                    }
                                });
                            }

                            // Call the function with the member_id of the specific enabler
                            fetchAndDisplayHistory(<?php echo $selected_enabler['member_id']; ?>);
                        </script>

                        <!-- JavaScript to handle tab switching -->
                        <script>
                            function showContent(tabName, tabBtn) {
                                // Hide all content
                                document.getElementById('eventsContent').style.display = 'none';
                                document.getElementById('blogsContent').style.display = 'none';
                                document.getElementById('mentorsContent').style.display = 'none';
                                document.getElementById('aboutContent').style.display = 'none';

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

    </body>

    </html>

<?php
}
?>