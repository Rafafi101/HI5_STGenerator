<?php
session_start();
require_once('config.php');

require 'vendor/autoload.php';

use GuzzleHttp\Client;

?>

<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!--<link rel="stylesheet" href="./Bootstrap/css/bootstrap.min.css">-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/31bb28fdf2.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script> <!-- Add XLSX library -->


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
  .sidebar .dropdown-container {
    display: inline;
    padding-left: 0;
  }
  .sidebar .dropdown-container a {
    text-align: center;
    padding: 5px;
  }

  .tag-container {
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .tag {
    display: inline-block;
    background-color: #e0e0e0;
    border-radius: 3px;
    padding: 5px 10px;
    margin-right: 5px;
    margin-bottom: 5px;
  }

  .tag .remove-tag {
    margin-left: 10px;
    cursor: pointer;
  }

  .dropdown-results {
    position: absolute;
    z-index: 1000;
    width: 100%;
    border: 1px solid #ccc;
    background-color: #fff;
    max-height: 200px;
    overflow-y: auto;
    display: none; /* Hide by default, show when there are results */
  }

  .dropdown-results div {
    padding: 8px;
    cursor: pointer;
  }

  .dropdown-results div:hover {
    background-color: #f1f1f1;
  }

  /* Ensure the input fields have relative positioning for the dropdown to align correctly */
  .position-relative {
    position: relative;
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
      <form method="post">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <button type="submit" class="btn btn-danger1" style="color: #ffffff;" name="logout">Logout</button>
          </li>
        </ul>
      </form>
    </nav>
  </div>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="sidebar bg-dark col-auto min-vh-100 position-fixed" id="sidebar">
        <div class="bg-dark">
          <button class="btn btn-primary toggle-btn" onclick="toggleSidebar()">☰</button>
          <ul class="nav nav-pills flex-column mt-4">
            <li class="nav-item">
              <a href="#" onclick="toggleDropdown(event)" class="nav-link text-white "><i class="fas fa-gear mr-2"></i> <span>Training Programs <i class="fas fa-chevron-down"></i></span></a>
              <div class="dropdown-container" style="display: block">
                <a href="training_view.php" class="nav-link text-white" name="donor-page">
                  <span>View List</span>
                </a>
                <a href="add-training.php" class="nav-link text-white active-nav" name="donor-page">
                  <span>Add</span>
                </a>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!----------------------------------------------------------
      Content of the page depending of the sidebar menu chosen
      ---------------------------------------------------------->
      <div class="content p-4 col my-container W-100 offset-1-5" id="content">
        <div class="container mx-auto">
          <h2 class="fs-5">Add Training Program</h2>
          <form id="trainingForm" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="skillschosenDropdown">Input Skill Name</label>
                <input type="text" id="skill_chosen" class="form-control" name="skill_chosen" id="skill_chosen">
              </div>
              <div class="form-group col-md-6">
                <label for="proficiencychosenDropdown">Select Proficiency</label>
                <select class="form-control" name="proficiency_chosen" id="proficiency">
                  <option value="1">1 - Basic</option>
                  <option value="2">2 - Intermediate</option>
                  <option value="3">3 - Advanced</option>
                  <option value="4">4 - Expert</option>
                </select>
              </div>

            </div>

            <div class="form-group">
              <button class="btn btn-danger1" name="generate_syllabus" id="generate_syllabus">
                <i style="color: white;">Generate</i>
              </button>
              <br>
              <br>
              <label for="trainingName">Training Name</label>
              <input type="text" class="form-control" id="trainingName" name="training_name">
            </div>


            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control auto-resize" id="description" name="description"></textarea>
            </div>

            <div class="form-group">
              <label for="syllabus">Syllabus</label>
              <textarea class="form-control auto-resize" id="syllabus" name="syllabus"></textarea>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="startDate">Start Date</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" id="startDate" name="start_date" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="fas fa-calendar-alt"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group col-md-6">
                <label for="endDate">End Date</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" id="endDate" name="end_date" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="fas fa-calendar-alt"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="form-group col-md-6">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="to be approved">To be approved</option>
                  <option value="in progress">In progress</option>
                  <option value="finished">Finished</option>
                </select>
              </div>
            </div>

            <div class="form-group position-relative" id="internalTrainerSection" style="display: block;">
              <label for="internalTrainer">Select Internal Trainer</label>
              <input type="text" class="form-control" id="internalTrainer" name="internal_trainer_name">
              <!-- Hidden field to store user_id -->
              <input type="hidden" name="internal_trainer_id" id="hidden_internal_trainer_id">
              <div id="internalTrainerResults" class="dropdown-results"></div>
              <!-- Implement search and display for internal trainers -->
            </div>

            <div class="form-group" id="externalTrainerSection" style="display: none;">
              <label for="externalCompany">Select External Company</label>
              <select class="form-control" id="externalCompany" name="external_company_id">
                <!-- Populate this dropdown with partner companies -->
              </select>
            </div>

            <div class="form-group position-relative">
              <label for="employeesDropdown">Select Employees to Attend</label>
              <input type="text" class="form-control" id="employeeSearch">
              <div id="employeeResults" class="dropdown-results"></div>
              <div class="tag-container" id="attendees-container">
                <!-- Attendees tags will be added here -->
              </div>
              <input type="hidden" name="attendees" id="attendees-input">
              <label for="attendeesExcel">Upload Excel for Attendees</label>
              <input type="file" class="form-control" id="attendeesExcel" name="attendeesExcel">
            </div>

            <div id="loading" style="display: none;">
              <p>Loading, please wait...</p>
            </div>

            <button type="submit" name="submitTraining" class="btn btn-success mt-3">Add Training Program</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <!-- Loading modal -->
  <div class="modal" id="loadingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-body text-center">
          <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
          </div>
          <p>Loading, please wait...</p>
        </div>
      </div>
    </div>
  </div>

  <script>
  $(document).ready(function() {
  $('#generate_syllabus').click(function(event) {
    event.preventDefault(); // Prevent form submission

    var job_Name = $('#skill_chosen').val();
    var proficiency = $('#proficiency').val();

    // Show the loading modal
    //$('#loadingModal').modal('show');

    $.ajax({
      url: 'http://127.0.0.1:5000/generate_syllabus',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        skill_name: job_Name,
        proficiency: proficiency
      }),
      success: function(response) {
        $('#trainingName').val(response.training_name);
        $('#description').val(response.description);
        $('#syllabus').val(response.syllabus);
        autoResizeTextarea($('#description')[0]);
        autoResizeTextarea($('#syllabus')[0]);

        // Trigger input event manually after setting the value
        $('#description').trigger('input');
        $('#syllabus').trigger('input');
        alert("pumasok");
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        // Optionally display an alert or feedback to the user
      },
      complete: function() {
        // Ensure the loading modal is hidden after completion
        $('#loadingModal').modal('hide');
      }
    });
  });

  // Initialize datepickers
  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
  });


});
</script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

  <script

  const textareas = document.querySelectorAll('.auto-resize');

  textareas.forEach(textarea => {
    textarea.addEventListener('input', () => {
      textarea.style.height = 'auto';
      textarea.style.height = textarea.scrollHeight + 'px';
    });

    textarea.addEventListener('paste', () => {
      setTimeout(() => {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
      }, 0);
    });

    // Adjust height on page load
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
  });


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
