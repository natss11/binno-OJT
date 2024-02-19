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

$events = fetch_api_data("http://217.196.51.115/m/api/events/");

if (!$events) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch events.";
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
        <title>BINNO | EVENTS</title>
    </head>

    <body>

        <?php include 'navbar-events.php'; ?>

        <main class="flex justify-center">
            <div class="container mx-16">
                <div class="text-center">
                    <h3 class="font-bold text-3xl md:text-4xl">Events</h3>
                </div>

                <div class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                        ?>
                                <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg">
                                    <a href="events-view.php?event_id=<?php echo $event['event_id']; ?>" class="link">
                                        <img src="<?php echo htmlspecialchars($event['event_img']); ?>" alt="<?php echo htmlspecialchars($event['event_img']); ?>" id="dynamicImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                        <div class="p-4 object-cover ml-5">
                                            <h2 class="text-2xl font-semibold"><?php echo strlen($event['event_title']) > 20 ? htmlspecialchars(substr($event['event_title'], 0, 20)) . '...' : htmlspecialchars($event['event_title']); ?></h2>
                                            <p class="font-semibold text-sm mt-2 mb-2">Event Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
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

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>