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

$programs = fetch_api_data("http://217.196.51.115/m/api/programs/");

if (!$programs) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch guides";
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
        <title>GUIDES</title>
    </head>

    <body>

        <?php include 'navbar-guides.php'; ?>

        <main class="container mx-16 flex justify-center items-center">
            <div class="container mx-16">
                <h3 class="font-semibold text-3xl md:text-5xl">Guides</h3>

                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php
                        //program_dateadded attribute
                        usort($programs, function ($a, $b) {
                            //program_dateadded is in Y-m-d H:i:s format
                            $dateA = strtotime(isset($a['program_dateadded']) ? $a['program_dateadded'] : 0);
                            $dateB = strtotime(isset($b['program_dateadded']) ? $b['program_dateadded'] : 0);

                            // Sort in descending order (newest to oldest)
                            return $dateB - $dateA;
                        });

                        $i = 0;
                        foreach ($programs as $program) {
                            $i++;
                        ?>
                            <div class="bg-white rounded-lg overflow-hidden shadow-lg">
                                <img src=<?php echo isset($program['program_img']) ? htmlspecialchars($program['program_img'], ENT_QUOTES, 'UTF-8') : ''; ?> alt=<?php echo isset($program['program_img']) ? htmlspecialchars($program['program_img'], ENT_QUOTES, 'UTF-8') : ''; ?> id="dynamicImg-<?php echo $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                <div class="p-4 object-cover ml-5">
                                    <h2 class="text-2xl font-semibold">
                                        <?php
                                        $heading = isset($program['program_heading']) ? $program['program_heading'] : '';
                                        echo htmlspecialchars(strlen($heading) > 15 ? substr($heading, 0, 15) . '...' : $heading, ENT_QUOTES, 'UTF-8');
                                        ?>
                                    </h2>
                                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars(isset($program['program_dateadded']) ? $program['program_dateadded'] : '', ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="mt-1 mb-3 mr-3 flex justify-end">
                                    <a href="<?php echo htmlspecialchars('guides-view.php') . '?program_id=' . (isset($program['program_id']) ? $program['program_id'] : ''); ?>" class="btn-seemore">See Program</a>
                                </div>
                            </div>
                        <?php } ?>
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
                const res = await fetch('http://217.196.51.115/m/api/images?filePath=guide-pics/' + encodeURIComponent(currentSrc))
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