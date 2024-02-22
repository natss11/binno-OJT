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

$blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : null;

$blogs = fetch_api_data("http://binnostartup.site/m/api/blogs/$blog_id");
$authors = fetch_api_data("http://binnostartup.site/m/api/members/enablers");

if (!$blogs || !$authors) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch blogs or authors.";
} else {
    // Find the author name based on the blog author's member_id
    $author_name = '';
    $author_profilepic = ''; // Variable to hold the author's profile picture URL
    foreach ($authors as $author) {
        if ($author['member_id'] === $blogs['blog_author']) {
            $author_name = $author['setting_institution'];
            $author_profilepic = $author['setting_profilepic']; // Get the author's profile picture
            break;
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
        <title>BINNO | BLOGS</title>
    </head>

    <body>

        <?php include 'navbar-blogs.php'; ?>

        <div class="container mx-auto p-8 max-w-5xl mx-auto">
            <!-- Back icon with link to 'blogs' page -->
            <a href="<?php echo htmlspecialchars("blogs.php"); ?>" class="blue-back text-lg">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <main class="container mx-auto p-8 max-w-5xl mx-auto mb-10">

            <div class="flex flex-col">
                <div class="flex items-center mb-3">
                    <?php if ($author_profilepic) : ?>
                        <!-- Display the author's profile picture -->
                        <img src="<?php echo $author_profilepic; ?>" alt="<?php echo $author_profilepic; ?>" id="author_profile_pic" class="w-16 h-16 object-cover rounded-full border-2">
                    <?php endif; ?>
                    <div class="ml-4">
                        <?php if ($author_name) : ?>
                            <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($author_name); ?></h2>
                        <?php endif; ?>
                        <?php if (isset($blogs['blog_dateadded'])) : ?>
                            <p class="text-gray-600 text-sm">
                                <?php
                                $datetime = strtotime($blogs['blog_dateadded']);
                                echo date('F j, Y | h:i A', $datetime);
                                ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($blogs['blog_title'])) : ?>
                    <h2 class="text-3xl font-semibold mt-3"><?php echo htmlspecialchars($blogs['blog_title']); ?></h2>
                <?php endif; ?>
                <?php if (isset($blogs['blog_img'])) : ?>
                    <img id="blog_pic" src="<?php echo $blogs['blog_img']; ?>" alt="<?php echo htmlspecialchars($blogs['blog_img']); ?>" class="mt-5 w-full h-full object-cover shadow-lg" style="background-color: #888888;">
                <?php endif; ?>
                <?php if (isset($blogs['blog_content'])) : ?>
                    <p class="mb-5 mt-5"><?php echo htmlspecialchars($blogs['blog_content']); ?></p>
                <?php endif; ?>
            </div>
        </main>

        <script>
            // Function to update image src from API
            const updateImageSrc = async (imgElement) => {
                // Get the current src value
                var currentSrc = imgElement.alt;

                // Fetch image data from API
                const res = await fetch('http://binnostartup.site/m/api/images?filePath=profile-img/' + encodeURIComponent(currentSrc))
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
            updateImageSrc(document.getElementById('post_pic'));

            // Ensure the post picture is displayed after the DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                const loadImage = async () => {
                    const currentSrc = document.getElementById('blog_pic').alt;
                    const res = await fetch(
                        `http://binnostartup.site/m/api/images?filePath=blog-pics/${encodeURIComponent(currentSrc)}`
                    );

                    const blob = await res.blob();
                    const imageUrl = URL.createObjectURL(blob);

                    document.getElementById('blog_pic').src = imageUrl;
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