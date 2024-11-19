<?php
session_start();
require_once('config.php');

use GuzzleHttp\Client;

$training_id = $_SESSION['training_id'];
// echo "<script type='text/javascript'>alert('Training ID: $training_id');</script>";

function generateSyllabus($skill_name, $proficiency) {
  $client = new Client();
  $response = $client->post('http://127.0.0.1:5000/generate_syllabus', [
    'json' => ['skill_name' => $skill_name, 'proficiency' => $proficiency]
  ]);

  if ($response->getStatusCode() != 200) {
    return false;
  }

  $body = $response->getBody();
  $data = json_decode($body, true);

  return $data;
}

if (isset($_POST['editTraining'])) {
  $skill = $_POST['skill_chosen'];
  $progprof = $_POST['proficiency_chosen'];
  $training_name = $_POST['training_name'];
  $description = $_POST['description'];
  $syllabus = $_POST['syllabus'];
  $start_date = empty($_POST['start_date']) ? null : $_POST['start_date'];
  $end_date = empty($_POST['end_date']) ? null : $_POST['end_date'];
  $status = $_POST['status'];

  // Update licenses table
  // Prepare the SQL query
  $updateTrainingQuery = "
  UPDATE training_programs
  SET training_name = ?,
  description = ?,
  syllabus = ?,
  start_date = ?,
  end_date = ?,
  skill_name = ?,
  proficiency_level = ?,
  status = ?
  WHERE training_id = ?
  ";

  // Prepare the statement
  $stmt = mysqli_prepare($conn, $updateTrainingQuery);

  // Bind parameters
  mysqli_stmt_bind_param(
    $stmt,
    'ssssssssi',
    $training_name,
    $description,
    $syllabus,
    $start_date,
    $end_date,
    $skill,
    $progprof,
    $status,
    $training_id
  );

  // Execute the statement
  mysqli_stmt_execute($stmt);

  echo "<script>
  alert('Training program updated successfully!');
  window.location.href = 'training_program_details.php';
  </script>";
  exit;

}

// Fetch training data based on training_id
if (isset($training_id)) {
  $query = "SELECT
  tp.*
  FROM
  training_programs tp
  WHERE
  tp.training_id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $training_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $training = $result->fetch_assoc();
}

$proficiency_chosen = isset($_POST['hidden_proficiency_chosen']) ? $_POST['hidden_proficiency_chosen'] : '';
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
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
      Content of the page depending of the sidebar menu chosen
      ---------------------------------------------------------->
      <div class="content p-4 col my-container W-100 offset-1-5" id="content">
        <div class="container mx-auto">
          <h2 class="fs-5">Add Training Program</h2>
          <form id="trainingForm" method="post">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="skillschosenDropdown">Input Skill Name</label>
                <input type="text" id="skill_chosen" class="form-control" name="skill_chosen" id="skill_chosen" value="<?php echo $training['skill_name']; ?>">
              </div>
              <div class="form-group col-md-6">
                <label for="proficiencychosenDropdown">Select Proficiency</label>
                <select class="form-control" name="proficiency_chosen" id="proficiency">
                  <option value="1" <?php echo ($proficiency_chosen == 1) || ($training['proficiency_level'] == 1) ? 'selected' : ''; ?>>1 - Basic</option>
                  <option value="2" <?php echo ($proficiency_chosen == 2) || ($training['proficiency_level'] == 2) ? 'selected' : ''; ?>>2 - Intermediate</option>
                  <option value="3" <?php echo ($proficiency_chosen == 3) || ($training['proficiency_level'] == 3) ? 'selected' : ''; ?>>3 - Advanced</option>
                  <option value="4" <?php echo ($proficiency_chosen == 4) || ($training['proficiency_level'] == 4) ? 'selected' : ''; ?>>4 - Expert</option>
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
              <input type="text" class="form-control" id="trainingName" name="training_name" value="<?php echo $training['training_name']; ?>">
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control auto-resize" id="description" name="description"><?php echo $training['description']; ?></textarea>
            </div>

            <div class="form-group">
              <label for="syllabus">Syllabus</label>
              <textarea class="form-control auto-resize" id="syllabus" name="syllabus"><?php echo $training['syllabus']; ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="startDate">Start Date</label>
                <div class="input-group">
                  <input type="text" class="form-control datepicker" id="startDate" name="start_date" value="<?php echo htmlspecialchars($training['start_date']); ?>" readonly>
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
                  <input type="text" class="form-control datepicker" id="endDate" name="end_date" value="<?php echo htmlspecialchars($training['end_date']); ?>"readonly>
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
                  <option value="to be approved" <?php echo ($training['status'] == 'to be approved') ? 'selected' : ''; ?>>To be approved</option>
                  <option value="in progress" <?php echo ($training['status'] == 'in progress') ? 'selected' : ''; ?>>In progress</option>
                  <option value="finished" <?php echo ($training['status'] == 'finished') ? 'selected' : ''; ?>>Finished</option>
                </select>
              </div>
            </div>

            <div id="loading" style="display: none;">
              <p>Loading, please wait...</p>
            </div>

            <button type="submit" name="editTraining" class="btn btn-success mt-3">Edit Training Program</button>
          </form>
        </div>
      </div>

    </div>
  </div>

  <script>
  $(document).ready(function() {
    var filters = <?php echo json_encode(isset($filters) ? $filters : []); ?>;
    var attendees = <?php echo json_encode(isset($attendees) ? $attendees : []); ?>;

    // Function to update the attendees container
    // Function to update the attendees container
    function updateAttendeesContainer() {
      $('#attendees-container').empty();
      // Use a Set to track unique employee names
      var uniqueAttendees = new Set();

      // Filter attendees to remove duplicates
      var filteredAttendees = attendees.filter(function(attendee) {
        if (uniqueAttendees.has(attendee.employee_name)) {
          return false; // Duplicate, don't include
        } else {
          uniqueAttendees.add(attendee.employee_name);
          return true; // Not a duplicate, include
        }
      });

      // Update the attendees array with the filtered list
      attendees = filteredAttendees;

      attendees.forEach(function(attendee, index) {
        var tag = $('<div class="tag"></div>');
        tag.text(attendee.employee_name);
        var removeBtn = $('<span class="remove-tag">&times;</span>');
        removeBtn.click(function() {
          attendees.splice(index, 1);
          updateAttendeesContainer();
          $('#attendees-input').val(JSON.stringify(attendees)); // Update hidden input value
        });
        tag.append(removeBtn);
        $('#attendees-container').append(tag);
      });

      // Update hidden input value with the attendees array
      $('#attendees-input').val(JSON.stringify(attendees));
    }


    function updateSkillsContainer() {
      $('#skills-container').empty();
      filters.forEach(function(filter, index) {
        var tag = $('<div class="tag"></div>');
        tag.text(filter.skill_name + ' - ' + filter.proficiency_name);
        var skillidd = filter.id;
        var removeBtn = $('<span class="remove-tag">&times;</span>');
        removeBtn.click(function() {
          filters.splice(index, 1);
          $('input[name="skill_ids[]"][value="' + index + '"]').remove();
          updateSkillsContainer();
          $('#filters-input').val(JSON.stringify(filters));
        });
        tag.append(removeBtn);
        var hiddenInput = `<input type="hidden" name="skill_ids[]" value="${skillidd}">`;
        $('#skills-container').append(tag);
      });
      $('#filters-input').val(JSON.stringify(filters)); // Update the hidden input
    }

    updateAttendeesContainer();
    updateSkillsContainer();

    $('#addSkillButton').click(function() {
      var skillId = $('#skill_name').val();
      var skillName = $('#skill_name option:selected').text();
      var minimumProficiency = $('#proficiencyDropdown').val();
      var minimumProficiencyText = $('#proficiencyDropdown option:selected').text();
      var skillExists = false;
      // Loop through the filters array to check for duplicates
      filters.forEach(function(filter) {
        if (filter.id.toString() === skillId.toString()) {
          skillExists = true;
          // Display an alert with both skill IDs
          //alert('Duplicate Skill ID Found:\nExisting Skill ID: ' + filter.id + '\nNew Skill ID: ' + skillId);
        }
        //alert('Duplicate Skill ID Found:\nExisting Skill ID: ' + filter.id + '\nNew Skill ID: ' + skillId);
      });

      if (skillExists) {
        alert('This skill has already been added.');
        return; // Stop the function if the skill already exists
      }
      filters.push({ id: skillId, skill_name: skillName, proficiency_id: minimumProficiency, proficiency_name: minimumProficiencyText });
      updateSkillsContainer();
      $('#filters-input').val(JSON.stringify(filters)); // Update the hidden input
    });

    function showDropdownResults(container, data) {
      container.empty();
      if (data.length > 0) {
        container.show();
        data.forEach(function(item) {
          var div = $('<div></div>');
          div.text(item.employee_name);
          div.click(function() {
            // Set the display value to employee_name
            container.siblings('input').val(item.employee_name);
            // Store the user_id in the hidden field
            $('#hidden_internal_trainer_id').val(item.user_id);
            container.hide();
          });
          container.append(div);
        });
      } else {
        container.hide();
      }
    }

    function showDropdownResultsemployee(container, data) {
      container.empty();
      if (data.length > 0) {
        container.show();
        data.forEach(function(item) {
          var div = $('<div></div>');
          div.text(item.employee_name);
          div.click(function() {
            attendees.push({
              employee_id: item.user_id,
              employee_name: item.employee_name
            });
            updateAttendeesContainer();
            $('#attendees-input').val(JSON.stringify(attendees));
            container.hide();
          });
          container.append(div);
        });
      } else {
        container.hide();
      }
    }

    $('#employeeSearch').on('input', function() {
      var searchTerm = $(this).val();
      if (searchTerm.length > 2) {
        $.ajax({
          url: 'search_employees.php',
          method: 'GET',
          data: { term: searchTerm },
          success: function(data) {
            var employees = JSON.parse(data);
            showDropdownResultsemployee($('#employeeResults'), employees);
          }
        });
      } else {
        $('#employeeResults').hide();
      }
    });

    $('#trainerType').change(function() {
      var trainerType = $(this).val();
      if (trainerType === 'internal') {
        $('#internalTrainerSection').show();
        $('#externalTrainerSection').hide();
      } else if (trainerType === 'external') {
        $('#internalTrainerSection').hide();
        $('#externalTrainerSection').show();
      }
    });

    $('#internalTrainer').on('input', function() {
      var searchTerm = $(this).val();
      if (searchTerm.length > 2) {
        $.ajax({
          url: 'search_employees.php',
          method: 'GET',
          data: { term: searchTerm },
          success: function(data) {
            var employees = JSON.parse(data);
            showDropdownResults($('#internalTrainerResults'), employees);
          }
        });
      } else {
        $('#internalTrainerResults').hide();
      }
    });

    $('#attendeesExcel').change(function(e) {
      var file = e.target.files[0];
      var reader = new FileReader();
      reader.onload = function(event) {
        var data = new Uint8Array(event.target.result);
        var workbook = XLSX.read(data, {type: 'array'});
        var sheetName = workbook.SheetNames[0];
        var sheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {header: 1});

        var usersTable = {}; // Object to store user data from the users table

        // Fetch all users from the users table
        $.ajax({
          url: 'fetch_users.php', // Create a new PHP file to fetch users
          method: 'GET',
          dataType: 'json',
          success: function(users) {
            users.forEach(function(user) {
              var fullName = user.first_name + ' ' + user.last_name;
              usersTable[fullName] = user.user_id;
            });

            // Process the Excel data
            sheet.forEach(function(row, index) {
              if (index === 0) return; // Skip header row
              var firstName = row[0];
              var lastName = row[1];
              var fullName = firstName + ' ' + lastName;

              if (usersTable[fullName]) {
                var userId = usersTable[fullName];
                attendees.push({
                  employee_id: userId,
                  employee_name: fullName
                });
                updateAttendeesContainer();
                $('#attendees-input').val(JSON.stringify(attendees));
              } else {
                console.log(`User ${fullName} not found in the users table.`);
              }
            });

            $('#attendees-input').val(JSON.stringify(attendees));
          },
          error: function() {
            console.log('Error fetching users from the database.');
          }
        });
      };
      reader.readAsArrayBuffer(file);
    });



    // Initial state setup
    updateFiltersContainer();
    updateAttendeesContainer();
  });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script>
  $(document).ready(function() {
    // Initialize datepickers
    $('.datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true
    });

    // Handle the display of internal/external trainer sections based on selection
    $('#trainerType').change(function() {
      var selectedType = $(this).val();
      if (selectedType == 'internal') {
        $('#internalTrainerSection').show();
        $('#externalTrainerSection').hide();
      } else if (selectedType == 'external') {
        $('#internalTrainerSection').hide();
        $('#externalTrainerSection').show();
      }
    });
  });


  document.getElementById('trainingForm').addEventListener('submit', function() {
    document.getElementById('hidden_skill_chosen').value = document.getElementById('skill_chosen').value;
    document.getElementById('hidden_proficiency_chosen').value = document.getElementById('proficiencychosenDropdown').value;
  });

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
