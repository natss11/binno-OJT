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

    <style>
        /* Default style for card-container */
        .image-container {
            width: 230px;
            height: 230px;
            position: relative;
        }

        /* Media query for smaller screens */
        @media (max-width: 768px) {
            .image-container {
                width: 100px;
                /* Adjust the width as needed for smaller screens */
                height: 100px;
                /* Let the height adjust based on content */
            }
        }

        /* Media query for smaller screens */
        @media (min-width: 768px) and (max-width: 1024px) {
            .image-container {
                width: 150px;
                /* Adjust the width as needed for smaller screens */
                height: 150px;
                /* Let the height adjust based on content */
            }
        }

        .pin-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            color: white;
            padding: 5px;
            border-radius: 50%;
        }

        .pin-icon i {
            font-size: 20px;
        }

        .contact-info-container {
            display: flex;
            justify-content: space-between;
        }

        .contact-info-item {
            width: 48%;
            /* Adjust as needed */
        }
    </style>

</head>

<body class="bg-gray-100">

    <div class="bg-white">
        <?php include 'navbar-companies.php'; ?>
    </div>

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
                <div class="container mx-auto px-4 sm:px-8 md:px-16 lg:px-20 xl:px-64">
                    <div class="bg-white rounded-lg overflow-hidden shadow-md mb-5">
                        <img id="cover_pic_<?php echo $selected_company['member_id']; ?>" src="<?php echo $selected_company['setting_coverpic']; ?>" alt="<?php echo str_replace('profile-cover-img/', '', $selected_company['setting_coverpic']); ?>" class="w-full h-64 object-cover shadow-lg" style="background-color: #ffffff;">
                    </div>
                    <div class="flex flex-col sm:flex-row items-center sm:items-start -mt-20 ml-0 sm:ml-20">
                        <img id="profile_pic_<?php echo $selected_company['member_id']; ?>" src="<?php echo $selected_company['setting_profilepic']; ?>" alt="<?php echo str_replace('profile-img/', '', $selected_company['setting_profilepic']); ?>" class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-lg" style="background-color: #ffffff;">
                        <div class="px-4 py-2 sm:mt-16">
                            <h2 class="text-3xl font-bold mb-2"><?php echo $selected_company['setting_institution']; ?></h2>
                            <p class="text-sm text-gray-600 mb-1">Startup Company</p>
                        </div>
                    </div>
                </div>

                <div class="container mx-auto px-4 sm:px-8 md:px-8 lg:px-20 xl:px-64">

                    <div class="p-4 flex flex-col gap-4">
                        <!-- Tab buttons -->
                        <div class="flex justify-center sm:justify-end gap-10 text-xl">
                            <button class="tab-btn active" onclick="showContent('events', this)">Events</button>
                            <button class="tab-btn" onclick="showContent('blogs', this)">Blogs</button>
                            <button class="tab-btn" onclick="showContent('about', this)">About</button>
                        </div>

                        <!-- Events content -->
                        <div id="eventsContent">
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
                                    <div class="bg-white border p-4 mb-4 mt-5" style="border-radius: 10px;">
                                        <div class="flex items-center">
                                            <img id="event_profile_pic_<?php echo $event['event_id']; ?>" src="<?php echo htmlspecialchars($selected_company['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_company['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full border">
                                            <div class="ml-4">
                                                <h2 class="text-xl font-bold"><?php echo $selected_company['setting_institution']; ?></h2>
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
                                        <h2 class="text-sm font-bold mt-3"><?php echo isset($event['event_title']) ? $event['event_title'] : ''; ?></h2>
                                        <img id="event_pic_<?php echo $event['event_id']; ?>" alt="<?php echo $event['event_img']; ?>" class="w-full h-full object-cover mb-2 mt-3 border" style="background-color: #ffffff;">
                                        <p class="text-sm text-black-800 mb-2 mt-2">
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
                        </div>

                        <!-- Blogs content (hidden by default) -->
                        <div id="blogsContent" style="display: none;">
                            <h10>Blogs</h10>
                            <div id="blogsContent" class="mt-5">
                                <?php
                                // Fetch blogs for the specific member
                                $blogs_url = "http://217.196.51.115/m/api/blogs/";
                                $member_id = $selected_company['member_id'];
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
                                                <img id="blog_profile_pic_<?php echo $blog['blog_id']; ?>" src="<?php echo htmlspecialchars($selected_company['setting_profilepic']); ?>" alt="<?php echo htmlspecialchars(str_replace('profile-img/', '', $selected_company['setting_profilepic'])); ?>" class="w-16 h-16 object-cover rounded-full border">
                                                <div class="ml-4">
                                                    <h2 class="text-xl font-bold"><?php echo $selected_company['setting_institution']; ?></h2>
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

                        <div id="aboutContent" style="display: none;">
                            <h10>About</h10>
                            <div id="aboutContent" class="mt-5">
                                <div class="p-4 bg-white" style="border-radius: 10px;">
                                    <div class="contact-info-container">
                                        <div class="contact-info-item">
                                            <h8 class="text-sm font-bold">Contact Number</h8>
                                            <?php if (!empty($selected_company['contact_number'])) : ?>
                                                <p class="text-sm mb-3 mt-3">
                                                    <i class="fas fa-phone-alt mr-2"></i><?php echo $selected_company['contact_number']; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="contact-info-item">
                                            <h8 class="text-sm font-bold">Email Address</h8>
                                            <?php if (!empty($selected_company['email_address'])) : ?>
                                                <p class="text-sm mb-3 mt-3">
                                                    <i class="far fa-envelope mr-2"></i><?php echo $selected_company['email_address']; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <h8 class="text-sm font-bold">Company Address</h8>
                                    <p class="text-sm mb-10 mt-3"><?php echo $selected_company['setting_address']; ?></p>

                                    <h8 class="text-sm font-bold">Company Description</h8>
                                    <p class="text-sm mb-10 mt-3"><?php echo $selected_company['setting_bio']; ?></p>

                                    <h8 class="text-sm font-bold">Company Links</h8>
                                    <p class="text-sm mb-10 mt-3">
                                        <?php
                                        if (isset($selected_company['links']) && is_array($selected_company['links'])) {
                                            foreach ($selected_company['links'] as $link) {
                                                if (isset($link['url']) && !empty($link['url'])) {
                                                    // Add the website icon before each URL
                                                    echo '<i class="fas fa-globe mb-2 mt-3"></i> ' . $link['url'] . "<br>";
                                                }
                                            }
                                        }
                                        ?>
                                    </p>
                                </div>
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
                                            var historyContent = '<h12>History</h12><div id="historyContent"><ul>';
                                            response.forEach(function(historyItem) {
                                                historyContent += '<li>' + historyItem.history_datecreated + ': ' + historyItem.history_text + '</li>';
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

                            // Call the function with the member_id of the specific company
                            fetchAndDisplayHistory(<?php echo $selected_company['member_id']; ?>);
                        </script>

                    </div>
                </div>

                <!-- JavaScript to handle tab switching -->
                <script>
                    function showContent(tabName, tabBtn) {
                        // Hide all content
                        document.getElementById('eventsContent').style.display = 'none';
                        document.getElementById('blogsContent').style.display = 'none';
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

            <?php
                // Call the function to load cover and profile images
                loadImage('cover_pic_' . $selected_company['member_id'], 'profile-cover-img');
                loadImage('profile_pic_' . $selected_company['member_id'], 'profile-img');
            } else {
                // Handle the case where the selected company or its institution is not found
                echo "Company not found.";
            }
            ?>

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

</body>

</html>