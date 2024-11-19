<?php
    session_start();
    require_once('config.php');

    // Fetch training data based on training_id
      $query = "SELECT
      tp.*, CASE
      WHEN tp.proficiency_level = 1 THEN '1 - Basic'
      WHEN tp.proficiency_level = 2 THEN '2 - Intermediate'
      WHEN tp.proficiency_level = 3 THEN '3 - Advanced'
      WHEN tp.proficiency_level = 4 THEN '4 - Expert'
      ELSE 'Unknown Proficiency'
      END AS proficiency_name
      FROM training_programs tp";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);


?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="./static/css/chatbot.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <!--<link rel="stylesheet" href="./Bootstrap/css/bootstrap.min.css">-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/31bb28fdf2.js" crossorigin="anonymous"></script>

        <title>Skills Inventory</title>

        <style>
            .nav-pills li a:hover{
                background-color: #434852;
            }
            .active-nav{
                background-color: #222831;
            }
            .form-control1 {
              width: 60px; /* Set the desired width */
            }
            /* Table css */
            th, td {
              width: 100px; /* Set the desired width */
              text-align: center;
            }
            .table-container1 {
              max-width: 79.2vw;
              overflow-x: auto;
            }
            .table-container1 {
              width: 1300px;
            }
            .offset-1-5 {
              margin-left: 13.5%;
            }

            /* Navbar css */
            .navbar {
              background-color: gray; /* Light Blue color */
            }
            .navbar-nav .nav-link {
              color: white; /* White color */
            }
            .navbar-nav .nav-link.active {
              color: #FF0000; /* Red color for active link */
            }

            .nav-link-with-padding {
              padding-right: 50px; /* Adjust the value as needed */
            }

            .nav-item.welcome-text {
              color: white;
            }
            .btn-danger1{
              background-color: #0000FF;
            }
            .btn-danger1:hover {
              background-color: #00008B; /* Darker blue color */
            }

            .container {
              margin-top: 10px;
              padding: 30px 50px 50px 50px;
              box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
              background-color: #fff; /* Optional: Add a background color */
              max-width: 800px;
          }

          .skill-tag {
              margin: 5px;
              padding: 5px 10px;
              display: inline-block;
          }

          .remove-skill {
              cursor: pointer;
              margin-left: 5px;
          }

          .form-row .form-group {
              margin-bottom: 15px;
          }

          .license-container {
              border: 1px solid #ccc;
              padding: 20px;
              margin-bottom: 20px;
          }

          .license-image {
            max-width: 200px; /* Adjust image size as needed */
            max-height: 200px;
            display: block;
            margin: 0 auto; /* Center align image */
            cursor: pointer; /* Ensure cursor changes to pointer on hover */
        }
        /* Additional styles for modal */
        .modal-content {
          margin: auto;
          display: block;
          width: auto; /* Remove max-width */
          height: auto; /* Remove max-height */
          max-width: 100%; /* Limit width if needed */
          max-height: 100%; /* Limit height if needed */
          position: relative;
      }
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: white;
            font-size: 40px;
            cursor: pointer;
        }
        .caption {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #ccc;
            font-size: 20px;
            text-align: center;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar .dropdown-container {
            display: none;
            padding-left: 0;
        }
        .sidebar .dropdown-container a {
            text-align: center;
            padding: 5px;
        }
        /*new sidebar addition */
        .sidebar {
          transition: width 0.3s;
          z-index: 1000; /* Ensure sidebar stays above content */
        }
        .sidebar.collapsed {
            width: 10px;
            background-color: rgba(0, 0, 0, 0);
        }
        .sidebar.collapsed a span {
            display: none;
        }
        .sidebar.collapsed a{
            display: none;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s;
        }
        .content.collapsed {
            margin-left: 60px;
        }

        /* Adjust dropdown behavior for collapsed sidebar */
        .sidebar.collapsed .dropdown-container {
            left: 60px; /* Align with collapsed sidebar */
            top: 0;
            width: 150px;
        }

        /* Show dropdown on hover when collapsed */
        .sidebar.collapsed .skills-menu:hover .dropdown-container {
            display: block;
        }

        /* Ensure dropdown shows below when not collapsed and clicked */
        .sidebar:not(.collapsed) .skills-menu .dropdown-container {
            top: 100%;
            left: 0;
        }
        .sidebar .toggle-btn {
            position: absolute;
            top: 10px;
            left: 100%;
            transform: translateX(-50%);
            cursor: pointer;
        }

        .see-more-container {
          text-align: center;
        }
        </style>

    </head>
    <body>

    <div class="top-container sticky-top">
        <nav class="navbar navbar-expand-lg">
          <a class="navbar-brand" href="index.php"><img src="safeway.png" alt="Safeway Logo" class="brand-logo" height="30"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto justify-content-center">
                    <li class="nav-item welcome-text">
                      <a>Welcome!</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class ="container-fluid">
        <div class="row flex-nowrap">
          <div class="sidebar bg-dark col-auto min-vh-100 position-fixed" id="sidebar">
              <div class="bg-dark">
                <button class="btn btn-primary toggle-btn" onclick="toggleSidebar()">☰</button>
                  <ul class="nav nav-pills flex-column mt-4">
                    <li class="nav-item">
                      <a href="#" onclick="toggleDropdown(event)" class="nav-link text-white "><i class="fas fa-gear mr-2"></i> <span>Training Programs <i class="fas fa-chevron-down"></i></span></a>
                      <div class="dropdown-container" style="display: block">
                        <a href="training_view.php" class="nav-link text-white active-nav" name="donor-page">
                              <span>View List</span>
                        </a>
                        <a href="add-training.php" class="nav-link text-white" name="donor-page">
                              <span>Add</span>
                        </a>
                      </div>
                    </li>
                  </ul>
              </div>
          </div>

            <!----------------------------------------------------------
            Content of the page depending on the sidebar menu chosen
            ---------------------------------------------------------->

            <div class="content p-4 col my-container W-100 offset-1-5" id="content">
              <div class="container mx-auto">
                <h2 class="fs-5">Training Programs List</h2>
                <br>

                <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="license-container">';
                            echo '<h4>' . htmlspecialchars($row['training_name']) . '</h4>';
                            echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p><strong>Start Date:</strong> ' . htmlspecialchars($row['start_date']) . '</p>';
                            echo '<p><strong>End Date:</strong> ' . htmlspecialchars($row['end_date']) . '</p>';
                            echo '<p><strong>Skill Name:</strong> ' . htmlspecialchars($row['skill_name']) . '</p>';
                            echo '<p><strong>Proficiency Level:</strong> ' . htmlspecialchars($row['proficiency_name']) . '</p>';
                            echo '<p><strong>Status:</strong> ' . htmlspecialchars($row['status']) . '</p>';
                            $_SESSION['training_id'] = $row['training_id'];
                            echo '<div class="see-more-container text-center">
                            <a href="training_program_details.php" class="eye-icon">See More</a>
                            </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No training programs found.</p>';
                    }
                    ?>
            </div>


              </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="licenseModal" class="modal">
        <span class="close" onclick="closeLicenseModal()">&times;</span>
        <img class="modal-content" id="modalLicenseImage">
        <div class="caption" id="modalCaption"></div>
    </div>

    <script>

    var licenseModal = document.getElementById("licenseModal");
    var modalLicenseImg = document.getElementById("modalLicenseImage");
    var modalCaption = document.getElementById("modalCaption");

    function openLicenseModal(imageSrc, caption) {
        modalLicenseImg.src = imageSrc;
        modalCaption.innerHTML = caption;
        licenseModal.style.display = "block";
    }

    function closeLicenseModal() {
        licenseModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == licenseModal) {
            closeLicenseModal();
        }
    };

    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "?action=logout";
        }
    }
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    }
    function toggleDropdown(event) {
        event.preventDefault();
        const dropdownContainer = event.target.closest('a').nextElementSibling;
        dropdownContainer.style.display = dropdownContainer.style.display === 'block' ? 'none' : 'block';
    }
    </script>
    </body>
</html>
