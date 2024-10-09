<?php
session_start(); // Start the session

// Set session timestamp if not already set
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = date('H:i'); // Only time
}

// Load the JSON dataset
$jsonData = file_get_contents('symptoms_data.json'); // Ensure this file path is correct
$symptomData = json_decode($jsonData, true);

// Function to find the best possible diagnosis based on user input
function findBestDiagnosis($inputSymptoms, $data) {
    // Convert user input into lowercase and split into individual words (keywords)
    $symptomsArray = explode(' ', strtolower($inputSymptoms));
    $symptomsArray = array_map('trim', $symptomsArray); // Trim whitespaces

    $bestDiagnosis = null;
    $bestMatchCount = 0;

    foreach ($data['symptoms'] as $symptom) {
        $matchCount = 0;

        foreach ($symptomsArray as $inputSymptom) {
            // Match if the symptom in the dataset contains the exact keyword from user input
            if (strpos(strtolower($symptom['symptom']), $inputSymptom) !== false) {
                $matchCount++;
            }
        }

        // If this diagnosis matches more symptoms, it becomes the best diagnosis
        if ($matchCount > $bestMatchCount) {
            $bestMatchCount = $matchCount;
            $bestDiagnosis = $symptom;
        }
    }

    return $bestDiagnosis; // Return the best found diagnosis
}

// Initialize chat history in session if not already set
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Handle chat input or clearing history
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_history'])) {
        // Clear the chat history if the clear button is pressed
        $_SESSION['chat_history'] = [];
        $_SESSION['start_time'] = date('H:i'); // Reset the session start time to only time
    } else {
        // Handle symptom input from the user
        $userInput = trim($_POST['symptom']);
        $diagnosis = findBestDiagnosis($userInput, $symptomData);
        $timestamp = date('H:i'); // Initial timestamp when the message is sent

        // Store user input and bot response in session history
        $_SESSION['chat_history'][] = [
            'user' => [
                'message' => htmlspecialchars($userInput),
                'timestamp' => $timestamp
            ],
            'bot' => $diagnosis ? [
                'diagnosis' => htmlspecialchars($diagnosis['diagnosis']),
                'prescription' => htmlspecialchars($diagnosis['prescription']),
                'timestamp' => $timestamp
            ] : null
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker Chatbot</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard-container {
            display: flex; /* Enable flexbox for the container */
            max-width: 100%; /* Allow full width of the viewport */
            
            padding: 20px;
        }

        .sidebar {
            width: 250px; /* Set width for the sidebar */
            background-color: #fff; /* Background for the sidebar */
            padding: 20px; /* Padding for the sidebar */
           
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Sidebar shadow */
            margin-left: 0; /* Remove left margin */
            margin-top: 0;
        }

        .chat-container {
            flex: 1; /* Take up remaining space */
            padding: 20px;
            background-color: #fff;
            
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            margin-left: 0; /* Set margin-left to 0 */
            max-width: 100%; /* Ensure full width */
        }

        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            position: relative;
        }

        .message {
            margin: 10px 0;
            padding: 12px;
            border-radius: 8px;
            max-width: fit-content;
            display: inline-block;
            position: relative;
            clear: both;
        }

        .user-message {
            text-align: right;
            background-color: #007bff;
            color: #fff;
            margin-left: 20%;
            margin-right: 0;
            float: right;
            border-top-left-radius: 0; /* Remove top left corner rounding */
        }

        .bot-message {
            text-align: left;
            background-color: #e1f5fe;
            color: #333;
            margin-right: 20%;
            margin-left: 0;
            float: left;
            border-top-right-radius: 0; /* Remove top right corner rounding */
        }

        .form-control {
            margin-bottom: 10px;
            border-radius: 20px;
        }

        .btn-primary {
            border-radius: 20px;
        }

        .disclaimer {
            font-size: 0.9em;
            color: #ff0000;
            margin-top: 10px;
            text-align: center;
        }

        .timestamp {
            font-size: 0.8em;
            color: #888;
            text-align: center;
            margin-top: 5px;
            display: block;
        }

        .clear-btn {
            margin-top: 10px;
            background-color: #ff4d4d;
            color: #fff;
            border-radius: 20px;
        }

        /* Icons */
        .message-icon {
            position: absolute;
            top: 12px;
            left: -40px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #007bff;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
        }

        .bot-icon {
            left: -40px;
            background-color: #e1f5fe; /* Softer color for bot icon */
        }

        .user-icon {
            left: -40px;
        }
        .disclaimer {
    font-size: 0.9em; /* Slightly smaller font size */
    color: #ff0000; /* Red color for emphasis */
    margin-top: 20px; /* Space above the disclaimer */
    padding: 10px; /* Padding for breathing room */
    border: 1px solid #ff0000; /* Red border for emphasis */
    border-radius: 8px; /* Rounded corners */
    background-color: #ffe6e6; /* Light red background */
    text-align: center; /* Centered text */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

    </style>
    <script>
    // Function to scroll the chat box to the bottom
    function scrollToBottom() {
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight; // Always scroll to the bottom
    }

    // Call to scroll after loading the chat history
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom(); // Initial scroll to the bottom when the page loads
    });

    // Function to get the user's timezone offset
    function getTimeZoneOffset() {
        const offset = new Date().getTimezoneOffset(); // Get offset in minutes
        return -offset * 60 * 1000; // Convert to milliseconds
    }

    // Function to update the timestamp of messages in real-time
    function updateTimestamps() {
        const messages = document.querySelectorAll('.message');
        const timezoneOffset = getTimeZoneOffset();

        messages.forEach(message => {
            const timestampElem = message.querySelector('.timestamp');
            if (timestampElem) {
                const timeParts = timestampElem.textContent.split(':');
                const date = new Date();
                date.setHours(parseInt(timeParts[0]), parseInt(timeParts[1]), 0, 0);
                const localTime = new Date(date.getTime() + timezoneOffset);
                timestampElem.textContent = localTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        });
    }

    // Update timestamps every minute
    setInterval(updateTimestamps, 60000);

    // Automatically scroll to the bottom when new messages are added
    const chatBox = document.getElementById('chatBox');
    const observer = new MutationObserver(scrollToBottom);
    observer.observe(chatBox, { childList: true, subtree: true });
</script>

</head>

<body>
    <?php include '../resources/includes/p_header.php'; ?> <!-- Include the header file -->
    
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <div class="chat-container">
            <h2 class="text-center">Online Doctor Chatbot</h2>
            <div class="chat-box" id="chatBox">
    <p><strong>Session started at:</strong> <?php echo $_SESSION['start_time']; ?></p>
    <?php
    // Display chat history
    foreach ($_SESSION['chat_history'] as $chat) {
        echo '<div class="message user-message"><div class="message-icon user-icon">U</div><strong>You:</strong> ' . $chat['user']['message'] . '<br><span class="timestamp">' . $chat['user']['timestamp'] . '</span></div>';
        if ($chat['bot']) {
            echo '<div class="message bot-message"><div class="message-icon bot-icon">B</div><strong>Bot:</strong> Based on your input, the most likely diagnosis is:</div>';
            echo '<div class="message bot-message"><strong>Diagnosis:</strong> ' . $chat['bot']['diagnosis'] . '<br><strong>Prescription:</strong> ' . $chat['bot']['prescription'] . '<br><span class="timestamp">' . $chat['bot']['timestamp'] . '</span></div>';
        } else {
            echo '<div class="message bot-message"><div class="message-icon bot-icon">B</div><strong>Bot:</strong> Sorry, I couldn\'t find any diagnoses for your symptoms.<br><span class="timestamp">' . $chat['bot']['timestamp'] . '</span></div>';
        }
    }
    ?>
    <div class="disclaimer">
        <strong>Note:</strong> This chatbot is not a substitute for professional medical advice. Always consult a healthcare provider for accurate diagnosis and treatment.
    </div>
</div>


            <form method="POST" action="">
                <input type="text" class="form-control" name="symptom" placeholder="Enter your symptoms, separated by commas or full sentences" required>
                <button type="submit" class="btn btn-primary btn-block">Check Symptoms</button>
            </form>

            <!-- Clear chat history button -->
            <form method="POST" action="">
                <input type="hidden" name="clear_history" value="1">
                <button type="submit" class="btn btn-danger btn-block clear-btn">Clear Chat History</button>
            </form>
        </div>
    </div>
    <?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
