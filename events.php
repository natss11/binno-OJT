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

function loadProfilePic($authorProfilePic, $elementId)
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
                document.getElementById('<?php echo $elementId; ?>').src = imageUrl;
            })
            .catch(error => {
                console.error('Error fetching profile picture:', error);
                console.log('Profile picture URL:', "<?php echo $authorProfilePic; ?>");
            });
    </script>
    <?php
}

$events = fetch_api_data("http://217.196.51.115/m/api/events/");

if ($events) {
    $event = $events[0];

    // Fetch data from both member APIs
    $enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");
    $companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

    if ($enablers && $companies) {
        // Search for the author's name based on member_id
        $authorName = '';
        $authorProfilePicUrl = '';
        foreach ($enablers as $enabler) {
            if ($enabler['member_id'] == $event['event_author']) {
                $authorName = $enabler['setting_institution'];
                $authorProfilePicUrl = $enabler['setting_profilepic']; // Fetching author's profile picture URL
                break;
            }
        }

        if (!$authorName) {
            foreach ($companies as $company) {
                if ($company['member_id'] == $event['event_author']) {
                    $authorName = $company['setting_institution'];
                    $authorProfilePicUrl = $company['setting_profilepic']; // Fetching author's profile picture URL
                    break;
                }
            }
        }

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
            <title>BINNO | EVENTS</title>
        </head>

        <body class="bg-gray-100">

            <div class="bg-white">
                <?php include 'navbar-events.php'; ?>
            </div>

            <main class="flex justify-center">
                <div class="container mx-16">
                    <div>
                        <h4 class="mt-5 font-bold text-3xl md:text-5xl">Events</h4>
                    </div>

                    <!-- Search Bar -->
                    <div class="my-4 flex justify-center">
                        <div class="relative" style="width: 700px;">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m4-6a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                                </svg>
                            </span>
                            <input type="text" placeholder="Search for a topic or organizer" class="pl-10 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500" style="width: calc(100% - 60px); border-radius: 15px;"> <!-- Subtracting 40px for the icon -->
                            <button type="submit" id="searchButton" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">Search</button>
                        </div>
                    </div>

                    <div class="container mx-auto p-8 px-4 md:px-8 lg:px-32 flex flex-col md:flex-column">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                            <?php
                            // Sort the events array by date in descending order
                            usort($events, function ($a, $b) {
                                return strtotime($b['event_date']) - strtotime($a['event_date']);
                            });

                            $i = 0;
                            foreach ($events as $event) {
                                $i++;
                                // Check if the required properties exist in the current event
                                if (isset($event['event_date']) && isset($event['event_img']) && isset($event['event_title'])) {
                                    // Fetch data from both member APIs
                                    $enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");
                                    $companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

                                    if ($enablers && $companies) {
                                        // Search for the author's name based on member_id
                                        $authorName = '';
                                        $authorProfilePicUrl = '';
                                        foreach ($enablers as $enabler) {
                                            if ($enabler['member_id'] == $event['event_author']) {
                                                $authorName = $enabler['setting_institution'];
                                                $authorProfilePicUrl = $enabler['setting_profilepic']; // Fetching author's profile picture URL
                                                break;
                                            }
                                        }

                                        if (!$authorName) {
                                            foreach ($companies as $company) {
                                                if ($company['member_id'] == $event['event_author']) {
                                                    $authorName = $company['setting_institution'];
                                                    $authorProfilePicUrl = $company['setting_profilepic']; // Fetching author's profile picture URL
                                                    break;
                                                }
                                            }
                                        }
                                    }
                            ?>
                                    <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg flex">
                                        <a href="events-view.php?event_id=<?php echo $event['event_id']; ?>" class="link flex">
                                            <img src="<?php echo htmlspecialchars($event['event_img']); ?>" alt="<?php echo htmlspecialchars($event['event_img']); ?>" id="dynamicImg-<?php echo $i ?>" class="h-32 w-64 object-cover" style="background-color: #888888; max-width: 200px;">
                                            <div class="p-4 w-2/3 flex flex-col justify-between">
                                                <div>
                                                    <h2 class="text-2xl font-semibold"><?php echo strlen($event['event_title']) > 20 ? htmlspecialchars(substr($event['event_title'], 0, 20)) . '...' : htmlspecialchars($event['event_title']); ?></h2>
                                                    <p class="font-semibold text-sm mt-2 mb-2">When: <?php echo date('F j, Y', strtotime($event['event_date'])); ?> | <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                                    <p class="font-semibold text-sm mt-2 mb-2">Where: <?php echo ($event['event_address']); ?></p>
                                                </div>
                                                <div class="flex items-center mt-2">
                                                    <img src="<?php echo $authorProfilePicUrl; ?>" alt="<?php echo $authorProfilePicUrl; ?>" class="h-16 w-16 rounded-full mr-2" id="author_profile_pic_<?php echo $i; ?>">
                                                    <p class="text-sm"><?php echo $authorName; ?></p>
                                                </div>
                                            </div>
                                        </a>
                                        <?php
                                        // Call the function to load profile picture
                                        loadProfilePic($authorProfilePicUrl, 'author_profile_pic_' . $i);
                                        ?>
                                    </div>
                            <?php
                                }
                            }
                            ?>
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
                    const res = await fetch('http://217.196.51.115/m/api/images?filePath=event-pics/' + encodeURIComponent(currentSrc))
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


                // Loop through images with IDs starting with "dynamicImg-"
                document.querySelectorAll('[id^="dynamicImg-"]').forEach(imgElement => {
                    // Update each image's src from the API
                    updateImageSrc(imgElement);
                });
            </script>

        </body>

        </html>

<?php
    }
}
?>