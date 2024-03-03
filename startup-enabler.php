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

function loadImage($id, $filePath, $imgType, $src)
{
?>
    <script>
        const loadImage<?php echo $id . ucfirst($imgType); ?> = async () => {
            const res = await fetch(
                `https://binnostartup.site/m/api/images?filePath=<?php echo $filePath; ?>/${encodeURIComponent('<?php echo $src; ?>')}`
            );

            const blob = await res.blob();
            const imageUrl = URL.createObjectURL(blob);

            document.getElementById('dynamicImg<?php echo ucfirst($imgType); ?>-<?php echo $id; ?>').src = imageUrl;
        }

        loadImage<?php echo $id . ucfirst($imgType); ?>();
    </script>
<?php
}

$enablers = fetch_api_data("https://binnostartup.site/m/api/members/enablers");

if (!$enablers) {
    // Handle the case where the API request failed or returned invalid data
    echo "<script>alert('There are no startup enablers registered yet.'); window.location.href = 'welcome.php';</script>";
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
        <title>BINNO | Startup Enablers</title>

        <style>
            /* Add this CSS to ensure images maintain aspect ratio */
            .card-container img {
                width: 100%;
                height: auto;
            }
        </style>


    </head>

    <body>

        <?php include 'navbar-profiles.php'; ?>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center">
            <h1 class="font-bold text-3xl md:text-6xl text-center mt-5 mb-5" style="color: #ff7a00;">Startup Enablers</h1>
            <p class="text-lg mb-10 mx-20" style="text-align: center;">Welcome to an exciting glimpse into
                the vibrant and dynamic world of startup enablers in the Bicol Region! Nestled in the Philippines,
                this picturesque region is not only known for its natural beauty but also for
                fostering an innovative and thriving entrepreneurial ecosystem. Join us as we explore
                the key players, initiatives, and resources that have transformed Bicol into a hotbed
                for startups, empowering the region's creative minds to turn their ideas into reality
                and shape the future of business.
            </p>

            <!-- Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 mx-20">
                <?php
                $i = 1;
                foreach ($enablers as $enabler) {
                    $i++;
                    $setting_institution = isset($enabler['setting_institution']) ? htmlspecialchars($enabler['setting_institution']) : '';
                    $setting_coverpic = isset($enabler['setting_coverpic']) ? htmlspecialchars(str_replace('profile-cover-img/', '', $enabler['setting_coverpic'])) : '';
                    $setting_profilepic = isset($enabler['setting_profilepic']) ? htmlspecialchars(str_replace('profile-img/', '', $enabler['setting_profilepic'])) : '';
                ?>
                    <div class="card-container bg-white rounded-lg overflow-hidden shadow-md relative">
                        <a onclick="redirectToProfile('<?php echo htmlspecialchars('startup-enabler-profile.php?setting_institution=' . urlencode($setting_institution) . '&member_id=' . urlencode($enabler['member_id'])); ?>')" class="link">
                            <!-- Load cover picture -->
                            <img src="<?php echo $setting_coverpic; ?>" alt="<?php echo $setting_coverpic; ?>" id="dynamicImgCover-<?php echo $i; ?>" class="w-64 h-32 object-cover" style="background-color: #ffffff;">
                            <!-- Load profile picture -->
                            <img src="<?php echo $setting_profilepic; ?>" alt="<?php echo $setting_profilepic; ?>" id="dynamicImgProfile-<?php echo $i; ?>" class="w-32 h-32 object-cover rounded-full -mt-20 square-profile object-cover absolute left-1/2 transform -translate-x-1/2" style="background-color: #ffffff;">

                            <div class="flex flex-col items-center px-4 py-2">
                                <h2 class="text-lg font-semibold mb-2 mt-10"><?php echo $setting_institution; ?></h2>
                                <p class="mb-2 mt-2" style="text-align: center;">
                                    <?php
                                    $words = str_word_count($enabler['setting_bio'], 1);
                                    echo htmlspecialchars(implode(' ', array_slice($words, 0, 7)));
                                    if (count($words) > 7) {
                                        echo '...';
                                    }
                                    ?>
                                </p>
                            </div>
                        </a>
                    </div>

                    <?php
                    // Call the loadImage function for each profile and cover image
                    loadImage($i, 'profile-cover-img', 'Cover', $setting_coverpic);
                    loadImage($i, 'profile-img', 'Profile', $setting_profilepic);
                    ?>
                <?php
                }
                ?>
            </div>

            <script>
                function redirectToProfile(profileUrl) {
                    window.location.href = profileUrl;
                }
            </script>

        </div>

        <script>
            // Function to update cover image src from API
            const updateCoverImageSrc = async (imgElement) => {
                // Get the current src value
                var currentSrc = imgElement.alt;

                // Fetch cover image data from API
                const res = await fetch('https://binnostartup.site/m/api/images?filePath=profile-cover-img/' + encodeURIComponent(currentSrc))
                    .then(response => response.blob())
                    .then(data => {
                        // Create a blob from the response data
                        var blob = new Blob([data], {
                            type: 'image/png'
                        }); // Adjust type if needed

                        // Set the new src value using a blob URL
                        imgElement.src = URL.createObjectURL(blob);
                    })
                    .catch(error => console.error('Error fetching cover image data:', error));
            }

            // Loop through cover images with IDs containing "dynamicImgCover-"
            for (var i = 0; i <= <?php echo count($enablers); ?>; i++) {
                // Update each cover image's src from the API
                updateCoverImageSrc(document.getElementById("dynamicImgCover-" + i));
            }
        </script>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>