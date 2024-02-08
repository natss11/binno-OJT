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

$posts = fetch_api_data("https://217.196.51.115/m/api/posts/");
$events = fetch_api_data("https://217.196.51.115/m/api/events/");
$blogs = fetch_api_data("https://217.196.51.115/m/api/blogs/");

if (!$posts || !$events || !$blogs) {
    // Handle the case where the API request failed or returned invalid data
    echo "Failed to fetch data.";
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
        <title>BINNO</title>
    </head>

    <body>

        <?php include 'navbar.php'; ?>

        <main class="container mx-16 flex justify-center items-center">
            <div class="container mx-16">

                <!-- Display Startup Posts -->
                <h3 class="font-semibold text-3xl md:text-5xl">Startup Posts</h3>
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="cardContainer">

                        <?php
                        // Sort the posts array by post date in descending order
                        usort($posts, function ($a, $b) {
                            return strtotime($b['post_dateadded']) - strtotime($a['post_dateadded']);
                        });

                        // Display only the first 3 posts
                        for ($i = 0; $i < min(3, count($posts)); $i++) {
                            $post = $posts[$i];
                            $shortened_heading = substr($post['post_heading'], 0, 15);
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <img src="<?= htmlspecialchars($post['post_img']); ?>" alt="<?= htmlspecialchars($post['post_img']); ?>" id="dynamicPostImg-<?= $i + 1 ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <div>
                                            <h2 class="text-2xl font-semibold"><?= htmlspecialchars($post['post_heading']); ?></h2>
                                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($post['post_dateadded']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                    </div>

                    <script>
                        const cardsPerPage = 3;
                        let currentPage = 0;
                        const cards = <?php echo json_encode($posts); ?>;

                        function displayCards() {
                            const cardContainer = document.getElementById('cardContainer');

                            // Clear card container
                            cardContainer.innerHTML = '';

                            const startIndex = currentPage * cardsPerPage;
                            const endIndex = startIndex + cardsPerPage;

                            for (let i = startIndex; i < endIndex && i < cards.length; i++) {
                                const card = document.createElement('div');
                                card.className = 'card-container bg-white rounded-lg overflow-hidden shadow-lg h-full';

                                // Limit the post_heading to 15 characters and append '...'
                                const truncatedHeading = cards[i].post_heading.length > 15 ?
                                    cards[i].post_heading.slice(0, 15) + '...' :
                                    cards[i].post_heading;

                                card.innerHTML = `
    <img src="${cards[i].post_img}" alt="${cards[i].post_img}" id="dynamicPostImg-${i}" class="w-full h-40 object-cover" style="background-color: #888888;">
    <div class="p-4">
        <div class="flex items-center mb-2">
            <div>
                <h2 class="text-2xl font-semibold">${truncatedHeading}</h2>
                <p class="text-gray-600 text-sm">${cards[i].post_dateadded}</p>
            </div>
        </div>
    </div>`;

                                cardContainer.appendChild(card);
                            }
                        }

                        // Initial display
                        displayCards();

                        // Function to fetch image data from API
                        async function updateImageSrc(imgSrc) {
                            imgSrc.src = `https://217.196.51.115/m/api/images?filePath=post-pics/${imgSrc.alt}`
                            console.log(imgSrc)
                        }

                        // Loop through images with IDs containing "dynamicPostImg"
                        for (let i = 0; i < 3; i++) {
                            const imgElement = document.getElementById(`dynamicPostImg-${i}`);
                            if (imgElement) {
                                updateImageSrc(imgElement);
                                console.log(`dynamicPostImg-${i}: `, imgElement);
                            }
                        }
                    </script>
                </div>

                <!-- Display Events -->
                <h3 class="font-semibold text-3xl md:text-5xl">Events</h3>
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="eventCardContainer">

                        <?php
                        // Sort events array by date in descending order
                        usort($events, function ($a, $b) {
                            return strtotime($b['event_datecreated']) - strtotime($a['event_datecreated']);
                        });

                        $i = 0;
                        foreach (array_slice($events, 0, 3) as $event) :
                            $i++;
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <img src="<?= htmlspecialchars($event['event_img']); ?>" alt="<?= htmlspecialchars($event['event_img']); ?>" id="dynamicEventImg-<?= $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <div>
                                            <h2 class="text-2xl font-semibold"><?= htmlspecialchars($event['event_title']); ?></h2>
                                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($event['event_datecreated']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        const eventCardsPerPage = 3;
                        let currentEventPage = 0;
                        const eventCards = <?php echo json_encode($events); ?>;

                        function displayEventCards() {
                            const eventCardContainer = document.getElementById('eventCardContainer');
                            eventCardContainer.innerHTML = '';

                            const startIndex = currentEventPage * eventCardsPerPage;
                            const endIndex = startIndex + eventCardsPerPage;

                            for (let i = 0; i < 3; i++) {
                                const card = document.createElement('div');
                                card.className = 'card-container bg-white rounded-lg overflow-hidden shadow-lg h-full';

                                const truncatedTitle = eventCards[i].event_title.length > 15 ?
                                    eventCards[i].event_title.slice(0, 15) + '...' :
                                    eventCards[i].event_title;

                                card.innerHTML = `
        <img src="${eventCards[i].event_img}" alt="${eventCards[i].event_img}" id="dynamicEventImg-${i}" class="w-full h-40 object-cover" style="background-color: #888888;">
        <div class="p-4">
            <div class="flex items-center mb-2">
                <div>
                    <h2 class="text-2xl font-semibold">${truncatedTitle}</h2>
                    <p class="text-gray-600 text-sm">${eventCards[i].event_datecreated}</p>
                </div>
            </div>
        </div>`;
                                eventCardContainer.appendChild(card);
                            }
                        }

                        // Initial display
                        displayEventCards();

                        // Function to fetch image data from API
                        async function updateImageSrc(imgSrc) {
                            imgSrc.src = `https://217.196.51.115/m/api/images?filePath=event-pics/${imgSrc.alt}`
                            console.log(imgSrc)
                        }

                        // Loop through images with IDs containing "dynamicEventImg"
                        for (var i = 0; i <= 3; i++) {
                            var imgElement = document.getElementById("dynamicEventImg-" + i);
                            updateImageSrc(imgElement);
                            console.log(`dynamicEventImg-${i}: `, imgElement);
                        }
                    </script>
                </div>

                <!-- Display Blogs -->
                <h3 class="font-semibold text-3xl md:text-5xl">Blogs</h3>
                <div class="container mx-auto p-8 px-16 flex flex-col md:flex-column">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="blogCardContainer">

                        <?php
                        // Sort blogs by 'blog_dateadded' in descending order
                        usort($blogs, function ($a, $b) {
                            return strtotime($b['blog_dateadded']) - strtotime($a['blog_dateadded']);
                        });

                        $i = 0;
                        foreach (array_slice($blogs, 0, 3) as $blog) :
                            $i++;
                        ?>
                            <div class="card-container bg-white rounded-lg overflow-hidden shadow-lg h-full">
                                <img src="<?= htmlspecialchars($blog['blog_img']); ?>" alt="<?= htmlspecialchars($blog['blog_img']); ?>" id="dynamicBlogImg-<?= $i ?>" class="w-full h-40 object-cover" style="background-color: #888888;">
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <div>
                                            <h2 class="text-2xl font-semibold"><?= htmlspecialchars($blog['blog_title']); ?></h2>
                                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($blog['blog_dateadded']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <script>
                        const blogCards = <?php echo json_encode($blogs); ?>;

                        function displayBlogCards() {
                            const blogCardContainer = document.getElementById('blogCardContainer');
                            blogCardContainer.innerHTML = '';

                            for (let i = 0; i < 3; i++) {
                                const blogCard = document.createElement('div');
                                blogCard.className = 'card-container bg-white rounded-lg overflow-hidden shadow-lg h-full';

                                // Limiting the display of blog_title to 15 characters and adding '...'
                                const truncatedTitle = blogCards[i].blog_title.length > 15 ?
                                    blogCards[i].blog_title.slice(0, 15) + '...' :
                                    blogCards[i].blog_title;

                                blogCard.innerHTML = `
        <img src="${blogCards[i].blog_img}" alt="${blogCards[i].blog_img}" id="dynamicBlogImg-${i}" class="w-full h-40 object-cover" style="background-color: #888888;">
        <div class="p-4">
            <div class="flex items-center mb-2">
                <div>
                    <h2 class="text-2xl font-semibold">${truncatedTitle}</h2>
                    <p class="text-gray-600 text-sm">${blogCards[i].blog_dateadded}</p>
                </div>
            </div>
        </div>`;
                                blogCardContainer.appendChild(blogCard);
                            }
                        }

                        // Initial display
                        displayBlogCards();

                        // Function to fetch image data from API
                        async function updateBlogImageSrc(imgSrc) {
                            imgSrc.src = `https://217.196.51.115/m/api/images?filePath=blog-pics/${imgSrc.alt}`;
                            console.log(imgSrc);
                        }

                        // Loop through images with IDs containing "dynamicBlogImg"
                        for (let i = 0; i < 3; i++) {
                            const imgElement = document.getElementById(`dynamicBlogImg-${i}`);
                            if (imgElement) {
                                updateBlogImageSrc(imgElement);
                                console.log(`dynamicBlogImg-${i}: `, imgElement);
                            }
                        }
                    </script>
                </div>
            </div>
        </main>

        <?php include 'footer.php'; ?>

    </body>

    </html>

<?php
}
?>