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

            <style>
                .recent {
                    background-color: #599EF3;
                    margin-right: 55px;
                    border-bottom-left-radius: 5px;
                    border-bottom-right-radius: 5px;
                }
            </style>

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

                    <div class="my-4 flex flex-col items-center">
                        <!-- Search Bar -->
                        <div style="height: 30px;"></div>

                        <div class="relative" style="width: 700px;">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m4-6a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                                </svg>
                            </span>
                            <input type="text" id="searchInput" placeholder="Search for event title or author" class="pl-10 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500" style="width: calc(100% - 60px); border-radius: 15px;"> <!-- Subtracting 40px for the icon -->
                            <button type="button" id="searchButton" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md" style="border-top-right-radius: 15px; border-bottom-right-radius: 15px;">Search</button>
                        </div>
                    </div>

                    <div id="eventSection" class="container mx-auto p-8 px-4 md:px-8 lg:px-32 flex flex-col md:flex-column">
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
                                    <div class="card-container bg-white overflow-hidden flex">
                                        <a href="events-view.php?event_id=<?php echo $event['event_id']; ?>" class="link flex">
                                            <div class="flex flex-col items-center">
                                                <div style="position: relative; padding: 20px;">
                                                    <img src="<?php echo htmlspecialchars($event['event_img']); ?>" alt="<?php echo htmlspecialchars($event['event_img']); ?>" id="dynamicImg-<?php echo $i ?>" class="h-48 w-36 object-cover" style="background-color: #888888; max-width: 200px; border-radius: 16px; object-fit: cover;">
                                                    <?php if ($authorProfilePicUrl) : ?>
                                                        <img src="http://217.196.51.115/m/api/images?filePath=profile-img/<?php echo urlencode($authorProfilePicUrl); ?>" id="author_profile_pic" alt="<?php echo urlencode($authorProfilePicUrl); ?>" class="h-20 w-20 rounded-full absolute top-4/5 left-1/2 transform -translate-x-1/2 -translate-y-1/2" style="border: 2px solid #f4f4f4; background-color: #888888; object-fit: cover;">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex items-center mt-6 mb-4">
                                                    <p class="font-bold" style="color: #FF7A00; font-size: 14px;"><?php echo $authorName; ?></p>
                                                </div>
                                            </div>
                                            <div class="p-4 w-2/3 flex flex-col justify-between">
                                                <div>
                                                    <h2 class="text-2xl font-semibold"><?php echo strlen($event['event_title']) > 20 ? htmlspecialchars(substr($event['event_title'], 0, 20)) . '...' : htmlspecialchars($event['event_title']); ?></h2>
                                                    <p class="font-semibold text-sm mt-2 mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo ($event['event_address']); ?></p>
                                                    <p class="font-semibold text-sm mt-2 mb-2"><i class="fas fa-calendar-alt mr-1"></i><?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                                                    <p class="font-semibold text-sm mt-2 mb-2"><i class="far fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                                    <p class="mb-2 mt-2">
                                                        <?php
                                                        $words = str_word_count($event['event_description'], 1);
                                                        echo htmlspecialchars(implode(' ', array_slice($words, 0, 20)));
                                                        if (count($words) > 20) {
                                                            echo '...';
                                                        }
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchInput');
                    const cards = document.querySelectorAll('.card-container');

                    searchInput.addEventListener('input', function(event) {
                        const searchTerm = event.target.value.toLowerCase().trim();

                        cards.forEach(card => {
                            const title = card.querySelector('h2').textContent.toLowerCase();
                            const author = card.querySelector('.font-bold').textContent.toLowerCase(); // Assuming .font-bold is used for author name

                            if (title.includes(searchTerm) || author.includes(searchTerm)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                });
            </script>

            <?php loadProfilePic($authorProfilePicUrl); ?>

        </body>

        </html>

<?php
    }
}
?>