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
$authors = fetch_api_data("http://217.196.51.115/m/api/members/enablers");

if (!$programs || !$authors) {
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
        <title>BINNO | GUIDES</title>

        <style>
            .programImage {
                border-radius: 8px;
            }
        </style>

    </head>

    <body class="bg-gray-100">

        <div class="bg-white">
            <?php include 'navbar-guides.php'; ?>
        </div>

        <main class="flex justify-center">
            <div class="container mx-16">
                <div>
                    <h4 class="mt-5 font-bold text-3xl md:text-5xl">Guides</h4>
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

                <div class="container mx-auto p-8 px-4 md:px-8 lg:px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                        <?php
                        //program_dateadded attribute
                        usort($programs, function ($a, $b) {
                            //program_dateadded is in Y-m-d H:i:s format
                            $dateA = strtotime(isset($a['program_dateadded']) ? $a['program_dateadded'] : 0);
                            $dateB = strtotime(isset($b['program_dateadded']) ? $b['program_dateadded'] : 0);

                            // Sort in descending order (newest to oldest)
                            return $dateB - $dateA;
                        });

                        foreach ($programs as $program) {
                            // Find the author information
                            $author = array_values(array_filter($authors, function ($author) use ($program) {
                                return $author['member_id'] == $program['program_author'];
                            }));

                            // If author exists, get the name and profile picture
                            $authorName = isset($author[0]['setting_institution']) ? $author[0]['setting_institution'] : 'Unknown';
                            $authorProfilePic = isset($author[0]['setting_profilepic']) ? $author[0]['setting_profilepic'] : '';

                        ?>
                                <div class="card-container bg-white">
                                    <a href="<?php echo htmlspecialchars('guides-view.php') . '?program_id=' . (isset($program['program_id']) ? $program['program_id'] : ''); ?>" class="link">
                                        <div style="padding: 20px 20px 5px 20px;">
                                            <img src="<?php echo isset($program['program_img']) ? htmlspecialchars($program['program_img'], ENT_QUOTES, 'UTF-8') : ''; ?>" alt="<?php echo isset($program['program_img']) ? htmlspecialchars($program['program_img'], ENT_QUOTES, 'UTF-8') : ''; ?>" id="dynamicImg-<?php echo $i ?>" class="w-full h-40 object-cover rounded-lg">
                                        </div>
                                        <div class="mt-16 absolute" style="margin-left: -25px;">
                                            <!-- Displaying author profile picture -->
                                            <img src="http://217.196.51.115/m/api/images?filePath=profile-img/<?php echo $authorProfilePic; ?>" alt="<?php echo $authorName; ?>" class="w-20 h-20 rounded-full mr-2 border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center -mt-28">
                                        </div>
                                        <div class="flex items-center mb-5">
                                            <div class="ml-16">
                                                <!-- Displaying program heading -->
                                                <h2 class="text-2xl font-semibold"><?php echo strlen($program['program_heading']) > 15 ? htmlspecialchars(substr($program['program_heading'], 0, 15)) . '...' : htmlspecialchars($program['program_heading']); ?></h2>
                                                <!-- Displaying author name -->
                                                <p class="text-gray-600 text-sm"><?php echo $authorName; ?></p>
                                                <p class="text-gray-600 text-sm">
                                                    <?php
                                                    $formatted_date = date('F j, Y | h:i A', strtotime($program['program_dateadded']));
                                                    echo $formatted_date;
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.querySelector('input[type="text"]');
                const cards = document.querySelectorAll('.card-container');

                searchInput.addEventListener('input', function(event) {
                    const searchTerm = event.target.value.toLowerCase().trim();

                    cards.forEach(card => {
                        const title = card.querySelector('h2').textContent.toLowerCase();
                        const author = card.querySelector('.text-gray-600').textContent.toLowerCase(); // Update class selector to target author name

                        if (title.includes(searchTerm) || author.includes(searchTerm)) { // Check if title or author name includes the search term
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        </script>

    </body>

    </html>
<?php
}
?>