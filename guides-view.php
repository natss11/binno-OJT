<?php

// Function to fetch data from API
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
        // Function to load and display profile picture
        function displayProfilePic(profilePicUrl) {
            const imgElement = document.getElementById('author_profile_pic');
            imgElement.src = profilePicUrl;
            imgElement.style.display = 'block'; // Show the image
        }

        // Load the profile picture
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
                displayProfilePic(imageUrl); // Display the profile picture
            })
            .catch(error => {
                console.error('Error fetching profile picture:', error);
            });
    </script>
<?php
}

// Get the program ID from the query parameter
$program_id = isset($_GET['program_id']) ? $_GET['program_id'] : 0;

// Fetch program data
$api_url_programs = "http://217.196.51.115/m/api/programs/$program_id";
$programs = fetch_api_data($api_url_programs);

if (!$programs) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch guides";
} else {
    // Fetch enablers data
    $api_url_enablers = "http://217.196.51.115/m/api/members/enablers";
    $enablers = fetch_api_data($api_url_enablers);

    // Find the author's name based on program_author and member_id
    $author_name = '';
    $author_profile_pic = ''; // Variable to hold the profile picture URL
    if (isset($programs['program_author'])) {
        foreach ($enablers as $enabler) {
            if ($enabler['member_id'] == $programs['program_author']) {
                $author_name = $enabler['setting_institution'];
                $author_profile_pic = $enabler['setting_profilepic']; // Get the author's profile picture
                loadProfilePic($author_profile_pic); // Call the function to load and display the profile picture
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
        <title>BINNO | GUIDES</title>
    </head>

    <body>

        <?php include 'navbar-guides.php'; ?>

        <div class="container mx-auto p-8 px-42">
            <!-- Back icon with link to 'events' page -->
            <a href="<?php echo htmlspecialchars('guides.php'); ?>" class="blue-back text-lg">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="flex flex-col lg:flex-row container mx-auto p-8 lg:px-20">
            <!-- Left column for chapters -->
            <div class="w-full lg:w-1/4 p-4">
                <h1 class="element_h1">Chapters</h1>
                <ul id="chapter-list" class="mt-5">
                    <?php
                    // Display chapters
                    if (isset($programs['program_pages']) && is_array($programs['program_pages'])) {
                        foreach ($programs['program_pages'] as $index => $page) {
                            echo "<li class='mt-4 lg:mt-10'><a href='#' onclick='loadChapter($index)' class='element_a'>{$page['program_pages_title']}</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>

            <!-- Right column for data -->
            <div class="w-full lg:w-3/4 p-4 flex flex-col gap-4 bg-gray-100 mb-10" id="content-container">
                <?php
                // Display initial content
                if ($programs) {
                    echo "<h1 class='element_h1'>" . (isset($programs['program_heading']) ? htmlspecialchars($programs['program_heading']) : '') . "</h1>";
                    echo "<img src='{$programs['program_img']}' alt='{$programs['program_img']}' id='guide_pic' class='w-full h-64 object-cover shadow-lg'>";

                    echo "<div class='flex items-center mt-4 mb-2'>";
                    echo "<img src='$author_profile_pic' alt='$author_profile_pic' id='author_profile_pic' class='w-16 h-16 object-cover rounded-full border-2 border-white shadow-lg'>";
                    echo "<div class='ml-4'>";
                    echo "<h2 class='text-xl font-semibold'>" . htmlspecialchars($author_name) . "</h2>";
                    echo "<p class='text-gray-600 text-sm'>" . (isset($programs['program_dateadded']) ? date('F j, Y', strtotime($programs['program_dateadded'])) : '') . "</p>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "Failed to fetch data.";
                }
                ?>

                <!-- JavaScript to handle tab switching and highlight the selected tab -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Add click event listeners to all chapter links
                        document.querySelectorAll('#chapter-list a').forEach(function(chapterLink) {
                            chapterLink.addEventListener('click', function(event) {
                                event.preventDefault();
                                // Remove 'active' class from all chapter links
                                document.querySelectorAll('#chapter-list a').forEach(function(link) {
                                    link.classList.remove('active');
                                });
                                // Add 'active' class to the clicked chapter link
                                this.classList.add('active');
                                // Load chapter content
                                loadChapter(this.getAttribute('data-index'));
                            });
                        });
                    });

                    // JavaScript function to load chapter content
                    function loadChapter(index) {
                        var chapterData = <?php echo json_encode(isset($programs['program_pages']) ? $programs['program_pages'] : array()); ?>;
                        var contentContainer = document.getElementById('content-container');

                        let contents = '';
                        if (Array.isArray(chapterData) && chapterData.length > 0 && index < chapterData.length) {
                            chapterData[index]['elements'].forEach(element => {
                                contents += `<${element['type']} ${element['attributes']}>${element['content']}</${element['type']}>`;
                            });
                        }

                        contentContainer.innerHTML = contents;
                    }
                </script>
            </div>
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

            // Update author's profile picture
            updateImageSrc(document.getElementById("author_profile_pic"));

            // Update program picture
            updateImageSrc(document.getElementById('guide_pic'));

            // Ensure the program picture is displayed after the DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                const loadImage = async () => {
                    const currentSrc = document.getElementById('guide_pic').alt;
                    const res = await fetch(
                        `http://217.196.51.115/m/api/images?filePath=guide-pics/${encodeURIComponent(currentSrc)}`
                    );

                    const blob = await res.blob();
                    const imageUrl = URL.createObjectURL(blob);

                    document.getElementById('guide_pic').src = imageUrl;
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