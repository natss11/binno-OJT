<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./dist/output.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <title>EVENTS</title>
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

    // Get the event ID from the query parameter
    $event_id = isset($_GET['event_id']) ? ($_GET['event_id']) : 0;

    // Check if a valid event ID is provided
    if ($event_id > 0) {
        $events = fetch_api_data("http://217.196.51.115/m/api/events/$event_id");

        if ($events) {
            $event = $events[0];
    ?>

            <?php include 'navbar-events.php'; ?>

            <div class="container mx-auto p-8 max-w-5xl mx-auto">
                <!-- Back icon with link to 'events' page -->
                <a href="<?php echo htmlspecialchars('events.php'); ?>" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="flex flex-row mb-4 mt-5">
                    <div>
                        <?php
                        // Assuming wpgetapi_endpoint is a custom function, you might need to replace it
                        ?>
                        <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($event['event_author']); ?></h2>
                        <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($event['event_datecreated']); ?></p>
                    </div>
                </div>
                <img id="event_pic" src="<?php echo htmlspecialchars($event['event_img']); ?>" alt="<?php echo htmlspecialchars($event['event_img']); ?>" class="w-full h-full object-cover mb-2" style="background-color: #888888;">
                <h2 class="text-2xl font-semibold mt-5 mb-2"><?php echo htmlspecialchars($event['event_title']); ?></h2>
                <p class="text-gray-600 mb-5" style="text-align: justify;"><?php echo htmlspecialchars($event['event_description']); ?></p>
            </div>
    <?php
        } else {
            echo '<p>No event found.</p>';
        }
    } else {
        echo '<p>Invalid event ID.</p>';
    }
    ?>
    <script>
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
    </script>

    <?php include 'footer.php'; ?>

</body>

</html>