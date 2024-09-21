<footer class="animated-footer">
    <style>
        .animated-footer {
            padding: 10px; /* Adjust padding as needed */
            background-color: rgba(255, 255, 255, 0); /* Transparent background */
            position: fixed;
            bottom: 0;
            left: 0;
            width: auto; /* Only take as much width as content needs */
            max-width: 200px; /* Set a maximum width for the footer */
            height: 50px; /* Set a height if needed */
            pointer-events: none; /* Prevents footer from blocking interactions */
        }
        
        .image-container {
            display: flex;
            align-items: center; /* Vertically aligns image and text */
            position: relative;
            pointer-events: auto; /* Allows interaction with text and image */

            /* add a space between left of screen and container */
            margin-left: 10px;
             /* add a space between bottom of screen and container */
            margin-bottom: 30px;
        }
        
        .footer-image {
            width: 50px; /* Adjust the size as needed */
            margin-right: 5px; /* Space between image and text */
            transition: transform 0.5s ease;
        }
        
        .footer-image:hover {
            transform: scale(2.2); /* Animation on hover */
        }
        
        .footer-text {
            font-family: 'Arial', sans-serif;
            font-size: 12px; /* Adjust font size as needed */
            color: #333; /* Text color */
        }
        .footer-text:hover {
            color: red;
            cursor: pointer;
            transition: color 0.5s ease;
            transform: scale(1.2);
        }
    </style>
    
    <div class="image-container">
        <img src="/uploads/logo.png" alt="Footer Image" class="footer-image">
        <p class="footer-text">Made with pain ❤️</p>
    </div>
</footer>
