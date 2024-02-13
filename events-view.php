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

<body>

    <?php

    // Function to fetch API data
    function fetch_api_data($api_url)
    {
        $response = file_get_contents($api_url);

        if ($response === false) {
            return false;
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
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


    // Get the event ID from the query parameter
    $event_id = isset($_GET['event_id']) ? ($_GET['event_id']) : 0;

    // Check if a valid event ID is provided
    if ($event_id > 0) {
        $events = fetch_api_data("http://217.196.51.115/m/api/events/$event_id");

        if ($events) {
            $event = $events[0];

            // Fetch data from both member APIs
            $enablers = fetch_api_data("http://217.196.51.115/m/api/members/enablers");
            $companies = fetch_api_data("http://217.196.51.115/m/api/members/companies");

            if ($enablers && $companies) {
                // Search for the author's name based on member_id
                $authorName = '';
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

                <?php include 'navbar-events.php'; ?>

                <div class="container mx-auto p-8 max-w-5xl mx-auto">
                    <!-- Back icon with link to 'events' page -->
                    <a href="<?php echo htmlspecialchars('events.php'); ?>" class="blue-back text-lg">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <div class="flex flex-row mb-4 mt-5">
                        <div class="flex items-center">
                            <!-- Display author's profile picture -->
                            <img src="" alt="<?php echo htmlspecialchars($authorProfilePicUrl); ?>" id="author_profile_pic" class="w-16 h-16 object-cover rounded-full border-2 border-white shadow-lg mb-2">
                            <div class="ml-4">
                                <?php echo '<h2 class="text-xl font-semibold">' . htmlspecialchars($authorName) . '</h2>'; ?>
                                <p class="text-gray-600">Created: <?php echo date('F j, Y', strtotime($event['event_datecreated'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <img id="event_pic" src="<?php echo htmlspecialchars($event['event_img']); ?>" alt="<?php echo htmlspecialchars($event['event_img']); ?>" class="w-full h-full object-cover mb-2" style="background-color: #888888;">
                    <p class="text-sm text-gray-600 mb-2 mt-2">Event Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                    <h2 class="text-2xl font-semibold mt-5 mb-2"><?php echo htmlspecialchars($event['event_title']); ?></h2>
                    <p class="mb-5"><?php echo htmlspecialchars($event['event_description']); ?></p>
                </div>

                <?php
                // Load and display author's profile picture
                loadProfilePic($authorProfilePicUrl, 'author_profile_pic');
                ?>

        <?php
            } else {
                echo '<p>No event found.</p>';
            }
        } else {
            echo '<p>Invalid event ID.</p>';
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
            updateImageSrc(document.getElementById('event_pic'));

            // Ensure the post picture is displayed after the DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                const loadImage = async () => {
                    const currentSrc = document.getElementById('event_pic').alt;
                    const res = await fetch(
                        `http://217.196.51.115/m/api/images?filePath=event-pics/${encodeURIComponent(currentSrc)}`
                    );

                    const blob = await res.blob();
                    const imageUrl = URL.createObjectURL(blob);

                    document.getElementById('event_pic').src = imageUrl;
                }

                loadImage();
            });
        </script>

        <?php include 'footer.php'; ?>

</body>

</html>

<?php
    }
?>