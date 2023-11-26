<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Add some basic styling for the navigation bar */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        nav {
            background-color: white;
            color: black;
            padding: 10px;
            display: flex;
            justify-content: left;
            align-items: left;
            text-align: center;
        }

        nav img {
            height: 60px; /* Adjust the height as needed */
            margin-right: 10px;
        }
        nav span {
            font-size: 20px;
            font-weight: bold;
            line-height: 40px;
            padding-top: 5px;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .topnav {
            background-color: lightgray; /* Blue background color */
            overflow: hidden;
            color: white; /* Text color */
        }

        .topnav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .topnav button {
            float: right;
            background-color: lightgray; /* Green button color */
            color: black;
            font-weight: bold;
            border: none;
            padding: 14px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
        }

        .dashboard-container {
            padding: 20px;
        }
        .carousel img {
            width: 100%;
            height: auto;
            display: none;
        }

        /* Style for navigation buttons */
        .carousel-nav {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .carousel-nav button {
            background-color: #333;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav>
        <!-- Nestle Logo on the left -->
        <img src="nestle_logo.svg" alt="Nestle Logo">

        <!-- Slogan on the right -->
        <span>Good Food Good Life</span>
    </nav>

    <!-- Add the rest of your content here -->
<div><div class="topnav">
  
  <button onclick="window.location.href='insights.php'">Insights</button>
  <button onclick="window.location.href='display_supplier.php'">Suppliers</button>
  <button onclick="window.location.href='display_product.php'">Products</button>
  <button onclick="window.location.href='display_orders.php'">Orders</button>
  <button onclick="window.location.href='display_employee.php'">Employees</button>
  <button onclick="window.location.href='display_customer.php'">Customers</button>
</div></div>
<div class="carousel">
  
  <img src="fgh.png" alt="Image 1">
  <img src="nestle.png" alt="Image 2">
  <img src="coffee-farming.jpg" alt="Image 3">
  <img src="orgain-innovation.jpg" alt="Image 5">
  
</div>

<!-- Navigation buttons for the carousel -->
<!-- <div class="carousel-nav">
  <button onclick="nextSlide()" >Previous</button>
  <button onclick="nextSlide()" >Next</button>
  
</div> -->

<script>
  let currentSlide = 0;
  const totalSlides = 4; // Update this if you add or remove slides
  const intervalTime = 3000; // Set the interval time in milliseconds (7 seconds)

  function showSlide(index) {
      const slides = document.querySelectorAll('.carousel img');
      slides.forEach((slide, i) => {
          slide.style.display = i === index ? 'block' : 'none';
      });
  }

  function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      showSlide(currentSlide);
  }

  function prevSlide() {
      currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      showSlide(currentSlide);
  }

  // Show the initial slide
  showSlide(currentSlide);

  // Auto-advance the carousel every 7 seconds
  setInterval(() => {
      nextSlide();
  }, intervalTime);
</script>
</body>
</html>