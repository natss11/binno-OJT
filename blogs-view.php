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

$blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : null;

$blogs = fetch_api_data("https://binnostartup.site/m/api/blogs/$blog_id");

if (!$blogs) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch blogs.";
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
        <title>BLOGS</title>
    </head>

    <body>

        <?php include 'navbar-blogs.php'; ?>

        <main class="container mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="container mx-auto p-8 max-w-5xl mx-auto">
                <!-- Back icon with link to 'blogs' page -->
                <a href="<?php echo htmlspecialchars("blogs.php"); ?>" class="blue-back text-lg">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                <div class="flex flex-col mt-5">
                    <?php if (isset($blogs['blog_author'])) : ?>
                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($blogs['blog_author']); ?></h3>
                    <?php endif; ?>
                    <?php if (isset($blogs['blog_dateadded'])) : ?>
                        <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($blogs['blog_dateadded']); ?></p>
                    <?php endif; ?>
                    <?php if (isset($blogs['blog_title'])) : ?>
                        <h2 class="text-3xl font-semibold mb-2"><?php echo htmlspecialchars($blogs['blog_title']); ?></h2>
                    <?php endif; ?>
                    <?php if (isset($blogs['blog_img'])) : ?>
                        <img id="blog_pic" src="<?php echo $blogs['blog_img']; ?>" alt="<?php echo htmlspecialchars($blogs['blog_img']); ?>" class="mt-5 w-full h-full object-cover" style="background-color: #888888;">
                    <?php endif; ?>
                    <?php if (isset($blogs['blog_content'])) : ?>
                        <p class="mb-5 mt-5" style="text-align: justify;"><?php echo htmlspecialchars($blogs['blog_content']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <script>
            const loadImage = async () => {
                const currentSrc = document.getElementById('blog_pic').alt
                const res = await fetch(
                    `https://binnostartup.site/m/api/images?filePath=blog-pics/${encodeURIComponent(currentSrc)}`
                )

                const blob = await res.blob();
                const imageUrl = URL.createObjectURL(blob);

                document.getElementById('blog_pic').src = imageUrl;

            }

            loadImage()
        </script>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>